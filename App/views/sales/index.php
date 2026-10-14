<!DOCTYPE html>
<html lang="es">
<head>
	<?php require_once(appRoot . '/views/includes/enca.php'); ?>
	<title><?php echo siteName; ?></title>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between">
<div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
	<header>
		<?php require_once(appRoot . '/views/includes/menu.php'); ?>
	</header>

	<main class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 min-h-[300px]">

		<?php require_once(appRoot . '/views/includes/flash.php'); ?>

		<div class="max-w-6xl mx-auto my-8 p-6 bg-white rounded-xl shadow-lg border border-slate-100">
			<div class="flex flex-wrap justify-between items-center gap-3 mb-6 pb-4 border-b border-slate-200">
				<div>
					<h1 class="text-2xl font-bold text-slate-800"><?php echo e($data['title']); ?></h1>
					<p class="text-sm text-slate-500">Se muestran las 100 ventas más recientes.</p>
				</div>
				<div class="flex gap-2">
					<a href="<?php echo urlRoot; ?>/sales/assisted" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-md">Nueva venta asistida</a>
					<a href="<?php echo urlRoot; ?>/sales/selfservice" class="px-4 py-2 bg-white hover:bg-slate-50 text-indigo-700 border border-indigo-200 font-medium rounded-lg transition-colors">Compra autogestionada</a>
				</div>
			</div>

			<div class="overflow-x-auto rounded-lg border border-slate-200">
				<table class="min-w-full divide-y divide-slate-200 text-sm">
					<thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider">
					<tr>
						<th class="px-6 py-3 text-left">Orden</th>
						<th class="px-6 py-3 text-left">Fecha</th>
						<th class="px-6 py-3 text-left">Cliente</th>
						<th class="px-6 py-3 text-left">Empleado</th>
						<th class="px-6 py-3 text-left">Envío</th>
						<th class="px-6 py-3 text-right">Total</th>
						<th class="px-6 py-3 text-center">Acciones</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-slate-200 bg-white">
					<?php if (empty($data['orders'])) : ?>
						<tr>
							<td colspan="7" class="px-6 py-8 text-center text-slate-500">Todavía no hay ventas registradas.</td>
						</tr>
					<?php endif; ?>

					<?php foreach ($data['orders'] as $o) : ?>
						<tr class="hover:bg-slate-50 transition-colors">
							<td class="px-6 py-4 font-mono text-indigo-600 font-semibold">#<?php echo (int)$o->OrderID; ?></td>
							<td class="px-6 py-4 whitespace-nowrap"><?php echo e(date('d/m/Y', strtotime($o->OrderDate))); ?></td>
							<td class="px-6 py-4 font-medium text-slate-800"><?php echo e($o->CustomerName); ?></td>
							<td class="px-6 py-4 text-slate-600"><?php echo e($o->EmployeeName); ?></td>
							<td class="px-6 py-4 text-slate-600"><?php echo e($o->ShipperName); ?></td>
							<td class="px-6 py-4 text-right font-medium">$<?php echo number_format((float)$o->Total, 2); ?></td>
							<td class="px-6 py-4 text-center">
								<a href="<?php echo urlRoot; ?>/sales/detail/<?php echo (int)$o->OrderID; ?>" class="inline-block px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-medium transition">Ver detalle</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</main>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>