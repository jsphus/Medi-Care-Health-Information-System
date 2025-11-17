<?php
require_once __DIR__ . '/../config/Database.php';

class PaymentStatus {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Payment Status class methods here
}
