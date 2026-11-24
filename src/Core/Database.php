<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $file = ROOT . '/config/database.php';
            if (!is_file($file)) {
                http_response_code(500);
                exit('Fichier config/database.php manquant. Copiez config/database.example.php et adaptez-le.');
            }
            $c = require $file;
            $dsn = 'mysql:host=' . $c['host'] . ';dbname=' . $c['dbname'] . ';charset=' . $c['charset'];
            try {
                self::$instance = new PDO($dsn, $c['user'], $c['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('Connexion a la base de donnees impossible. Verifiez config/database.php.');
            }
        }
        return self::$instance;
    }
}
