<?php

namespace controller;

class StatisticsController
{

    public function showAll($summaryTestId = 0): void
    {
        AuthController::checkAdminOrTeacherPrivilege();

        $testsForSummary = TestController::getForSummary();
        $fillSummary = [];
        if ($summaryTestId > 0) {
            $fillSummary = TestCompletionController::getTestFillSummary($summaryTestId);
        }

        $resultsSummary = TestCompletionController::getTestResultsSummary();

        $userPerformanceSummary = UserController::getUserPerformanceSummary();

        $generalSummary = $this->getGeneralSummary();

        require_once 'app/view/statistics.php';
    }

    private function getGeneralSummary(): array
    {
        return DataController::selectModels(null,
        "
            SELECT
                COUNT(DISTINCT users.id) as user_count,
                COUNT(DISTINCT tests.id) as test_count,
                COUNT(DISTINCT test_completions.id) as test_completion_count,
                COUNT(DISTINCT questions.id) as question_count,
                COUNT(DISTINCT answers.id) as answer_count
            FROM
                users
            LEFT JOIN
                test_completions ON users.id
            LEFT JOIN
                tests ON test_completions.test_id
            LEFT JOIN
                questions ON questions.id
            LEFT JOIN
                answers ON questions.id;",
            true);
    }
}