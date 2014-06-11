<?php

namespace spec\polymer;

use kahlan\plugin\Stub;
use polymer\action\Endpoint;
use polymer\data\Binding;
use lithium\action\Request;

describe("endpoint", function() {
	describe("config getter", function() {
		it("should return all options", function() {
			$endpoint = new Endpoint([ 'name' => 'test' ]);

			expect($endpoint->config())->toEqual([
				'name' => 'test',
				'init' => true
			]);
		});

		it("should return a given option", function() {
			$endpoint = new Endpoint([ 'name' => 'test' ]);

			expect($endpoint->config('name'))->toEqual('test');
		});
	});

	describe("url getter", function() {
		it("should return `url` prefixed with `/`", function() {
			$endpoint = new Endpoint([
				'name' => 'test',
				'url' => 'some-alias'
			]);

			expect($endpoint->url())->toEqual('/some-alias');

			$endpoint = new Endpoint([
				'name' => 'test',
				'url' => '/some-alias'
			]);

			expect($endpoint->url())->toEqual('/some-alias');
		});

		it("should use `name` if `url` is not set", function() {
			$endpoint = new Endpoint([ 'name' => 'test', ]);

			expect($endpoint->url())->toEqual('/test');
		});
	});

	describe("response", function() {
		beforeEach(function() {
			Binding::config([
				'test' => [
					'adapter' => Stub::create()
				]
			]);

			$this->request = new Request([
				'env' => [
					'HTTP_ACCEPT' => 'application/json'
				]
			]);
		});

		describe("binding", function() {
			it("should throw if responding with no binding", function() {
				$endpoint = new Endpoint([ 'name' => 'test' ]);

				$fn = function() use ($endpoint) {
					$endpoint->respond($this->request);
				};

				$message = "Endpoint `test` cannot respond without a binding";
				expect($fn)->toThrow(new \lithium\action\DispatchException($message));
			});

			it("should invoke a binding with a named adapter", function() {
				$model = 'spec\polymer\mock\Li3Model';
				$conditions = [
					'foo' => 'bar'
				];

				$endpoint = new Endpoint([
					'name' => 'test',
					'binding' => [
						'adapter' => 'test',
						'class'   => $model,
						'method'  => 'all',
						'params'  => compact('conditions')
					]
				]);

				expect(Binding::adapter('test'))->toReceive('apply')->with($model, 'all', compact('conditions'));
				$endpoint->respond($this->request);
			});

			it("should merge binding parameters when responding", function() {
				$app = Stub::create();

				$model = 'spec\polymer\mock\Li3Model';
				$parent = new Endpoint([
					'app' => $app,
					'name' => 'test',
					'abstract' => true,
					'binding' => [
						'adapter' => 'test',
						'class'   => $model,
					]
				]);

				$index = new Endpoint([
					'app' => $app,
					'name' => 'test.index',
					'binding' => [
						'method' => 'all'
					]
				]);

				$conditions = [
					'id' => 100
				];

				$view = new Endpoint([
					'app' => $app,
					'name' => 'test.view',
					'binding' => [
						'method' => 'first',
						'params' => compact('conditions')
					]
				]);

				$binding = Binding::adapter('test');
				expect($binding)->toReceive('apply')->with($model, 'all');
				expect($binding)->toReceive('apply')->with($model, 'first', compact('conditions'));

				Stub::on($app)->method('traverse')->andReturn([$index, $parent]);
				$index->respond($this->request);

				Stub::on($app)->method('traverse')->andReturn([$view, $parent]);
				$view->respond($this->request);
			});
		});

		it("should substitute url parameters into binding", function() {
			$model = 'spec\polymer\mock\Li3Model';

			$endpoint = new Endpoint([
				'name' => 'test.view',
				'url'  => '{:id}',
				'binding' => [
					'adapter' => 'test',
					'class'   => $model,
					'method'  => 'first',
					'params'  => [
						'_id'     => '{:id}',
						'deleted' => false
					]
				]
			]);

			$this->request->params = [
				'id' => 100
			];

			$binding = Binding::adapter('test');

			expect($binding)->toReceive('apply')->with($model, 'first', [ '_id' => '100', 'deleted' => false ]);
			$endpoint->respond($this->request);
		});

		describe("media type", function() {
			beforeEach(function() {
				$binding = Binding::adapter('test');
				Stub::on($binding)->method('apply')->andReturn(['foo' => 'bar']);

				$this->endpoint = new Endpoint([
					'name' => 'test.index',
					'binding' => [
						'adapter' => 'test',
						'class'   => 'spec\polymer\mock\Li3Model',
						'method'  => 'first'
					]
				]);
			});

			it("should be an instance of response", function() {
				$response = $this->endpoint->respond($this->request);
				expect($response)->toBeAnInstanceOf('lithium\action\Response');
			});

			it("should negotiate encoding by Accept header", function() {
				$response = $this->endpoint->respond($this->request);
				expect($response->headers('Content-Type'))->toEqual('application/json; charset=UTF-8');
				expect($response->body())->toEqual('{"foo":"bar"}');
			});
		});
	});
});

?>
