<?php

namespace Pzn\BelajarPhpMvc\Middleware;

use Pzn\BelajarPhpMvc\App\View;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Repository\SessionRepository;
use Pzn\BelajarPhpMvc\Repository\UserRepository;
use Pzn\BelajarPhpMvc\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user != null) {
            View::redirect('/');
        }
    }
}