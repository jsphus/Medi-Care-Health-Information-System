<?php
require_once __DIR__ . '/../config/Database.php';

class Schedule {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Schedule class methods here
}
