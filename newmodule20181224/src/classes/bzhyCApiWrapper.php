<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


/**
 * This class should be used as a client for calling API services.
 */
class bzhyCApiWrapper {

	/**
	 * Currently used API.
	 *
	 * @var string
	 */
	public $api;

	/**
	 * Authentication token.
	 *
	 * @var string
	 */
	public $auth;

	/**
	 * Current API client.
	 *
	 * @var CApiClient
	 */
	protected $client;

	/**
	 * @param CApiClient $client	the API client to use
	 */
	public function __construct(bzhyCApiClient $client) {
		$this->setClient($client);
	}

	/**
	 * Sets the API client.
	 *
	 * @param CApiClient $client
	 */
	public function setClient(bzhyCApiClient $client) {
		$this->client = $client;
	}

	/**
	 * Returns the API client.
	 *
	 * @return CApiClient
	 */
	public function getClient() {
		return $this->client;
	}

	/**
	 * A magic method for calling the public methods of the API client.
	 *
	 * @param string 	$method		API method name
	 * @param array 	$params		API method parameters
	 *
	 * @return CApiClientResponse
	 */
	public function __call($method, array $params) {
		return $this->callMethod($method, reset($params));
	}

	/**
	 * Pre-process and call the client method.
	 *
	 * @param string 	$method		API method name
	 * @param mixed 	$params		API method parameters
	 *
	 * @return CApiClientResponse
	 */
	protected function callMethod($method, $params) {
		return $this->callClientMethod($method, $params);
	}

	/**
	 * Call the client method and return the result.
	 *
	 * @param string 	$method		API method name
	 * @param mixed 	$params		API method parameters
	 *
	 * @return CApiClientResponse
	 */
	protected function callClientMethod($method, $params) {
		return $this->client->callMethod($this->api, $method, $params, $this->auth);
	}
}
