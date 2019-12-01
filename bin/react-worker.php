<?php

declare(strict_types=1);

use App\Bridges\ReactBridge;
use Illuminate\Foundation\Application;

ini_set('display_errors', 'stderr');

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';

$bridge = new ReactBridge($app, dirname(__DIR__));

$bridge->start();
