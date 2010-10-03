<?php
	/**
	* List of HTTP status codes defined as constants
	*
	* @internal I (skwashd) have no intention of documenting the rest of this file
	*	if you have no life and have the inclination, post a patch on savannah
	*
	* @internal Lifted from RESTy.php (http://gonze.com/resty/resty.php)
	*
	* @copright copyright &copy; 2004 Lucas Gonze <lucas@gonze.com>, 
	* @license dedicated to the public domain
	*/

	define('HTTP_100','100 Continue');
	define('HTTP_101','101 Switching Protocols');
	define('HTTP_200','200 OK');
	define('HTTP_201','201 Created');
	define('HTTP_202','202 Accepted');
	define('HTTP_203','203 Non-Authoritative Information');
	define('HTTP_204','204 No Content');
	define('HTTP_205','205 Reset Content');
	define('HTTP_206','206 Partial Content');
	define('HTTP_300','300 Multiple Choices');
	define('HTTP_301','301 Moved Permanently');
	define('HTTP_302','302 Found');
	define('HTTP_303','303 See Other');
	define('HTTP_304','304 Not Modified');
	define('HTTP_305','305 Use Proxy');
	define('HTTP_306','306 (Unused)');
	define('HTTP_307','307 Temporary Redirect');
	define('HTTP_400','400 Bad Request');
	define('HTTP_401','401 Unauthorized');
	define('HTTP_402','402 Payment Required');
	define('HTTP_403','403 Forbidden');
	define('HTTP_404','404 Not Found');
	define('HTTP_405','405 Method Not Allowed');
	define('HTTP_406','406 Not Acceptable');
	define('HTTP_407','407 Proxy Authentication Required');
	define('HTTP_408','408 Request Timeout');
	define('HTTP_409','409 Conflict');
	define('HTTP_410','410 Gone');
	define('HTTP_411','411 Length Required');
	define('HTTP_412','412 Precondition Failed');
	define('HTTP_413','413 Request Entity Too Large');
	define('HTTP_414','414 Request-URI Too Long');
	define('HTTP_415','415 Unsupported Media Type');
	define('HTTP_416','416 Requested Range Not Satisfiable');
	define('HTTP_417','417 Expectation Failed');
	define('HTTP_500','500 Internal Server Error');
	define('HTTP_501','501 Not Implemented');
	define('HTTP_502','502 Bad Gateway');
	define('HTTP_503','503 Service Unavailable');
	define('HTTP_504','504 Gateway Timeout');
	define('HTTP_505','505 HTTP Version Not Supported');
?>
