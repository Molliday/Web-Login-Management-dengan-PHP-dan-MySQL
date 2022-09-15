<?php

namespace Pzn\BelajarPhpMvc\Controller;

use Pzn\BelajarPhpMvc\App\View;

class HomeController
{

    public function index(): void
    {
        $model = [
            "title" => "Belajar PHP MVC",
            "content" => "Selamat Belajar PHP MVC dari Programmer Zaman Now",
        ];
        View::render('Home/index', $model);
    }

    public function hello(): void
    {
        echo "HomeController.hello()";
    }

    public function world(): void
    {
        echo "HomeController.world()";
    }

    public function about(): void
    {
        echo "Author : Figur Ulul Azmi";
    }

    public function login(): void
    {
        $request = [
            "username" => $_POST['username'],
            "password" => $_POST['password'],
        ];

        $user = [

        ];

        $response = [
            "message" => "Login Sukses",
        ];
    }
}
