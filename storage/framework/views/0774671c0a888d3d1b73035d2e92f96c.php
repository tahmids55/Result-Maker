
<nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
    <?php
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
            ['route' => 'settings.index',  'icon' => '⚙️', 'label' => 'Settings'],
        ];
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route($item['route'])); ?>"
           <?php if(isset($sidebarOpen)): ?> @click="sidebarOpen = false" <?php endif; ?>
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  <?php echo e(request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route'])
                     ? 'bg-blue-600 text-white'
                     : 'text-slate-300 hover:bg-slate-700 hover:text-white'); ?>">
            <span><?php echo e($item['icon']); ?></span>
            <span><?php echo e($item['label']); ?></span>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</nav>
<?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/layouts/_nav.blade.php ENDPATH**/ ?>