<?php
require_once __DIR__ . '/../config/Database.php';

class Patient {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all patients with optional search
    public function getAll($search = '') {
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = "WHERE pat_first_name ILIKE :search OR pat_last_name ILIKE :search OR pat_email ILIKE :search";
            $params['search'] = "%$search%";
        }
        
        return $this->db->fetchAll("SELECT * FROM patients $whereClause ORDER BY pat_id DESC", $params);
    }
    
    // Get patient by ID
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM patients WHERE pat_id = :id", ['id' => $id]);
    }
    
    // Create new patient
    public function create($data) {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email already exists
        $existing = $this->db->fetchOne("SELECT pat_id FROM patients WHERE pat_email = :email", 
                                      ['email' => $data['pat_email']]);
        if ($existing) {
            return ['success' => false, 'errors' => ['Email already exists.']];
        }
        
        try {
            $sql = "INSERT INTO patients (pat_first_name, pat_last_name, pat_email, pat_phone, 
                    pat_date_of_birth, pat_gender, pat_address, pat_emergency_contact, 
                    pat_emergency_phone, pat_medical_history, pat_allergies, 
                    pat_insurance_provider, pat_insurance_number) 
                    VALUES (:first_name, :last_name, :email, :phone, :dob, :gender, :address, 
                    :emergency_contact, :emergency_phone, :medical_history, :allergies, 
                    :insurance_provider, :insurance_number)";
            
            $this->db->execute($sql, [
                'first_name' => $data['pat_first_name'],
                'last_name' => $data['pat_last_name'],
                'email' => $data['pat_email'],
                'phone' => $data['pat_phone'] ?: null,
                'dob' => $data['pat_date_of_birth'] ?: null,
                'gender' => $data['pat_gender'] ?: null,
                'address' => $data['pat_address'] ?: null,
                'emergency_contact' => $data['pat_emergency_contact'] ?: null,
                'emergency_phone' => $data['pat_emergency_phone'] ?: null,
                'medical_history' => $data['pat_medical_history'] ?: null,
                'allergies' => $data['pat_allergies'] ?: null,
                'insurance_provider' => $data['pat_insurance_provider'] ?: null,
                'insurance_number' => $data['pat_insurance_number'] ?: null
            ]);
            
            $patientId = $this->db->lastInsertId();
            return ['success' => true, 'id' => $patientId];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to add patient: ' . $e->getMessage()]];
        }
    }
    
    // Update patient
    public function update($id, $data) {
        $errors = $this->validate($data, $id);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email already exists (excluding current patient)
        $existing = $this->db->fetchOne("SELECT pat_id FROM patients WHERE pat_email = :email AND pat_id != :id", 
                                      ['email' => $data['pat_email'], 'id' => $id]);
        if ($existing) {
            return ['success' => false, 'errors' => ['Email already exists.']];
        }
        
        try {
            $sql = "UPDATE patients SET 
                    pat_first_name = :first_name, 
                    pat_last_name = :last_name, 
                    pat_email = :email, 
                    pat_phone = :phone, 
                    pat_date_of_birth = :dob, 
                    pat_gender = :gender, 
                    pat_address = :address, 
                    pat_emergency_contact = :emergency_contact, 
                    pat_emergency_phone = :emergency_phone, 
                    pat_medical_history = :medical_history, 
                    pat_allergies = :allergies, 
                    pat_insurance_provider = :insurance_provider, 
                    pat_insurance_number = :insurance_number
                    WHERE pat_id = :id";
            
            $this->db->execute($sql, [
                'id' => $id,
                'first_name' => $data['pat_first_name'],
                'last_name' => $data['pat_last_name'],
                'email' => $data['pat_email'],
                'phone' => $data['pat_phone'] ?: null,
                'dob' => $data['pat_date_of_birth'] ?: null,
                'gender' => $data['pat_gender'] ?: null,
                'address' => $data['pat_address'] ?: null,
                'emergency_contact' => $data['pat_emergency_contact'] ?: null,
                'emergency_phone' => $data['pat_emergency_phone'] ?: null,
                'medical_history' => $data['pat_medical_history'] ?: null,
                'allergies' => $data['pat_allergies'] ?: null,
                'insurance_provider' => $data['pat_insurance_provider'] ?: null,
                'insurance_number' => $data['pat_insurance_number'] ?: null
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update patient: ' . $e->getMessage()]];
        }
    }
    
    // Delete patient
    public function delete($id) {
        try {
            $this->db->execute("DELETE FROM patients WHERE pat_id = :id", ['id' => $id]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete patient: ' . $e->getMessage()]];
        }
    }
    
    // Validate patient data
    private function validate($data, $excludeId = null) {
        $errors = [];
        
        if (empty($data['pat_first_name'])) $errors[] = 'First name is required.';
        if (empty($data['pat_last_name'])) $errors[] = 'Last name is required.';
        if (empty($data['pat_email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['pat_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        
        return $errors;
    }
}
