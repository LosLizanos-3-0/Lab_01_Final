<?php $flash = getFlash(); ?>
<?php if ($flash) : ?>
	<div class="mb-4 px-4 py-3 rounded-lg text-sm border <?php echo ($flash['type'] === 'ok') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'; ?>">
		<?php echo e($flash['message']); ?>
	</div>
<?php endif; ?>