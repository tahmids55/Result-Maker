{{-- Shared Navigation Partial --}}
<nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
    @php
        $isAdmin = auth()->user()->isAdmin();

        $nav = [];

        if ($isAdmin) {
            $nav = [
                ['route' => 'dashboard',       'icon' => '📊', 'label' => 'Dashboard'],
                ['route' => 'classes.index',   'icon' => '🏫', 'label' => 'Classes'],
                ['route' => 'sections.index',  'icon' => '📂', 'label' => 'Sections'],
                ['route' => 'students.index',  'icon' => '👥', 'label' => 'Students'],
                ['route' => 'subjects.index',  'icon' => '📚', 'label' => 'Subjects'],
                ['route' => 'exams.index',     'icon' => '📝', 'label' => 'Exams'],
                ['route' => 'marks.index',     'icon' => '✏️',  'label' => 'Marks Entry'],
                ['route' => 'results.index',   'icon' => '📈', 'label' => 'Results'],
                ['route' => 'templates.index', 'icon' => '📄', 'label' => 'Templates'],
                ['route' => 'marksheets.index','icon' => '🖨️', 'label' => 'Marksheets'],
                ['route' => 'ocr.index',       'icon' => '🔍', 'label' => 'OCR Import'],
                ['route' => 'sms.index',       'icon' => '💬', 'label' => 'SMS/WhatsApp'],
                ['route' => 'teachers.index',  'icon' => '👨‍🏫', 'label' => 'Teachers'],
                ['route' => 'settings.index',  'icon' => '⚙️', 'label' => 'Settings'],
            ];
        } else {
            // Teacher navigation — only marks entry and results
            $nav = [
                ['route' => 'dashboard',       'icon' => '📊', 'label' => 'Dashboard'],
                ['route' => 'marks.index',     'icon' => '✏️',  'label' => 'Marks Entry'],
                ['route' => 'results.index',   'icon' => '📈', 'label' => 'Results'],
            ];
        }
    @endphp

    @foreach ($nav as $item)
        <a href="{{ route($item['route']) }}"
           @if(isset($sidebarOpen)) @click="sidebarOpen = false" @endif
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route'])
                     ? 'bg-blue-600 text-white'
                     : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <span>{{ $item['icon'] }}</span>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
