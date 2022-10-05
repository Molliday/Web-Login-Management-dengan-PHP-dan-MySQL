<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Pzn\BelajarPhpMvc\App\Router;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Controller\HomeController;
use Pzn\BelajarPhpMvc\Controller\UserController;
use Pzn\BelajarPhpMvc\Middleware\MustLoginMiddleware;
use Pzn\BelajarPhpMvc\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);

Router::run();
