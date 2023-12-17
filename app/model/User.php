<?php

namespace model;

use DateTime;
use enum\Role;

class User extends AuditedModel
{
    private ?string $email;
    private ?string $password;
    private ?string $name;
    private ?Role $role;

    /**
     * @param int|null $id
     * @param DateTime|null $created_at
     * @param int|null $created_by
     * @param string|null $email
     * @param string|null $password
     * @param string|null $name
     * @param Role|null $role
     */
    public function __construct(
        ?int $id = null,
        ?DateTime $created_at = null,
        ?int $created_by = null,
        ?string $email = null,
        ?string $password = null,
        ?string $name = null,
        ?Role $role = null)
    {
        parent::__construct($id, $created_at, $created_by);
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->role = $role;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    public function sessionView(): array {
        return parent::sessionView() +
        [
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->role->value
        ];
    }
}