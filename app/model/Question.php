<?php

namespace model;

use DateTime;

class Question extends AuditedModel
{
    private string $text;
    private int $point;
    private Answer|null $goodAnswer;
    private array $wrongAnswers;
    private array $tests;

    /**
     * @param string $text
     * @param int $point
     * @param int $id
     * @param Answer|null $goodAnswer
     * @param array|null $wrongAnswers
     * @param array|null $tests
     * @param DateTime|null $created_at
     * @param int $created_by
     */
    public function __construct(string $text = '', int $point = 0, int $id = 0, Answer|null $goodAnswer = null, ?array $wrongAnswers = [], ?array $tests = [], DateTime $created_at = null, int $created_by = 0)
    {
        parent::__construct($id, $created_at, $created_by);
        $this->text = $text;
        $this->point = $point;
        $this->goodAnswer = $goodAnswer;
        $this->wrongAnswers = $wrongAnswers;
        $this->tests = $tests;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getPoint(): int
    {
        return $this->point;
    }

    public function setPoint(int $point): void
    {
        $this->point = $point;
    }

    public function getGoodAnswer(): Answer|null
    {
        return $this->goodAnswer;
    }

    public function setGoodAnswer(Answer $goodAnswer): void
    {
        $this->goodAnswer = $goodAnswer;
    }

    public function getWrongAnswers(): array
    {
        return $this->wrongAnswers;
    }

    public function setWrongAnswers(array $wrongAnswers): void
    {
        $this->wrongAnswers = $wrongAnswers;
    }

    public function appendWrongAnswer(Answer $wrongAnswer): void
    {
        $this->wrongAnswers[] = $wrongAnswer;
    }

    public function getTests(): array
    {
        return $this->tests;
    }

    public function setTests(array $tests): void
    {
        $this->tests = $tests;
    }

    public function appendTest(Test $test): void
    {
        $this->tests[] = $test;
    }


    public function getRandomizedAnswers(): array
    {
        $arr = $this->getWrongAnswers();
        $arr[] = $this->getGoodAnswer();
        shuffle($arr);
        return $arr;
    }

    public function getAnswers(): array
    {
        $arr = $this->getWrongAnswers();
        $arr[] = $this->getGoodAnswer();
        return $arr;
    }
}