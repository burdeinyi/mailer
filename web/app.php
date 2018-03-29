<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;

$app = new Application();

require_once __DIR__ . '/../config/prod.php';
require_once __DIR__ . '/../src/controllers.php';

$app->run();