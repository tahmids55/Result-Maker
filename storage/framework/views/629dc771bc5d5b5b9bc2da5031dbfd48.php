
<div class="px-4 py-4 border-t border-slate-700">
    <div class="flex items-center justify-between">
        <div class="text-sm text-slate-300 truncate"><?php echo e(auth()->user()?->name); ?></div>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button class="text-xs text-slate-400 hover:text-white transition-colors">Logout</button>
        </form>
    </div>
</div>
<?php /**PATH /home/tahmids55/Deployment/markscraft/resources/views/layouts/_user.blade.php ENDPATH**/ ?>