<?php

namespace polymer\action;

use lithium\action\Response;

class Endpoint extends \lithium\core\Object {

	protected $_autoConfig = [
		'name',
		'url'
	];

	protected $_name;

	protected $_url;

	public function url() {
		return '/' . ($this->_url ?: $this->_name);
	}

	public function config($option = null) {
		if (!$option) {
			return $this->_config;
		}

		return $this->_config[$option];
	}

	public function respond($request = null) {
		return [
			'code' => 200,
			'body' => "Hello from {$this->_name}!"
		];
	}
}

?>
