<?php
	class Product {
		private $db;

		public function __construct() {
			$this->db = new Database();
		}

		
		public function getProducts($categoryId = null) {
			$sql = 'SELECT p.ProductID, p.ProductName, p.UnitsInStock, p.UnitPrice, p.Discontinued,
						   c.CategoryName, s.CompanyName AS SupplierName
					FROM products p
					INNER JOIN categories c ON c.CategoryID = p.CategoryID
					INNER JOIN suppliers  s ON s.SupplierID = p.SupplierID';

			if ($categoryId !== null) {
				$sql .= ' WHERE p.CategoryID = :cat';
			}
			$sql .= ' ORDER BY p.ProductName ASC';

			$this->db->query($sql);
			if ($categoryId !== null) {
				$this->db->bind(':cat', (int)$categoryId);
			}
			return $this->db->resultSet();
		}

		// Un producto con todos sus campos
		public function getProductById($id) {
			$this->db->query('SELECT * FROM products WHERE ProductID = :id');
			$this->db->bind(':id', (int)$id);
			return $this->db->singleRow();
		}

		// Proveedores (tabla de consulta para el formulario)
		public function getSuppliers() {
			$this->db->query('SELECT SupplierID, CompanyName FROM suppliers ORDER BY CompanyName ASC');
			return $this->db->resultSet();
		}

		// Insertar producto
		public function addProduct($d) {
			$this->db->query('INSERT INTO products
				(ProductName, SupplierID, CategoryID, QuantityPerUnit, UnitPrice, UnitsInStock, UnitsOnOrder, ReorderLevel, Discontinued)
				VALUES (:name, :sup, :cat, :qpu, :price, :stock, :onorder, :reorder, :disc)');
			$this->bindFields($d);
			return $this->db->execute();
		}

		// Modificar producto
		public function updateProduct($d) {
			$this->db->query('UPDATE products SET
					ProductName = :name, SupplierID = :sup, CategoryID = :cat, QuantityPerUnit = :qpu,
					UnitPrice = :price, UnitsInStock = :stock, UnitsOnOrder = :onorder,
					ReorderLevel = :reorder, Discontinued = :disc
				WHERE ProductID = :id');
			$this->bindFields($d);
			$this->db->bind(':id', (int)$d['id']);
			return $this->db->execute();
		}

		
		public function deleteProduct($id) {
			try {
				$this->db->query('DELETE FROM products WHERE ProductID = :id');
				$this->db->bind(':id', (int)$id);
				return $this->db->execute();
			} catch (PDOException $e) {
				return false;
			}
		}

		
		public function getSellableProducts() {
			$this->db->query('SELECT p.ProductID, p.ProductName, p.UnitPrice, p.UnitsInStock, c.CategoryName
							  FROM products p
							  INNER JOIN categories c ON c.CategoryID = p.CategoryID
							  WHERE p.Discontinued = 0 AND p.UnitsInStock > 0
							  ORDER BY c.CategoryName ASC, p.ProductName ASC');
			return $this->db->resultSet();
		}

		public function getSellableProduct($id) {
			$this->db->query('SELECT ProductID, ProductName, UnitPrice, UnitsInStock
							  FROM products
							  WHERE ProductID = :id AND Discontinued = 0');
			$this->db->bind(':id', (int)$id);
			return $this->db->singleRow();
		}

		// Enlaza los parametros comunes de INSERT y UPDATE
		private function bindFields($d) {
			$this->db->bind(':name',    $d['name']);
			$this->db->bind(':sup',     (int)$d['supplier']);
			$this->db->bind(':cat',     (int)$d['category']);
			$this->db->bind(':qpu',     $d['qpu']);
			$this->db->bind(':price',   (string)$d['price']);
			$this->db->bind(':stock',   (int)$d['stock']);
			$this->db->bind(':onorder', (int)$d['onorder']);
			$this->db->bind(':reorder', (int)$d['reorder']);
			$this->db->bind(':disc',    (int)$d['discontinued']);
		}
	}
?>