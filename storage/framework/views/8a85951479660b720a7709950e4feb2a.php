<?php $__env->startSection('title', $class->name . ' – Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900"><?php echo e($class->name); ?></h2>
            <p class="text-sm text-gray-500"><?php echo e($class->sections->count()); ?> sections · <?php echo e($class->student_count); ?> students</p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('sections.create')); ?>?class_id=<?php echo e($class->id); ?>"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Add Section
            </a>
            <a href="<?php echo e(route('classes.index')); ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
                ← Back
            </a>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $class->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-200">
            <div class="font-semibold text-gray-800">Section <?php echo e($section->name); ?></div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500"><?php echo e($section->student_count); ?> students</span>
                <a href="<?php echo e(route('students.create')); ?>" class="text-xs text-blue-600 hover:underline">+ Student</a>
                <a href="<?php echo e(route('sections.edit', $section)); ?>" class="text-xs text-gray-500 hover:text-gray-700">Edit</a>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($section->students->isEmpty()): ?>
            <div class="px-5 py-6 text-center text-sm text-gray-400">No students in this section yet.</div>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Roll</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Father</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Session</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $section->students->sortBy('roll'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono text-gray-600 text-xs"><?php echo e($student->roll); ?></td>
                    <td class="px-4 py-2 font-medium text-gray-800">
                        <a href="<?php echo e(route('students.show', $student)); ?>" class="hover:text-blue-600"><?php echo e($student->name); ?></a>
                    </td>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?php echo e($student->father_name ?? '–'); ?></td>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?php echo e($student->session ?? '–'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center text-gray-400">
            <p class="text-3xl mb-2">📂</p>
            <p class="text-sm">No sections yet. <a href="<?php echo e(route('sections.create')); ?>" class="text-blue-600 hover:underline">Add a section →</a></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/classes/show.blade.php ENDPATH**/ ?>