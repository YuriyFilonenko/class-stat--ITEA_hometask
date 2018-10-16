#!/usr/bin/env php
<?php

use App\ClassStatisticCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application('Class-stat v1.0');

$application->add(new ClassStatisticCommand());

$exitCode = $application->run();
exit($exitCode);
