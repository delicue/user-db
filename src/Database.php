<?php 

namespace App;

use PDO;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO('sqlite:' . __DIR__ . '/databases/database.db');
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL
            )");
            Log::info("Connected to the SQLite database successfully.");
        } catch (\PDOException $e) {
            Log::error("Database connection failed: " . $e->getMessage());
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        self::$instance ??= new self();
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Finds all records in specified query.
     * @param mixed $query
     * @param mixed $params
     * @return array
     */
    public function fetchAll($query, $params = []): array {
        $stmt = self::getInstance()
            ->getConnection()
            ->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Finds a single record from the database.
     *
     * @param [type] $query
     * @param array $params
     * @return array|null
     */
    public function fetchOne($query, $params = []): ?array {
        $stmt = self::getInstance()
            ->getConnection()
            ->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function execute($query, $params = []): bool {
        $stmt = self::getInstance()->getConnection()->prepare($query);
        return $stmt->execute($params);
    }

    public function all($table): array {
        $query = "SELECT * FROM :table";
        return self::fetchAll($query, [$table]);
    }

    public function count($table): int {
        $stmt = self::getInstance()->getConnection()->prepare("SELECT COUNT(*) as count FROM $table");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}