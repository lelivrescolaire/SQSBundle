<?php
use mageekguy\atoum;
use mageekguy\atoum\reports;

$script->addDefaultReport();

$coverallsReport = new reports\asynchronous\coveralls('.', 'COVERALLS_TOKEN');

$defaultFinder = $coverallsReport->getBranchFinder();
$coverallsReport
	->setBranchFinder(function() use ($defaultFinder) {
		if (($branch = getenv('TRAVIS_BRANCH')) === false)
		{
			$branch = $defaultFinder();
		}

		return $branch;
	})
	->setServiceName(getenv('TRAVIS') ? 'travis-ci' : null)
	->setServiceJobId(getenv('TRAVIS_JOB_ID') ?: null)
	->addDefaultWriter()
;

$runner->addTestsFromDirectory(__DIR__.'/Tests/');
$runner->addReport($coverallsReport);