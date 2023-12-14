<?php

namespace controller;
use enum\NotificationType;
use enum\SqlValueType;

require_once 'app/controller/FormController.php';
require_once 'app/enum/SqlValueType.php';

class AuthController extends Controller
{

    public function index(): void
    {
        require_once 'app/view/login.php';
    }

    public static function checkAdminOrTeacherPrivilege(): void {
        if (!UserController::adminOrTeacher()) {
            NotificationController::setNotification(NotificationType::ERROR, 'Ehhez nincs elég jogosultsága!');
            header('Location: home');
            exit;
        }
    }

    public static function returnHomeIfLoggedOut(): void {
        if (!UserController::userLoggedIn()) {
            header('Location: home');
            exit;
        }}

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
        NotificationController::setNotification(NotificationType::SUCCESS, "Sikeres bejelentkezés!");
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
        NotificationController::setNotification(NotificationType::SUCCESS, "Sikeres regisztráció!");
        header('Location: home');
    }

    public function logout(): void {
        UserController::logout();
        NotificationController::setNotification(NotificationType::SUCCESS, "Sikeres kijelentkezés!");

        header('Location: login');
    }
}