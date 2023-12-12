<?php

namespace model;

use DateTime;

class Answer extends AuditedModel
{
    private bool $correct;
    private string $text;
    private Question|null $question;
    private int $question_id;

    /**
     * @param bool $correct
     * @param string $text
     * @param Question|null $question
     * @param int $question_id
     * @param int $id
     * @param DateTime|null $created_at
     * @param int $created_by
     */
    public function __construct(bool $correct = true, string $text = '', Question|null $question = null, int $question_id = 0, int $id = 0, DateTime $created_at = null, int $created_by = 0)
    {
        parent::__construct($id, $created_at, $created_by);
        $this->correct = $correct;
        $this->text = $text;
        $this->question = $question;
        $this->question_id = $question_id;
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

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getQuestionId(): int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $question_id): void
    {
        $this->question_id = $question_id;
    }
}