<?php

namespace spec\polymer;

use polymer\core\App;
use kahlan\plugins\Stub;

describe("app", function() {

	$this->router = 'lithium\net\http\Router';

	describe("constructor", function() {
		it("should set the root URL", function() {
			$app = new App();
			expect($app->url())->toEqual('/');

			$app = new App([
				'namespace' => 'v1'
			]);
			expect($app->url())->toEqual('/v1');
		});

		it("should connect the root URL to the Router", function() {
			expect($this->router)->toReceive('::connect')->with('/');
			$app = new App();

			expect($this->router)->toReceive('::connect')->with('/v1');
			$app = new App([
				'namespace' => 'v1'
			]);
		});
	});

	describe("methods", function() {
		beforeEach(function() {
			$this->app = new App();
		});

		it("should return a response", function() {
			$response = $this->app->respond();

			expect($response)->toEqual([
				'code' => 200,
				'body' => "Hello World!"
			]);
		});

		it("should run", function() {
			expect('lithium\action\Request')->toReceive('__construct');
			expect('lithium\action\Dispatcher')->toReceive('::run');
			$this->app->run();
		});

		it("should get the root url", function() {
			$url = $this->app->url();
			expect($url)->toEqual('/');

			$app = new App(['namespace' => 'v1']);
			$url = $app->url();
			expect($url)->toEqual('/v1');
		});
	});

	describe("endpoint", function() {
		beforeEach(function() {
			$this->app = new App([
				'namespace' => 'v1'
			]);
		});

		it("should throw if name is not defined", function() {
			$fn = function() {
				$this->app->endpoint([]);
			};

			expect($fn)->toThrow(new \lithium\core\ConfigException("Endpoint name required"));
		});

		it("should throw if name already exists", function() {
			$fn = function() {
				$this->app->endpoint(['name' => 'widgets']);
				$this->app->endpoint(['name' => 'widgets']);
			};

			expect($fn)->toThrow(new \lithium\core\ConfigException("Endpoint `widgets` already defined"));
		});

		it("should be created", function() {
			$endpoint = $this->app->endpoint(['name' => 'widgets']);

			expect($endpoint)->toBeAnInstanceOf('polymer\action\Endpoint');
		});

		it("should connect the endpoint using `url` if given", function() {
			expect($this->router)->toReceive('::connect')->with('/v1/some-alias');

			$this->app->endpoint([
				'name' => 'widgets',
				'url'  => 'some-alias'
			]);
		});

		it("should connect the endpoint using `name` if `url` is missing", function() {
			expect($this->router)->toReceive('::connect')->with('/v1/widgets');

			$this->app->endpoint(['name' => 'widgets']);
		});

		it("should connect the endpoint if the App has no namespace", function() {
			expect($this->router)->toReceive('::connect')->with('/some-alias');

			$app = new App();
			$app->endpoint([
				'name' => 'widgets',
				'url'  => 'some-alias'
			]);

			expect($this->router)->toReceive('::connect')->with('/gizmos');
			$app->endpoint(['name' => 'gizmos']);
		});

		it("should not connect abstract endpoints", function() {
			expect($this->router)->not->toReceive('::connect');

			$this->app->endpoint([
				'name' => 'widgets',
				'abstract'  => true
			]);
		});

		it("should list endpoints", function() {
			$children = $this->app->children();

			expect($children)->toHaveLength(0);

			$widgets = $this->app->endpoint(['name' => 'widgets']);
			$children = $this->app->children();

			expect($children)->toHaveLength(1);
			expect($children['widgets'])->toBe($widgets);

			$gizmos = $this->app->endpoint(['name' => 'gizmos']);
			$children = $this->app->children();

			expect($children)->toHaveLength(2);
			expect($children['gizmos'])->toBe($gizmos);
		});
	});
});

?>
