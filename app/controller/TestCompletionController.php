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

    public static function getCompletionsForTestsByUser($testIds, $userId): array
    {
        return self::selectModels(TestCompletion::class, 'SELECT * FROM test_completions WHERE created_by = ? AND test_id IN (' . implode(',', $testIds) . ')', false, [SqlValueType::INT->value], [$userId]);
    }

    public static function getTestFillSummary(int $testId): array
    {
        return self::selectModels(null,
            "SELECT
                    users.email as user_email,
                    users.name as user_name,
                    owner_user.email as test_created_by,
                    tests.min_points as test_min_points,
                    test_completions.started_at as test_started_at,
                    tests.min_points as test_min_points,
                    test_completions.earned_points as test_earned_points,
                    test_completions.successful_completion as test_successful_completion,
                    CONCAT(
                        GREATEST(TIMESTAMPDIFF(HOUR, test_completions.created_at, test_completions.started_at), 0), ':', 
                        LPAD(GREATEST(TIMESTAMPDIFF(MINUTE, test_completions.created_at, test_completions.started_at), 1) % 60, 2, '0')
                    ) AS time_difference
                    FROM test_completions
                    LEFT JOIN users ON users.id = test_completions.created_by
                    LEFT JOIN tests ON tests.id = test_completions.test_id
                    LEFT JOIN users as owner_user ON owner_user.id = tests.created_by
                    WHERE test_completions.test_id = ? ORDER BY users.email;"
            , false, [SqlValueType::INT->value], [$testId]);
    }

    public static function getTestResultsSummary()
    {
        return self::selectModels(null,
            "SELECT
                    tests.name as test_name,
                    COUNT(test_completions.id) as test_count,
                    ROUND(AVG(test_completions.earned_points), 1) as avg_points,
                    MIN(test_completions.earned_points) as min_points,
                    MAX(test_completions.earned_points) as max_points,
                    CONCAT(
                        GREATEST(ROUND(AVG(TIMESTAMPDIFF(HOUR, test_completions.created_at, test_completions.started_at)), 0), 0), ':', 
                        LPAD(GREATEST(ROUND(AVG(TIMESTAMPDIFF(MINUTE, test_completions.created_at, test_completions.started_at)), 0), 1) % 60, 2, '0')
                    ) AS average_time_difference,
                    CONCAT(ROUND(AVG(test_completions.successful_completion) * 100, 0), '%') as avg_successful_completion_percentage
                    FROM test_completions
                    LEFT JOIN tests ON tests.id = test_completions.test_id
                    GROUP BY test_completions.test_id",
            false);
    }

    public static function save(TestCompletion|AuditedModel $model): int
    {
        return Database::insert('insert into test_completions (test_id, earned_points, successful_completion, started_at, created_at, created_by) values (?, ?, ?, ?, ?, ?)',
            [SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::STRING->value, SqlValueType::STRING->value, SqlValueType::INT->value],
            [$model->getTestId(), $model->getEarnedPoints(), $model->isSuccessfulCompletion(), $model->sqlStarted_at(), $model->sqlCreated_at(), $model->getCreated_by()]);
    }

    public static function delete(int $id): bool
    {
        return Database::delete('delete from test_completions where id = ?', [SqlValueType::INT->value], [$id]);
    }
}