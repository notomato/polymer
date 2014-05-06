<?php

namespace spec\polymer;

use polymer\core\App;
use kahlan\plugins\Stub;

describe("app", function() {

	it("should set the root URL", function() {
		$app = new App();
		expect($app->getUrl())->toEqual('/');

		$app = new App([
			'namespace' => 'v1'
		]);
		expect($app->getUrl())->toEqual('/v1');
	});

	it("should connect the root URL to the Router", function() {
		expect('lithium\net\http\Router')->toReceive('::connect')->with('/');
		$app = new App();

		expect('lithium\net\http\Router')->toReceive('::connect')->with('/v1');
		$app = new App([
			'namespace' => 'v1'
		]);
	});

	it("should return a response", function() {
		$response = App::respond();

		expect($response)->toBeAnInstanceOf('lithium\action\response');
		expect($response->body())->toEqual("Hello World!");
		expect($response->status['code'])->toEqual(200);
	});

	it("should run", function() {
		$app = new App();

		expect('lithium\action\Request')->toReceive('__construct');
		expect('lithium\action\Dispatcher')->toReceive('::run');
		$app->run();
	});
});

?>
