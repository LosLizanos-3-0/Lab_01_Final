<?php

			class Categories extends Controller {

				private $categoryModel;

				public function __construct() {
					// Redirigir a login si no está autenticado
					if (!isLoggedIn()) {
						header('Location: ' . urlRoot . '/users/login');
						exit();
					}
					
					$this->categoryModel = $this->model('Category');
				}

				// Muestra la lista principal de categorías
				public function index() {
					$categories = $this->categoryModel->getCategories();
					$data = [
						'title' => 'Gestión de Categorías',
						'categories' => $categories
					];
					$this->view('categories/index', $data);
				}

				// Vista y procesamiento para crear categoría
				public function create() {
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

						$data = [
							'name' => trim($_POST['txtNombre']),
							'description' => trim($_POST['txtDescrip']),
							'imagen' => null,
							'mime' => null,
							'name_err' => ''
						];

						if (empty($data['name'])) {
							$data['name_err'] = 'Por favor ingrese el nombre de la categoría';
						}

						// Procesar la imagen si fue subida
						if (isset($_FILES['txtArchi']) && $_FILES['txtArchi']['error'] === UPLOAD_ERR_OK) {
							$data['imagen'] = file_get_contents($_FILES['txtArchi']['tmp_name']);
							$data['mime'] = $_FILES['txtArchi']['type'];
						}

						if (empty($data['name_err'])) {
							if ($this->categoryModel->addCategory($data)) {
								header('Location: ' . urlRoot . '/categories');
								exit();
							} else {
								die('Error al guardar la categoría.');
							}
						} else {
							$this->view('categories/inscateg', $data);
						}
					} else {
						$data = [
							'name' => '',
							'description' => '',
							'name_err' => ''
						];
						$this->view('categories/inscateg', $data);
					}
				}

				// Vista y procesamiento para modificar categoría
				public function edit($id) {
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

						$data = [
							'id' => $id,
							'name' => trim($_POST['txtNombre']),
							'description' => trim($_POST['txtDescrip']),
							'imagen' => null,
							'mime' => null,
							'name_err' => ''
						];

						if (empty($data['name'])) {
							$data['name_err'] = 'El nombre no puede estar vacío';
						}

						if (isset($_FILES['txtArchi']) && $_FILES['txtArchi']['error'] === UPLOAD_ERR_OK) {
							$data['imagen'] = file_get_contents($_FILES['txtArchi']['tmp_name']);
							$data['mime'] = $_FILES['txtArchi']['type'];
						}

						if (empty($data['name_err'])) {
							if ($this->categoryModel->updateCategory($data)) {
								header('Location: ' . urlRoot . '/categories');
								exit();
							} else {
								die('Error al actualizar.');
							}
						} else {
							$this->view('categories/modcateg', $data);
						}
					} else {
						$category = $this->categoryModel->getCategoryById($id);
						$data = [
							'id' => $category->CategoryID,
							'name' => $category->CategoryName,
							'description' => $category->Description,
							'has_image' => !empty($category->Imagen),
							'name_err' => ''
						];
						$this->view('categories/modcateg', $data);
					}
				}

				// Método para servir la imagen BLOB directamente con sus cabeceras HTTP
			public function image($id) {

    $category = $this->categoryModel->getCategoryById($id);

    if ($category && !empty($category->Imagen)) {

        // Limpiar cualquier salida previa
        if (ob_get_length()) {
            ob_clean();
        }

        // Indicar al navegador qué tipo de archivo recibirá
        header('Content-Type: ' . $category->Mime);
        header('Content-Length: ' . strlen($category->Imagen));
        header('Cache-Control: public, max-age=86400');

        // Enviar únicamente la imagen
        echo $category->Imagen;
        exit();

    } else {

        http_response_code(404);
        exit('Imagen no encontrada');
    }
}

				// Eliminar categoría
				public function delete($id) {
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						try {
							$this->categoryModel->deleteCategory($id);
							setFlash('ok', 'Categoría eliminada correctamente.');
						} catch (PDOException $e) {
							// la categoría tiene productos asociados (llave foránea)
							setFlash('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
						}
					}
					header('Location: ' . urlRoot . '/categories');
					exit();
				}

				// Imprime la lista de categorías en formato PDF
		public function pdfCategorias() {
			require_once(appRoot . '/libraries/fpdf/fpdf.php');

			// Obtiene los datos desde la base de datos vía Modelo (PDO)
			$categorias = $this->categoryModel->getCategories();

			// Obtiene los datos del usuario desde la variable de sesión (o fallback a 'Invitado')
			$usuario = $_SESSION['usuario'] ?? 'Invitado';

			// Crea instancia personalizada de FPDF para manejar encabezado y pie de página
			$pdf = new class($usuario) extends FPDF {
				private $user;

				public function __construct($user) {
					parent::__construct('P', 'mm', 'letter');
					$this->user = $user;
				}

				// Encabezado de la página
				function Header() {
					// Logotipo (ubicado en mvc2/public/img/logoNorthWind.png)
					// Se escala automáticamente de 1024x1024 a 22x22 mm
					$imgPath = appRoot . '/../public/img/logoNorthWind.jpeg';
					if (file_exists($imgPath)) {
						$this->Image($imgPath, 10, 8, 22, 22);
					}

					// Título principal / Marca
					$this->SetFont('Arial', 'B', 16);
					$this->SetTextColor(15, 23, 42); // Slate 900
					$this->SetXY(35, 10);
					$this->Cell(0, 6, 'Northwind', 0, 1, 'L');

					// Subtítulos institucionales
					$this->SetFont('Arial', '', 9);
					$this->SetTextColor(100, 116, 139); // Slate 500
					$this->SetX(35);
					$this->Cell(0, 4, 'Enterprise Solutions - Est. 1989', 0, 1, 'L');
					$this->SetX(35);
					$this->Cell(0, 4, 'Sample Sales Database', 0, 1, 'L');

					$this->Ln(6);

					// Título de la sección del reporte
					$this->SetFont('Arial', 'B', 12);
					$this->SetTextColor(30, 41, 59); // Slate 800
					$this->Cell(0, 8, 'List of Available Categories', 0, 1, 'L');

					// Línea divisora
					$this->SetDrawColor(226, 232, 240); // Slate 200
					$this->SetLineWidth(0.4);
					$this->Line(10, $this->GetY(), 200, $this->GetY());
					$this->Ln(4);

					// Encabezados de la tabla
					$this->SetFont('Arial', 'B', 9);
					$this->SetFillColor(241, 245, 249); // Slate 100
					$this->SetTextColor(51, 65, 85); // Slate 700
					$this->SetDrawColor(203, 213, 225); // Slate 300

					$this->Cell(15, 8, 'ID', 1, 0, 'C', true);
					$this->Cell(45, 8, 'Name', 1, 0, 'L', true);
					$this->Cell(130, 8, 'Description', 1, 1, 'L', true);
				}

				// Pie de página
				function Footer() {
					$this->SetY(-18);
					$this->SetFont('Arial', '', 8);
					$this->SetTextColor(100, 116, 139); // Slate 500

					// Línea superior del pie de página
					$this->SetDrawColor(226, 232, 240);
					$this->Line(10, $this->GetY(), 200, $this->GetY());
					$this->Ln(2);

					// Información de impresión
					$fechaImpresion = date('Y/M/d H:i');
					$this->Cell(0, 4, mb_convert_encoding("Printing by: " . $this->user, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
					$this->Cell(0, 4, mb_convert_encoding("Printing date: " . strtolower($fechaImpresion), 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

					// Número de página
					$this->SetY(-14);
					$this->Cell(0, 4, mb_convert_encoding('Page: ' . $this->PageNo() . '/{nb}', 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
				}
			};

			// Configura el documento PDF
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(30, 41, 59);

			// Imprime los datos de las categorías
			foreach ($categorias as $cat) {
				$pdf->Cell(15, 7, $cat->CategoryID, 1, 0, 'C');
				$pdf->Cell(45, 7, mb_convert_encoding($cat->CategoryName, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L');
				$pdf->Cell(130, 7, mb_convert_encoding($cat->Description, 'ISO-8859-1', 'UTF-8'), 1, 1, 'L');
			}

			// Genera salida al navegador
			$pdf->Output('I', 'rptCategories.pdf');
			exit();
		}	  

			}

			
		?>