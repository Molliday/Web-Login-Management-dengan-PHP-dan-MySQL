<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Pzn\BelajarPhpMvc\App\Router;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Controller\HomeController;
use Pzn\BelajarPhpMvc\Controller\UserController;

Database::getConnection('prod');

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);
Router::add('GET', '/users/login', UserController::class, 'login', []);
Router::add('POST', '/users/login', UserController::class, 'postLogin', []);

Router::run();
