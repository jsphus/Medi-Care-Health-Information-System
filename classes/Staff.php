<?php
require_once __DIR__ . '/../config/Database.php';

class Staff {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all staff with optional search
    public function getAll($search = '') {
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = "WHERE staff_first_name ILIKE :search OR staff_last_name ILIKE :search OR staff_email ILIKE :search";
            $params['search'] = "%$search%";
        }
        
        return $this->db->fetchAll("SELECT * FROM staff $whereClause ORDER BY staff_id DESC", $params);
    }
    
    // Get staff by ID
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM staff WHERE staff_id = :id", ['id' => $id]);
    }
    
    // Create new staff
    public function create($data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email already exists
        $existing = $this->db->fetchOne("SELECT staff_id FROM staff WHERE staff_email = :email", 
                                      ['email' => $data['staff_email']]);
        if ($existing) {
            return ['success' => false, 'errors' => ['Email already exists.']];
        }
        
        try {
            $sql = "INSERT INTO staff (staff_first_name, staff_last_name, staff_email, staff_phone, 
                    staff_position, staff_hire_date, staff_salary, staff_status) 
                    VALUES (:first_name, :last_name, :email, :phone, :position, :hire_date, :salary, :status)";
            
            $this->db->execute($sql, [
                'first_name' => $data['staff_first_name'],
                'last_name' => $data['staff_last_name'],
                'email' => $data['staff_email'],
                'phone' => $data['staff_phone'] ?: null,
                'position' => $data['staff_position'] ?: null,
                'hire_date' => $data['staff_hire_date'] ?: null,
                'salary' => $data['staff_salary'] ?: null,
                'status' => $data['staff_status']
            ]);
            
            $staffId = $this->db->lastInsertId();
            return ['success' => true, 'id' => $staffId];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to add staff member: ' . $e->getMessage()]];
        }
    }
    
    // Update staff
    public function update($id, $data) {
        $errors = $this->validate($data, $id);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email already exists (excluding current staff)
        $existing = $this->db->fetchOne("SELECT staff_id FROM staff WHERE staff_email = :email AND staff_id != :id", 
                                      ['email' => $data['staff_email'], 'id' => $id]);
        if ($existing) {
            return ['success' => false, 'errors' => ['Email already exists.']];
        }
        
        try {
            $sql = "UPDATE staff SET 
                    staff_first_name = :first_name, 
                    staff_last_name = :last_name, 
                    staff_email = :email, 
                    staff_phone = :phone, 
                    staff_position = :position, 
                    staff_hire_date = :hire_date, 
                    staff_salary = :salary, 
                    staff_status = :status
                    WHERE staff_id = :id";
            
            $this->db->execute($sql, [
                'id' => $id,
                'first_name' => $data['staff_first_name'],
                'last_name' => $data['staff_last_name'],
                'email' => $data['staff_email'],
                'phone' => $data['staff_phone'] ?: null,
                'position' => $data['staff_position'] ?: null,
                'hire_date' => $data['staff_hire_date'] ?: null,
                'salary' => $data['staff_salary'] ?: null,
                'status' => $data['staff_status']
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update staff member: ' . $e->getMessage()]];
        }
    }
    
    // Delete staff
    public function delete($id) {
        try {
            $this->db->execute("DELETE FROM staff WHERE staff_id = :id", ['id' => $id]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete staff member: ' . $e->getMessage()]];
        }
    }
    
    // Validate staff data
    private function validate($data, $excludeId = null) {
        $errors = [];
        
        if (empty($data['staff_first_name'])) $errors[] = 'First name is required.';
        if (empty($data['staff_last_name'])) $errors[] = 'Last name is required.';
        if (empty($data['staff_email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['staff_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        
        return $errors;
    }
}
