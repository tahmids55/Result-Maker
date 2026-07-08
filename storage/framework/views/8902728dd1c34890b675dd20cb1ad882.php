<?php $__env->startSection('title', 'Marksheet Generation History'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">
    <div class="flex items-center justify-between">
        <form method="GET" action="<?php echo e(route('marksheets.history')); ?>" class="flex gap-3">
            <select name="exam_id" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Exams</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($exam->id); ?>" <?php echo e(request('exam_id') == $exam->id ? 'selected' : ''); ?>>
                        <?php echo e($exam->name); ?> <?php echo e($exam->year); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </form>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('bulk-delete-form').submit()"
                    class="border border-red-300 text-red-600 hover:bg-red-50 text-sm px-4 py-2 rounded-lg transition-colors">
                🗑 Delete Selected
            </button>
            <a href="<?php echo e(route('marksheets.index')); ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
                ← Generate More
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($marksheets->isEmpty()): ?>
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">🖨️</p>
                <p class="text-sm">No marksheets generated yet.</p>
            </div>
        <?php else: ?>
            <form id="bulk-delete-form" method="POST" action="<?php echo e(route('marksheets.bulk-delete')); ?>">
            <?php echo csrf_field(); ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox" onchange="document.querySelectorAll('.delete-checkbox').forEach(cb => cb.checked = this.checked)" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Student</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Exam</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Template</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Generated</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $marksheets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sheet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="marksheet_ids[]" value="<?php echo e($sheet->id); ?>" class="delete-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500">
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900"><?php echo e($sheet->student->name); ?></div>
                            <div class="text-xs text-gray-400">Roll: <?php echo e($sheet->student->roll); ?></div>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($sheet->exam->name); ?> <?php echo e($sheet->exam->year); ?></td>
                        <td class="px-4 py-3 text-gray-500 text-xs"><?php echo e($sheet->template->name); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 uppercase">
                                <?php echo e($sheet->file_type); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            <?php echo e($sheet->generated_at?->format('d M Y H:i') ?? '–'); ?>

                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="<?php echo e(route('marksheets.download', $sheet)); ?>"
                               class="text-xs bg-green-50 hover:bg-green-100 text-green-700 font-medium px-3 py-1.5 rounded-lg transition-colors">
                                ⬇ Download
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
            </form>
            <div class="px-4 py-3 border-t border-gray-200">
                <?php echo e($marksheets->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/marksheets/history.blade.php ENDPATH**/ ?>