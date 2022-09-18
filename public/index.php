<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Pzn\BelajarPhpMvc\App\Router;
use Pzn\BelajarPhpMvc\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index', []);

Router::run();
