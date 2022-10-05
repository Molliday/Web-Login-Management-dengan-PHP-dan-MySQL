<?php

namespace Pzn\BelajarPhpMvc\Controller;

use PHPUnit\Framework\TestCase;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Domain\Session;
use Pzn\BelajarPhpMvc\Domain\User;
use Pzn\BelajarPhpMvc\Repository\SessionRepository;
use Pzn\BelajarPhpMvc\Repository\UserRepository;
use Pzn\BelajarPhpMvc\Service\SessionService;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();
        $this->expectOutputRegex("[Login Management]");
    }
    
    public function testUserLogin()
    {
        $user = new User();
        $user->id = "figur";
        $user->name = "Figur";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();

        $this->expectOutputRegex("[Hello Figur]");
    }

}