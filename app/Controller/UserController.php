<?php

namespace Pzn\BelajarPhpMvc\Controller;

use Pzn\BelajarPhpMvc\App\View;
use Pzn\BelajarPhpMvc\Config\Database;
use Pzn\BelajarPhpMvc\Exception\ValidationException;
use Pzn\BelajarPhpMvc\Model\UserRegisterRequest;
use Pzn\BelajarPhpMvc\Service\UserService;
use Pzn\BelajarPhpMvc\Repository\UserRepository;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }

    public function register(){
        View::render('User/register', [
            'title' => 'Register new User'
            
        ]);
    }

    public function postregister(){
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
}