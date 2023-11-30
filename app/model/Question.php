<?php

namespace model;

use DateTime;

class Question extends AuditedModel
{
    private string $text;
    private int $point;
    private int $goodAnswerId;
    private array $wrongAnswerIds;

    /**
     * @param string $text
     * @param int $point
     * @param int $goodAnswerId
     * @param array $wrongAnswerIds
     * @param int $id
     * @param DateTime|null $created_at
     * @param int $created_by
     */
    public function __construct(string $text, int $point, int $goodAnswerId, array $wrongAnswerIds, int $id = 0, DateTime $created_at = null, int $created_by = 0)
    {
        parent::__construct($id, $created_at, $created_by);
        $this->text = $text;
        $this->point = $point;
        $this->goodAnswerId = $goodAnswerId;
        $this->wrongAnswerIds = $wrongAnswerIds;
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

    public function getGoodAnswerId(): int
    {
        return $this->goodAnswerId;
    }

    public function setGoodAnswerId(int $goodAnswerId): void
    {
        $this->goodAnswerId = $goodAnswerId;
    }

    public function getWrongAnswerIds(): array
    {
        return $this->wrongAnswerIds;
    }

    public function setWrongAnswerIds(array $wrongAnswerIds): void
    {
        $this->wrongAnswerIds = $wrongAnswerIds;
    }
}