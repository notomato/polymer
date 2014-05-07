<?php
use filter\Filter;
use kahlan\reporter\coverage\exporter\Coveralls;

Filter::register('kahlan.coveralls_reporting', function($chain) {
	$coverage = $this->reporters()->get('coverage');
	if (!$coverage || !$this->args('coverage-coveralls')) {
		return $chain->next();
	}
	Coveralls::write([
		'coverage' => $coverage,
		'file' => $this->args('coverage-coveralls'),
		'service_name' => 'travis-ci',
		'service_job_id' => getenv('TRAVIS_JOB_ID') ?: null
	]);
	return $chain->next();
});

Filter::apply($this, 'postProcess', 'kahlan.coveralls_reporting');

?>
