<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Payment class methods here
}
