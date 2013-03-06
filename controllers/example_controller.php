<?php
require_once("utils/rest_utils.php");
require_once("utils/db_utils.php");

class ExampleController {
	function __construct() {
		$this->db = new DBUtils();
	}

	function get($params, $data) {
		$id = $params[0];
		if ($id) {
			return new RestResponse(200, $this->db->get("tasks", $id));
		} else {
			return new RestResponse(200, $this->db->search("tasks"));
		}
	}

	function post($params, $data) {
		return new RestResponse(200, $this->db->insert("tasks", $data));
	}

	function put($params, $data) {
		$data["id"] = $params[0];
		return new RestResponse(200, $this->db->update("tasks", $data));
	}
	
	function delete($params, $data) {
		$id = $params[0];
		return new RestResponse(200, $this->db->delete("tasks", $id));
	}
}