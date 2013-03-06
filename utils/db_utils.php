<?php
class DBUtils {
	private $con;
	
	public function __construct() {
		$con = mysql_connect("localhost", "username", "password");
		if (!$con) die("Could not connect: " . mysql_error());
		mysql_select_db("database_name", $con) or die(mysql_error());
	}
	
	public function search($table_name, $where_params = null, $order_by_params = null) {
		$entries = array();
		
		$query = $this->prepare_select($table_name);
		$query .= $this->prepare_where($where_params);
		$query .= $this->prepare_order_by($order_by_params);
		
		$result = $this->execute($query);
		
		while ($entry = $this->fetch($result)) {
			array_push($entries, $entry);
		}
		
		return $entries;
	}
	
	public function get($table_name, $id) {
		$entry = null;
		
		$query = $this->prepare_select($table_name);
		$query .= $this->prepare_where(array("id" => $id));
		
		$result = $this->execute($query);
		$entry = $this->fetch($result);

		return $entry;
	}
	
	public function insert($table_name, $entry) {
		$query = $this->prepare_insert($table_name, $entry);

		$this->execute($query);
		
		$entry["id"] = $this->auto_increment_id();
		
		return $entry;
	}
	
	public function update($table_name, $entry) {
		$query = $this->prepare_update($table_name, $entry);
		$query .= $this->prepare_where(array("id" => $entry["id"]));
		
		$this->execute($query);
		
		return $entry;
	}
	
	public function delete($table_name, $id) {
		$query = $this->prepare_delete($table_name);
		$query .= $this->prepare_where(array("id" => $id));
		
		$this->execute($query);
	}
	
	public function execute($query) {
		return mysql_query($query);
	}
	
	public function escape($value) {
		return mysql_real_escape_string($value);
	}
	
	public function fetch($result) {
		return mysql_fetch_assoc($result);
	}
	
	public function auto_increment_id() {
		return mysql_insert_id();
	}
	
	private function prepare_select($table_name, $column_names = array("*")) {
		$query = "";
		
		$i = 0;
		foreach ($column_names as $value) {
			if ($i++ == 0) {
				$query .= "SELECT ";
			} else {
				$query .= ", ";
			}
			$query .= $value . " ";
		}
		
		$query .= "FROM $table_name ";
		
		return $query;
	}
	
	private function prepare_insert($table_name, $insert_params) {
		$query = "INSERT INTO $table_name (";
		
		$i = 0;
		foreach (array_keys($insert_params) as $key) {
			if ($i++ > 0) {
				$query .= ", ";
			}
			$query .= $key;
		}
		
		$query .= ") VALUES (";
		
		$i = 0;
		foreach (array_values($insert_params) as $value) {
			if ($i++ > 0) {
				$query .= ", ";
			}
			if (!is_null($value)) {
				if ($value != "now()") {
					$query .= "'" . $this->escape($value) . "'";
				} else {
					$query .= $value;
				}
			} else {
				$query .= "null";
			}
		}
		
		$query .= ")";
		
		return $query;
	}
	
	private function prepare_update($table_name, $set_params) {
		$query = "UPDATE $table_name ";
		
		$i = 0;
		foreach ($set_params as $key => $value) {
			if ($i++ == 0) {
				$query .= "SET ";
			} else {
				$query .= ", ";
			}
			if (!is_null($value)) {
				if ($value != "now()") {
					$query .= "$key = '" . $this->escape($value) . "'";
				} else {
					$query .= "$key = $value";
				}
			} else {
				$query .= "$key = null";
			}
		}
		
		return $query . " ";
	}
	
	private function prepare_delete($table_name) {
		$query = "DELETE FROM $table_name ";
		
		return $query;
	}
	
	private function prepare_where($where_params) {
		$query = "";
		
		$i = 0;
		if ($where_params) {
			foreach ($where_params as $key => $value) {
				if ($i++ == 0) {
					$query .= "WHERE ";
				} else {
					$query .= "AND ";
				}
				
				if (!is_null($value)) {
					$query .= "$key = '" . $this->escape($value) . "' ";
				} else {
					$query .= "$key IS NULL ";
				}
			}
		}
		
		return $query;
	}
	
	private function prepare_order_by($order_by_params) {
		$query = "";
		
		$i = 0;
		if ($order_by_params) {
			foreach ($order_by_params as $value) {
				if ($i++ == 0) {
					$query .= "ORDER BY ";
				} else {
					$query .= ", ";
				}
				$query .= $value;
			}
		}
		
		return $query;
	}
}
?>