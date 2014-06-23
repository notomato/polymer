<?php

namespace polymer\data;

class Decorator extends \lithium\core\Adaptable {

	protected static $_configurations = [];

	protected static $_adapters = 'adapter.data.decorator';

	public static function apply($name, $data, array $options = []) {
		return static::adapter($name)->apply($data, $options);
	}
}

?>

