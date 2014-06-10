<?php

namespace polymer\data;

class Binding extends \lithium\core\Adaptable {

	protected static $_configurations = [];

	protected static $_adapters = 'adapter.data.binding';

	public static function apply($name, $class, $method, array $params = []) {
		return static::adapter($name)->apply($class, $method, $params);
	}

}

?>
