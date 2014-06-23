<?php

namespace polymer\extensions\adapter\data\decorator;

class Links extends \lithium\core\Object {

	protected static $_classes = [
		'string' => 'lithium\util\String'
	];

	public function apply($data, array $options = []) {
		$blacklist = ['app'];

		$links = array_diff_key($options, array_fill_keys($blacklist, true));
		$app = $options['app'];

		return array_map(function($config) use ($app) {
			$endpoint = $app->children($config['name']);
			$url = $app->url($endpoint);

			if (!isset($config['params'])) {
				return $url;
			}

			$string = static::$_classes['string'];
			return $string::insert($url, $config['params']);
		}, $links);
	}
}

?>

