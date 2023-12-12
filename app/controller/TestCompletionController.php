<?php

namespace controller;

use Database;
use enum\SqlValueType;
use model\AuditedModel;
use model\TestCompletion;

class TestCompletionController extends DataController
{

    public function index(): void
    {
        // TODO: Implement index() method.
    }

    public static function save(TestCompletion|AuditedModel $model): int
    {
        return Database::insert('insert into test_completions (test_id, earned_points, successful_completion, created_at, created_by) values (?, ?, ?, ?, ?)',
            [SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::STRING->value, SqlValueType::INT->value],
            [$model->getTestId(), $model->getEarnedPoints(), $model->isSuccessfulCompletion(), $model->sqlCreated_at(), $model->getCreated_by()]);
    }

    public static function delete(int $id): bool
    {
        return false;
    }
}