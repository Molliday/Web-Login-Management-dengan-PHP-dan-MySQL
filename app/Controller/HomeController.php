<?php

namespace Pzn\BelajarPhpMvc\Controller;

use Pzn\BelajarPhpMvc\App\View;
use Pzn\BelajarPhpMvc\Config\database;
use Pzn\BelajarPhpMvc\Repository\SessionRepository;
use Pzn\BelajarPhpMvc\Repository\UserRepository;
use Pzn\BelajarPhpMvc\Service\SessionService;

class HomeController
{

    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function index()
    {

        $user = $this->sessionService->current();
        if ($user == null) {
            View::render('Home/index', [
                "title" => "PHP Login Management",
            ]);
        } else {
            View::render('Home/dashboard', [
                "title" => "Dashboard",
                "user" => [
                    "name" => $user->name
                ]
            ]);
        }

        
    }
}
