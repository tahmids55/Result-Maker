<?php $__env->startSection('title', 'OCR Result Review'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">OCR Review</h2>
            <p class="text-sm text-gray-500">Image: <?php echo e(basename($import->image_path)); ?> · Status:
                <span class="font-medium <?php echo e($import->isProcessed() ? 'text-green-600' : 'text-red-500'); ?>"><?php echo e(ucfirst($import->status)); ?></span>
            </p>
        </div>
        <a href="<?php echo e(route('ocr.index')); ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-4 py-2 rounded-lg">← Back</a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($import->isFailed()): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-sm text-red-800">
            <p class="font-semibold mb-1">OCR Processing Failed</p>
            <p><?php echo e($import->error_message); ?></p>
            <p class="mt-2 text-xs text-red-600">Make sure Tesseract is installed and the image is clear enough to read.</p>
        </div>
    <?php elseif($import->isPending()): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-sm text-yellow-800">
            <p>⏳ Processing in queue... Please refresh in a moment.</p>
        </div>
    <?php elseif($import->isProcessed()): ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Original Image</h3>
            <img src="<?php echo e($import->image_url); ?>" alt="OCR Image"
                 class="w-full rounded-lg border border-gray-200 max-h-96 object-contain">
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Extracted Raw Text</h3>
            <pre class="text-xs text-gray-600 bg-gray-50 rounded-lg p-3 overflow-auto max-h-96 font-mono whitespace-pre-wrap"><?php echo e($import->extracted_data['raw_text'] ?? 'No text extracted'); ?></pre>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($import->extracted_data['parsed'])): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5" x-data="{ rows: <?php echo json_encode($import->extracted_data['parsed'], 15, 512) ?> }">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Parsed Data — Review & Confirm</h3>

        <form method="POST" action="<?php echo e(route('ocr.save-marks', $import)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Exam</label>
                    <select name="exam_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Select Exam --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Exam::orderByDesc('year')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($exam->id); ?>"><?php echo e($exam->name); ?> <?php echo e($exam->year); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                    <select name="subject_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Select Subject --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Models\Subject::with(['schoolClass','section'])->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject->id); ?>">
                                <?php echo e($subject->schoolClass->name); ?>/<?php echo e($subject->section->name); ?> — <?php echo e($subject->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Roll</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Name (detected)</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Marks (detected)</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Raw Line</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(row, i) in rows" :key="i">
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <input type="number" :name="`rows[${i}][roll]`" x-model="row.roll"
                                           class="w-16 border border-gray-300 rounded px-2 py-1 text-xs focus:ring-1 focus:ring-blue-400 outline-none">
                                </td>
                                <td class="px-3 py-2 text-gray-600" x-text="row.name ?? '–'"></td>
                                <td class="px-3 py-2">
                                    <template x-for="(mark, j) in row.marks" :key="j">
                                        <input type="number" :name="`rows[${i}][components][comp_${j}]`" x-model="row.marks[j]"
                                               class="w-14 border border-gray-300 rounded px-1 py-1 text-xs mr-1 focus:ring-1 focus:ring-blue-400 outline-none">
                                    </template>
                                </td>
                                <td class="px-3 py-2 font-mono text-gray-400" x-text="row.raw"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-400">
                ⚠ Review the parsed data carefully. Correct any OCR errors before saving. Roll numbers must match existing students.
            </p>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                ✔ Confirm & Save Marks to Database
            </button>
        </form>
    </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-sm text-yellow-800">
            OCR extracted text but couldn't parse structured data. Review the raw text above and enter marks manually.
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/ocr/show.blade.php ENDPATH**/ ?>