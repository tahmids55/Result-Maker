<?php $__env->startSection('title', 'Merit List – ' . $class->name . ' ' . $section->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Merit List</h2>
            <p class="text-sm text-gray-500"><?php echo e($class->name); ?> – Section <?php echo e($section->name); ?> · <?php echo e($exam->name); ?> <?php echo e($exam->year); ?></p>
        </div>
        <a href="<?php echo e(route('results.index')); ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">← Back</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->isEmpty()): ?>
            <div class="py-12 text-center text-gray-400 text-sm">No results available. Enter marks and recalculate.</div>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600 w-16">Position</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Roll</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Marks</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50 <?php echo e(!$result->is_passed ? 'bg-red-50/30' : ''); ?>">
                    <td class="px-4 py-3 text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($result->rank === 1): ?> <span class="text-2xl">🥇</span>
                        <?php elseif($result->rank === 2): ?> <span class="text-2xl">🥈</span>
                        <?php elseif($result->rank === 3): ?> <span class="text-2xl">🥉</span>
                        <?php else: ?> <span class="font-bold text-gray-600"><?php echo e($result->rank); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-600 font-medium"><?php echo e($result->student->roll); ?></td>
                    <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e($result->student->name); ?></td>
                    <td class="px-4 py-3 text-center font-bold text-gray-800">
                        <?php echo e($result->total_marks); ?><span class="text-gray-400 font-normal text-xs">/<?php echo e($result->full_marks); ?></span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-700"><?php echo e(number_format($result->percentage, 1)); ?>%</td>
                    <td class="px-4 py-3 text-center font-semibold"><?php echo e(number_format($result->gpa, 2)); ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?php echo e($result->grade_badge_color); ?> text-white"><?php echo e($result->grade); ?></span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-semibold <?php echo e($result->is_passed ? 'text-green-600' : 'text-red-500'); ?>">
                            <?php echo e($result->is_passed ? '✓ Pass' : '✗ Fail'); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/results/merit.blade.php ENDPATH**/ ?>