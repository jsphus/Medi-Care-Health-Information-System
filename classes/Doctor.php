<?php
require_once __DIR__ . '/../config/Database.php';

class Doctor {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all doctors
    public function getAll() {
        return $this->db->fetchAll("SELECT doc_id, doc_first_name, doc_last_name FROM doctors WHERE doc_status = 'active' ORDER BY doc_first_name, doc_last_name");
    }
    
    // Get doctor by ID
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM doctors WHERE doc_id = :id", ['id' => $id]);
    }
    
    // Create new doctor
    public function create($data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $sql = "INSERT INTO doctors (doc_first_name, doc_last_name, doc_email, doc_phone, 
                    doc_specialization, doc_license_number, doc_status) 
                    VALUES (:first_name, :last_name, :email, :phone, :specialization, :license_number, :status)";
            
            $this->db->execute($sql, [
                'first_name' => $data['doc_first_name'],
                'last_name' => $data['doc_last_name'],
                'email' => $data['doc_email'],
                'phone' => $data['doc_phone'] ?: null,
                'specialization' => $data['doc_specialization'] ?: null,
                'license_number' => $data['doc_license_number'] ?: null,
                'status' => $data['doc_status'] ?: 'active'
            ]);
            
            $doctorId = $this->db->lastInsertId();
            return ['success' => true, 'id' => $doctorId];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to add doctor: ' . $e->getMessage()]];
        }
    }
    
    // Update doctor
    public function update($id, $data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            $sql = "UPDATE doctors SET 
                    doc_first_name = :first_name, 
                    doc_last_name = :last_name, 
                    doc_email = :email, 
                    doc_phone = :phone, 
                    doc_specialization = :specialization, 
                    doc_license_number = :license_number, 
                    doc_status = :status
                    WHERE doc_id = :id";
            
            $this->db->execute($sql, [
                'id' => $id,
                'first_name' => $data['doc_first_name'],
                'last_name' => $data['doc_last_name'],
                'email' => $data['doc_email'],
                'phone' => $data['doc_phone'] ?: null,
                'specialization' => $data['doc_specialization'] ?: null,
                'license_number' => $data['doc_license_number'] ?: null,
                'status' => $data['doc_status'] ?: 'active'
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update doctor: ' . $e->getMessage()]];
        }
    }
    
    // Delete doctor
    public function delete($id) {
        try {
            $this->db->execute("DELETE FROM doctors WHERE doc_id = :id", ['id' => $id]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete doctor: ' . $e->getMessage()]];
        }
    }
    
    // Validate doctor data
    private function validate($data) {
        $errors = [];
        
        if (empty($data['doc_first_name'])) $errors[] = 'First name is required.';
        if (empty($data['doc_last_name'])) $errors[] = 'Last name is required.';
        if (empty($data['doc_email'])) $errors[] = 'Email is required.';
        
        return $errors;
    }
}
