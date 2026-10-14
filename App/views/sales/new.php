<?php
	$mode      = $data['mode'];
	$isAssist  = ($mode === 'assisted');
	$formUrl   = urlRoot . '/sales/' . ($isAssist ? 'assisted' : 'selfservice');
	$state     = $data['state'];
	$selProd   = (int)($_POST['cboProducto'] ?? 0);
	$inputClass = 'w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white';
?>
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

		<div class="max-w-6xl mx-auto my-6">
			<div class="mb-6 pb-4 border-b border-slate-200">
				<h1 class="text-2xl font-bold text-slate-800"><?php echo e($data['title']); ?></h1>
				<p class="text-sm text-slate-500">
					<?php if ($isAssist) : ?>
						Venta atendida por un empleado: elija el cliente, el empleado, la empresa de envío y agregue los productos al carrito.
					<?php else : ?>
						Elija su empresa, la empresa de envío y agregue los productos que desea comprar al carrito.
					<?php endif; ?>
				</p>
			</div>

			<?php if ($data['error'] !== '') : ?>
				<div class="mb-4 px-4 py-3 rounded-lg text-sm border bg-rose-50 text-rose-700 border-rose-200">
					<?php echo e($data['error']); ?>
				</div>
			<?php endif; ?>

			<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

				<!-- Columna izquierda: datos de la venta y selección de producto -->
				<form id="frmVenta" action="<?php echo $formUrl; ?>" method="POST" class="lg:col-span-2 space-y-4 p-6 rounded-xl border border-slate-200 bg-slate-50 self-start">

					<div>
						<label class="block text-sm font-semibold text-slate-700 mb-1"><?php echo $isAssist ? 'Cliente' : 'Su empresa (cliente)'; ?></label>
						<select name="cboCliente" class="<?php echo $inputClass; ?>">
							<option value="">-- Seleccione --</option>
							<?php foreach ($data['customers'] as $c) : ?>
								<option value="<?php echo e($c->CustomerID); ?>" <?php echo ($state['customer'] === $c->CustomerID) ? 'selected' : ''; ?>>
									<?php echo e($c->CompanyName); ?> (<?php echo e($c->CustomerID); ?>) - <?php echo e($c->Country); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<?php if ($isAssist) : ?>
						<div>
							<label class="block text-sm font-semibold text-slate-700 mb-1">Empleado que atiende</label>
							<select name="cboEmpleado" class="<?php echo $inputClass; ?>">
								<option value="">-- Seleccione --</option>
								<?php foreach ($data['employees'] as $emp) : ?>
									<option value="<?php echo (int)$emp->EmployeeID; ?>" <?php echo ((int)$state['employee'] === (int)$emp->EmployeeID) ? 'selected' : ''; ?>>
										<?php echo e($emp->FirstName . ' ' . $emp->LastName); ?> - <?php echo e($emp->Title); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>

					<div>
						<label class="block text-sm font-semibold text-slate-700 mb-1">Empresa de envío</label>
						<select name="cboEnvio" class="<?php echo $inputClass; ?>">
							<option value="">-- Seleccione --</option>
							<?php foreach ($data['shippers'] as $sh) : ?>
								<option value="<?php echo (int)$sh->ShipperID; ?>" <?php echo ((int)$state['shipper'] === (int)$sh->ShipperID) ? 'selected' : ''; ?>>
									<?php echo e($sh->CompanyName); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<hr class="border-slate-200">

					<div>
						<label class="block text-sm font-semibold text-slate-700 mb-1">Producto</label>
						<select name="cboProducto" class="<?php echo $inputClass; ?>">
							<option value="">-- Seleccione --</option>
							<?php
								$lastCat = null;
								foreach ($data['products'] as $p) {
									if ($p->CategoryName !== $lastCat) {
										if ($lastCat !== null) {
											echo '</optgroup>';
										}
										echo '<optgroup label="' . e($p->CategoryName) . '">';
										$lastCat = $p->CategoryName;
									}
									$selected = ($selProd === (int)$p->ProductID) ? ' selected' : '';
									echo '<option value="' . (int)$p->ProductID . '"' . $selected . '>' .
										 e($p->ProductName) . ' - $' . number_format((float)$p->UnitPrice, 2) .
										 ' (stock: ' . (int)$p->UnitsInStock . ')</option>';
								}
								if ($lastCat !== null) {
									echo '</optgroup>';
								}
							?>
						</select>
					</div>

					<div>
						<label class="block text-sm font-semibold text-slate-700 mb-1">Cantidad</label>
						<input type="number" name="txtCantidad" min="1" step="1" value="<?php echo e($_POST['txtCantidad'] ?? 1); ?>" class="<?php echo $inputClass; ?>">
					</div>

					<button type="submit" name="accion" value="agregar" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition shadow-md">
						Agregar al carrito
					</button>
				</form>

				
				<div class="lg:col-span-3 p-6 rounded-xl border border-slate-200 bg-white shadow-sm">
					<h2 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b">Carrito de compra</h2>

					<?php if (empty($data['cart'])) : ?>
						<p class="py-8 text-center text-slate-500 text-sm">El carrito está vacío. Agregue productos desde el formulario.</p>
					<?php else : ?>
						<div class="overflow-x-auto rounded-lg border border-slate-200">
							<table class="min-w-full divide-y divide-slate-200 text-sm">
								<thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider">
								<tr>
									<th class="px-4 py-3 text-left">Producto</th>
									<th class="px-4 py-3 text-right">Precio</th>
									<th class="px-4 py-3 text-right">Cant.</th>
									<th class="px-4 py-3 text-right">Subtotal</th>
									<th class="px-4 py-3"></th>
								</tr>
								</thead>
								<tbody class="divide-y divide-slate-200">
								<?php foreach ($data['cart'] as $line) : ?>
									<tr>
										<td class="px-4 py-3 font-medium text-slate-800"><?php echo e($line['name']); ?></td>
										<td class="px-4 py-3 text-right">$<?php echo number_format($line['price'], 2); ?></td>
										<td class="px-4 py-3 text-right"><?php echo (int)$line['qty']; ?></td>
										<td class="px-4 py-3 text-right">$<?php echo number_format($line['price'] * $line['qty'], 2); ?></td>
										<td class="px-4 py-3 text-right">
											<form action="<?php echo urlRoot; ?>/sales/removeItem/<?php echo $mode; ?>/<?php echo (int)$line['id']; ?>" method="POST">
												<button type="submit" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-medium transition">Quitar</button>
											</form>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
								<tfoot class="bg-slate-50">
									<tr>
										<td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
										<td class="px-4 py-3 text-right font-bold text-indigo-700">$<?php echo number_format($data['total'], 2); ?></td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>

						<div class="flex flex-wrap justify-end gap-3 pt-4">
							<form action="<?php echo urlRoot; ?>/sales/clearCart/<?php echo $mode; ?>" method="POST" onsubmit="return confirm('¿Vaciar el carrito?');">
								<button type="submit" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition">Vaciar carrito</button>
							</form>

							<button type="submit" form="frmVenta" name="accion" value="confirmar" formnovalidate class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition shadow-md">
								Confirmar venta
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</main>

	<?php require_once(appRoot . '/views/includes/pie.php'); ?>
</div>
</body>
</html>