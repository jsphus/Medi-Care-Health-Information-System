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

        if (!$host || !$dbname || !$user) {
            die("Database configuration incomplete. Please check your .env file.");
        }

        // Correct DSN format
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            $this->conn = new PDO($dsn, $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Ensure all columns are returned
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // For PostgreSQL, ensure we get all column data
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection instance
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Fetch all rows from a query
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return array Array of associative arrays
     */
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Database query failed: " . $e->getMessage());
        }
    }

    /**
     * Fetch a single row from a query
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return array|null Associative array or null if not found
     */
    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug: Log if we get fewer columns than expected
            if ($result && count($result) < 3) {
                error_log("Database::fetchOne() - Only got " . count($result) . " columns. SQL: " . $sql);
                error_log("Database::fetchOne() - Keys: " . implode(', ', array_keys($result)));
            }
            
            return $result ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Database query failed: " . $e->getMessage());
        }
    }

    /**
     * Execute a query (INSERT, UPDATE, DELETE)
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return bool True on success
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new PDOException("Database execution failed: " . $e->getMessage());
        }
    }

    /**
     * Get the last insert ID
     * @param string|null $sequence PostgreSQL sequence name (optional, auto-detected if null)
     * @return string Last insert ID
     */
    public function lastInsertId($sequence = null) {
        return $this->conn->lastInsertId($sequence);
    }

    /**
     * Prepare a statement
     * @param string $sql SQL query
     * @return PDOStatement
     */
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    /**
     * Execute a query and return a statement
     * @param string $sql SQL query
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($sql) {
        try {
            return $this->conn->query($sql);
        } catch (PDOException $e) {
            throw new PDOException("Database query failed: " . $e->getMessage());
        }
    }

    /**
     * Begin a transaction
     * @return bool
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Commit a transaction
     * @return bool
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Rollback a transaction
     * @return bool
     */
    public function rollBack() {
        return $this->conn->rollBack();
    }

    /**
     * Check if currently inside a transaction
     * @return bool
     */
    public function inTransaction() {
        return $this->conn->inTransaction();
    }
}
