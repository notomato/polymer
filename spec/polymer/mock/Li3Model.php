<?php

namespace spec\polymer\mock;
use lithium\util\Collection;

class Li3Model extends \lithium\data\Model {

	public static function all(array $options = []) {
		return new Collection();
	}
}

?>
