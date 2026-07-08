<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-6">

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php
            $cards = [
                ['label' => 'Classes',  'value' => $stats['total_classes'],   'icon' => '🏫', 'color' => 'blue'],
                ['label' => 'Students', 'value' => $stats['total_students'],  'icon' => '👥', 'color' => 'green'],
                ['label' => 'Subjects', 'value' => $stats['total_subjects'],  'icon' => '📚', 'color' => 'purple'],
                ['label' => 'Exams',    'value' => $stats['total_exams'],     'icon' => '📝', 'color' => 'yellow'],
            ];
            $colorMap = [
                'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
                'green'  => 'bg-green-50 border-green-200 text-green-700',
                'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
                'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
            ];
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border <?php echo e($colorMap[$card['color']]); ?> p-5 shadow-sm">
            <div class="text-3xl mb-2"><?php echo e($card['icon']); ?></div>
            <div class="text-3xl font-bold text-gray-900"><?php echo e(number_format($card['value'])); ?></div>
            <div class="text-sm text-gray-500 mt-1">Total <?php echo e($card['label']); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="<?php echo e(route('marks.index')); ?>"
               class="flex flex-col items-center gap-2 p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">✏️</span>
                <span class="text-sm font-medium">Enter Marks</span>
            </a>
            <a href="<?php echo e(route('marksheets.index')); ?>"
               class="flex flex-col items-center gap-2 p-4 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">🖨️</span>
                <span class="text-sm font-medium">Generate Marksheets</span>
            </a>
            <a href="<?php echo e(route('templates.create')); ?>"
               class="flex flex-col items-center gap-2 p-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">📄</span>
                <span class="text-sm font-medium">Upload Template</span>
            </a>
            <a href="<?php echo e(route('ocr.index')); ?>"
               class="flex flex-col items-center gap-2 p-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-colors text-center">
                <span class="text-2xl">🔍</span>
                <span class="text-sm font-medium">OCR Import</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Students per Class</h2>
            <canvas id="classChart" height="180"></canvas>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Recent Results</h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentResults->isEmpty()): ?>
                <p class="text-sm text-gray-400 text-center py-8">No results calculated yet.</p>
            <?php else: ?>
                <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between text-sm py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium text-gray-800"><?php echo e($result->student->name); ?></span>
                            <span class="text-gray-400 ml-2 text-xs"><?php echo e($result->exam->name); ?> <?php echo e($result->exam->year); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                <?php echo e($result->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>">
                                <?php echo e($result->grade); ?> / <?php echo e(number_format($result->gpa, 2)); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stats['active_exam']): ?>
    <div class="bg-blue-600 text-white rounded-xl p-5 flex items-center justify-between shadow">
        <div>
            <div class="font-semibold">Active Exam: <?php echo e($stats['active_exam']->name); ?> <?php echo e($stats['active_exam']->year); ?></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stats['active_exam']->end_date): ?>
                <div class="text-blue-200 text-sm mt-1">Ends: <?php echo e($stats['active_exam']->end_date->format('d M Y')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <a href="<?php echo e(route('marks.index')); ?>"
           class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium text-sm hover:bg-blue-50 transition-colors">
            Enter Marks →
        </a>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const ctx = document.getElementById('classChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($classStats->pluck('name'), 15, 512) ?>,
            datasets: [{
                label: 'Students',
                data: <?php echo json_encode($classStats->pluck('count'), 15, 512) ?>,
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/dashboard/index.blade.php ENDPATH**/ ?>