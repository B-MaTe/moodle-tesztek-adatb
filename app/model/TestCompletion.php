<?php

namespace model;

use DateTime;

class TestCompletion extends AuditedModel
{
    private int $test_id;
    private int $earned_points;
    private bool $successful_completion;
    private array $questions = [];
    private DateTime|null $started_at;

    /**
     * @param int $test_id
     * @param int $created_by
     * @param int $earned_points
     * @param bool $successful_completion
     * @param int $id
     * @param DateTime|null $created_at
     * @param DateTime|null $started_at
     */
    public function __construct(
        int $test_id = 0,
        int $created_by = 0,
        int $earned_points = 0,
        bool $successful_completion = false,
        int $id = 0,
        DateTime $created_at = null,
        DateTime|null $started_at = null
    ) {
        parent::__construct($id, $created_at, $created_by);
        $this->test_id = $test_id;
        $this->earned_points = $earned_points;
        $this->successful_completion = $successful_completion;
        $this->started_at = $started_at;
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

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function setQuestions(array $questions): void
    {
        $this->questions = $questions;
    }

    public function appendQuestion(Question $question): void
    {
        $this->questions[] = $question;
    }

    public function getStartedAt(): DateTime
    {
        return $this->started_at;
    }

    public function setStartedAt(DateTime $started_at): void
    {
        $this->started_at = $started_at;
    }

    public function sqlStarted_at(): string
    {
        return $this?->started_at->format('Y-m-d H:i:s');
    }
}
