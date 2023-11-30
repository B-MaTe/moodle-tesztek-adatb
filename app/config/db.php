<?php

class Database
{
    public static function query(string $sql, array $types, array $params): mysqli_result|bool
    {
        $connection = new mysqli(...DB_DATA);
        $connection->autocommit(true);

        $stmt = $connection->prepare($sql);

        if ($stmt === false) {
            die("Hiba az sql előkészítése közben: " .  $connection->error);
        }

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param(implode('', $types), ...$params);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $connection->close();

        return $result;
    }

    public static function insert(string $sql, array $types, array $params): int {
        $connection = new mysqli(...DB_DATA);

        $stmt = $connection->prepare($sql);

        if ($stmt === false) {
            die("Hiba az sql előkészítése közben: " .  $connection->error);
        }

        if (!empty($types) && !empty($params)) {
            $stmt->bind_param(implode('', $types), ...$params);
        }

        $result = $stmt->execute();

        $id = $result ? $connection->insert_id : 0;

        $connection->close();

        return $id;
    }
}