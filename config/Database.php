<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $dotenv = parse_ini_file(__DIR__ . '/../.env');
        if ($dotenv === false) {
            die("Database configuration file (.env) not found or invalid.");
        }
        
        $host = $dotenv['SUPABASE_DB_HOST'] ?? '';
        $port = $dotenv['SUPABASE_DB_PORT'] ?? '5432';
        $dbname = $dotenv['SUPABASE_DB_NAME'] ?? '';
        $user = $dotenv['SUPABASE_DB_USER'] ?? '';
        $password = $dotenv['SUPABASE_DB_PASS'] ?? '';
        
        if (empty($host) || empty($dbname) || empty($user)) {
            die("Database configuration incomplete. Please check your .env file.");
        }
        
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};user={$user};password={$password}";
        
        try {
            $this->conn = new PDO($dsn);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
