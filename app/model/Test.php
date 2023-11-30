<?php

namespace model;

use DateTime;

class Test extends AuditedModel
{
    private string $name;
    private int $min_points;
    private bool $active;

    /**
     * @param string|null $name
     * @param int|null $min_points
     * @param bool $active
     * @param DateTime|null $created_at
     * @param mixed $created_by
     * @param mixed|null $id
     */
    public function __construct(
        ?string $name,
        ?int $min_points,
        ?bool $active,
        ?DateTime $created_at,
        ?int $created_by,
        ?int $id = null)
    {
        parent::__construct($id, $created_at, $created_by);
        $this->name = $name;
        $this->min_points = $min_points;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMin_points(): int
    {
        return $this->min_points;
    }

    public function setMin_points(int $min_points): void
    {
        $this->min_points = $min_points;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}