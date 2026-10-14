<?php
	class Employee {
		private $db;

		public function __construct() {
			$this->db = new Database();
		}

		// Lista de empleados para el combo de venta asistida
		public function getEmployees() {
			$this->db->query('SELECT EmployeeID, FirstName, LastName, Title
							  FROM employees ORDER BY LastName ASC, FirstName ASC');
			return $this->db->resultSet();
		}

		public function getEmployeeById($id) {
			$this->db->query('SELECT EmployeeID, FirstName, LastName, Title FROM employees WHERE EmployeeID = :id');
			$this->db->bind(':id', (int)$id);
			return $this->db->singleRow();
		}
	}
?>