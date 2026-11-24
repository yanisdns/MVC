<?php

namespace App\Core;

use PDO;
use PDOStatement;

abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Prepare et execute une requete. Les entiers sont lies en tant qu'entiers (utile pour LIMIT).
    protected function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $name => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : (is_null($value) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue($name, $value, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    protected function fetchValue(string $sql, array $params = [])
    {
        return $this->run($sql, $params)->fetchColumn();
    }
}
