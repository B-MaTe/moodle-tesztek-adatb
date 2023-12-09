<?php

namespace controller;


use Database;
use enum\SqlValueType;
use model\AuditedModel;
use model\Question;

class QuestionController extends DataController
{
    public function index(): void
    {
        // TODO: Implement index() method.
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
        foreach ($model->getRandomizedAnswers() as $answer) {
            $answer->setQuestion($model);
            AnswerController::save($answer);
        }

        return $id;
    }
}