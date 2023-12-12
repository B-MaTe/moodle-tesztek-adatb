<?php

namespace controller;

use Database;
use enum\SqlValueType;
use model\Answer;
use model\AuditedModel;

class AnswerController extends DataController
{

    public function index(): void
    {
        // TODO: Implement index() method.
    }

    public static function save(Answer|AuditedModel $model): int
    {
        return Database::insert('insert into answers (text, correct, created_at, created_by, question_id) values (?, ?, ?, ?, ?)',
            [SqlValueType::STRING->value, SqlValueType::INT->value, SqlValueType::STRING->value, SqlValueType::INT->value, SqlValueType::INT->value],
        [$model->getText(), $model->isCorrect(), $model->sqlCreated_at(), $model->getCreated_by(), $model->getQuestion()->getId()]);
    }

    public static function delete(int $id): bool
    {
        return Database::query('delete from answers where id = ?', [SqlValueType::INT->value], [$id]) != false;
    }
}