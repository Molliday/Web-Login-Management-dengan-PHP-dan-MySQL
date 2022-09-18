<?php

namespace Pzn\BelajarPhpMvc\Controller;

use Pzn\BelajarPhpMvc\App\View;

class HomeController
{

    public function index()
    {
        View::render('Home/index', [
            "title" => "PHP Login Management",
        ]);
    }
}
