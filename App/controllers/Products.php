<?php

	class Products extends Controller {

		private $productModel;
		private $categoryModel;

		public function __construct() {
			// Redirigir a login si no está autenticado
			if (!isLoggedIn()) {
				header('Location: ' . urlRoot . '/users/login');
				exit();
			}

			$this->productModel  = $this->model('Product');
			$this->categoryModel = $this->model('Category');
		}

		
		public function index($categoryId = null) {
			if ($categoryId !== null && $categoryId !== '') {
				$category = $this->categoryModel->getCategoryById((int)$categoryId);

				if (!$category) {
					setFlash('error', 'La categoría solicitada no existe.');
					header('Location: ' . urlRoot . '/products');
					exit();
				}

				$title    = 'Lista de productos por la categoría ' . $category->CategoryName;
				$products = $this->productModel->getProducts((int)$categoryId);
				$filtered = true;
			} else {
				$title    = 'Lista general de productos';
				$products = $this->productModel->getProducts();
				$filtered = false;
			}

			$data = [
				'title'    => $title,
				'products' => $products,
				'filtered' => $filtered
			];
			$this->view('products/index', $data);
		}

		// Agregar producto
		public function create() {
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$data = $this->collectForm(null);
				$data['errors'] = $this->validateForm($data);

				if (empty($data['errors'])) {
					if ($this->productModel->addProduct($data)) {
						setFlash('ok', 'Producto agregado correctamente.');
						header('Location: ' . urlRoot . '/products');
						exit();
					}
					die('Error al guardar el producto.');
				}
			} else {
				$data = $this->emptyForm();
			}

			$this->view('products/insproduc', $data);
		}

		// Modificar producto
		public function edit($id = 0) {
			$product = $this->productModel->getProductById((int)$id);

			if (!$product) {
				setFlash('error', 'El producto solicitado no existe.');
				header('Location: ' . urlRoot . '/products');
				exit();
			}

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$data = $this->collectForm((int)$id);
				$data['errors'] = $this->validateForm($data);

				if (empty($data['errors'])) {
					if ($this->productModel->updateProduct($data)) {
						setFlash('ok', 'Producto modificado correctamente.');
						header('Location: ' . urlRoot . '/products');
						exit();
					}
					die('Error al actualizar el producto.');
				}
			} else {
				$data = [
					'id'           => (int)$product->ProductID,
					'name'         => $product->ProductName,
					'supplier'     => (int)$product->SupplierID,
					'category'     => (int)$product->CategoryID,
					'qpu'          => $product->QuantityPerUnit,
					'price'        => number_format((float)$product->UnitPrice, 2, '.', ''),
					'stock'        => $product->UnitsInStock,
					'onorder'      => $product->UnitsOnOrder,
					'reorder'      => $product->ReorderLevel,
					'discontinued' => (int)$product->Discontinued,
					'errors'       => []
				];
			}

			$data['suppliers']  = $this->productModel->getSuppliers();
			$data['categories'] = $this->categoryModel->getCategories();
			$this->view('products/modproduc', $data);
		}

		// Borrar producto
		public function delete($id = 0) {
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if ($this->productModel->deleteProduct((int)$id)) {
					setFlash('ok', 'Producto eliminado correctamente.');
				} else {
					setFlash('error', 'No se puede eliminar el producto porque tiene ventas registradas. Puede marcarlo como descontinuado.');
				}
			}
			header('Location: ' . urlRoot . '/products');
			exit();
		}

	

		// Valores iniciales del formulario de agregar
		private function emptyForm() {
			return [
				'id'           => null,
				'name'         => '',
				'supplier'     => 0,
				'category'     => 0,
				'qpu'          => '',
				'price'        => '',
				'stock'        => '0',
				'onorder'      => '0',
				'reorder'      => '0',
				'discontinued' => 0,
				'errors'       => [],
				'suppliers'    => $this->productModel->getSuppliers(),
				'categories'   => $this->categoryModel->getCategories()
			];
		}

		// Lee los campos enviados por el formulario
		private function collectForm($id) {
			return [
				'id'           => $id,
				'name'         => trim($_POST['txtNombre'] ?? ''),
				'supplier'     => (int)($_POST['cboProveedor'] ?? 0),
				'category'     => (int)($_POST['cboCategoria'] ?? 0),
				'qpu'          => trim($_POST['txtCantUni'] ?? ''),
				'price'        => trim($_POST['txtPrecio'] ?? ''),
				'stock'        => trim($_POST['txtExistencia'] ?? ''),
				'onorder'      => trim($_POST['txtPedido'] ?? ''),
				'reorder'      => trim($_POST['txtReorden'] ?? ''),
				'discontinued' => isset($_POST['chkDescon']) ? 1 : 0,
				'errors'       => [],
				'suppliers'    => $this->productModel->getSuppliers(),
				'categories'   => $this->categoryModel->getCategories()
			];
		}

		// Valida los datos y devuelve un arreglo de errores (vacío si todo está bien)
		private function validateForm(&$data) {
			$errors = [];

			if ($data['name'] === '') {
				$errors['name'] = 'El nombre del producto es obligatorio.';
			} elseif (mb_strlen($data['name']) > 40) {
				$errors['name'] = 'El nombre no puede superar los 40 caracteres.';
			}

			if (!in_array($data['supplier'], array_map('intval', array_column($data['suppliers'], 'SupplierID')), true)) {
				$errors['supplier'] = 'Seleccione un proveedor.';
			}

			if (!in_array($data['category'], array_map('intval', array_column($data['categories'], 'CategoryID')), true)) {
				$errors['category'] = 'Seleccione una categoría.';
			}

			if (mb_strlen($data['qpu']) > 20) {
				$errors['qpu'] = 'La cantidad por unidad no puede superar los 20 caracteres.';
			}

			if ($data['price'] === '' || !is_numeric($data['price']) || $data['price'] < 0 || $data['price'] > 999999) {
				$errors['price'] = 'Ingrese un precio válido (0 o mayor).';
			} else {
				// se normaliza a formato decimal con punto (ej. 12.50)
				$data['price'] = number_format((float)$data['price'], 2, '.', '');
			}

			foreach (['stock' => 'La existencia', 'onorder' => 'La cantidad en pedido', 'reorder' => 'El nivel de reorden'] as $field => $label) {
				if ($data[$field] === '') {
					$data[$field] = '0';
				}
				$value = filter_var($data[$field], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 32767]]);
				if ($value === false) {
					$errors[$field] = $label . ' debe ser un número entero entre 0 y 32767.';
				}
			}

			return $errors;
		}
	}
?>