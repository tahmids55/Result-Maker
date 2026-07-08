<?php $__env->startSection('title', 'Sections'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="<?php echo e(route('sections.index')); ?>" class="flex flex-wrap md:flex-nowrap gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="Search section or class name..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Search
                </button>
                <a href="<?php echo e(route('sections.index')); ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm px-3 py-2 rounded-lg transition-colors flex items-center">
                    ✕
                </a>
            </div>
        </form>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500"><?php echo e($sections->total()); ?> sections found</p>
        <div class="flex gap-2">
            <div x-data="{ open: false }" class="relative">
                <button @click="open=!open" class="border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    📥 Bulk Import CSV
                </button>
                <div x-show="open" x-cloak @click.away="open=false"
                     class="absolute z-20 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg p-4 w-72 right-0">
                    <p class="text-xs font-semibold text-gray-700 mb-1">Import Sections via CSV</p>
                    <p class="text-xs text-gray-500 mb-3">Format: class_name, section_name</p>
                    <form method="POST" action="<?php echo e(route('sections.bulk-import')); ?>" enctype="multipart/form-data" class="space-y-2">
                        <?php echo csrf_field(); ?>
                        <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full text-xs border border-gray-300 rounded px-2 py-1.5">
                        <button type="submit" class="w-full bg-green-600 text-white text-xs font-medium py-2 rounded-lg">Import</button>
                    </form>
                </div>
            </div>
            <a href="<?php echo e(route('sections.create')); ?>"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Add Section
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sections->isEmpty()): ?>
            <div class="py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">📂</p>
                <p class="text-sm">No sections yet. <a href="<?php echo e(route('sections.create')); ?>" class="text-blue-600 hover:underline">Add one →</a></p>
            </div>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Class</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Section</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Students</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700 font-medium"><?php echo e($section->schoolClass->name); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($section->name); ?></td>
                        <td class="px-4 py-3 text-center text-gray-500"><?php echo e($section->students_count); ?></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?php echo e(route('sections.edit', $section)); ?>"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded transition-colors">Edit</a>
                                <form method="POST" action="<?php echo e(route('sections.destroy', $section)); ?>"
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
                <?php echo e($sections->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/sections/index.blade.php ENDPATH**/ ?>