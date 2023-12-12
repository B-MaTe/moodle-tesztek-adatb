<?php

namespace controller;
use Database;
use DateTime;
use enum\NotificationType;
use enum\SqlValueType;
use Exception;
use model\Answer;
use model\AuditedModel;
use model\Question;
use model\Test;
use model\TestCompletion;
use util\pageable\Page;
use util\pageable\Pageable;

require_once 'app/controller/AuthController.php';
require_once 'app/model/Test.php';
require_once 'app/model/Question.php';
require_once 'app/model/Answer.php';
require_once 'app/model/TestCompletion.php';

class TestController extends DataController
{
    public function index(): void
    {
        $page = $this->getActivePageable(Pageable::builder()->withPageSize(PHP_INT_MAX)->withPage(0)->withTotalRecords($this->count(Test::class))->build());
        require_once 'app/view/tests.php';
    }

    public function listPageable($pageSize = Pageable::DEFAULT_PAGE_SIZE, $page = 0): void {
        $pageSize = numOrDefault($pageSize, Pageable::DEFAULT_PAGE_SIZE);
        $page = numOrDefault($page, 0);

        $page = $this->getActivePageable(
            Pageable::builder()
                ->withPageSize($pageSize)
                ->withPage($page)->withTotalRecords($this->count(Test::class, true))
                ->build());
        require_once 'app/view/tests.php';
    }



    public static function deleteTest($id): void
    {
        AuthController::checkAdminOrTeacherPrivilege();

        if (self::delete($id)) {
            NotificationController::setNotification(NotificationType::SUCCESS, 'Sikeres teszt törlés!');
        } else {
            NotificationController::setNotification(NotificationType::ERROR, 'Sikertelen teszt törlés!');
        }

        header('Location: tests');
    }

    public static function delete(int $id): bool {
        return Database::delete('delete from tests where id = ?', [SqlValueType::INT->value], [$id]);
    }

    public static function fillTest($data): void {
        $testCompletion = new TestCompletion();
        $test = self::selectModels(Test::class, 'select * from tests where id = ?', true, [SqlValueType::INT->value], [$data['id']]);
        $collectedPoints = 0;

        $testCompletion->setTestId($test->getId());
        $testCompletion->setCreated_by(UserController::getLoggedInUser()->getId());
        $testCompletion->setCreated_at(new DateTime());

        foreach (array_filter($data, function ($key) {
            return $key !== 'id';
        }, ARRAY_FILTER_USE_KEY) as $questionId => $chosenAnswerId) {
            $question = QuestionController::getQuestionWithAnswers($questionId);

            if ($question->getGoodAnswer()->getId() == $chosenAnswerId) {
                $collectedPoints += $question->getPoint();
            }
        };

        $testCompletion->setEarnedPoints($collectedPoints);
        $testCompletion->setSuccessfulCompletion($collectedPoints >= $test->getMin_points());

        $id = TestCompletionController::save($testCompletion);
        if ($id > 0) {
            NotificationController::setNotification(NotificationType::SUCCESS, 'Sikeres teszt kitöltés!');
        } else {
            NotificationController::setNotification(NotificationType::ERROR, 'Hiba történt!');
        }
        header('Location: tests');
    }

    public function addTest($test): void {
        AuthController::checkAdminOrTeacherPrivilege();

        $currentUserId = UserController::getLoggedInUser()->getId();
        $model = new Test();
        $model->setName($test['title']);
        $model->setActive(true);
        $model->setMin_points($test['min_points']);
        $model->setCreated_at(new DateTime());
        $model->setCreated_by($currentUserId);

        $questions = array_filter($test, function ($key) {
            return str_starts_with($key, 'q-');
        }, ARRAY_FILTER_USE_KEY);
        $answers = array_filter($test, function ($key) {
            return str_starts_with($key, 'a-');
        }, ARRAY_FILTER_USE_KEY);
        $correctAnswers = array_filter($test, function ($key) {
            return str_starts_with($key, 'ca-');
        }, ARRAY_FILTER_USE_KEY);
        $points = array_filter($test, function ($key) {
            return str_starts_with($key, 'p-');
        }, ARRAY_FILTER_USE_KEY);

        foreach ($questions as $questionId => $questionText) {
            $questionParts = explode($questionId, '-');
            $found = false;
            foreach ($answers as $answer) {
                $answerParts = explode($answer, '-');

                if ($questionParts[1] == $answerParts[1]) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                NotificationController::setNotification(NotificationType::ERROR, 'Nem minden kérdésnek lett válasz lehetőség megadva.');
                header("Location: test");
                exit;
            }
        }

        $pointsSum = 0;

        $questionModels = [];
        foreach ($questions as $key => $questionText) {
            $questionId = substr($key, -1);
            $question = new Question();
            $question->setText($questionText);
            $point = array_filter($points, function ($answerKey) use ($questionId) { return str_starts_with($answerKey, 'p-' . $questionId); }, ARRAY_FILTER_USE_KEY);
            $point = reset($point);
            $question->setPoint($point);
            $pointsSum += $point;
            $question->setCreated_by($currentUserId);
            $question->setCreated_at(new DateTime());
            foreach (array_filter($answers, function ($answerKey) use ($questionId) { return str_starts_with($answerKey, 'a-' . $questionId); }, ARRAY_FILTER_USE_KEY) as $answerKey => $answerText) {
                $answer = new Answer();
                $answer->setText($answerText);
                $answer->setCorrect(in_array($answerKey, $correctAnswers));
                $answer->setCreated_by($currentUserId);
                $answer->setCreated_at(new DateTime());
                $answer->setQuestion($question);

                if ($answer->isCorrect()) {
                    $question->setGoodAnswer($answer);
                } else {
                    $question->appendWrongAnswer($answer);
                }
            }
            $questionModels[] = $question;
        }

        $existingQuestionIds = array_map(function($existingQuestion) {
            $parts = explode('-', $existingQuestion);
            return (int)end($parts);
        }, $test['existing-questions']);

        $existingPoints = QuestionController::sumPoints($existingQuestionIds);

        if ($pointsSum + $existingPoints < $test['min_points']) {
            FormController::addError('min_points', 'A sikeres teszt pontszáma nem lehet nagyobb mint a kérdések pontszámának összege! (Kérdések pontszámának összege: ' . $pointsSum .')');
            header('Location: test');
            // TODO: save form data
            exit;
        }

        $model->setQuestions($questionModels);

        $id = self::save($model);

        foreach ($test['existing-questions'] as $selectedQuestion) {
            $array = explode('-', $selectedQuestion);
            $existingQuestionId = (int)end($array);
            Database::insert('insert ignore into test_question (test_id, question_id) values (?, ?)',
                [SqlValueType::INT->value, SqlValueType::INT->value],
                [$id, $existingQuestionId]);
        }

        NotificationController::setNotification(NotificationType::SUCCESS, 'Sikeres teszt létrehozás!');
        header('Location: test?id=' . $id);
    }

    /**
     * @throws Exception
     */
    public function singleTest($id = 0): void {
        $id = numOrDefault($id, 0);
        $result = null;

        if ($id != 0) {
            $result = self::selectModels(null,
        'SELECT
                    tests.id AS test_id,
                    tests.name AS test_name,
                    tests.min_points AS test_min_points,
                    tests.active AS test_active,
                    tests.created_at AS test_created_at,
                    tests.created_by AS test_created_by,
                    questions.id AS question_id,
                    questions.text AS question_text,
                    questions.point AS question_point,
                    answers.id AS answer_id,
                    answers.text AS answer_text,
                    answers.correct AS answer_correct,
                    answers.question_id AS answer_question_id
                FROM
                    tests
                LEFT JOIN
                    test_question ON tests.id = test_question.test_id
                LEFT JOIN
                    questions ON test_question.question_id = questions.id
                LEFT JOIN
                    answers ON questions.id = answers.question_id
                WHERE
                    tests.id = ? AND tests.active = true;',
        false, [SqlValueType::INT->value], [$id]);
        }

        $test = $result != null ? self::getTestFromQueryResult($result) : new Test();

        if ($test->getId() == 0) {
            AuthController::checkAdminOrTeacherPrivilege();
        }

        require_once 'app/view/test.php';
    }

    private function getActivePageable(Pageable $pageable): Page {
        $page = new Page([], $pageable->getPage(), $pageable->getPageSize(), $pageable->getTotalRecords());

        $pageSql = 'select * from tests where active = true ORDER BY id LIMIT ?, ?';

        $page->setItems(self::selectModels(Test::class, $pageSql, false, [SqlValueType::INT->value, SqlValueType::INT->value], [$pageable->getOffset(), $pageable->getPageSize()]));
        return $page;
    }

    /**
     * @throws Exception
     */
    private function getTestFromQueryResult($result): Test {
        $test = null;
        $questions = [];
        $answers = [];
        foreach ($result as $row) {
            if ($test === null) {
                $test = new Test(
                    $row['test_name'],
                    $row['test_min_points'],
                    $row['test_active'],
                    [],
                    new DateTime($row['test_created_at']),
                    $row['test_created_by'],
                    $row['test_id']
                );
            }

            $questionId = $row['question_id'];

            if (!isset($questions[$questionId])) {
                $questions[$questionId] = new Question(
                    $row['question_text'],
                    $row['question_point'],
                    $row['question_id']
                );
            }

            $answers[] = new Answer(
                $row['answer_correct'],
                $row['answer_text'],
                new Question(id: (int)$row['answer_question_id']),
                $row['question_id'],
                $row['answer_id']
            );
        }

        foreach ($questions as $question) {
             $questionAnswers = array_filter($answers, function ($answer) use ($question) { return $answer->getQuestion()->getId() === $question->getId(); });

             foreach ($questionAnswers as $questionAnswer) {
                 if ($questionAnswer->isCorrect()) {
                     $question->setGoodAnswer($questionAnswer);
                 } else {
                     $question->appendWrongAnswer($questionAnswer);
                 }
             }
        }
        $test->setQuestions($questions);
        return $test;
    }

    public static function save(Test|AuditedModel $model): int
    {
        $id = Database::insert('insert into tests (name, min_points, active, created_by, created_at) values (?, ?, ?, ?, ?)',
        [SqlValueType::STRING->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::STRING->value],
        [$model->getName(), $model->getMin_points(), $model->isActive(), $model->getCreated_by(), $model->sqlCreated_at()]);

        $model->setId($id);
        foreach ($model->getQuestions() as $question) {
            $question->setTests([$model]);
            $questionId = QuestionController::save($question);

            Database::insert('insert ignore into test_question (test_id, question_id) values (?, ?)',
                [SqlValueType::INT->value, SqlValueType::INT->value],
                [$id, $questionId]);
        }

        return $id;
    }
}