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
		'binding'  => 'polymer\data\Binding',
		'response' => 'lithium\action\Response',
		'media'    => 'lithium\net\http\Media',
		'string'   => 'lithium\util\String'
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

		$media = $this->_instance('media');
		$type = $media::negotiate($request);
		$response = $this->_instance('response');

		$params = isset($config['params']) ? $this->_params($request, $config['params']) : [];
		$data = $binding->apply($config['class'], $config['method'], $params);

		return $media::render($response, $data, compact('type'));
	}

	protected function _params($request, array $params) {
		$string = $this->_instance('string');
		$data = $request->params;

		$replacer = function($value) use ($string, $data, &$replacer) {
			if (is_array($value)) {
				return array_map($replacer, $value);
			}

			if (!is_string($value)) {
				return $value;
			}

			return $string::insert($value, $data);
		};

		return array_map($replacer, $params);
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
