<?php

namespace spec\polymer;

use kahlan\plugin\Stub;
use polymer\core\App;
use polymer\data\Decorator;
use polymer\extensions\adapter\data\decorator\Links;

describe("decorator", function() {
	describe("base", function() {
		it("should use adaptable pattern", function() {
			$mock = Stub::create();

			Decorator::config([
				'test' => [
					'adapter' => $mock
				]
			]);

			$decorator = Decorator::adapter('test');
			expect($decorator)->toEqual($mock);

			$result = Decorator::apply('test', ['foo' => 'bar']);
			expect($decorator)->toReceive('apply')->with(['foo' => 'bar']);
		});
	});

	describe("links", function() {
		beforeEach(function() {
			$this->app = new App();
			$this->app->endpoint([
				'name' => 'test',
				'abstract' => true
			]);
			$this->app->endpoint([
				'name' => 'test.index',
				'url' => ''
			]);
			$this->app->endpoint([
				'name' => 'test.view',
				'url' => '{:id}'
			]);

			$this->links = new Links();
		});

		it("should locate the Endpoint specified and generate URL", function() {
			$result = $this->links->apply([], [
				'app' => $this->app,
				'index' => [
					'name' => 'test.index'
				]
			]);

			expect($result)->toEqual([
				'index' => '/test'
			]);
		});

		it("should locate the Endpoint specified and generate URL", function() {
			$result = $this->links->apply([], [
				'app' => $this->app,
				'index' => [
					'name' => 'test.index'
				],
				'view' => [
					'name' => 'test.view',
					'params' => ['id' => 10]
				]
			]);

			expect($result)->toEqual([
				'index' => '/test',
				'view' => '/test/10'
			]);
		});
	});
});

?>
