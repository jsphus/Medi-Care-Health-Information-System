<?php
require_once __DIR__ . '/../config/Database.php';

class Service {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all services
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM services ORDER BY service_name");
    }
    
    // Get service by ID
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM services WHERE service_id = :id", ['id' => $id]);
    }
    
    // Create new service
    public function create($data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $sql = "INSERT INTO services (service_name, service_description, service_price, 
                    service_duration_minutes, service_category) 
                    VALUES (:name, :description, :price, :duration, :category)";
            
            $this->db->execute($sql, [
                'name' => $data['service_name'],
                'description' => $data['service_description'] ?: null,
                'price' => $data['service_price'] ?: 0,
                'duration' => $data['service_duration_minutes'] ?: 30,
                'category' => $data['service_category'] ?: null
            ]);
            
            $serviceId = $this->db->lastInsertId();
            return ['success' => true, 'id' => $serviceId];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to add service: ' . $e->getMessage()]];
        }
    }
    
    // Update service
    public function update($id, $data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $sql = "UPDATE services SET 
                    service_name = :name, 
                    service_description = :description, 
                    service_price = :price, 
                    service_duration_minutes = :duration, 
                    service_category = :category
                    WHERE service_id = :id";
            
            $this->db->execute($sql, [
                'id' => $id,
                'name' => $data['service_name'],
                'description' => $data['service_description'] ?: null,
                'price' => $data['service_price'] ?: 0,
                'duration' => $data['service_duration_minutes'] ?: 30,
                'category' => $data['service_category'] ?: null
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update service: ' . $e->getMessage()]];
        }
    }
    
    // Delete service
    public function delete($id) {
        try {
            $this->db->execute("DELETE FROM services WHERE service_id = :id", ['id' => $id]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete service: ' . $e->getMessage()]];
        }
    }
    
    // Validate service data
    private function validate($data) {
        $errors = [];
        
        if (empty($data['service_name'])) $errors[] = 'Service name is required.';
        if (!empty($data['service_price']) && !is_numeric($data['service_price'])) {
            $errors[] = 'Service price must be a valid number.';
        }
        if (!empty($data['service_duration_minutes']) && !is_numeric($data['service_duration_minutes'])) {
            $errors[] = 'Service duration must be a valid number.';
        }
        
        return $errors;
    }
}
