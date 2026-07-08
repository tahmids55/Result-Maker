<?php $__env->startSection('title', 'Exams'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="<?php echo e(route('exams.index')); ?>" class="flex flex-wrap md:flex-nowrap gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="Search exam name or year..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Search
                </button>
                <a href="<?php echo e(route('exams.index')); ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-3 py-2 rounded-lg transition-colors flex items-center">
                    ✕
                </a>
            </div>
        </form>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500"><?php echo e($exams->total()); ?> exams found</p>
        <a href="<?php echo e(route('exams.create')); ?>"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Create Exam
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($exams->isEmpty()): ?>
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">📝</p>
                <p class="text-sm">No exams yet. <a href="<?php echo e(route('exams.create')); ?>" class="text-blue-600 hover:underline">Create one →</a></p>
            </div>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Exam</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Year</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Period</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($exam->name); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($exam->year); ?></td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($exam->start_date): ?>
                                <?php echo e($exam->start_date->format('d M Y')); ?> – <?php echo e($exam->end_date?->format('d M Y') ?? '...'); ?>

                            <?php else: ?>
                                –
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="<?php echo e(route('exams.toggle-active', $exam)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors
                                               <?php echo e($exam->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'); ?>">
                                    <?php echo e($exam->is_active ? '● Active' : '○ Inactive'); ?>

                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?php echo e(route('marks.index')); ?>?exam_id=<?php echo e($exam->id); ?>"
                                   class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-2 py-1 rounded transition-colors">Enter Marks</a>
                                <a href="<?php echo e(route('exams.edit', $exam)); ?>"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded transition-colors">Edit</a>
                                <form method="POST" action="<?php echo e(route('exams.destroy', $exam)); ?>"
                                      @submit.prevent="deleteForm = $event.target; confirmDelete = true;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-200">
                <?php echo e($exams->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/exams/index.blade.php ENDPATH**/ ?>