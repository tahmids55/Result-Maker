<?php $__env->startSection('title', 'Class Result – ' . $class->name . ' ' . $section->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900"><?php echo e($class->name); ?> – Section <?php echo e($section->name); ?></h2>
            <p class="text-sm text-gray-500"><?php echo e($exam->name); ?> <?php echo e($exam->year); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('results.index')); ?>" class="text-sm border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg transition-colors">
                ← Back
            </a>
            <form method="POST" action="<?php echo e(route('results.export')); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="class_id" value="<?php echo e($class->id); ?>">
                <input type="hidden" name="section_id" value="<?php echo e($section->id); ?>">
                <input type="hidden" name="exam_id" value="<?php echo e($exam->id); ?>">
                <button class="text-sm bg-orange-500 hover:bg-orange-600 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    ⬇ Export CSV
                </button>
            </form>
        </div>
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php
            $passRate = $totalStudents > 0 ? round(($passedStudents / $totalStudents) * 100, 1) : 0;
            $avgGpa   = $results->avg('gpa');
        ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-gray-900"><?php echo e($totalStudents); ?></div>
            <div class="text-xs text-gray-500 mt-1">Total Students</div>
        </div>
        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-green-600"><?php echo e($passedStudents); ?></div>
            <div class="text-xs text-gray-500 mt-1">Passed (<?php echo e($passRate); ?>%)</div>
        </div>
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-red-500"><?php echo e($totalStudents - $passedStudents); ?></div>
            <div class="text-xs text-gray-500 mt-1">Failed</div>
        </div>
        <div class="bg-white rounded-xl border border-blue-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo e(number_format($avgGpa, 2)); ?></div>
            <div class="text-xs text-gray-500 mt-1">Average GPA</div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Pass / Fail Distribution</h3>
            <canvas id="passChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Grade Distribution</h3>
            <canvas id="gradeChart" height="200"></canvas>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Result Sheet (Ranked by Total Marks)</h3>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->isEmpty()): ?>
            <div class="py-12 text-center text-gray-400 text-sm">
                No results found. Make sure marks are entered and click Recalculate.
            </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Rank</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Roll</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">%</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">GPA</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Grade</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Division</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 <?php echo e(!$result->is_passed ? 'bg-red-50/30' : ''); ?>">
                        <td class="px-4 py-3 text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($result->rank <= 3): ?>
                                <span class="font-bold text-lg"><?php echo e(['🥇','🥈','🥉'][$result->rank - 1]); ?></span>
                            <?php else: ?>
                                <span class="text-gray-600 font-medium"><?php echo e($result->rank); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-4 py-3 font-mono text-gray-700"><?php echo e($result->student->roll); ?></td>
                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($result->student->name); ?></td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800">
                            <?php echo e($result->total_marks); ?><span class="text-gray-400 font-normal">/<?php echo e($result->full_marks); ?></span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700"><?php echo e(number_format($result->percentage, 1)); ?>%</td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800"><?php echo e(number_format($result->gpa, 2)); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold <?php echo e($result->grade_badge_color); ?> text-white">
                                <?php echo e($result->grade); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-600"><?php echo e($result->division); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                <?php echo e($result->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'); ?>">
                                <?php echo e($result->is_passed ? 'Pass' : 'Fail'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="<?php echo e(route('results.student', [$result->student, $exam])); ?>"
                               class="text-xs text-blue-600 hover:underline">View →</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const gradeData = <?php echo json_encode($gradeDistrib, 15, 512) ?>;
const gradeLabels = Object.keys(gradeData);
const gradeCounts = Object.values(gradeData);
const colors = ['#16a34a','#22c55e','#86efac','#3b82f6','#fbbf24','#f97316','#ef4444'];

new Chart(document.getElementById('gradeChart'), {
    type: 'doughnut',
    data: {
        labels: gradeLabels,
        datasets: [{ data: gradeCounts, backgroundColor: colors }]
    },
    options: { responsive: true, plugins: { legend: { position: 'right' } } }
});

new Chart(document.getElementById('passChart'), {
    type: 'bar',
    data: {
        labels: ['Passed', 'Failed'],
        datasets: [{
            data: [<?php echo e($passedStudents); ?>, <?php echo e($totalStudents - $passedStudents); ?>],
            backgroundColor: ['#16a34a', '#ef4444'],
            borderRadius: 8,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/results/class.blade.php ENDPATH**/ ?>