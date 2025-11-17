<?php
require_once __DIR__ . '/../config/Database.php';

class AppointmentStatus {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add your Appointment Status class methods here
}
