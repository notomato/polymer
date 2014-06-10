<?php

namespace spec\polymer\mock;

class Li3Model extends \lithium\data\Model {

	public static $stack = [];

	public static function all(array $options = []) {
		static::$stack[] = [
			'method' => 'all',
			'options' => $options
		];

		return null;
	}
}

?>
