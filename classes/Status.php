<?php
require_once __DIR__ . '/../config/database.php';

class Status {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Status class methods here
}
