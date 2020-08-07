<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


/**
 * This class should be used for calling API services.
 */
abstract class bzhyCApiClient {

	/**
	 * Call the given API service method and return the response.
	 *
	 * @param string 	$api
	 * @param string 	$method
	 * @param mixed 	$params
	 * @param string	$auth
	 *
	 * @return CApiClientResponse
	 */
	abstract public function callMethod($api, $method, $params, $auth);
}
