<?php
	class Shipper {
		private $db;

		public function __construct() {
			$this->db = new Database();
		}

		
		public function getShippers() {
			$this->db->query('SELECT ShipperID, CompanyName, Phone FROM shippers ORDER BY CompanyName ASC');
			return $this->db->resultSet();
		}

		public function getShipperById($id) {
			$this->db->query('SELECT ShipperID, CompanyName, Phone FROM shippers WHERE ShipperID = :id');
			$this->db->bind(':id', (int)$id);
			return $this->db->singleRow();
		}
	}
?>