<?php
	class Order {
		private $db;

		public function __construct() {
			$this->db = new Database();
		}

		
		public function getOrders($limit = 100) {
			$this->db->query('SELECT o.OrderID, o.OrderDate, c.CompanyName AS CustomerName,
									 CONCAT(e.FirstName, \' \', e.LastName) AS EmployeeName,
									 sh.CompanyName AS ShipperName,
									 COALESCE(SUM(od.UnitPrice * od.Quantity * (1 - od.Discount)), 0) AS Total
							  FROM orders o
							  INNER JOIN customers c  ON c.CustomerID  = o.CustomerID
							  INNER JOIN employees e  ON e.EmployeeID  = o.EmployeeID
							  INNER JOIN shippers  sh ON sh.ShipperID  = o.ShipVia
							  LEFT  JOIN order_details od ON od.OrderID = o.OrderID
							  GROUP BY o.OrderID, o.OrderDate, c.CompanyName, e.FirstName, e.LastName, sh.CompanyName
							  ORDER BY o.OrderID DESC
							  LIMIT ' . (int)$limit);
			return $this->db->resultSet();
		}

		// Encabezado de una orden
		public function getOrderById($id) {
			$this->db->query('SELECT o.*, c.CompanyName AS CustomerName, c.ContactName,
									 CONCAT(e.FirstName, \' \', e.LastName) AS EmployeeName,
									 sh.CompanyName AS ShipperName
							  FROM orders o
							  INNER JOIN customers c  ON c.CustomerID = o.CustomerID
							  INNER JOIN employees e  ON e.EmployeeID = o.EmployeeID
							  INNER JOIN shippers  sh ON sh.ShipperID = o.ShipVia
							  WHERE o.OrderID = :id');
			$this->db->bind(':id', (int)$id);
			return $this->db->singleRow();
		}

		// Lineas (detalle) de una orden
		public function getOrderDetails($id) {
			$this->db->query('SELECT od.ProductID, p.ProductName, c.CategoryName,
									 od.UnitPrice, od.Quantity, od.Discount,
									 (od.UnitPrice * od.Quantity * (1 - od.Discount)) AS Subtotal
							  FROM order_details od
							  INNER JOIN products   p ON p.ProductID  = od.ProductID
							  INNER JOIN categories c ON c.CategoryID = p.CategoryID
							  WHERE od.OrderID = :id
							  ORDER BY p.ProductName ASC');
			$this->db->bind(':id', (int)$id);
			return $this->db->resultSet();
		}

		
		public function createOrder($customerId, $employeeId, $shipperId, $items) {
			try {
				$this->db->beginTransaction();

				// 1) encabezado
				$this->db->query('INSERT INTO orders
					(CustomerID, EmployeeID, OrderDate, RequiredDate, ShippedDate, ShipVia, Freight,
					 ShipName, ShipAddress, ShipCity, ShipRegion, ShipPostalCode, ShipCountry)
					SELECT CustomerID, :emp, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY),
						   :ship, 0, CompanyName, Address, City, Region, PostalCode, Country
					FROM customers WHERE CustomerID = :cust');
				$this->db->bind(':emp',  (int)$employeeId);
				$this->db->bind(':ship', (int)$shipperId);
				$this->db->bind(':cust', (string)$customerId);
				$this->db->execute();

				if ($this->db->affectedRows() !== 1) {
					throw new Exception('El cliente seleccionado no existe.');
				}
				$orderId = (int)$this->db->lastInsertId();

				// 2) detalle + inventario
				foreach ($items as $item) {
					// se bloquea la fila del producto para que nadie mas la modifique mientras se vende
					$this->db->query('SELECT ProductName, UnitPrice, UnitsInStock
									  FROM products
									  WHERE ProductID = :id AND Discontinued = 0 FOR UPDATE');
					$this->db->bind(':id', (int)$item['id']);
					$product = $this->db->singleRow();

					if (!$product) {
						throw new Exception('Uno de los productos ya no está disponible.');
					}
					if ($product->UnitsInStock < (int)$item['qty']) {
						throw new Exception('Existencia insuficiente para "' . $product->ProductName .
											'". Disponible: ' . $product->UnitsInStock);
					}

					$this->db->query('INSERT INTO order_details (OrderID, ProductID, UnitPrice, Quantity, Discount)
									  VALUES (:ord, :prod, :price, :qty, 0)');
					$this->db->bind(':ord',   $orderId);
					$this->db->bind(':prod',  (int)$item['id']);
					$this->db->bind(':price', (string)$product->UnitPrice);
					$this->db->bind(':qty',   (int)$item['qty']);
					$this->db->execute();

					$this->db->query('UPDATE products SET UnitsInStock = UnitsInStock - :qty WHERE ProductID = :id');
					$this->db->bind(':qty', (int)$item['qty']);
					$this->db->bind(':id',  (int)$item['id']);
					$this->db->execute();
				}

				$this->db->commit();
				return ['ok' => true, 'orderId' => $orderId];

			} catch (Exception $e) {
				$this->db->rollBack();

				// PDOException hereda de Exception: se oculta el detalle tecnico al usuario
				if ($e instanceof PDOException) {
					return ['ok' => false, 'error' => 'No se pudo guardar la venta. Intente de nuevo.'];
				}
				return ['ok' => false, 'error' => $e->getMessage()];
			}
		}
	}
?>