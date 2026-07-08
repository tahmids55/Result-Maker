<?php $__env->startSection('title', 'Map Placeholders – ' . $template->name); ?>

<?php $__env->startSection('content'); ?>
<div class="py-4 max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-gray-800">Map Placeholders: <?php echo e($template->name); ?></h2>
            <p class="text-sm text-gray-500 mt-1">
                Assign each detected placeholder to the corresponding database field.
                Unmapped placeholders will be left blank in generated marksheets.
            </p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($template->placeholders)): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                ⚠ No placeholders detected in this template. Make sure your .docx uses <code>${placeholder_name}</code> format.
            </div>
        <?php else: ?>
        <form method="POST" action="<?php echo e(route('templates.save-map', $template)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-b">Placeholder in Template</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 border-b">Maps To</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $template->placeholders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $placeholder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <code class="bg-blue-50 text-blue-800 px-2 py-1 rounded text-xs font-mono"><?php echo e('${' . $placeholder . '}'); ?></code>
                            </td>
                            <td class="px-4 py-3">
                                <select name="mappings[<?php echo e($placeholder); ?>]"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">-- Leave blank --</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $fields): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <optgroup label="<?php echo e($group); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"
                                                    <?php echo e((($template->field_mappings[$placeholder] ?? '') === $key) ? 'selected' : ''); ?>>
                                                    <?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </optgroup>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors text-sm">
                    💾 Save Mappings
                </button>
                <a href="<?php echo e(route('templates.index')); ?>"
                   class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/templates/map.blade.php ENDPATH**/ ?>