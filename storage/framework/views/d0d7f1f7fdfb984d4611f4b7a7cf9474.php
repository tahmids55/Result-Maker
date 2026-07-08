<div class="py-4 space-y-4">

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Select Class / Section / Exam / Subject</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Class</label>
                <select wire:model.live="classId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Select Class --</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Section</label>
                <select wire:model.live="sectionId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        <?php echo e(empty($classId) ? 'disabled' : ''); ?>>
                    <option value="">-- Select Section --</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($section->id); ?>"><?php echo e($section->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Exam</label>
                <select wire:model.live="examId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Select Exam --</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($exam->id); ?>"><?php echo e($exam->name); ?> <?php echo e($exam->year); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject (Optional)</label>
                <select wire:model.live="subjectId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                        <?php echo e(empty($sectionId) ? 'disabled' : ''); ?>>
                    <option value="">-- All Subjects --</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            
            <div class="flex items-end">
                <button wire:click="loadMarks" wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="loadMarks">📋 Load Marks</span>
                    <span wire:loading wire:target="loadMarks">Loading...</span>
                </button>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loaded): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-700">
                <?php echo e(count($students)); ?> Students ·
                <?php echo e(count($subjects)); ?> Subjects
            </div>
            <div class="flex gap-2">
                <button wire:click="saveMarks" wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveMarks">💾 Save Marks</span>
                    <span wire:loading wire:target="saveMarks">Saving...</span>
                </button>
                <button wire:click="saveAndCalculateMarks" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveAndCalculateMarks">⚡ Save & Calculate</span>
                    <span wire:loading wire:target="saveAndCalculateMarks">Processing...</span>
                </button>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($errors_)): ?>
        <div class="mx-5 mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-xs font-semibold text-yellow-800 mb-1">⚠ Validation Issues:</p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors_; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="text-xs text-yellow-700">• <?php echo e($err); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="overflow-x-auto scrollbar-thin" x-data="marksGrid()">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b border-r border-gray-200 sticky left-0 bg-slate-50 w-8">#</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b border-r border-gray-200 sticky left-8 bg-slate-50 min-w-[60px]">Roll</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b border-r border-gray-200 sticky left-20 bg-slate-50 min-w-[140px]">Name</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subject['has_sub_subjects']): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subject['sub_subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sub['exam_components']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compName => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="px-2 py-1 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[70px]"
                                            title="<?php echo e($subject['name']); ?> - <?php echo e($sub['name']); ?> - <?php echo e(strtoupper($compName)); ?> (Full: <?php echo e($config['full']); ?>)">
                                            <div class="text-gray-500 font-normal truncate max-w-[70px]"><?php echo e(Str::limit($subject['name'], 8)); ?></div>
                                            <div class="text-indigo-500 font-medium text-[10px] truncate max-w-[70px]"><?php echo e($sub['name']); ?></div>
                                            <div class="text-blue-600 uppercase"><?php echo e($compName); ?></div>
                                            <div class="text-gray-400 font-normal">/<?php echo e($config['full']); ?></div>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subject['exam_components']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compName => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="px-2 py-1 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[70px]"
                                        title="<?php echo e($subject['name']); ?> - <?php echo e(strtoupper($compName)); ?> (Full: <?php echo e($config['full']); ?>)">
                                        <div class="text-gray-500 font-normal truncate max-w-[70px]"><?php echo e(Str::limit($subject['name'], 8)); ?></div>
                                        <div class="text-blue-600 uppercase"><?php echo e($compName); ?></div>
                                        <div class="text-gray-400 font-normal">/<?php echo e($config['full']); ?></div>
                                    </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[60px] bg-blue-50">Total</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[55px] bg-blue-50">%</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-r border-gray-200 min-w-[50px] bg-blue-50">GPA</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-600 border-b border-gray-200 min-w-[50px] bg-blue-50">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $sid = $student['id']; ?>
                    <tr class="hover:bg-gray-50 <?php echo e(($r % 2 === 0) ? 'bg-white' : 'bg-slate-50/50'); ?>">
                        <td class="px-3 py-1.5 text-gray-400 border-b border-r border-gray-100 sticky left-0 <?php echo e(($r % 2 === 0) ? 'bg-white' : 'bg-slate-50'); ?>"><?php echo e($r + 1); ?></td>
                        <td class="px-3 py-1.5 font-mono text-gray-700 border-b border-r border-gray-100 sticky left-8 <?php echo e(($r % 2 === 0) ? 'bg-white' : 'bg-slate-50'); ?>"><?php echo e($student['roll']); ?></td>
                        <td class="px-3 py-1.5 font-medium text-gray-800 border-b border-r border-gray-100 sticky left-20 <?php echo e(($r % 2 === 0) ? 'bg-white' : 'bg-slate-50'); ?>"><?php echo e($student['name']); ?></td>

                        <?php $c = 0; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subject['has_sub_subjects']): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subject['sub_subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sub['exam_components']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compName => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $val      = $marks[$sid][$subject['id']][$sub['id']][$compName] ?? '';
                                        $obtained = (float) $val;
                                        $full     = (float) $config['full'];
                                        $pass     = (float) $config['pass'];
                                        $isOver   = $val !== '' && $obtained > $full;
                                        $isFail   = $val !== '' && $obtained < $pass && $val !== '';
                                    ?>
                                    <td class="border-b border-r border-gray-100 p-0">
                                        <input type="number"
                                               wire:model.lazy="marks.<?php echo e($sid); ?>.<?php echo e($subject['id']); ?>.<?php echo e($sub['id']); ?>.<?php echo e($compName); ?>"
                                               min="0" max="<?php echo e($config['full']); ?>" step="0.5"
                                               data-row="<?php echo e($r); ?>" data-col="<?php echo e($c); ?>"
                                               @keydown="handleKey($event)"
                                               class="mark-input w-full px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 rounded transition-colors
                                                      <?php echo e($isOver ? 'bg-red-100 text-red-700 ring-1 ring-red-400'
                                                         : ($isFail ? 'bg-orange-50 text-orange-700'
                                                            : 'bg-transparent')); ?>"
                                               placeholder="–">
                                    </td>
                                    <?php $c++; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subject['exam_components']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compName => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $val      = $marks[$sid][$subject['id']][0][$compName] ?? '';
                                    $obtained = (float) $val;
                                    $full     = (float) $config['full'];
                                    $pass     = (float) $config['pass'];
                                    $isOver   = $val !== '' && $obtained > $full;
                                    $isFail   = $val !== '' && $obtained < $pass && $val !== '';
                                ?>
                                <td class="border-b border-r border-gray-100 p-0">
                                    <input type="number"
                                           wire:model.lazy="marks.<?php echo e($sid); ?>.<?php echo e($subject['id']); ?>.0.<?php echo e($compName); ?>"
                                           min="0" max="<?php echo e($config['full']); ?>" step="0.5"
                                           data-row="<?php echo e($r); ?>" data-col="<?php echo e($c); ?>"
                                           @keydown="handleKey($event)"
                                           class="mark-input w-full px-2 py-1.5 text-center text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 rounded transition-colors
                                                  <?php echo e($isOver ? 'bg-red-100 text-red-700 ring-1 ring-red-400'
                                                     : ($isFail ? 'bg-orange-50 text-orange-700'
                                                        : 'bg-transparent')); ?>"
                                           placeholder="–">
                                </td>
                                <?php $c++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <td class="px-2 py-1.5 text-center font-semibold text-gray-800 border-b border-r border-gray-100 bg-blue-50/40">
                            <?php echo e(isset($rowTotals[$sid]) ? number_format($rowTotals[$sid], 1) : '–'); ?>

                        </td>
                        <td class="px-2 py-1.5 text-center text-gray-700 border-b border-r border-gray-100 bg-blue-50/40">
                            <?php echo e(isset($rowPercentages[$sid]) ? number_format($rowPercentages[$sid], 1) . '%' : '–'); ?>

                        </td>
                        <td class="px-2 py-1.5 text-center text-gray-700 border-b border-r border-gray-100 bg-blue-50/40">
                            <?php echo e(isset($rowGpas[$sid]) ? number_format($rowGpas[$sid], 2) : '–'); ?>

                        </td>
                        <td class="px-2 py-1.5 text-center border-b border-gray-100 bg-blue-50/40">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($rowGrades[$sid])): ?>
                                <span class="px-1.5 py-0.5 rounded text-xs font-bold
                                    <?php echo e(($rowPassed[$sid] ?? false) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'); ?>">
                                    <?php echo e($rowGrades[$sid]); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">–</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="px-5 py-4 border-t border-gray-200 flex justify-between items-center">
            <p class="text-xs text-gray-500">
                💡 Marks are color-coded: <span class="text-orange-600">orange = below pass mark</span>,
                <span class="text-red-600">red = exceeds full marks</span>
            </p>
            <div class="flex gap-2">
                <button wire:click="saveMarks" wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveMarks">💾 Save Marks</span>
                    <span wire:loading wire:target="saveMarks">Saving...</span>
                </button>
                <button wire:click="saveAndCalculateMarks" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove wire:target="saveAndCalculateMarks">⚡ Save & Calculate</span>
                    <span wire:loading wire:target="saveAndCalculateMarks">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <?php elseif($classId && $sectionId && $examId): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-400 shadow-sm">
        <p class="text-4xl mb-3">📋</p>
        <p class="text-sm">Click <strong>Load Marks</strong> to display the spreadsheet.</p>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-400 shadow-sm">
        <p class="text-4xl mb-3">✏️</p>
        <p class="text-sm">Select a class, section, and exam above to begin entering marks.</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('alpine:init', () => {
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
                        if (input.selectionStart === 0) {
                            nextCol = col - 1;
                        }
                        break;
                    case 'ArrowRight':
                        if (input.selectionStart === input.value.length) {
                            nextCol = col + 1;
                        }
                        break;
                    default:
                        return; // Let default behavior happen
                }

                // If moving left/right triggered a column change
                if (nextCol !== col || nextRow !== row) {
                    const nextInput = document.querySelector(`.mark-input[data-row="${nextRow}"][data-col="${nextCol}"]`);
                    if (nextInput) {
                        nextInput.focus();
                        nextInput.select();
                    }
                }
            }
        }))
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/livewire/marks-entry.blade.php ENDPATH**/ ?>