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

		<div class="max-w-4xl mx-auto my-8 p-6 bg-white rounded-xl shadow-lg border border-slate-100">
			<div class="mb-6 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
				<p class="font-semibold">¡Venta registrada correctamente!</p>
				<p class="text-sm">Se descontaron las unidades vendidas del inventario.</p>
			</div>

			<?php require(appRoot . '/views/sales/lineas.php'); ?>

			<div class="flex flex-wrap justify-end gap-3 pt-6">
				<?php if ($data['mode'] === 'assisted') : ?>
					<a href="<?php echo urlRoot; ?>/sales" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition">Ver todas las ventas</a>
					<a href="<?php echo urlRoot; ?>/sales/assisted" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">Nueva venta asistida</a>
				<?php else : ?>
					<a href="<?php echo urlRoot; ?>/sales/selfservice" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">Realizar otra compra</a>
				<?php endif; ?>
			</div>
		</div>
	</main>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>