<?php

namespace spec\polymer;

use kahlan\plugin\Stub;
use polymer\data\Binding;

describe("binding", function() {
	describe("base", function() {
		it("should use adaptable pattern", function() {
			$mock = Stub::create();

			Binding::config([
				'test' => [
					'adapter' => $mock
				]
			]);

			$binding = Binding::adapter('test');
			expect($binding)->toEqual($mock);

			$result = Binding::apply('test', 'someClass', 'someMethod', ['foo' => 'bar']);
			expect($binding)->toReceive('apply')->with('someClass', 'someMethod', ['foo' => 'bar']);
		});
	});

	describe("li3 adapter", function() {
		it("should call the correct class, model and parameters", function() {
			$binding = new \polymer\data\binding\Li3();
			$model = '\spec\polymer\mock\Li3Model';
			$conditions = [ 'group' => 123 ];

			$binding->apply($model, 'all', compact('conditions'));
			expect($model::$stack)->toHaveLength(1);
			expect($model::$stack[0])->toEqual([
				'method' => 'all',
				'options' => compact('conditions')
			]);
		});
	});
});
