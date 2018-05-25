<?php
class Request
{
	/**
	 * authentication token
	 * 
	 * @var $token
	 */
	public $token = null;
	
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 */
	public $uri;
	
	/**
	 * http request method
	 *
	 * @var string post, put, delete, get
	 */
	public $method = 'get';
	
	/**
	 * data posted/put in the request
	 *
	 * @var string
	 */
	public $data = null;

	/**
	 * if true, Caller will json_decode the response
	 *
	 * @var boolean
	 */
	public $decodeResponse = true;
}
?>
