<?php

namespace controller;
use Cassandra\Date;
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

require_once 'app/model/Test.php';
require_once 'app/model/TestCompletion.php';

class TestController extends DataController
{
    public function index(): void
    {
        AuthController::returnHomeIfLoggedOut();

        $page = $this->getPageable(Pageable::builder()->withPageSize(PHP_INT_MAX)->withPage(0)->withTotalRecords($this->count(Test::class))->build());

        self::getTestPageWithCompletions($page);
        require_once 'app/view/tests.php';
    }

    public function listPageable($pageSize = Pageable::DEFAULT_PAGE_SIZE, $page = 0): void {
        AuthController::returnHomeIfLoggedOut();

        $pageSize = numOrDefault($pageSize, Pageable::DEFAULT_PAGE_SIZE);
        $page = numOrDefault($page, 0);
        $page = $this->getPageable(
            Pageable::builder()
                ->withPageSize($pageSize)
                ->withPage($page)->withTotalRecords($this->count(Test::class, true))
                ->build());
        self::getTestPageWithCompletions($page);

        require_once 'app/view/tests.php';
    }

    public static function testStatistics($testId): void {
        AuthController::returnHomeIfLoggedOut();
        $completions = TestCompletionController::getCompletionsForTestsByUser([$testId], UserController::getLoggedInUser()->getId());
        array_map(function (TestCompletion $completion) {
            $testCompletionAnswers = self::selectModels(null, 'select test_completion_id, question_id, answer_id from test_completion_question_answers where test_completion_id = ?', false, [SqlValueType::INT->value], [$completion->getId()]);

            foreach ($testCompletionAnswers as $testCompletionAnswer) {
                $questionWithAnswers = QuestionController::getQuestionWithAnswers($testCompletionAnswer['question_id']);
                $selectedAnswerArray = array_filter($questionWithAnswers->getAnswers(), function (Answer $answer) use ($testCompletionAnswer) {
                    return $answer->getId() == $testCompletionAnswer['answer_id'];
                });

                $selectedAnswer = reset($selectedAnswerArray);
                $questionWithAnswers->setSelectedAnswer($selectedAnswer);

                $completion->appendQuestion($questionWithAnswers);
            }
        }, $completions);

        require_once 'app/view/test_statistics.php';
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

    public static function editTestName($data): void {
        AuthController::checkAdminOrTeacherPrivilege();

        $testId = $data['id'];
        $newName = $data['name'];
        $page = $data['page'] ?? 0;
        $pageSize = $data['pageSize'] ?? Pageable::DEFAULT_PAGE_SIZE;

        Database::query('update tests set name = ? where id = ?', [SqlValueType::STRING->value, SqlValueType::INT->value], [$newName, $testId]);

        NotificationController::setNotification(NotificationType::SUCCESS, 'A teszt neve sikeresen frissítve!');
        header('Location: tests?pageSize=' . $pageSize . '&page=' . $page);
    }

    public static function fillTest($data): void {
        AuthController::returnHomeIfLoggedOut();

        $testCompletion = new TestCompletion();
        $test = self::selectModels(Test::class, 'select * from tests where id = ?', true, [SqlValueType::INT->value], [$data['id']]);
        $collectedPoints = 0;
        $currentUserId = UserController::getLoggedInUser()->getId();

        $testCompletion->setTestId($test->getId());
        $testCompletion->setCreated_by($currentUserId);
        $testCompletion->setCreated_at(new DateTime());

        $questionsWithAnswers = [];
        foreach (array_filter($data, function ($key) {
            return $key !== 'id';
        }, ARRAY_FILTER_USE_KEY) as $questionId => $chosenAnswerId) {
            $question = QuestionController::getQuestionWithAnswers($questionId);
            $questionsWithAnswers[$questionId] = $chosenAnswerId;

            if ($question->getGoodAnswer()->getId() == $chosenAnswerId) {
                $collectedPoints += $question->getPoint();
            }
        };

        $testCompletion->setStartedAt($_SESSION['started_at'] ?? new DateTime());
        $testCompletion->setEarnedPoints($collectedPoints);
        $testCompletion->setSuccessfulCompletion($collectedPoints >= $test->getMin_points());

        $id = TestCompletionController::save($testCompletion);

        $now = new DateTime();
        foreach ($questionsWithAnswers as $questionId => $answerId) {
            Database::insert('insert into test_completion_question_answers (test_completion_id, question_id, answer_id, created_by, created_at) values (?, ?, ?, ?, ?)',
                [SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::STRING->value],
                [$id, $questionId, $answerId, $currentUserId, $now->format('Y-m-d H:i:s')]);
        }

        $location = 'Location: test';
        if ($id > 0) {
            $location = 'Location: evaluate-test?id=' . $id;
        } else {
            NotificationController::setNotification(NotificationType::ERROR, 'Hiba történt!');
        }
        header($location);
    }

    public static function evaluateTest(int $completionId): void {
        AuthController::returnHomeIfLoggedOut();

        $completion = self::selectModels(TestCompletion::class, 'select * from test_completions where id = ?', true, [SqlValueType::INT->value], [$completionId]);
        $test = self::selectModels(Test::class, 'select distinct * from tests where id = ?', true, [SqlValueType::INT->value], [$completion->getTestId()]);
        $questionIds = array_map(function ($question) {
            return $question['question_id'];
        }, self::selectModels(null, 'select * from test_question where test_id = ?', false, [SqlValueType::INT->value], [$test->getId()]));

        $oldCompletions = self::selectModels(TestCompletion::class, 'select * from test_completions where id != ? and test_id = ? and created_by = ? order by created_at desc', false, [SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::INT->value], [$completionId, $test->getId(), UserController::getLoggedInUser()->getId()]);
        $maxPoints = QuestionController::sumPoints($questionIds);
        require_once 'app/view/evaluation.php';
    }

    public function addTest($test): void {
        AuthController::checkAdminOrTeacherPrivilege();

        $currentUserId = UserController::getLoggedInUser()->getId();
        $model = new Test();
        $model->setName($test['title']);
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
        $existingPoints = 0;

        if ($test['existing-questions'] != null) {
            $existingQuestionIds = array_map(function($existingQuestion) {
                $parts = explode('-', $existingQuestion);
                return (int)end($parts);
            }, $test['existing-questions']);

            $existingPoints = QuestionController::sumPoints($existingQuestionIds);
        }

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
        AuthController::returnHomeIfLoggedOut();
        $id = numOrDefault($id, 0);
        $result = null;

        if ($id != 0) {
            $result = self::selectModels(null,
        'SELECT
                    tests.id AS test_id,
                    tests.name AS test_name,
                    tests.min_points AS test_min_points,
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
                    tests.id = ?;',
        false, [SqlValueType::INT->value], [$id]);
        }

        $test = $result != null ? self::getTestFromQueryResult($result) : new Test();

        if ($test->getId() == 0) {
            AuthController::checkAdminOrTeacherPrivilege();
        } else {
            $_SESSION['started_at'] = new DateTime();
        }

        require_once 'app/view/test.php';
    }

    public static function getForSummary(): array {
        return self::selectModels(Test::class, 'select id, name from tests', false);
    }

    private function getTestPageWithCompletions(Page $page): void {
        $completionsPerTest = TestCompletionController::getCompletionsForTestsByUser(array_map(function ($test) { return $test->getId(); }, $page->getItems()), UserController::getLoggedInUser()->getId());
        $items = $page->getItems();
        array_walk($items, function($test) use ($completionsPerTest) {
            $completionsForTest = array_filter($completionsPerTest, function($completion) use ($test) {
                return $completion->getTestId() == $test->getId();
            });

            usort($completionsForTest, function($a, $b) {
                return $b->getEarnedPoints() - $a->getEarnedPoints();
            });

            $test->setCompletions($completionsForTest);
        });

        $page->setItems($items);
    }

    private function getPageable(Pageable $pageable): Page {
        $page = new Page([], $pageable->getPage(), $pageable->getPageSize(), $pageable->getTotalRecords());

        $pageSql = 'select * from tests ORDER BY id LIMIT ?, ?';

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
                $row['answer_correct'] == 1,
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
        $id = Database::insert('insert into tests (name, min_points, created_by, created_at) values (?, ?, ?, ?)',
        [SqlValueType::STRING->value, SqlValueType::INT->value, SqlValueType::INT->value, SqlValueType::STRING->value],
        [$model->getName(), $model->getMin_points(), $model->getCreated_by(), $model->sqlCreated_at()]);

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