<?php

namespace polymer\action;

use lithium\action\Response;
use lithium\action\DispatchException;

class Endpoint extends \lithium\core\Object {

	protected $_autoConfig = [
		'name',
		'url',
		'binding',
		'app'
	];

	protected $_name;

	protected $_url;

	protected $_binding;

	protected $_app;

	protected $_classes = [
		'binding' => 'polymer\data\Binding'
	];

	public function url() {
		if ($this->_url === null) {
			return '/' . $this->_name;
		}

		if ($this->_url === '') {
			return $this->_url;
		}

		if ($this->_url[0] === '/') {
			return $this->_url;
		}

		return '/' . $this->_url;

	}

	public function config($option = null, $merge = false) {
		if (!$option) {
			return $this->_config;
		}

		if ($merge === false || !$this->_app) {
			return $this->_config[$option];
		}

		$chain = $this->_app->traverse($this->_name);
		return array_reduce(array_reverse($chain), function($config, $endpoint) use ($option) {
			return $config + $endpoint->config($option);
		}, $this->config($option));
	}

	public function respond($request = null) {
		$binding = $this->_binding();

		$config = $this->config('binding', true);

		if (!isset($config['params'])) {
			$config['params'] = null;
		}

		return $binding->apply($config['class'], $config['method'], $config['params']);
	}

	protected function _binding() {
		if (!$this->_binding) {
			throw new DispatchException("Endpoint `{$this->_name}` cannot respond without a binding");
		}

		$binding = $this->_instance('binding');
		$config = $this->config('binding', true);
		$name = isset($config['adapter']) ? $config['adapter'] : 'default';

		return $binding::adapter($name);
	}
}

?>
