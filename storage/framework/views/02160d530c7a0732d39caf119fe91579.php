<?php $__env->startSection('title', $student->name . ' – ' . $exam->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between print:hidden">
        <div>
            <h2 class="text-lg font-bold text-gray-900"><?php echo e($student->name); ?></h2>
            <p class="text-sm text-gray-500">Roll: <?php echo e($student->roll); ?> · <?php echo e($exam->name); ?> <?php echo e($exam->year); ?></p>
        </div>
        <a href="<?php echo e(route('results.index')); ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
            ← Back
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$result): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-sm text-yellow-800">
            No result calculated yet. Go to Results → Recalculate for this exam.
        </div>
    <?php else: ?>

    
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-900"><?php echo e($result->total_marks); ?></div>
            <div class="text-xs text-gray-500 mt-1">Total / <?php echo e($result->full_marks); ?></div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-blue-600"><?php echo e(number_format($result->percentage, 1)); ?>%</div>
            <div class="text-xs text-gray-500 mt-1">Percentage</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-purple-600"><?php echo e(number_format($result->gpa, 2)); ?></div>
            <div class="text-xs text-gray-500 mt-1">GPA</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <span class="text-2xl font-bold px-3 py-1 rounded-lg <?php echo e($result->grade_badge_color); ?> text-white"><?php echo e($result->grade); ?></span>
            <div class="text-xs text-gray-500 mt-2">Grade</div>
        </div>
        <div class="bg-white rounded-xl border border-<?php echo e($result->is_passed ? 'green' : 'red'); ?>-200 shadow-sm p-4 text-center">
            <div class="text-xl font-bold <?php echo e($result->is_passed ? 'text-green-600' : 'text-red-600'); ?>">
                <?php echo e($result->is_passed ? '✓ PASSED' : '✗ FAILED'); ?>

            </div>
            <div class="text-xs text-gray-500 mt-1"><?php echo e($result->division); ?></div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Subject-wise Breakdown</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Subject</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Components</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $result->subject_details ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50 <?php echo e(!$detail['is_passed'] ? 'bg-red-50/30' : ''); ?>">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900"><?php echo e($detail['subject_name']); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($detail['subject_code'])): ?><div class="text-xs text-gray-400"><?php echo e($detail['subject_code']); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1 justify-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $detail['components']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compName => $cd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="text-xs px-2 py-0.5 rounded
                                    <?php echo e($cd['is_passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'); ?>"
                                      title="Pass: <?php echo e($cd['pass']); ?>">
                                    <?php echo e(strtoupper($compName)); ?>: <?php echo e($cd['obtained']); ?>/<?php echo e($cd['full']); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center font-semibold text-gray-800">
                        <?php echo e($detail['obtained']); ?><span class="text-gray-400 font-normal">/<?php echo e($detail['full']); ?></span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-700"><?php echo e(number_format($detail['percentage'], 1)); ?>%</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-700"><?php echo e($detail['grade']); ?></span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-700"><?php echo e(number_format($detail['gpa'], 2)); ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            <?php echo e($detail['is_passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'); ?>">
                            <?php echo e($detail['is_passed'] ? 'Pass' : 'Fail'); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="flex gap-3 print:hidden">
        <?php $generated = $student->generatedMarksheets()->where('exam_id', $exam->id)->latest()->first(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($generated): ?>
            <a href="<?php echo e(route('marksheets.download', $generated)); ?>"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                ⬇ Download Marksheet
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('marksheets.index')); ?>"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                🖨️ Generate Marksheet
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <a href="<?php echo e(route('sms.index')); ?>"
           class="border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium px-5 py-2 rounded-lg transition-colors">
            💬 Send Result SMS
        </a>
        <button onclick="window.print()"
           class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors ml-auto">
            📄 Export to PDF
        </button>
    </div>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/results/student.blade.php ENDPATH**/ ?>