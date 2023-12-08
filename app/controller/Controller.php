<?php

namespace controller;

use Database;
use DateTime;
use enum\Role;
use Exception;

abstract class Controller
{
    public abstract function index(): void;

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