<?php

namespace controller;
use Database;
use DateTime;
use enum\Role;
use enum\SqlValueType;
use model\User;

require_once 'app/config/db.php';
require_once 'app/model/User.php';
require_once 'app/enum/Role.php';
require_once 'app/enum/SqlValueType.php';

class UserController extends DataController
{

    private static string $publicValues = 'id, email, name, role, created_at, created_by';

    public static function getAllUsers(): array {
        return self::selectModels(User::class, 'select * from users', false);
    }

    public static function adminOrTeacher(): bool
    {
        return self::userLoggedIn() && (self::getLoggedInUser()->getRole() == Role::TEACHER || self::getLoggedInUser()->getRole() == Role::ADMIN);
    }

    public static function login(int $id): void {
        $_SESSION['user'] = self::getSessionUserById($id)->sessionView();
    }

    public static function logout(): void {
        unset($_SESSION['user']);
    }

    public static function userLoggedIn(): bool {
        return isset($_SESSION['user']) && $_SESSION['user']['id'] > 0;
    }

    public static function getLoggedInUser(): User|null {
        return self::userLoggedIn() ? self::getSessionUserById($_SESSION['user']['id']) : null;
    }

    private static function getSessionUserById(int $id): User|null {
        return self::selectModels(User::class, 'select ' . self::$publicValues . ' from users where id = ? LIMIT 1', true, [SqlValueType::INT->value], [$id]);
    }

    public static function getIdByEmailAndPassword(string $email, string $password): int {
        $existingUser = self::selectModels(User::class, 'select id, email, password from users where email = ? LIMIT 1', true, [SqlValueType::STRING->value], [$email]);

        return $existingUser != null && password_verify($password, $existingUser->getPassword()) ? $existingUser->getId() : 0;
    }

    public static function userExistsByField(string $field, mixed $value, string $valueType, string $operator): bool {
        return self::selectModels(
            User::class, 'select * from users where ' . $field . ' ' . $operator . ' ? LIMIT 1', true, [$valueType],  [$value]
            ) != null;
    }

    public static function registerAndReturnId(array $data): int {
        $newUser = new User(
            null,
            new DateTime(),
            null,
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['name'],
            Role::USER
        );

        $sql = "INSERT INTO users (created_at, created_by, email, password, name, role) 
        VALUES (?, NULL, ?, ?, ?, ?)";

        return Database::insert($sql, [
            SqlValueType::STRING->value,
            SqlValueType::STRING->value,
            SqlValueType::STRING->value,
            SqlValueType::STRING->value,
            SqlValueType::STRING->value
        ], [
            $newUser->sqlCreated_at(),
            $newUser->getEmail(),
            $newUser->getPassword(),
            $newUser->getName(),
            $newUser->getRole()->value
        ]);
    }

    public function index(): void
    {
        // TODO: Implement index() method.
    }

    public static function save($model): int
    {
        return 0;
    }
}