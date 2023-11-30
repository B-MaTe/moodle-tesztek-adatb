<?php

namespace controller;
use Database;
use DateTime;
use enum\Role;
use enum\SqlValueType;
use Exception;
use model\User;

require_once 'app/config/db.php';
require_once 'app/controller/Controller.php';
require_once 'app/model/User.php';
require_once 'app/enum/Role.php';
require_once 'app/enum/SqlValueType.php';

class UserController extends Controller
{

    private static string $publicValues = 'id, email, name, role, created_at, created_by';

    public static function getAllUsers(): array {
        return self::selectModels('select * from users', false);
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
        return self::selectModels('select ' . self::$publicValues . ' from users where id = ? LIMIT 1', true, [SqlValueType::INT->value], [$id]);
    }

    public static function getIdByEmailAndPassword(string $email, string $password): int {
        $existingUser = self::selectModels('select id, email, password from users where email = ? LIMIT 1', true, [SqlValueType::STRING->value], [$email]);

        return $existingUser != null && password_verify($password, $existingUser->getPassword()) ? $existingUser->getId() : 0;
    }

    public static function userExistsByField(string $field, mixed $value, string $valueType, string $operator): bool {
        return self::selectModels(
            'select * from users where ' . $field . ' ' . $operator . ' ? LIMIT 1', true, [$valueType],  [$value]
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
            $newUser->getCreated_at()->format('Y-m-d H:i:s'),
            $newUser->getEmail(),
            $newUser->getPassword(),
            $newUser->getName(),
            $newUser->getRole()->value
        ]);
    }

    private static function selectModels(string $query, bool $single, array $types = [], array $params = []): array|User|null {
        $users = [];
        $result = Database::query($query, $types, $params);
        $user = $result->fetch_assoc();

        do {
            if ($user != null) {
                if (isset($user['created_at'])) {
                    try {
                        $user['created_at'] = new DateTime($user['created_at']);
                    } catch (Exception $ignored) {
                        $user['created_at'] = null;
                    }
                }

                if (isset($user['role'])) {
                    $user['role'] = Role::from($user['role']);
                }

                $users[] = new User(...$user);
            }
        } while ($user = $result->fetch_assoc() && !$single);

        return $single ? $users[0] ?? null : $users;
    }

    public function index(): void
    {
        // TODO: Implement index() method.
    }

}