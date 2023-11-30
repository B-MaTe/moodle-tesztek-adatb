<?php

namespace controller;
use enum\SqlValueType;

require_once 'app/controller/Controller.php';
require_once 'app/controller/FormController.php';
require_once 'app/enum/SqlValueType.php';

class AuthController extends Controller
{

    public function index(): void
    {
        require_once 'app/view/login.php';
    }

    public function register(): void
    {
        require_once 'app/view/register.php';
    }
    public function loginUser($data): void
    {
        if (!FormController::validateLoginForm($data)) {
            header('Location: login');
            return;
        }

        $id = UserController::getIdByEmailAndPassword($data['email'], $data['password']);

        if ($id == 0) {
            FormController::addError('email', 'Helytelen bejelentkezési adatok!');
            header('Location: login');
            return;
        }

        UserController::login($id);
        header('Location: home');

    }

    public function registerUser($data): void
    {
        if (!FormController::validateSignupForm($data)) {
            header('Location: register');
            return;
        }

        if (UserController::userExistsByField('email', $data['email'], SqlValueType::STRING->value, '=')) {
            FormController::addError('email', 'Foglalt E-mail!');
            header('Location: register');
            return;
        }

        $id = UserController::registerAndReturnId($data);
        UserController::login($id);
        header('Location: home');
    }

    public function logout(): void {
        UserController::logout();

        header('Location: login');
    }
}