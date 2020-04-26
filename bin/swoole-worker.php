<?php

declare(strict_types=1);

use App\Bridges\SwooleBridge;
use Illuminate\Foundation\Application;

ini_set('display_errors', 'stderr');

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';

$bridge = new SwooleBridge($app, dirname(__DIR__));

$bridge->start();
