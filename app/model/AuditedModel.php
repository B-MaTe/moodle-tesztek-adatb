<?php

namespace model;

use DateTime;

abstract class AuditedModel
{
    private ?int $id;
    private ?DateTime $created_at;
    private ?int $created_by;

    /**
     * @param int|null $id
     * @param DateTime|null $created_at
     * @param int|null $created_by
     */
    public function __construct(?int $id = 0, ?DateTime $created_at = new DateTime(), ?int $created_by = 0)
    {
        $this->id = $id;
        $this->created_at = $created_at;
        $this->created_by = $created_by;
    }

    public function getCreated_at(): ?DateTime
    {
        return $this->created_at;
    }

    public function sqlCreated_at(): string
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    public function setCreated_at(?DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getCreated_by(): int
    {
        return $this->created_by;
    }

    public function setCreated_by(int $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function sessionView(): array {
        return [
            'id' => $this->id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at
        ];
    }
}