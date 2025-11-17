<?php
require_once __DIR__ . '/../config/Database.php';

class PaymentMethod {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Payment Method class methods here
}
