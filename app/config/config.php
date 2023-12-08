<?php

const DB_DATA = ['localhost', 'root', '', 'moodle_test'];

const ROUTES = [
        '' => ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET'],
        '/' => ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET'],
        'home' => ['controller' => 'HomeController', 'action' => 'index', 'method' => 'GET'],
        'tests' => ['controller' => 'TestController', 'action' => 'listPageable', 'method' => 'GET'],
        'test' => ['controller' => 'TestController', 'action' => 'singleTest', 'method' => 'GET'],
        'login' => ['controller' => 'AuthController', 'action' => 'index', 'method' => 'GET'],
        'logout' => ['controller' => 'AuthController', 'action' => 'logout', 'method' => 'GET'],
        'register' => ['controller' => 'AuthController', 'action' => 'register', 'method' => 'GET'],
        'login-user' => ['controller' => 'AuthController', 'action' => 'loginUser', 'method' => 'POST'],
        'register-user' => ['controller' => 'AuthController', 'action' => 'registerUser', 'method' => 'POST'],
    ];