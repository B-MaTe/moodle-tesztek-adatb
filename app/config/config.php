<?php

const DB_DATA = ['localhost', 'root', '', 'moodle_test'];

const ROUTES = [
        '' => ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET'],
        '/' => ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET'],
        'home' => ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET'],
        'tests' => ['controller' => 'TestController', 'action' => 'index', 'method' => 'GET'],
        'login' => ['controller' => 'AuthController', 'action' => 'index', 'method' => 'GET'],
        'logout' => ['controller' => 'AuthController', 'action' => 'logout', 'method' => 'GET'],
        'register' => ['controller' => 'AuthController', 'action' => 'register', 'method' => 'GET'],
        'loginUser' => ['controller' => 'AuthController', 'action' => 'loginUser', 'method' => 'POST'],
        'registerUser' => ['controller' => 'AuthController', 'action' => 'registerUser', 'method' => 'POST'],
    ];