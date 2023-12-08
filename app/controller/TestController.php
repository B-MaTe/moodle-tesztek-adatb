<?php

namespace controller;
use Database;
use DateTime;
use enum\SqlValueType;
use Exception;
use model\Answer;
use model\Question;
use model\Test;
use util\pageable\Page;
use util\pageable\Pageable;

require_once 'app/controller/Controller.php';
require_once 'app/controller/AuthController.php';
require_once 'app/model/Test.php';
require_once 'app/model/Question.php';
require_once 'app/model/Answer.php';

class TestController extends Controller
{

    public function index(): void
    {
        $page = $this->getActivePageable(Pageable::builder()->withPageSize(PHP_INT_MAX)->withPage(0)->withTotalRecords($this->countActiveTests())->build());
        require_once 'app/view/tests.php';
    }

    public function listPageable($pageSize = Pageable::DEFAULT_PAGE_SIZE, $page = 0): void {
        $pageSize = self::numOrDefault($pageSize, Pageable::DEFAULT_PAGE_SIZE);
        $page = self::numOrDefault($page, 0);

        $page = $this->getActivePageable(
            Pageable::builder()
                ->withPageSize($pageSize)
                ->withPage($page)->withTotalRecords($this->countActiveTests())
                ->build());
        require_once 'app/view/tests.php';
    }

    public function getActivePageable(Pageable $pageable): Page {
        $page = new Page([], $pageable->getPage(), $pageable->getPageSize(), $pageable->getTotalRecords());

        $testPageSql = 'select * from tests where active = true ORDER BY id LIMIT ?, ?';

        $page->setItems(self::selectModels(Test::class, $testPageSql, false, [SqlValueType::INT->value, SqlValueType::INT->value], [$pageable->getOffset(), $pageable->getPageSize()]));
        return $page;
    }

    /**
     * @throws Exception
     */
    public function singleTest($id = 0): void {
        $id = self::numOrDefault($id, 0);
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
                    questions ON tests.id = questions.test_id
                LEFT JOIN
                    answers ON questions.id = answers.question_id
                WHERE tests.id = ? AND tests.active = true',
        false, [SqlValueType::INT->value], [$id]);
        }

        $test = $result != null ? self::getTestFromQueryResult($result) : new Test();

        require_once 'app/view/test.php';
    }

    private function countActiveTests(): int {
        $sql = 'select count(*) from tests where active = true';

        return Database::query($sql, [], [])->fetch_row()[0];
    }

    private function numOrDefault($number, int $default): int {
        return filter_var($number, FILTER_VALIDATE_INT) !== false ? $number : $default;
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
}