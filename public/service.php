<?php

use Webchecker\Classes\Test;
use Webchecker\Handlers\NotifyHandler;
use Webchecker\Handlers\OutputHandler;
use Webchecker\Utilities\ConfigLoader;

include_once __DIR__ . '/../vendor/autoload.php';

$configLoader = new ConfigLoader("config.json");
$websites = $configLoader->get("websites", []);

$testResults = [];
foreach ($websites as $website) {
    $test = new Test($website['url']);
    $test->runTest();
    $testResults[] = $test->getResult();
    NotifyHandler::sendNotification($website['url'], $test->getResult());
}

OutputHandler::print($testResults);
