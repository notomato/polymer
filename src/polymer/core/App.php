<?php

namespace polymer\core;

use lithium\core\ConfigException;
use polymer\action\Endpoint;

class App extends \lithium\core\Object {

	protected $_autoConfig = [
		'namespace'
	];

	/**
	 * A cache of Endpoints that are direct children of the root
	 */
	protected $_children = [];

	/**
	 * Namespace of this App. It is used to build the root URL. All Endpoints 
	 * will be children of this URL.
	 *
	 * For example, a namespace of `v1` would result in a root URL of 
	 * `http://myapp.com/v1`. Connecting an Endpoint named `widgets` would have 
	 * a URL of `http://myapp.com/v1/widgets`.
	 *
	 * Using a Namespace is not mandatory, but strongly recommended.
	 */
	protected $_namespace = '';

	protected $_classes = [
		'router'     => 'lithium\net\http\Router',
		'dispatcher' => 'lithium\action\Dispatcher',
		'request'    => 'lithium\action\Request',
		'response'   => 'lithium\action\Response'
	];

	public function __construct(array $config = []) {
		parent::__construct($config);

		$this->_connect();
	}

	/**
	 * Get the full URL of the Endpoint named `$endpoint` if given, otherwise get the App's 
	 * root URL.
	 */
	public function url($endpoint = null) {
		if (!$endpoint) {
			return '/' . $this->_namespace;
		}

		$initial = $this->_namespace ? $this->url() : '';
		$chain = $this->traverse($endpoint->config('name'));

		return array_reduce(array_reverse($chain), function($url, $endpoint) {
			return $url . $endpoint->url();
		}, $initial);
	}

	/**
	 * Connect the root URL or Endpoint URL using the li3 Router.
	 */
	protected function _connect($endpoint = null) {
		$callee = $endpoint ?: $this;
		$router = $this->_instance('router');

		$router::connect($this->url($endpoint), [], function($request) use ($callee) {
			return $callee->respond($request);
		});
	}

	/**
	 * Return an array that can be used to construct an instance of `lithium\action\Response`
	 */
	public function respond($request = null) {
		return $this->_instance('response', [
			'status' => 200,
			'body' => "Hello World!"
		]);
	}

	/**
	 * Run the App by creating an instance of Request and running it through the Dispatcher
	 */
	public function run() {
		$request = $this->_instance('request');
		$dispatcher = $this->_instance('dispatcher');
		return $dispatcher::run($request);
	}

	/**
	 * Create an Endpoint
	 */
	public function endpoint(array $options) {
		$defaults = [
			'abstract' => false,
			'app' => $this
		];
		$options += $defaults;

		if(!isset($options['name'])) {
			throw new ConfigException("Endpoint name required");
		}

		$name = $options['name'];
		if (isset($this->_children[$name])) {
			throw new ConfigException("Endpoint `$name` already defined");
		}

		$endpoint = new Endpoint($options);

		$this->_children[$name] = $endpoint;

		if ($options['abstract'] === false) {
			$this->_connect($endpoint);
		}

		return $endpoint;
	}

	/**
	 * Return a list of Endpoints that are direct children
	 */
	public function children() {
		return $this->_children;
	}

	/**
	 * Return the inheritance chain for the given endpoint name
	 */
	public function traverse($name) {
		$chain = [ $this->_children[$name] ];

		$pos = strrpos($name, '.');

		if ($pos === false) {
			return $chain;
		}

		$parent = substr($name, 0, $pos);

		return array_merge($chain, $this->traverse($parent));
	}
}

?>
