<?php

	class Sales extends Controller {

		private $orderModel;
		private $customerModel;
		private $employeeModel;
		private $shipperModel;
		private $productModel;

		
		private $selfServiceEmployeeId = 1;

		public function __construct() {
			$this->orderModel    = $this->model('Order');
			$this->customerModel = $this->model('Customer');
			$this->employeeModel = $this->model('Employee');
			$this->shipperModel  = $this->model('Shipper');
			$this->productModel  = $this->model('Product');
		}


		// Lista de ventas registradas
		public function index() {
			$this->requireLogin();

			$data = [
				'title'  => 'Ventas registradas',
				'orders' => $this->orderModel->getOrders(100)
			];
			$this->view('sales/index', $data);
		}

		// Detalle de una venta
		public function detail($id = 0) {
			$this->requireLogin();

			$order = $this->orderModel->getOrderById((int)$id);
			if (!$order) {
				setFlash('error', 'La venta solicitada no existe.');
				header('Location: ' . urlRoot . '/sales');
				exit();
			}

			$data = [
				'title' => 'Detalle de la venta #' . $order->OrderID,
				'order' => $order,
				'lines' => $this->orderModel->getOrderDetails((int)$id)
			];
			$this->view('sales/detail', $data);
		}

		// Venta asistida: la realiza un empleado para un cliente
		public function assisted() {
			$this->requireLogin();
			$this->saleScreen('assisted');
		}

	

		// Venta autogestionada: la realiza el propio cliente
		public function selfservice() {
			$this->saleScreen('self');
		}

		// Confirmación de la última venta realizada en esta sesión
		public function success() {
			if (!isset($_SESSION['last_order'])) {
				header('Location: ' . urlRoot . '/sales/selfservice');
				exit();
			}

			$orderId = (int)$_SESSION['last_order'];
			$order   = $this->orderModel->getOrderById($orderId);

			$data = [
				'title' => 'Venta registrada',
				'order' => $order,
				'lines' => $this->orderModel->getOrderDetails($orderId),
				'mode'  => $_SESSION['last_order_mode'] ?? 'self'
			];
			$this->view('sales/success', $data);
		}

		

		// Quitar un producto del carrito
		public function removeItem($mode = 'self', $productId = 0) {
			$mode = $this->cleanMode($mode);
			if ($mode === 'assisted') {
				$this->requireLogin();
			}

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				unset($_SESSION['cart_' . $mode][(int)$productId]);
			}
			header('Location: ' . $this->screenUrl($mode));
			exit();
		}

		// Vaciar el carrito
		public function clearCart($mode = 'self') {
			$mode = $this->cleanMode($mode);
			if ($mode === 'assisted') {
				$this->requireLogin();
			}

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$_SESSION['cart_' . $mode] = [];
			}
			header('Location: ' . $this->screenUrl($mode));
			exit();
		}

	

		private function requireLogin() {
			if (!isLoggedIn()) {
				header('Location: ' . urlRoot . '/users/login');
				exit();
			}
		}

		private function cleanMode($mode) {
			return ($mode === 'assisted') ? 'assisted' : 'self';
		}

		private function screenUrl($mode) {
			return urlRoot . '/sales/' . (($mode === 'assisted') ? 'assisted' : 'selfservice');
		}

		// Pantalla de venta (la misma vista para las dos modalidades)
		private function saleScreen($mode) {
			$cartKey  = 'cart_' . $mode;
			$stateKey = 'sale_' . $mode;

			if (!isset($_SESSION[$cartKey])) {
				$_SESSION[$cartKey] = [];
			}
			if (!isset($_SESSION[$stateKey])) {
				$_SESSION[$stateKey] = ['customer' => '', 'employee' => 0, 'shipper' => 0];
			}

			$error = '';

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// recordar lo seleccionado en el formulario para no perderlo al agregar productos
				$_SESSION[$stateKey]['customer'] = trim($_POST['cboCliente'] ?? '');
				$_SESSION[$stateKey]['shipper']  = (int)($_POST['cboEnvio'] ?? 0);
				if ($mode === 'assisted') {
					$_SESSION[$stateKey]['employee'] = (int)($_POST['cboEmpleado'] ?? 0);
				}

				$action = $_POST['accion'] ?? '';

				if ($action === 'agregar') {
					$error = $this->addToCart($mode);
					if ($error === '') {
						header('Location: ' . $this->screenUrl($mode));
						exit();
					}
				} elseif ($action === 'confirmar') {
					$error = $this->confirmSale($mode);
				}
			}

			// total del carrito
			$total = 0;
			foreach ($_SESSION[$cartKey] as $line) {
				$total += $line['price'] * $line['qty'];
			}

			$data = [
				'title'     => ($mode === 'assisted') ? 'Venta asistida' : 'Compra autogestionada',
				'mode'      => $mode,
				'customers' => $this->customerModel->getCustomers(),
				'employees' => ($mode === 'assisted') ? $this->employeeModel->getEmployees() : [],
				'shippers'  => $this->shipperModel->getShippers(),
				'products'  => $this->productModel->getSellableProducts(),
				'cart'      => $_SESSION[$cartKey],
				'state'     => $_SESSION[$stateKey],
				'total'     => $total,
				'error'     => $error
			];
			$this->view('sales/new', $data);
		}

		// Agrega un producto al carrito. Devuelve '' si todo salió bien o el mensaje de error.
		private function addToCart($mode) {
			$cartKey   = 'cart_' . $mode;
			$productId = (int)($_POST['cboProducto'] ?? 0);
			$qty       = (int)($_POST['txtCantidad'] ?? 0);

			if ($productId <= 0) {
				return 'Seleccione un producto.';
			}
			if ($qty <= 0) {
				return 'La cantidad debe ser mayor que cero.';
			}

			$product = $this->productModel->getSellableProduct($productId);
			if (!$product) {
				return 'El producto no existe o está descontinuado.';
			}

			$inCart = isset($_SESSION[$cartKey][$productId]) ? $_SESSION[$cartKey][$productId]['qty'] : 0;
			if ($inCart + $qty > $product->UnitsInStock) {
				return 'Existencia insuficiente de "' . $product->ProductName . '". Disponible: ' . $product->UnitsInStock;
			}

			$_SESSION[$cartKey][$productId] = [
				'id'    => $productId,
				'name'  => $product->ProductName,
				'price' => (float)$product->UnitPrice,
				'qty'   => $inCart + $qty
			];
			return '';
		}

		
		private function confirmSale($mode) {
			$cartKey  = 'cart_' . $mode;
			$stateKey = 'sale_' . $mode;
			$cart     = $_SESSION[$cartKey];
			$state    = $_SESSION[$stateKey];

			if (empty($cart)) {
				return 'El carrito está vacío. Agregue al menos un producto.';
			}

			if ($state['customer'] === '' || !$this->customerModel->getCustomerById($state['customer'])) {
				return 'Seleccione un cliente válido.';
			}

			if (!$this->shipperModel->getShipperById($state['shipper'])) {
				return 'Seleccione la empresa de envío.';
			}

			if ($mode === 'assisted') {
				$employeeId = (int)$state['employee'];
				if (!$this->employeeModel->getEmployeeById($employeeId)) {
					return 'Seleccione el empleado que atiende la venta.';
				}
			} else {
				$employeeId = $this->selfServiceEmployeeId;
				if (!$this->employeeModel->getEmployeeById($employeeId)) {
					return 'No se pudo asignar un empleado a la venta autogestionada.';
				}
			}

			$items = [];
			foreach ($cart as $line) {
				$items[] = ['id' => $line['id'], 'qty' => $line['qty']];
			}

			$result = $this->orderModel->createOrder($state['customer'], $employeeId, $state['shipper'], $items);
			if (!$result['ok']) {
				return $result['error'];
			}

			// venta guardada: se limpia el carrito y se muestra la confirmación
			$_SESSION[$cartKey]  = [];
			$_SESSION[$stateKey] = ['customer' => '', 'employee' => 0, 'shipper' => 0];
			$_SESSION['last_order']      = $result['orderId'];
			$_SESSION['last_order_mode'] = $mode;

			header('Location: ' . urlRoot . '/sales/success');
			exit();
		}
	}
?>