<?php $__env->startSection('title', 'Upload Template'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Upload Marksheet Template</h2>

        <form method="POST" action="<?php echo e(route('templates.store')); ?>" enctype="multipart/form-data" class="space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="e.g. Annual Exam Marksheet 2024">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                          placeholder="Optional description"><?php echo e(old('description')); ?></textarea>
            </div>

            
            <div x-data="{ dragging: false, fileName: '' }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Template File (.docx) *</label>
                <div @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                     @drop.prevent="dragging=false; fileName=$event.dataTransfer.files[0]?.name; $refs.fileInput.files = $event.dataTransfer.files"
                     :class="dragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50'"
                     class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
                     @click="$refs.fileInput.click()">
                    <div class="text-4xl mb-2">📄</div>
                    <p x-show="!fileName" class="text-sm text-gray-500">
                        Drag & drop your .docx file here, or <span class="text-blue-600 font-medium">click to browse</span>
                    </p>
                    <p x-show="fileName" class="text-sm font-medium text-green-700" x-text="'✔ ' + fileName"></p>
                    <p class="text-xs text-gray-400 mt-1">Maximum file size: 10MB</p>
                    <input type="file" name="template_file" x-ref="fileInput" required accept=".docx"
                           @change="fileName=$event.target.files[0]?.name" class="hidden">
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['template_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-800">
                <p class="font-semibold mb-1">💡 Placeholder Format</p>
                <p>Use double curly braces in your Word document: <code class="bg-blue-100 px-1 rounded">{{student_name}}</code>, <code class="bg-blue-100 px-1 rounded">{{roll}}</code>, <code class="bg-blue-100 px-1 rounded">{{grade}}</code></p>
                <p class="mt-1 text-blue-600">After upload you'll map each placeholder to the correct database field.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors text-sm">
                    Upload & Detect Placeholders
                </button>
                <a href="<?php echo e(route('templates.index')); ?>"
                   class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg transition-colors text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/templates/create.blade.php ENDPATH**/ ?>