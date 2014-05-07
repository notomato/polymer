<?php

namespace spec\polymer;

use polymer\action\Endpoint;

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
		it("should respond", function() {
			$endpoint = new Endpoint([ 'name' => 'test', ]);
			$response = $endpoint->respond();

			expect($response)->toEqual([
				'code' => 200,
				'body' => "Hello from test!"
			]);
		});
	});
});

?>
