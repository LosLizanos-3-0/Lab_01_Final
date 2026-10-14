<?php
	$err = $data['errors'];
	$inputClass = 'w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none';
?>

<div class="md:col-span-2">
	<label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del producto</label>
	<input type="text" name="txtNombre" maxlength="40" value="<?php echo e($data['name']); ?>" class="<?php echo $inputClass; ?>" required>
	<span class="text-xs text-rose-500"><?php echo e($err['name'] ?? ''); ?></span>
</div>

<div>
	<label class="block text-sm font-semibold text-slate-700 mb-1">Proveedor</label>
	<select name="cboProveedor" class="<?php echo $inputClass; ?>" required>
		<option value="">-- Seleccione --</option>
		<?php foreach ($data['suppliers'] as $s) : ?>
			<option value="<?php echo (int)$s->SupplierID; ?>" <?php echo ((int)$data['supplier'] === (int)$s->SupplierID) ? 'selected' : ''; ?>>
				<?php echo e($s->CompanyName); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<span class="text-xs text-rose-500"><?php echo e($err['supplier'] ?? ''); ?></span>
</div>

<div>
	<label class="block text-sm font-semibold text-slate-700 mb-1">Categoría</label>
	<select name="cboCategoria" class="<?php echo $inputClass; ?>" required>
		<option value="">-- Seleccione --</option>
		<?php foreach ($data['categories'] as $c) : ?>
			<option value="<?php echo (int)$c->CategoryID; ?>" <?php echo ((int)$data['category'] === (int)$c->CategoryID) ? 'selected' : ''; ?>>
				<?php echo e($c->CategoryName); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<span class="text-xs text-rose-500"><?php echo e($err['category'] ?? ''); ?></span>
</div>

<div>
	<label class="block text-sm font-semibold text-slate-700 mb-1">Cantidad por unidad</label>
	<input type="text" name="txtCantUni" maxlength="20" value="<?php echo e($data['qpu']); ?>" placeholder="Ej. 10 cajas x 20 bolsas" class="<?php echo $inputClass; ?>">
	<span class="text-xs text-rose-500"><?php echo e($err['qpu'] ?? ''); ?></span>
</div>

<div>
	<label class="block text-sm font-semibold text-slate-700 mb-1">Precio unitario ($)</label>
	<input type="number" name="txtPrecio" step="0.01" min="0" value="<?php echo e($data['price']); ?>" class="<?php echo $inputClass; ?>" required>
	<span class="text-xs text-rose-500"><?php echo e($err['price'] ?? ''); ?></span>
</div>

<div>
	<label class="block text-sm font-semibold text-slate-700 mb-1">Existencia</label>
	<input type="number" name="txtExistencia" step="1" min="0" max="32767" value="<?php echo e($data['stock']); ?>" class="<?php echo $inputClass; ?>" required>
	<span class="text-xs text-rose-500"><?php echo e($err['stock'] ?? ''); ?></span>
</div>

<div>
	<label class="block text-sm font-semibold text-slate-700 mb-1">Unidades en pedido</label>
	<input type="number" name="txtPedido" step="1" min="0" max="32767" value="<?php echo e($data['onorder']); ?>" class="<?php echo $inputClass; ?>">
	<span class="text-xs text-rose-500"><?php echo e($err['onorder'] ?? ''); ?></span>
</div>

<div>
	<label class="block text-sm font-semibold text-slate-700 mb-1">Nivel de reorden</label>
	<input type="number" name="txtReorden" step="1" min="0" max="32767" value="<?php echo e($data['reorder']); ?>" class="<?php echo $inputClass; ?>">
	<span class="text-xs text-rose-500"><?php echo e($err['reorder'] ?? ''); ?></span>
</div>

<div class="md:col-span-2 flex items-center gap-2">
	<input type="checkbox" name="chkDescon" id="chkDescon" value="1" <?php echo !empty($data['discontinued']) ? 'checked' : ''; ?> class="w-4 h-4 text-indigo-600 border-slate-300 rounded">
	<label for="chkDescon" class="text-sm font-semibold text-slate-700">Producto descontinuado</label>
</div>