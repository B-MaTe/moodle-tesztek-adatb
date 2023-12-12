<?php

namespace model;

use DateTime;

class TestCompletion extends AuditedModel
{
    private int $test_id;
    private int $earned_points;
    private bool $successful_completion;

    /**
     * @param int $test_id
     * @param int $created_by
     * @param int $earned_points
     * @param bool $successful_completion
     * @param int $id
     * @param DateTime|null $created_at
     */
    public function __construct(
        int $test_id = 0,
        int $created_by = 0,
        int $earned_points = 0,
        bool $successful_completion = false,
        int $id = 0,
        DateTime $created_at = null
    ) {
        parent::__construct($id, $created_at, $created_by);
        $this->test_id = $test_id;
        $this->earned_points = $earned_points;
        $this->successful_completion = $successful_completion;
    }

    public function getTestId(): int
    {
        return $this->test_id;
    }

    public function setTestId(int $test_id): void
    {
        $this->test_id = $test_id;
    }

    public function getEarnedPoints(): int
    {
        return $this->earned_points;
    }

    public function setEarnedPoints(int $earned_points): void
    {
        $this->earned_points = $earned_points;
    }

    public function isSuccessfulCompletion(): bool
    {
        return $this->successful_completion;
    }

    public function setSuccessfulCompletion(bool $successful_completion): void
    {
        $this->successful_completion = $successful_completion;
    }
}
