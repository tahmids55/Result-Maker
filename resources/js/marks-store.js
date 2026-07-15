/**
 * ResultMaker — Marks Entry Client State Engine
 * 
 * Manages all cell state, dirty tracking, local GPA/grade calculation,
 * and debounced batch saves. Replaces per-cell wire:model with a single
 * Alpine.js reactive store for 60fps performance.
 */
document.addEventListener('alpine:init', () => {

    Alpine.store('marks', {
        // ── Core State ──
        cells: {},           // { studentId: { subjectId: { subId: { comp: value } } } }
        dirty: [],           // Array of dirty cell keys: "sid_subjId_subId_comp"
        students: [],
        subjects: [],
        gradeMap: [],
        
        // ── UI State ──
        saveState: 'idle',   // idle | saving | saved | error
        lastSaveAt: null,
        errorCount: 0,
        hydrated: false,
        
        // ── Computed Results ──
        rowResults: {},      // { studentId: { total, pct, gpa, grade, passed } }

        // ── Timer ──
        _saveTimer: null,
        _retryCount: 0,
        _wireId: null,

        /**
         * Hydrate the store with server data (called once after Livewire loads)
         */
        hydrate(data) {
            this.cells = JSON.parse(JSON.stringify(data.marks || {}));
            this.students = data.students || [];
            this.subjects = data.subjects || [];
            this.gradeMap = data.gradeMap || [];
            this._wireId = data.wireId;
            this.dirty = [];
            this.saveState = 'idle';
            this.hydrated = true;

            // Calculate all rows
            this.students.forEach(s => this.recalcRow(s.id));
        },

        /**
         * Get a cell value
         */
        getCell(sid, subjId, subId, comp) {
            return this.cells?.[sid]?.[subjId]?.[subId]?.[comp] ?? '';
        },

        /**
         * Set a cell value — triggers local recalc + queues dirty save
         */
        setCell(sid, subjId, subId, comp, val) {
            if (!this.cells[sid]) this.cells[sid] = {};
            if (!this.cells[sid][subjId]) this.cells[sid][subjId] = {};
            if (!this.cells[sid][subjId][subId]) this.cells[sid][subjId][subId] = {};
            this.cells[sid][subjId][subId][comp] = val;

            const key = `${sid}_${subjId}_${subId}_${comp}`;
            if (!this.dirty.includes(key)) {
                this.dirty.push(key);
            }

            this.recalcRow(sid);
            this._scheduleSave();
        },

        /**
         * Recalculate GPA/grade/total for a single student row (client-side)
         */
        recalcRow(sid) {
            let totalObt = 0, totalFull = 0, totalGpa = 0;
            let normalCount = 0, failed = false;

            for (const subj of this.subjects) {
                let subjObt = 0, subjFull = 0, compFailed = false;

                if (subj.has_sub_subjects) {
                    const aggComps = {};

                    for (const sub of (subj.sub_subjects || [])) {
                        for (const [compName, config] of Object.entries(sub.exam_components || {})) {
                            const val = parseFloat(this.getCell(sid, subj.id, sub.id, compName)) || 0;
                            const full = parseFloat(config.full) || 0;
                            const pass = parseFloat(config.pass) || 0;

                            subjObt += val;
                            subjFull += full;

                            if (!aggComps[compName]) aggComps[compName] = { obtained: 0, pass: 0 };
                            aggComps[compName].obtained += val;
                            aggComps[compName].pass += pass;
                        }
                    }

                    for (const data of Object.values(aggComps)) {
                        if (data.obtained < data.pass) compFailed = true;
                    }
                } else {
                    for (const [compName, config] of Object.entries(subj.exam_components || {})) {
                        const val = parseFloat(this.getCell(sid, subj.id, 0, compName)) || 0;
                        const full = parseFloat(config.full) || 0;
                        const pass = parseFloat(config.pass) || 0;

                        subjObt += val;
                        subjFull += full;

                        if (val < pass) compFailed = true;
                    }
                }

                const pct = subjFull > 0 ? (subjObt / subjFull) * 100 : 0;
                let gradeInfo = compFailed ? { grade: 'F', gpa: 0 } : this._lookupGrade(pct);

                if (subj.is_optional) {
                    const bonus = Math.max(0, gradeInfo.gpa - 2.0);
                    totalGpa += bonus;
                    totalObt += Math.max(0, subjObt - 40);
                } else {
                    if (compFailed) {
                        failed = true;
                        gradeInfo = { grade: 'F', gpa: 0 };
                    }
                    totalGpa += gradeInfo.gpa;
                    normalCount++;
                    totalObt += subjObt;
                    totalFull += subjFull;
                }
            }

            const pct = totalFull > 0 ? (totalObt / totalFull * 100) : 0;
            const avgGpa = failed ? 0 : (normalCount > 0 ? Math.min(5, totalGpa / normalCount) : 0);

            this.rowResults[sid] = {
                total: Math.round(totalObt * 10) / 10,
                pct: Math.round(pct * 10) / 10,
                gpa: Math.round(avgGpa * 100) / 100,
                grade: failed ? 'F' : this._gradeFromGpa(avgGpa),
                passed: !failed,
            };
        },

        /**
         * Lookup grade from percentage using the gradeMap
         */
        _lookupGrade(pct) {
            for (const g of this.gradeMap) {
                if (pct >= g.min && pct <= g.max) {
                    return { grade: g.grade, gpa: g.gpa };
                }
            }
            return { grade: 'F', gpa: 0 };
        },

        /**
         * Resolve grade letter from GPA value
         */
        _gradeFromGpa(gpa) {
            if (gpa < 1) return 'F';
            // gradeMap is sorted desc by min_percentage, so gpa is also roughly desc
            let best = 'F';
            for (const g of this.gradeMap) {
                if (gpa >= g.gpa) {
                    best = g.grade;
                    break;
                }
            }
            return best;
        },

        /**
         * Schedule a debounced save (2 seconds after last edit)
         */
        _scheduleSave() {
            clearTimeout(this._saveTimer);
            this._saveTimer = setTimeout(() => this.flushDirty(), 2000);
        },

        /**
         * Flush all dirty cells to the server via Livewire batch endpoint
         */
        async flushDirty() {
            if (this.dirty.length === 0) return;

            this.saveState = 'saving';
            const batch = this.dirty.map(key => {
                const [sid, subjId, subId, comp] = key.split('_');
                return {
                    key: key,
                    value: this.getCell(sid, subjId, subId, comp),
                };
            });

            // Snapshot the dirty keys being saved
            const savedKeys = [...this.dirty];

            try {
                const component = Livewire.find(this._wireId);
                if (!component) throw new Error('Livewire component not found');

                const result = await component.call('saveBatch', batch);
                
                // Remove only the keys that were in this batch (new edits may have arrived)
                this.dirty = this.dirty.filter(k => !savedKeys.includes(k));
                
                this.saveState = 'saved';
                this.lastSaveAt = new Date();
                this.errorCount = result?.errors?.length || 0;
                this._retryCount = 0;

                // Auto-reset status after 3s
                setTimeout(() => {
                    if (this.saveState === 'saved') this.saveState = 'idle';
                }, 3000);
            } catch (e) {
                console.error('Save failed:', e);
                this.saveState = 'error';
                this._retryCount++;
                // Exponential backoff retry
                const delay = Math.min(30000, 1000 * Math.pow(2, this._retryCount));
                setTimeout(() => this.flushDirty(), delay);
            }
        },

        /**
         * Force save all dirty cells immediately (for Save button)
         */
        forceSave() {
            clearTimeout(this._saveTimer);
            this.flushDirty();
        },

        /**
         * Check if a cell value exceeds the full marks
         */
        isOver(sid, subjId, subId, comp, full) {
            const val = this.getCell(sid, subjId, subId, comp);
            return val !== '' && parseFloat(val) > parseFloat(full);
        },

        /**
         * Check if a cell value is below pass marks
         */
        isFail(sid, subjId, subId, comp, pass) {
            const val = this.getCell(sid, subjId, subId, comp);
            return val !== '' && parseFloat(val) < parseFloat(pass);
        },
    });

    /**
     * Alpine component for keyboard navigation within the grid
     */
    Alpine.data('marksGrid', () => ({
        handleKey(e) {
            const input = e.target;
            const row = parseInt(input.dataset.row);
            const col = parseInt(input.dataset.col);

            let nextRow = row;
            let nextCol = col;

            switch (e.key) {
                case 'ArrowUp':
                    nextRow = row - 1;
                    break;
                case 'ArrowDown':
                case 'Enter':
                    nextRow = row + 1;
                    e.preventDefault();
                    break;
                case 'ArrowLeft':
                    if (input.selectionStart === 0) nextCol = col - 1;
                    break;
                case 'ArrowRight':
                    if (input.selectionStart === input.value.length) nextCol = col + 1;
                    break;
                case 'Tab':
                    // Let Tab work naturally for accessibility
                    return;
                default:
                    return;
            }

            if (nextCol !== col || nextRow !== row) {
                const nextInput = document.querySelector(
                    `.mark-input[data-row="${nextRow}"][data-col="${nextCol}"]`
                );
                if (nextInput) {
                    nextInput.focus();
                    nextInput.select();
                }
            }
        }
    }));
});
