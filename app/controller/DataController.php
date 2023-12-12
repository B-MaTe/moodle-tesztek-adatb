<?php

namespace controller;

use Database;
use DateTime;
use enum\Role;
use Exception;
use model\Answer;
use model\AuditedModel;
use model\Question;
use model\Test;
use model\User;

abstract class DataController extends Controller
{

    public static abstract function save(AuditedModel $model): int;
    public static abstract function delete(int $id): bool;

    public static function selectModels($modelClass, string $query, bool $single, array $types = [], array $params = []): mixed {
        $models = [];
        $result = Database::query($query, $types, $params);
        $model = $result->fetch_assoc();

        do {
            if ($model != null) {
                if (isset($model['created_at'])) {
                    try {
                        $model['created_at'] = new DateTime($model['created_at']);
                    } catch (Exception $ignored) {
                        $model['created_at'] = null;
                    }
                }

                if (isset($model['role'])) {
                    $model['role'] = Role::from($model['role']);
                }

                $models[] = $modelClass != null ? new $modelClass(...$model) : $model;
            }
        } while (($model = $result->fetch_assoc()) && !$single);

        return $single ? ($models[0] ?? null) : $models;
    }

    public static function count($model, bool $onlyActive = false): int {
        $sql = 'select count(*) from ' . self::getTableFromModel($model) . ($onlyActive ? ' where active = 1 ' : ' ');

        return Database::query($sql, [], [])->fetch_row()[0];
    }

    private static function getTableFromModel($model): string {
        switch ($model) {
            case Test::class;
                $table = 'tests';
                break;
            case Question::class:
                $table = 'questions';
                break;
            case Answer::class:
                $table = 'answers';
                break;
            case User::class:
                $table = 'users';
                break;
            default:
                die('Ismeretlen model.');
        }

        return $table;
    }
}