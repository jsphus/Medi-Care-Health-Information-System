<?php
require_once __DIR__ . '/../config/Database.php';

class MedicalRecord {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your MedicalRecord class methods here
}
