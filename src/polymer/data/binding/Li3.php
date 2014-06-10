<?php

namespace polymer\data\binding;

class Li3 extends \lithium\core\Object {
	public function apply($class, $method, $params) {
		return $class::$method($params);
	}
}

?>
