<?php $__env->startSection('title', $student->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start gap-5">
            
            <div class="flex-shrink-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->profile_photo): ?>
                    <img src="<?php echo e($student->photo_url); ?>" alt="<?php echo e($student->name); ?>"
                         class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                <?php else: ?>
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-3xl font-bold text-blue-700">
                        <?php echo e(strtoupper(substr($student->name, 0, 1))); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900"><?php echo e($student->name); ?></h2>
                <p class="text-sm text-gray-500">Roll: <strong><?php echo e($student->roll); ?></strong>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->registration_no): ?> · Reg: <strong><?php echo e($student->registration_no); ?></strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Class</span>
                        <div class="font-medium text-gray-800"><?php echo e($student->schoolClass->name); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Section</span>
                        <div class="font-medium text-gray-800"><?php echo e($student->section->name); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Session</span>
                        <div class="font-medium text-gray-800"><?php echo e($student->session ?? '–'); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Father</span>
                        <div class="font-medium text-gray-800"><?php echo e($student->father_name ?? '–'); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-500">Mother</span>
                        <div class="font-medium text-gray-800"><?php echo e($student->mother_name ?? '–'); ?></div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->dob): ?>
                    <div>
                        <span class="text-gray-500">Date of Birth</span>
                        <div class="font-medium text-gray-800"><?php echo e($student->dob->format('d M Y')); ?></div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->phone): ?>
                    <div>
                        <span class="text-gray-500">Phone</span>
                        <div class="font-medium text-gray-800"><?php echo e($student->phone); ?></div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="flex gap-2 flex-shrink-0">
                <a href="<?php echo e(route('students.edit', $student)); ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Edit
                </a>
                <a href="<?php echo e(route('students.index')); ?>"
                   class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg transition-colors">
                    ← Back
                </a>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Result History</h3>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->results->isEmpty()): ?>
            <div class="py-10 text-center text-gray-400 text-sm">
                No results calculated yet for this student.
            </div>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Exam</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Rank</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Marksheet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $student->results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <?php echo e($result->exam->name); ?> <?php echo e($result->exam->year); ?>

                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">
                            <?php echo e($result->total_marks); ?><span class="text-gray-400">/<?php echo e($result->full_marks); ?></span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700"><?php echo e(number_format($result->percentage, 1)); ?>%</td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800"><?php echo e(number_format($result->gpa, 2)); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold <?php echo e($result->grade_badge_color); ?> text-white">
                                <?php echo e($result->grade); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600"><?php echo e($result->rank ?? '–'); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                <?php echo e($result->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'); ?>">
                                <?php echo e($result->is_passed ? 'Pass' : 'Fail'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="<?php echo e(route('results.student', [$student, $result->exam])); ?>"
                               class="text-xs text-blue-600 hover:underline">View →</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/students/show.blade.php ENDPATH**/ ?>