<?php

namespace polymer\action;

use lithium\action\Response;
use lithium\action\DispatchException;

class Endpoint extends \lithium\core\Object {

	protected $_autoConfig = [
		'name',
		'url',
		'binding'
	];

	protected $_name;

	protected $_url;

	protected $_binding;

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

	public function config($option = null) {
		if (!$option) {
			return $this->_config;
		}

		return $this->_config[$option];
	}

	public function respond($request = null) {
		$binding = $this->_binding();

		$config = $this->_binding;
		return $binding->apply($config['class'], $config['method'], $config['params']);
	}

	protected function _binding() {
		if (!$this->_binding) {
			throw new DispatchException("Endpoint `{$this->_name}` cannot respond without a binding");
		}

		$binding = $this->_instance('binding');
		$name = isset($this->_binding['adapter']) ? $this->_binding['adapter'] : 'default';

		return $binding::adapter($name);
	}
}

?>
