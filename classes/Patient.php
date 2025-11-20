<?php
require_once __DIR__ . '/Entity.php';

class Patient extends Entity {
    // Private properties - Encapsulation
    private $pat_id;
    private $pat_first_name;
    private $pat_last_name;
    private $pat_email;
    private $pat_phone;
    private $pat_date_of_birth;
    private $pat_gender;
    private $pat_address;
    private $pat_emergency_contact;
    private $pat_emergency_phone;
    private $pat_medical_history;
    private $pat_allergies;
    private $pat_insurance_provider;
    private $pat_insurance_number;
    private $created_at;
    private $updated_at;

    public function __construct($data = []) {
        parent::__construct();
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    // Abstract method implementations
    protected function getTableName(): string {
        return 'patients';
    }

    protected function getPrimaryKey(): string {
        return 'pat_id';
    }

    protected function getColumns(): array {
        return [
            'pat_id', 'pat_first_name', 'pat_last_name', 'pat_email', 'pat_phone',
            'pat_date_of_birth', 'pat_gender', 'pat_address',
            'pat_emergency_contact', 'pat_emergency_phone',
            'pat_medical_history', 'pat_allergies',
            'pat_insurance_provider', 'pat_insurance_number',
            'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['pat_first_name'])) {
            $errors[] = 'First name is required.';
        }
        if (empty($data['pat_last_name'])) {
            $errors[] = 'Last name is required.';
        }
        if (empty($data['pat_email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['pat_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        // Check email uniqueness
        if ($isNew || (isset($data['pat_id']) && isset($data['pat_email']))) {
            $existing = $this->db->fetchOne(
                "SELECT pat_id FROM patients WHERE pat_email = :email" . 
                ($isNew ? '' : " AND pat_id != :id"),
                $isNew ? ['email' => $data['pat_email']] : ['email' => $data['pat_email'], 'id' => $data['pat_id']]
            );
            if ($existing) {
                $errors[] = 'Email already exists.';
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'pat_id' => $this->pat_id,
            'pat_first_name' => $this->pat_first_name,
            'pat_last_name' => $this->pat_last_name,
            'pat_email' => $this->pat_email,
            'pat_phone' => $this->pat_phone,
            'pat_date_of_birth' => $this->pat_date_of_birth,
            'pat_gender' => $this->pat_gender,
            'pat_address' => $this->pat_address,
            'pat_emergency_contact' => $this->pat_emergency_contact,
            'pat_emergency_phone' => $this->pat_emergency_phone,
            'pat_medical_history' => $this->pat_medical_history,
            'pat_allergies' => $this->pat_allergies,
            'pat_insurance_provider' => $this->pat_insurance_provider,
            'pat_insurance_number' => $this->pat_insurance_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->pat_id = $data['pat_id'] ?? null;
        $this->pat_first_name = $data['pat_first_name'] ?? null;
        $this->pat_last_name = $data['pat_last_name'] ?? null;
        $this->pat_email = $data['pat_email'] ?? null;
        $this->pat_phone = $data['pat_phone'] ?? null;
        $this->pat_date_of_birth = $data['pat_date_of_birth'] ?? null;
        $this->pat_gender = $data['pat_gender'] ?? null;
        $this->pat_address = $data['pat_address'] ?? null;
        $this->pat_emergency_contact = $data['pat_emergency_contact'] ?? null;
        $this->pat_emergency_phone = $data['pat_emergency_phone'] ?? null;
        $this->pat_medical_history = $data['pat_medical_history'] ?? null;
        $this->pat_allergies = $data['pat_allergies'] ?? null;
        $this->pat_insurance_provider = $data['pat_insurance_provider'] ?? null;
        $this->pat_insurance_number = $data['pat_insurance_number'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getPatId() { return $this->pat_id; }
    public function getPatFirstName() { return $this->pat_first_name; }
    public function getPatLastName() { return $this->pat_last_name; }
    public function getPatEmail() { return $this->pat_email; }
    public function getPatPhone() { return $this->pat_phone; }
    public function getPatDateOfBirth() { return $this->pat_date_of_birth; }
    public function getPatGender() { return $this->pat_gender; }
    public function getPatAddress() { return $this->pat_address; }
    public function getPatEmergencyContact() { return $this->pat_emergency_contact; }
    public function getPatEmergencyPhone() { return $this->pat_emergency_phone; }
    public function getPatMedicalHistory() { return $this->pat_medical_history; }
    public function getPatAllergies() { return $this->pat_allergies; }
    public function getPatInsuranceProvider() { return $this->pat_insurance_provider; }
    public function getPatInsuranceNumber() { return $this->pat_insurance_number; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setPatId($value) { $this->pat_id = $value; return $this; }
    public function setPatFirstName($value) { $this->pat_first_name = $value; return $this; }
    public function setPatLastName($value) { $this->pat_last_name = $value; return $this; }
    public function setPatEmail($value) { $this->pat_email = $value; return $this; }
    public function setPatPhone($value) { $this->pat_phone = $value; return $this; }
    public function setPatDateOfBirth($value) { $this->pat_date_of_birth = $value; return $this; }
    public function setPatGender($value) { $this->pat_gender = $value; return $this; }
    public function setPatAddress($value) { $this->pat_address = $value; return $this; }
    public function setPatEmergencyContact($value) { $this->pat_emergency_contact = $value; return $this; }
    public function setPatEmergencyPhone($value) { $this->pat_emergency_phone = $value; return $this; }
    public function setPatMedicalHistory($value) { $this->pat_medical_history = $value; return $this; }
    public function setPatAllergies($value) { $this->pat_allergies = $value; return $this; }
    public function setPatInsuranceProvider($value) { $this->pat_insurance_provider = $value; return $this; }
    public function setPatInsuranceNumber($value) { $this->pat_insurance_number = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get all patients with optional search (maintains backward compatibility)
    public function getAll($search = '') {
        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE pat_first_name ILIKE :search OR pat_last_name ILIKE :search OR pat_email ILIKE :search";
            $params['search'] = "%$search%";
        }

        return $this->db->fetchAll("SELECT * FROM patients $whereClause ORDER BY pat_id DESC", $params);
    }

    // Get patient by ID (maintains backward compatibility)
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM patients WHERE pat_id = :id", ['id' => $id]);
    }

    // Create new patient (maintains backward compatibility)
    public function create($data) {
        $this->fromArray($data);
        return $this->save();
    }

    // Update patient (maintains backward compatibility)
    public function update($id, array $data): array {
        $data['pat_id'] = $id;
        $this->fromArray($data);
        // Call parent's protected updateEntity method with correct parameter order
        return parent::updateEntity($data, $id);
    }

    // Delete patient (maintains backward compatibility)
    public function delete($id = null) {
        if ($id !== null) {
            $this->pat_id = $id;
        }
        return parent::delete($id);
    }
}
