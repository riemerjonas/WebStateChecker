<?php

use Webchecker\Classes\Test;
use Webchecker\Handlers\TestResultPrinter;
use Webchecker\Utilities\ConfigLoader;

include_once __DIR__ . '/../vendor/autoload.php';

$configLoader = new ConfigLoader("config.json");
$websites = $configLoader->get("websites", []);

$testResults = [];
foreach ($websites as $website) {
    $test = new Test($website);
    $test->runTest();
    $testResults[] = $test->getResult();
}

TestResultPrinter::print($testResults);

?>