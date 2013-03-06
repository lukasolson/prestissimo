<?php
class RestRequest {
	public $data, $http_accept, $method, $request_uri;

	public function __construct() 	{
		$this->data				= array();
		$this->http_accept		= (strpos($_SERVER["HTTP_ACCEPT"], "json")) ? "json" : "xml";
		$this->method			= "get";
		$this->request_uri		= $_SERVER["REQUEST_URI"];
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function setMethod($method) {
		$this->method = $method;
	}

	public function getData() {
		return $this->data;
	}

	public function getMethod() {
		return $this->method;
	}

	public function getHttpAccept() {
		return $this->http_accept;
	}
}

class RestResponse {
	private $status, $body, $content_type;
	
	public function __construct($status = 200, $body = null, $content_type = "application/json") {
		$this->setStatus($status);
		$this->setBody($body);
		$this->setContentType($content_type);
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus($status) {
		$this->status = $status;
	}
	
	public function getBody() {
		return $this->body;
	}
	
	public function setBody($body) {
		$this->body = $body;
	}
	
	public function getContentType() {
		return $this->content_type;
	}
	
	public function setContentType($content_type) {
		$this->content_type = $content_type;
	}
}

class RestUtils {
	public static function processRequest() {
		// Get our verb
		$request_method = strtolower($_SERVER["REQUEST_METHOD"]);
		$request_obj = new RestRequest();
		$data = array();

		switch ($request_method) {
			case "get":
			case "delete":
				$data = $_GET;
				break;
			case "post":
			case "put":
				$data = json_decode(file_get_contents("php://input"), true);
				break;
		}

		// Store the method
		$request_obj->setMethod($request_method);

		// Set the raw data, so we can access it if needed (there may be
		// other pieces to your requests)
		$request_obj->setData($data);
		
		return $request_obj;
	}

	public static function sendResponse($response) {
		$status_header = "HTTP/1.1 " . $response->getStatus() . " " . RestUtils::getStatusCodeMessage($response->getStatus());
		
		header($status_header);
		header("Content-type: " . $response->getContentType());
		
		echo json_encode($response->getBody());
	}

	public static function getStatusCodeMessage($status) {
		$codes = Array(
		    100 => "Continue",
		    101 => "Switching Protocols",
		    200 => "OK",
		    201 => "Created",
		    202 => "Accepted",
		    203 => "Non-Authoritative Information",
		    204 => "No Content",
		    205 => "Reset Content",
		    206 => "Partial Content",
		    300 => "Multiple Choices",
		    301 => "Moved Permanently",
		    302 => "Found",
		    303 => "See Other",
		    304 => "Not Modified",
		    305 => "Use Proxy",
		    306 => "(Unused)",
		    307 => "Temporary Redirect",
		    400 => "Bad Request",
		    401 => "Unauthorized",
		    402 => "Payment Required",
		    403 => "Forbidden",
		    404 => "Not Found",
		    405 => "Method Not Allowed",
		    406 => "Not Acceptable",
		    407 => "Proxy Authentication Required",
		    408 => "Request Timeout",
		    409 => "Conflict",
		    410 => "Gone",
		    411 => "Length Required",
		    412 => "Precondition Failed",
		    413 => "Request Entity Too Large",
		    414 => "Request-URI Too Long",
		    415 => "Unsupported Media Type",
		    416 => "Requested Range Not Satisfiable",
		    417 => "Expectation Failed",
		    500 => "Internal Server Error",
		    501 => "Not Implemented",
		    502 => "Bad Gateway",
		    503 => "Service Unavailable",
		    504 => "Gateway Timeout",
		    505 => "HTTP Version Not Supported"
		);

		return (isset($codes[$status])) ? $codes[$status] : "";
	}
}
?>