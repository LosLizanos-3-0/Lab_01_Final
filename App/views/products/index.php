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
					<p class="text-sm text-slate-500">
						<?php $total = count($data['products']); echo $total . ($total === 1 ? ' producto' : ' productos'); ?>
						<?php if ($data['filtered']) : ?>
							&middot;
							<a href="<?php echo urlRoot; ?>/products" class="text-indigo-600 hover:underline">Ver lista general</a>
							&middot;
							<a href="<?php echo urlRoot; ?>/categories" class="text-indigo-600 hover:underline">Volver a categorías</a>
						<?php endif; ?>
					</p>
				</div>
				<a href="<?php echo urlRoot; ?>/products/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-md flex items-center gap-2">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
					Agregar Producto
				</a>
			</div>

			<div class="overflow-x-auto rounded-lg border border-slate-200">
				<table class="min-w-full divide-y divide-slate-200 text-sm">
					<thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider">
					<tr>
						<th class="px-6 py-3 text-left">Código</th>
						<th class="px-6 py-3 text-left">Producto</th>
						<th class="px-6 py-3 text-left">Categoría</th>
						<th class="px-6 py-3 text-left">Proveedor</th>
						<th class="px-6 py-3 text-right">Existencia</th>
						<th class="px-6 py-3 text-right">Precio</th>
						<th class="px-6 py-3 text-center">Acciones</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-slate-200 bg-white">
					<?php if (empty($data['products'])) : ?>
						<tr>
							<td colspan="7" class="px-6 py-8 text-center text-slate-500">No hay productos para mostrar.</td>
						</tr>
					<?php endif; ?>

					<?php foreach ($data['products'] as $p) : ?>
						<tr class="hover:bg-slate-50 transition-colors">
							<td class="px-6 py-4 font-mono text-indigo-600 font-semibold">#<?php echo (int)$p->ProductID; ?></td>
							<td class="px-6 py-4 font-medium text-slate-800">
								<?php echo e($p->ProductName); ?>
								<?php if ($p->Discontinued) : ?>
									<span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-slate-200 text-slate-600">Descontinuado</span>
								<?php endif; ?>
							</td>
							<td class="px-6 py-4 text-slate-600"><?php echo e($p->CategoryName); ?></td>
							<td class="px-6 py-4 text-slate-600"><?php echo e($p->SupplierName); ?></td>
							<td class="px-6 py-4 text-right"><?php echo (int)$p->UnitsInStock; ?></td>
							<td class="px-6 py-4 text-right font-medium">$<?php echo number_format((float)$p->UnitPrice, 2); ?></td>
							<td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
								<a href="<?php echo urlRoot; ?>/products/edit/<?php echo (int)$p->ProductID; ?>" class="inline-block px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-xs font-medium transition">Editar</a>
								<form action="<?php echo urlRoot; ?>/products/delete/<?php echo (int)$p->ProductID; ?>" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de eliminar este producto?');">
									<button type="submit" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-medium transition">Borrar</button>
								</form>
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