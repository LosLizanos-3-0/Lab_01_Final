<?php
	$order = $data['order'];
	$total = 0;
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
	<div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
		<p><span class="font-semibold">Orden:</span> #<?php echo (int)$order->OrderID; ?></p>
		<p><span class="font-semibold">Fecha:</span> <?php echo e(date('d/m/Y H:i', strtotime($order->OrderDate))); ?></p>
		<p><span class="font-semibold">Cliente:</span> <?php echo e($order->CustomerName); ?> (<?php echo e($order->CustomerID); ?>)</p>
		<p><span class="font-semibold">Atendido por:</span> <?php echo e($order->EmployeeName); ?></p>
	</div>
	<div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
		<p><span class="font-semibold">Envío con:</span> <?php echo e($order->ShipperName); ?></p>
		<p><span class="font-semibold">Entrega estimada:</span> <?php echo e(date('d/m/Y', strtotime($order->ShippedDate))); ?></p>
		<p><span class="font-semibold">Dirección:</span> <?php echo e($order->ShipAddress); ?>, <?php echo e($order->ShipCity); ?>, <?php echo e($order->ShipCountry); ?></p>
	</div>
</div>

<div class="overflow-x-auto rounded-lg border border-slate-200">
	<table class="min-w-full divide-y divide-slate-200 text-sm">
		<thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider">
		<tr>
			<th class="px-6 py-3 text-left">Producto</th>
			<th class="px-6 py-3 text-left">Categoría</th>
			<th class="px-6 py-3 text-right">Precio</th>
			<th class="px-6 py-3 text-right">Cantidad</th>
			<th class="px-6 py-3 text-right">Subtotal</th>
		</tr>
		</thead>
		<tbody class="divide-y divide-slate-200 bg-white">
		<?php foreach ($data['lines'] as $l) : ?>
			<?php $total += (float)$l->Subtotal; ?>
			<tr>
				<td class="px-6 py-3 font-medium text-slate-800"><?php echo e($l->ProductName); ?></td>
				<td class="px-6 py-3 text-slate-600"><?php echo e($l->CategoryName); ?></td>
				<td class="px-6 py-3 text-right">$<?php echo number_format((float)$l->UnitPrice, 2); ?></td>
				<td class="px-6 py-3 text-right"><?php echo (int)$l->Quantity; ?></td>
				<td class="px-6 py-3 text-right">$<?php echo number_format((float)$l->Subtotal, 2); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot class="bg-slate-50">
			<tr>
				<td colspan="4" class="px-6 py-3 text-right font-semibold">Total</td>
				<td class="px-6 py-3 text-right font-bold text-indigo-700">$<?php echo number_format($total, 2); ?></td>
			</tr>
		</tfoot>
	</table>
</div>