<?php $__env->startSection('title', 'Edit Subject – ' . $subject->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Edit Subject: <?php echo e($subject->name); ?></h2>

        <form method="POST" action="<?php echo e(route('subjects.update', $subject)); ?>" x-data="subjectEditForm()" class="space-y-5">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $subject->name)); ?>" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                    <input type="text" name="code" value="<?php echo e(old('code', $subject->code)); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="<?php echo e(old('sort_order', $subject->sort_order)); ?>" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class *</label>
                    <select name="class_id" required onchange="fetchSections(this.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->id); ?>" <?php echo e($subject->class_id == $class->id ? 'selected' : ''); ?>><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section *</label>
                    <select name="section_id" id="section_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($section->id); ?>" <?php echo e($subject->section_id == $section->id ? 'selected' : ''); ?>><?php echo e($section->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_optional" value="1" <?php echo e($subject->is_optional ? 'checked' : ''); ?> class="rounded">
                        Optional Subject
                    </label>
                </div>
            </div>

            
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-700">Exam Components</label>
                    <button type="button" @click="addComponent()"
                            class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium px-3 py-1.5 rounded-lg">
                        + Add Component
                    </button>
                </div>
                <div class="space-y-2">
                    <template x-for="(comp, index) in components" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-center bg-gray-50 p-3 rounded-lg">
                            <div class="col-span-4">
                                <input type="text" :name="`components[${index}][name]`" x-model="comp.name"
                                       placeholder="Component name"
                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                            </div>
                            <div class="col-span-3">
                                <input type="number" :name="`components[${index}][full]`" x-model="comp.full"
                                       placeholder="Full marks" min="1"
                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                            </div>
                            <div class="col-span-3">
                                <input type="number" :name="`components[${index}][pass]`" x-model="comp.pass"
                                       placeholder="Pass marks" min="1"
                                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-400 outline-none">
                            </div>
                            <div class="col-span-2 text-right">
                                <button type="button" @click="components.splice(index,1)" x-show="components.length > 1"
                                        class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50">✕</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                    Update Subject
                </button>
                <a href="<?php echo e(route('subjects.index')); ?>" class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function subjectEditForm() {
    return {
        components: <?php echo json_encode(collect($subject->exam_components)->map(fn($v, $k) => ['name' => $k, 'full' => $v['full'], 'pass' => $v['pass']])->values()->all()); ?>,
        addComponent() {
            this.components.push({ name: '', full: '', pass: '' });
        }
    };
}

function fetchSections(classId) {
    const sel = document.getElementById('section_id');
    sel.innerHTML = '<option value="">Loading...</option>';
    if (!classId) { sel.innerHTML = '<option value="">-- Section --</option>'; return; }
    fetch(`/api/sections-by-class?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Select Section --</option>';
            data.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
        });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/subjects/edit.blade.php ENDPATH**/ ?>