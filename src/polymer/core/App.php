<?php

namespace polymer\core;

use lithium\net\http\Router;
use lithium\action\Request;
use lithium\action\Response;
use lithium\action\Dispatcher;

class App extends \lithium\core\Object {

	protected $_autoConfig = [
		'namespace'
	];

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

	public function __construct(array $config = []) {
		parent::__construct($config);

		$this->connect();
	}
	/**
	 * Get the App's root URL. Consists of the namespace preceeded by a forward slash.
	 */
	public function getUrl() {
		return '/' . $this->_namespace;
	}

	/**
	 * Connect the root URL using the li3 Router
	 *
	 * @todo: Support li3-style DI
	 */
	public function connect() {
		Router::connect($this->getUrl(), [], [$this, 'respond']);
	}

	/**
	 * Return a Response that can be handled by the li3 Router
	 */
	public static function respond($request = null) {
		return new Response([
			'code' => 200,
			'body' => "Hello World!"
		]);
	}

	/**
	 * Run the App by creating an instance of Request and running it through the Dispatcher
	 */
	public function run() {
		$request = new Request();
		return Dispatcher::run($request);
	}
}

?>
