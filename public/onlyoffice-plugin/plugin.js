(function (window, undefined) {
    let placeholders = [];
    let categories = {};

    window.Asc.plugin.init = function () {
        document.getElementById('listContainer').innerHTML = '<div class="no-results" style="color: blue;">Init function triggered...</div>';
        let apiUrl = '/onlyoffice/placeholders';
        let school_id = window.__SCHOOL_ID || '';
        
        if (school_id) {
            apiUrl += '?school_id=' + school_id;
        }

        fetch(apiUrl)
            .then(res => res.json())
            .then(data => {
                placeholders = data;
                renderList(placeholders);
            })
            .catch(err => {
                document.getElementById('listContainer').innerHTML = '<div class="no-results text-red-500">Failed to load placeholders.</div>';
            });

        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const filtered = placeholders.filter(p => 
                p.label.toLowerCase().includes(query) || 
                p.placeholder.toLowerCase().includes(query) ||
                p.category.toLowerCase().includes(query)
            );
            renderList(filtered, query.length > 0);
        });
    };

    function renderList(list, forceExpand = false) {
        const container = document.getElementById('listContainer');
        container.innerHTML = '';
        
        if (list.length === 0) {
            container.innerHTML = '<div class="no-results">No placeholders found matching your search.</div>';
            return;
        }

        // Group by category
        const grouped = list.reduce((acc, item) => {
            const cat = item.category || 'General';
            if (!acc[cat]) acc[cat] = [];
            acc[cat].push(item);
            return acc;
        }, {});

        // Render groups
        for (const [catName, items] of Object.entries(grouped)) {
            const groupDiv = document.createElement('div');
            groupDiv.className = 'category-group';
            if (!forceExpand && catName !== 'Student') { 
                // By default, collapse non-student groups unless searching
                groupDiv.classList.add('collapsed');
            }

            const headerDiv = document.createElement('div');
            headerDiv.className = 'category-header';
            headerDiv.innerText = catName;
            headerDiv.onclick = function() {
                groupDiv.classList.toggle('collapsed');
            };

            const itemsDiv = document.createElement('div');
            itemsDiv.className = 'category-items';

            items.forEach(item => {
                const btn = document.createElement('button');
                btn.className = 'placeholder-btn';
                btn.draggable = true;
                
                // Set up basic click-to-insert
                btn.onclick = function() {
                    insertText('${' + item.placeholder + '}');
                };

                // Set up drag-and-drop
                btn.ondragstart = function(e) {
                    e.dataTransfer.setData('text/plain', '${' + item.placeholder + '}');
                    e.dataTransfer.effectAllowed = 'copy';
                };

                btn.innerHTML = `
                    <span class="btn-label">${item.label}</span>
                    ${item.description ? `<span class="btn-desc">${item.description}</span>` : ''}
                    <span class="btn-val">\${${item.placeholder}}</span>
                `;
                itemsDiv.appendChild(btn);
            });

            groupDiv.appendChild(headerDiv);
            groupDiv.appendChild(itemsDiv);
            container.appendChild(groupDiv);
        }
    }

    function insertText(text) {
        Asc.scope.textToInsert = text;
        window.Asc.plugin.callCommand(function() {
            var oDocument = Api.GetDocument();
            var oParagraph = Api.CreateParagraph();
            oParagraph.AddText(Asc.scope.textToInsert);
            oDocument.InsertContent([oParagraph], true);
        }, false);
    }

    window.Asc.plugin.button = function (id) {
        this.executeCommand("close", "");
    };

})(window, undefined);
