<?php

namespace Pzn\BelajarPhpMvc\Service;

use PHPUnit\Framework\TestCase;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Domain\User;
use Pzn\BelajarPhpMvc\Exception\ValidationException;
use Pzn\BelajarPhpMvc\Model\UserRegisterRequest;
use Pzn\BelajarPhpMvc\Service\UserService;
use Pzn\BelajarPhpMvc\Repository\UserRepository;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        $connection = Database::getConnetion();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "figur";
        $request->name = "Figur";
        $request->password = "rahasia";

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }
    
    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "figur";
        $user->name = "Figur";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "figur";
        $request->name = "Figur";
        $request->password = "rahasia";

        $this->userService->register($request);
    }

}
