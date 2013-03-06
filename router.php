<?php
$base_url = "";

require_once("utils/rest_utils.php");

function to_camel_case($str) {
	$str[0] = strtoupper($str[0]);
	$func = create_function('$c', 'return strtoupper($c[1]);');
	return preg_replace_callback('/_([a-z])/', $func, $str);
}

$request = RestUtils::processRequest();
$response = new RestResponse(404, array("errorMessage" => "Resource not found"));

// Ignore everything before the base url
$route = substr($request->request_uri, strpos($request->request_uri, $base_url) + strlen($base_url));
$uri_chunks = explode("/", $route);

$controller = "";
$params = array();

$i = 0;

// The first chunk is the controller
if ($uri_chunks[$i]) {
	$controller_file_name = $uri_chunks[$i++];
	
	// Ignore any GET parameters (they can still be accessed through the $data object)
	if (strrpos($controller_file_name, "?")) {
		$controller_file_name = substr($controller_file_name, 0, strrpos($controller_file_name, "?"));
	}
}

// Everything following /api/controller/ is parameters
while ($uri_chunks[$i]) {
	array_push($params, $uri_chunks[$i++]);
}

// Create a new instance of the given controller and call the given method with the given parameters
if (file_exists("controllers/" . $controller_file_name . "_controller.php")) {
	require_once("controllers/" . $controller_file_name . "_controller.php");
	$controller_name = to_camel_case($controller_file_name) . "Controller";
	$controller = new $controller_name;
	
	if (method_exists($controller, $request->getMethod())) {
		$response = $controller->{$request->getMethod()}($params, $request->getData());
	} else {
		$response = new RestResponse(405, json_encode(array("errorMessage" => "Method not found")));
	}
}

RestUtils::sendResponse($response);
?>