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
});
