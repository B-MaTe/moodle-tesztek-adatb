<?php

namespace model;

use DateTime;

class Test extends AuditedModel
{
    private string $name;
    private int $min_points;
    private bool $active;
    private array $questions;

    /**
     * @param string|null $name
     * @param int|null $min_points
     * @param bool $active
     * @param array|null $questions
     * @param DateTime|null $created_at
     * @param mixed $created_by
     * @param mixed|null $id
     */
    public function __construct(
        ?string $name = '',
        ?int $min_points = 0,
        ?bool $active = true,
        ?array $questions = [],
        ?DateTime $created_at = new DateTime(),
        ?int $created_by = 0,
        ?int $id = 0)
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

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): void
    {
        $this->questions = $questions;
    }
}