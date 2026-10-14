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
			<div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
				<h1 class="text-2xl font-bold text-slate-800"><?php echo e($data['title']); ?></h1>
				<a href="<?php echo urlRoot; ?>/sales" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition">Volver</a>
			</div>

			<?php require(appRoot . '/views/sales/lineas.php'); ?>
		</div>
	</main>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>