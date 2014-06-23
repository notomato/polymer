<?php

namespace polymer\action;

use lithium\action\Response;
use lithium\action\DispatchException;

class Endpoint extends \lithium\core\Object {

	protected $_autoConfig = [
		'app',
		'name',
		'url',
		'binding',
		'decorators'
	];

	protected $_app;

	protected $_name;

	protected $_url;

	protected $_binding;

	protected $_decorators;

	protected $_classes = [
		'binding'   => 'polymer\data\Binding',
		'decorator' => 'polymer\data\Decorator',
		'response'  => 'lithium\action\Response',
		'media'     => 'lithium\net\http\Media',
		'string'    => 'lithium\util\String'
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

	public function respond($request = null, array $options = []) {
		$options += [
			'render' => true
		];

		$data = $this->_binding($request);
		$decorators = $this->_decorators($data);

		$pipeline = compact('data', 'decorators');

		if ($options['render'] !== true) {
			return $pipeline;
		}

		$media = $this->_instance('media');
		$response = $this->_instance('response');

		$type = $media::negotiate($request);

		return $media::render($response, $pipeline, compact('type'));
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

	/**
	 * Get binding adapter and configuration, merging in data from $request
	 */
	protected function _binding($request) {
		if (!$this->_binding) {
			throw new DispatchException("Endpoint `{$this->_name}` cannot respond without a binding");
		}

		$defaults = [
			'adapter' => 'default',
			'params' => []
		];

		$binding = $this->_instance('binding');

		$config = $this->config('binding', true) + $defaults;
		$config['params'] = $this->_params($request, $config['params']);

		$adapter = $binding::adapter($config['adapter']);

		return $adapter->apply($config['class'], $config['method'], $config['params']);
	}

	/**
	 * Get Decorator adapters and configuration, and apply $data
	 */
	protected function _decorators($data) {
		if (!$this->_decorators) {
			return [];
		}

		$defaults = [
			'adapter' => 'default',
			'app' => $this->_app
		];

		$decorator = $this->_instance('decorator');

		$configs = $this->config('decorators');

		return array_reduce($configs, function($decorators, $config) use ($data, $defaults, $decorator) {
			$config += $defaults;

			$name = $config['adapter'];
			$adapter = $decorator::adapter($name);

			$decorators[$name] = $adapter->apply($data, $config);

			return $decorators;
		}, []);
	}
}

?>
