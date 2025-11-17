<?php
require_once __DIR__ . '/../config/Database.php';

class Specialization {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Specialization class methods here
}
