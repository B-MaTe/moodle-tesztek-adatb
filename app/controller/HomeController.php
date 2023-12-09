<?php

namespace controller;

class HomeController extends Controller
{

    public function index(): void
    {
        $data = ['curr' => 'Home'];

        require_once 'app/view/home.php';
    }
}