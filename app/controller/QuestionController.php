<?php

namespace controller;


use Database;
use DateTime;
use enum\NotificationType;
use enum\SqlValueType;
use model\Answer;
use model\AuditedModel;
use model\Question;
use util\pageable\Page;
use util\pageable\Pageable;

class QuestionController extends DataController
{
    public function index(): void
    {
        AuthController::checkAdminOrTeacherPrivilege();

        require_once 'app/view/question_index.php';
    }

    public function listQuestions($pageSize = Pageable::DEFAULT_PAGE_SIZE_QUESTIONS, $page = 0): void {
        AuthController::checkAdminOrTeacherPrivilege();
        $pageSize = numOrDefault($pageSize, Pageable::DEFAULT_PAGE_SIZE_QUESTIONS);
        $page = numOrDefault($page, 0);


        $sql = 'select * from questions ORDER BY id LIMIT ?, ?';
        $count = $this->count(Question::class);

        $page = new Page(self::selectModels(Question::class, $sql, false, [SqlValueType::INT->value, SqlValueType::INT->value], [$page * $pageSize, $pageSize]), $page, $pageSize, $count);

        require_once 'app/view/questions.php';
    }

    public function deleteQuestion($id): void {
        AuthController::checkAdminOrTeacherPrivilege();
        $id = numOrDefault($id, 0);

        if ($id != 0) {
            if (Database::query('select count(1) as q_sum from test_question where question_id = ?', [SqlValueType::INT->value], [$id])->fetch_assoc()['q_sum'] != 0) {
                NotificationController::setNotification(NotificationType::ERROR, 'Ez a kérdés nem törölhető, legalább egy teszthez tartozik még!');
                header('Location: questions');
                exit;
            }

            self::delete($id);
            NotificationController::setNotification(NotificationType::SUCCESS, 'Sikeresen törölte a kérdést!');

        } else {
            NotificationController::setNotification(NotificationType::ERROR, 'Hiba történt!');
        }


        header('Location: questions');
        exit;
    }

    public function editQuestion($data): void {
        AuthController::checkAdminOrTeacherPrivilege();
        $id = $data['id'];
        $text = $data['text'];
        $page = $data['page'] ?? 0;
        $pageSize = $data['pageSize'] ?? Pageable::DEFAULT_PAGE_SIZE;

        Database::query('UPDATE questions SET text = ? WHERE id = ?', [SqlValueType::STRING->value, SqlValueType::INT->value], [$text, $id]);

        NotificationController::setNotification(NotificationType::SUCCESS, 'Sikeresen frissitette a kérdést!');
        header('Location: questions?pageSize=' . $pageSize . '&page=' . $page);
    }

    public function singleQuestion(): void {
        AuthController::checkAdminOrTeacherPrivilege();

        require_once 'app/view/question.php';
    }

    public function addQuestion($data): void {
        $now = new DateTime();
        $currentUserId = UserController::getLoggedInUser()->getId();
        $question = new Question();
        $question->setText($data['q-1']);
        $question->setCreated_at($now);
        $question->setCreated_by($currentUserId);
        $question->setPoint($data['p-1']);
        $answers = array_filter($data, function ($key) {
            return str_starts_with($key, 'a-');
        }, ARRAY_FILTER_USE_KEY);

        foreach ($answers as $key => $value) {
            $answer = new Answer();
            $answer->setText($value);
            $answer->setCreated_at($now);
            $answer->setCreated_by($currentUserId);

            if ($key === $data['ca-1']) {
                $answer->setCorrect(true);
                $question->setGoodAnswer($answer);
            } else {
                $answer->setCorrect(false);
                $question->appendWrongAnswer($answer);
            }
        }

        self::save($question);

        NotificationController::setNotification(NotificationType::SUCCESS, 'Kérdés sikeresen hozzáadva!');
        header('Location: manage-questions');
    }

    public static function getPageForTest(int $pageSize = Pageable::DEFAULT_PAGE_SIZE, int $page = 0, int $testId = 0): Page
    {
        AuthController::checkAdminOrTeacherPrivilege();
        $pageSize = numOrDefault($pageSize, Pageable::DEFAULT_PAGE_SIZE);
        $page = numOrDefault($page, 0);

        $sql = 'SELECT q.*
                  FROM questions q
                  WHERE ? = 0 OR NOT EXISTS (
                      SELECT 1
                      FROM test_question tq
                      WHERE tq.question_id = q.id
                      AND tq.test_id = ?
                  ) ORDER BY id LIMIT ?, ?';

        $result = self::selectModels(null, $sql, false, [SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value], [$testId, $testId, $page * $pageSize, $pageSize]);


        return new Page(self::getQuestionsFromQueryResult($result),
            $page, $pageSize, self::count(Question::class));
    }

    public static function save(Question|AuditedModel $model): int
    {
        $id = Database::insert('insert into questions (text, point, created_at, created_by) values (?, ?, ?, ?)',
            [SqlValueType::STRING->value, SqlValueType::INT->value, SqlValueType::STRING->value, SqlValueType::INT->value],
            [$model->getText(), $model->getPoint(), $model->sqlCreated_at(), $model->getCreated_by()]);


        foreach ($model->getTests() as $test) {
            Database::insert('insert ignore into test_question (test_id, question_id) values (?, ?)',
                [SqlValueType::INT->value, SqlValueType::INT->value],
                [$test->getId(), $id]);
        }

        $model->setId($id);
        foreach ($model->getAnswers() as $answer) {
            $answer->setQuestion($model);
            AnswerController::save($answer);
        }

        return $id;
    }

    public static function sumPoints(array $questionIds): int {
        return (int)self::selectModels(null, 'select sum(point) as sum_points from questions where id in (' . implode(',', $questionIds) . ')', true)['sum_points'];
    }

    public static function delete(int $id): bool
    {
        return Database::query('delete from questions where id = ?', [SqlValueType::INT->value], [$id]) != false;
    }

    public static function getQuestionWithAnswers($questionId): ?Question {
        $question = self::selectModels(Question::class, 'select * from questions where id = ?', true, [SqlValueType::INT->value], [$questionId]);
        $answers = self::selectModels(Answer::class, 'select * from answers where question_id = ?', false, [SqlValueType::INT->value], [$questionId]);

        foreach ($answers as $answer) {
            if ($answer->isCorrect()) {
                $question->setGoodAnswer($answer);
            } else {
                $question->appendWrongAnswer($answer);
            }
        }

        return $question;
    }

    private static function getQuestionsFromQueryResult($data): array {
        $questions = [];
        foreach ($data as $row) {
            $question = new Question();
            $question->setId($row['id']);
            $question->setText($row['text']);
            $question->setPoint($row['point']);
            $question->setCreated_at($row['created_at']);
            $question->setCreated_by($row['created_by']);

            $answers = self::selectModels(Answer::class, 'select * from answers where question_id = ?', false, [SqlValueType::INT->value], [$question->getId()]);

            foreach ($answers as $answer) {
                if ($answer->isCorrect()) {
                    $question->setGoodAnswer($answer);
                } else {
                    $question->appendWrongAnswer($answer);
                }
            }
            $questions[] = $question;
        }
        return $questions;
    }
}