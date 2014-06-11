<?php

namespace polymer\extensions\adapter\data\binding;

class Li3 extends \lithium\core\Object {
	public function apply($model, $method, array $params = []) {
		return $model::invokeMethod($method, $params)->to('array');
	}
}

?>
