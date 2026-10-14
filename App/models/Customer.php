<?php
	class Customer {
		private $db;

		public function __construct() {
			$this->db = new Database();
		}

		// Lista de clientes para los combos de seleccion
		public function getCustomers() {
			$this->db->query('SELECT CustomerID, CompanyName, ContactName, City, Country
							  FROM customers ORDER BY CompanyName ASC');
			return $this->db->resultSet();
		}

		public function getCustomerById($id) {
			$this->db->query('SELECT * FROM customers WHERE CustomerID = :id');
			$this->db->bind(':id', (string)$id);
			return $this->db->singleRow();
		}
	}
?>