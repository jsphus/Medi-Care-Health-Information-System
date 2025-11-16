<?php
require_once __DIR__ . '/../config/database.php';

class Schedule {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Schedule class methods here
}
