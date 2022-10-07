<?php

namespace Pzn\BelajarPhpMvc\Service;

require_once __DIR__ . '/../Helper/helper.php'; 

use PHPUnit\Framework\TestCase;
use Pzn\BelajarPhpMvc\Config\database;
use Pzn\BelajarPhpMvc\Domain\Session;
use Pzn\BelajarPhpMvc\Domain\User;
use Pzn\BelajarPhpMvc\Repository\SessionRepository;
use Pzn\BelajarPhpMvc\Repository\UserRepository;


class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "figur";
        $user->name = "Figur";
        $user->password = "rahasia";

        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("figur");

        $this->expectOutputRegex("[X-PZN-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals("figur", $result->userId);
    }
    
    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "figur";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-PZN-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "figur";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }


}