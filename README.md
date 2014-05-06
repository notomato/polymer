# polymer

PHP Hypermedia Application Framework

## design goals

### Composer Friendly

Leverage Composer from the ground-up to ensure lowest possible barrier to entry.

### Mask li3 in route definitions

Acts as a facade over li3's router, dispatcher and request/response classes to give a workflow that is specific to building Hypermedia Applications, whilst retaining flexible configuration.

### Business Logic Binding

Bind to Models using some kind of Adaptable class. Responsible for determining classes, methods and parameters from request. Ship with li3 model adapter, allow custom adapters for other ORMs.

### Media Type agnostic

Leverage li3's rich media type handling capabilities. Delegate response rendering to adapters. Ship with adapters such as HAL and Siren, allow custom adapters to be written and encouraged.

### Transformers - Decorators & Formatters

Transform data going into/coming out of bound business logic. May be implemented and applied ad-hoc on an endpoint-specific basis, or as part of a Media Type. Ship with transformers that support Hypermedia factors such as `link` and `embed`.

## usage

### Define and run Application

The App class is the root of an API build with Polymer. The `namespace` option is used to determine the root URL. A namespace is not required, but is recommended:

```
<?php

$app = new polymer\App(['namespace' => 'v1']);
echo $app->run();

?>
```

This root endpoint is published at `/v1`.

### Add an Endpoint

The `endpoint` method is used to declare API endpoints. The `name` parameter specifies a unique identifier for the endpoint in dot-notation. This notation is used to denote nested relationships. All endpoints inherit configuration from the chain of parent endpoints back to the root. This inheritance may be customised on a per-endpoint basis.

Those famililar with angular-router will be familiar with the similar style of creating 'states', along with the 'abstract' option.

```
<?php

/**
 * Create a common 'abstract' endpoint that is bound to the 'widgets' model.
 */
$app->endpoint('widgets', [
	'abstract' => true,
	'binding' => [
		'class' => 'app\models\Widgets',
	]
]);

/**
 * Create an 'index' endpoint that inherits from the abstract state, published
 * at '/widgets'.
 *
 * The 'class' binding option is merged with the 'method' option.
 */
$app->endpoint('widgets.index', [
	'binding' => [
		'method' => 'all',
	]
]);

/**
 * The 'view' endpoint also inherits from the abstract state, with merged
 * 'method' and 'params' options.
 *
 * The 'params' configuration can use variable substitution to apply parameters
 * based on the URL
 */
$app->endpoint('widgets.view', [
	'url' => '/:widgetId',
	'binding' => [
		'method' => 'first',
		'params' => [ '_id' => 'url:widgetId' ]
	]
]);

/**
 * An example of how to fetch collections of related data.
 *
 * It is possible to bind to a different class and method than the parent
 * Endpoint, but still inherit variables from it.
 */
$app->endpoint('widgets.view.gizmos', [
	'url' => '/gizmos',
	'binding' => [
		'class' => 'app\models\gizmos',
		'method' => 'all',
		'params' => [ 'widget' => 'url:widgetId' ]
	]
]);

?>
```

### Specify Media Types

@todo

### Custom Media Types

@todo

### Apply Decorators

@todo

### Custom Decorators

@todo
