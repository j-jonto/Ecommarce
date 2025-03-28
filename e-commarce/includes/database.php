<?php
// Database connection configuration for SQLite
class Database {
    private $db_file;
    private $conn;

    public function __construct() {
        // Set the database file path
        $this->db_file = dirname(__DIR__) . '/data/ecommerce.db';

        // Make sure the data directory exists
        $data_dir = dirname($this->db_file);
        if (!file_exists($data_dir)) {
            mkdir($data_dir, 0777, true);
        }
    }

    // Get database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Enable foreign keys in SQLite
            $this->conn->exec('PRAGMA foreign_keys = ON');
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>