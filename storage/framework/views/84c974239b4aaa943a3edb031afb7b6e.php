<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visual Editor - <?php echo e($template->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
            background-color: #f1f5f9;
        }
        .editor-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            width: 100%;
        }
        .editor-header {
            height: 36px;
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            z-index: 50;
        }
        #editor_container {
            flex-grow: 1;
            width: 100%;
            height: calc(100% - 48px);
        }
    </style>
    <!-- Load ONLYOFFICE API. Update this IP/Domain if your Docker container is hosted elsewhere -->
    <script type="text/javascript" src="<?php echo e(env('ONLYOFFICE_URL', 'http://' . request()->getHost() . ':8088')); ?>/web-apps/apps/api/documents/api.js"></script>
</head>
<body>
    <div class="editor-wrapper">
        <div class="editor-header">
            <div class="text-sm font-semibold text-gray-800 flex items-center gap-2">
            <span class="text-xl">📝</span> Visual Editor <span class="text-gray-400">|</span> <span class="text-indigo-600"><?php echo e($template->name); ?></span>
        </div>
        <a href="<?php echo e(route('templates.index')); ?>" class="text-sm text-gray-600 hover:text-indigo-600 font-medium px-3 py-1.5 rounded-md hover:bg-indigo-50 transition-colors">
            ← Back to Templates
        </a>
    </div>
    
    <div id="editor_container"></div>

    <script>
        var docEditor;
        var config = <?php echo json_encode($config, 15, 512) ?>;
        
        var innerAlert = function(message) {
            if (console && console.log) console.log(message);
        };

        var onAppReady = function() {
            innerAlert("Document editor ready");
        };

        var onDocumentStateChange = function(event) {
            var title = document.title.replace(/\*$/g, "");
            document.title = title + (event.data ? "*" : "");
        };

        var onRequestSaveAs = function(event) {
            console.log("Save as request: ", event.data);
        };

        var onError = function(event) {
            if (event) innerAlert("ONLYOFFICE Error: " + event.data);
        };

        var onOutdatedVersion = function(event) {
            location.reload(true);
        };

        document.addEventListener("DOMContentLoaded", function() {
            config.events = {
                "onAppReady": onAppReady,
                "onDocumentStateChange": onDocumentStateChange,
                "onRequestSaveAs": onRequestSaveAs,
                "onError": onError,
                "onOutdatedVersion": onOutdatedVersion,
            };

            docEditor = new DocsAPI.DocEditor("editor_container", config);
        });
    </script>
    </div>
</body>
</html>
<?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/editor.blade.php ENDPATH**/ ?>