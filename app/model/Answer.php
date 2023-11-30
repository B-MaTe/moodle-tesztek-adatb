<?php

namespace model;

use DateTime;

class Answer extends AuditedModel
{
    private bool $correct;
    private string $text;

    /**
     * @param bool $correct
     * @param string $text
     * @param int $id
     * @param DateTime|null $created_at
     * @param int $created_by
     */
    public function __construct(bool $correct, string $text, int $id = 0, DateTime $created_at = null, int $created_by = 0)
    {
        parent::__construct($id, $created_at, $created_by);
        $this->correct = $correct;
        $this->text = $text;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): void
    {
        $this->correct = $correct;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}