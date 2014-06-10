<?php

namespace spec\polymer;

use kahlan\plugin\Stub;
use polymer\action\Endpoint;
use polymer\data\Binding;

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
		it("should throw if responding with no binding", function() {
			$endpoint = new Endpoint([ 'name' => 'test' ]);

			$fn = function() use ($endpoint) {
				$endpoint->respond();
			};

			$message = "Endpoint `test` cannot respond without a binding";
			expect($fn)->toThrow(new \lithium\action\DispatchException($message));
		});

		it("should invoke a binding with a named adapter", function() {
			Binding::config([
				'test' => [
					'adapter' => Stub::create()
				]
			]);
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
			$endpoint->respond();
		});
	});
});

?>
