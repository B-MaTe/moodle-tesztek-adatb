<?php

namespace controller;

use Database;
use DateTime;
use enum\Role;
use Exception;
use model\AuditedModel;

abstract class DataController extends Controller
{

    public static abstract function save(AuditedModel $model): int;

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
}