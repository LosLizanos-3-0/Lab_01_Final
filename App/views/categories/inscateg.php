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
			<h2 class="text-xl font-bold text-slate-800 mb-6 pb-2 border-b">Insertar Nueva Categoría</h2>

			<form action="<?php echo urlRoot; ?>/categories/create" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6">

				<div class="md:col-span-2 space-y-4">
					<div>
						<label class="block text-sm font-semibold text-slate-700 mb-1">Nombre</label>
						<input type="text" name="txtNombre" maxlength="15" value="<?php echo e($data['name']); ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
						<span class="text-xs text-rose-500"><?php echo e($data['name_err']); ?></span>
					</div>

					<div>
						<label class="block text-sm font-semibold text-slate-700 mb-1">Descripción</label>
						<textarea name="txtDescrip" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"><?php echo e($data['description']); ?></textarea>
					</div>

					<div>
						<label class="block text-sm font-semibold text-slate-700 mb-1">Imagen (Guardada en BD)</label>
						<input type="file" name="txtArchi" id="txtArchi" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
						<span id="msgImagen" class="text-xs text-rose-500"></span>
						<span class="block text-xs text-slate-400">Opcional. Tamaño máximo: 1 MB.</span>
					</div>
				</div>

				<!-- Presentación de la imagen seleccionada -->
				<div class="flex flex-col items-center justify-start space-y-2">
					<label class="block text-sm font-semibold text-slate-700">Vista Previa</label>
					<div class="w-40 h-40 rounded-lg border-2 border-dashed border-slate-300 overflow-hidden flex items-center justify-center bg-slate-50">
						<img id="imgSalida" src="" alt="Vista previa" class="w-full h-full object-cover hidden">
						<span id="txtSinImagen" class="text-xs text-slate-400 text-center px-2">Aún no ha seleccionado una imagen</span>
					</div>
				</div>

				<div class="md:col-span-3 flex justify-end gap-3 pt-4 border-t">
					<a href="<?php echo urlRoot; ?>/categories" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition">Cancelar</a>
					<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">Aceptar</button>
				</div>
			</form>
		</div>
	</main>

	<script>
		const maxBytes   = 1024 * 1024; // 1 MB
		const inputFile  = document.getElementById('txtArchi');
		const imgSalida  = document.getElementById('imgSalida');
		const txtSinImg  = document.getElementById('txtSinImagen');
		const msgImagen  = document.getElementById('msgImagen');

		function limpiarVistaPrevia() {
			imgSalida.src = '';
			imgSalida.classList.add('hidden');
			txtSinImg.classList.remove('hidden');
		}

		inputFile.addEventListener('change', function (e) {
			const file = e.target.files[0];
			msgImagen.textContent = '';

			if (!file) {
				limpiarVistaPrevia();
				return;
			}

			if (!file.type.match('image.*')) {
				msgImagen.textContent = 'El archivo seleccionado no es una imagen.';
				inputFile.value = '';
				limpiarVistaPrevia();
				return;
			}

			if (file.size > maxBytes) {
				msgImagen.textContent = 'La imagen supera el tamaño máximo de 1 MB.';
				inputFile.value = '';
				limpiarVistaPrevia();
				return;
			}

			const reader = new FileReader();
			reader.onload = function (ev) {
				imgSalida.src = ev.target.result;
				imgSalida.classList.remove('hidden');
				txtSinImg.classList.add('hidden');
			};
			reader.readAsDataURL(file);
		});
	</script>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>