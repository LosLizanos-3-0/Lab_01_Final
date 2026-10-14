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

		<div class="max-w-3xl mx-auto my-10 p-8 bg-white rounded-xl shadow-lg border border-slate-100">
			<h2 class="text-xl font-bold text-slate-800 mb-6 pb-2 border-b">Insertar Nuevo Producto</h2>

			<form action="<?php echo urlRoot; ?>/products/create" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">

				<?php require(appRoot . '/views/products/campos.php'); ?>

				<div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t">
					<a href="<?php echo urlRoot; ?>/products" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition">Cancelar</a>
					<button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">Aceptar</button>
				</div>
			</form>
		</div>
	</main>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>