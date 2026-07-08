<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Insert Placeholder</title>
    <script type="text/javascript" src="https://onlyoffice.github.io/sdkjs-plugins/v1/plugins.js"></script>
    <script type="text/javascript" src="https://onlyoffice.github.io/sdkjs-plugins/v1/plugins-ui.js"></script>
    <link rel="stylesheet" href="https://onlyoffice.github.io/sdkjs-plugins/v1/plugins.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #fff;
            padding: 12px;
            color: #333;
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            overflow: hidden;
        }
        .header-text {
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 12px;
            line-height: 1.4;
            flex-shrink: 0;
        }
        .search-box {
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 15px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 13px;
            transition: border-color 0.2s;
            flex-shrink: 0;
        }
        .search-box:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        #listContainer {
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 4px;
        }
        
        /* Category Accordion */
        .category-group {
            margin-bottom: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }
        .category-header {
            background-color: #f9fafb;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 12px;
            color: #374151;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
        }
        .category-header:hover {
            background-color: #f3f4f6;
        }
        .category-header::after {
            content: '▼';
            font-size: 10px;
            color: #9ca3af;
            transition: transform 0.2s;
        }
        .category-group.collapsed .category-header::after {
            transform: rotate(-90deg);
        }
        .category-items {
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            background: #fff;
        }
        .category-group.collapsed .category-items {
            display: none;
        }

        /* Placeholder Items */
        .placeholder-btn {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            border-radius: 4px;
            text-align: left;
            cursor: grab;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
        }
        .placeholder-btn:active {
            cursor: grabbing;
        }
        .placeholder-btn:hover {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        .btn-label {
            font-weight: 600;
            font-size: 12px;
            color: #1f2937;
        }
        .btn-desc {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }
        .btn-val {
            font-size: 10px;
            color: #8b5cf6;
            margin-top: 4px;
            font-family: monospace;
            background: #ede9fe;
            padding: 2px 4px;
            border-radius: 3px;
            display: inline-block;
            align-self: flex-start;
        }
        .no-results {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
            padding: 20px 0;
        }
    </style>
    <script>
        window.__SCHOOL_ID = '';
    </script>
</head>
<body>
    <div class="header-text">
        Click a placeholder to insert it into the document at your cursor.
    </div>
    
    <input type="text" id="searchInput" class="search-box" placeholder="🔍 Search placeholders...">
    
    <div id="listContainer">
        <div class="no-results">Loading placeholders...</div>
    </div>

    <script type="text/javascript" src="/onlyoffice-plugin/plugin.js?v={{ time() }}"></script>
</body>
</html>
