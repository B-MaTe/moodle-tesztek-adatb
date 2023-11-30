<?php

namespace controller;
require_once 'app/controller/Controller.php';

class HomeController extends Controller
{

    public function index(): void
    {
        $data = ['curr' => 'Home'];

        require_once 'app/view/home.php';
    }
}