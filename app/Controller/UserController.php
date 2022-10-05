<?php

namespace Pzn\BelajarPhpMvc\Controller;

use Pzn\BelajarPhpMvc\App\View;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Exception\ValidationException;
use Pzn\BelajarPhpMvc\Model\UserLoginRequest;
use Pzn\BelajarPhpMvc\Model\UserRegisterRequest;
use Pzn\BelajarPhpMvc\Repository\SessionRepository;
use Pzn\BelajarPhpMvc\Service\UserService;
use Pzn\BelajarPhpMvc\Repository\UserRepository;
use Pzn\BelajarPhpMvc\Service\SessionService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register(){
        View::render('User/register', [
            'title' => 'Register new User'
            
        ]);
    }

    public function postRegister(){
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            // redirect to /users/login
            View::redirect('/users/login');
        }catch (ValidationException $exception){
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render('User/login', [
            "title" => "Login user"
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                'title' => 'Login user',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function logout(){
        $this->sessionService->destroy();
        View::redirect("/");
    }
}