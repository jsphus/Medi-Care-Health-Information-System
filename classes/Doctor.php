<?php
require_once __DIR__ . '/Entity.php';

class Doctor extends Entity {
    // Private properties - Encapsulation
    private $doc_id;
    private $doc_first_name;
    private $doc_middle_initial;
    private $doc_last_name;
    private $doc_email;
    private $doc_phone;
    private $doc_license_number;
    private $doc_specialization_id;
    private $doc_experience_years;
    private $doc_consultation_fee;
    private $doc_qualification;
    private $doc_bio;
    private $doc_status;
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
        return 'doctors';
    }

    protected function getPrimaryKey(): string {
        return 'doc_id';
    }

    protected function getColumns(): array {
        return [
            'doc_id', 'doc_first_name', 'doc_middle_initial', 'doc_last_name', 'doc_email', 'doc_phone',
            'doc_license_number', 'doc_specialization_id', 'doc_experience_years',
            'doc_consultation_fee', 'doc_qualification', 'doc_bio', 'doc_status',
            'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['doc_first_name'])) {
            $errors[] = 'First name is required.';
        }
        if (empty($data['doc_last_name'])) {
            $errors[] = 'Last name is required.';
        }
        if (empty($data['doc_email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['doc_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        // Get existing record if updating to compare values
        $existingRecord = null;
        if (!$isNew && isset($data['doc_id'])) {
            $existingRecord = $this->db->fetchOne(
                "SELECT doc_email, doc_license_number FROM doctors WHERE doc_id = :id",
                ['id' => $data['doc_id']]
            );
        }

        // Check email uniqueness - only if it's new or if email has changed
        if ($isNew || (isset($data['doc_id']) && isset($data['doc_email']))) {
            $emailChanged = $isNew || !$existingRecord || ($existingRecord['doc_email'] !== $data['doc_email']);
            
            if ($emailChanged) {
                $existing = $this->db->fetchOne(
                    "SELECT doc_id FROM doctors WHERE doc_email = :email" . 
                    ($isNew ? '' : " AND doc_id != :id"),
                    $isNew ? ['email' => $data['doc_email']] : ['email' => $data['doc_email'], 'id' => $data['doc_id']]
                );
                if ($existing) {
                    $errors[] = 'Email already exists.';
                }
            }
        }

        // Check license number uniqueness if provided - only if it's new or if license has changed
        if (!empty($data['doc_license_number'])) {
            $licenseChanged = $isNew || !$existingRecord || 
                (($existingRecord['doc_license_number'] ?? '') !== $data['doc_license_number']);
            
            if ($licenseChanged) {
                $existing = $this->db->fetchOne(
                    "SELECT doc_id FROM doctors WHERE doc_license_number = :license" . 
                    ($isNew ? '' : " AND doc_id != :id"),
                    $isNew ? ['license' => $data['doc_license_number']] : ['license' => $data['doc_license_number'], 'id' => $data['doc_id']]
                );
                if ($existing) {
                    $errors[] = 'License number already exists.';
                }
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'doc_id' => $this->doc_id,
            'doc_first_name' => $this->doc_first_name,
            'doc_middle_initial' => $this->doc_middle_initial,
            'doc_last_name' => $this->doc_last_name,
            'doc_email' => $this->doc_email,
            'doc_phone' => $this->doc_phone,
            'doc_license_number' => $this->doc_license_number,
            'doc_specialization_id' => $this->doc_specialization_id,
            'doc_experience_years' => $this->doc_experience_years,
            'doc_consultation_fee' => $this->doc_consultation_fee,
            'doc_qualification' => $this->doc_qualification,
            'doc_bio' => $this->doc_bio,
            'doc_status' => $this->doc_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->doc_id = $data['doc_id'] ?? null;
        $this->doc_first_name = $data['doc_first_name'] ?? null;
        $this->doc_middle_initial = $data['doc_middle_initial'] ?? null;
        $this->doc_last_name = $data['doc_last_name'] ?? null;
        $this->doc_email = $data['doc_email'] ?? null;
        $this->doc_phone = $data['doc_phone'] ?? null;
        $this->doc_license_number = $data['doc_license_number'] ?? null;
        $this->doc_specialization_id = $data['doc_specialization_id'] ?? null;
        $this->doc_experience_years = $data['doc_experience_years'] ?? null;
        $this->doc_consultation_fee = $data['doc_consultation_fee'] ?? null;
        $this->doc_qualification = $data['doc_qualification'] ?? null;
        $this->doc_bio = $data['doc_bio'] ?? null;
        $this->doc_status = $data['doc_status'] ?? 'active';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getDocId() { return $this->doc_id; }
    public function getDocFirstName() { return $this->doc_first_name; }
    public function getDocMiddleInitial() { return $this->doc_middle_initial; }
    public function getDocLastName() { return $this->doc_last_name; }
    public function getDocEmail() { return $this->doc_email; }
    public function getDocPhone() { return $this->doc_phone; }
    public function getDocLicenseNumber() { return $this->doc_license_number; }
    public function getDocSpecializationId() { return $this->doc_specialization_id; }
    public function getDocExperienceYears() { return $this->doc_experience_years; }
    public function getDocConsultationFee() { return $this->doc_consultation_fee; }
    public function getDocQualification() { return $this->doc_qualification; }
    public function getDocBio() { return $this->doc_bio; }
    public function getDocStatus() { return $this->doc_status; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setDocId($value) { $this->doc_id = $value; return $this; }
    public function setDocFirstName($value) { $this->doc_first_name = $value; return $this; }
    public function setDocMiddleInitial($value) { $this->doc_middle_initial = $value; return $this; }
    public function setDocLastName($value) { $this->doc_last_name = $value; return $this; }
    public function setDocEmail($value) { $this->doc_email = $value; return $this; }
    public function setDocPhone($value) { $this->doc_phone = $value; return $this; }
    public function setDocLicenseNumber($value) { $this->doc_license_number = $value; return $this; }
    public function setDocSpecializationId($value) { $this->doc_specialization_id = $value; return $this; }
    public function setDocExperienceYears($value) { $this->doc_experience_years = $value; return $this; }
    public function setDocConsultationFee($value) { $this->doc_consultation_fee = $value; return $this; }
    public function setDocQualification($value) { $this->doc_qualification = $value; return $this; }
    public function setDocBio($value) { $this->doc_bio = $value; return $this; }
    public function setDocStatus($value) { $this->doc_status = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get all doctors (maintains backward compatibility)
    public function getAll() {
        return $this->db->fetchAll("SELECT doc_id, doc_first_name, doc_last_name FROM doctors ORDER BY created_at DESC, doc_first_name, doc_last_name");
    }

    // Get doctor by ID (maintains backward compatibility)
    public function getById($id) {
        // Explicitly select all columns to ensure all fields are returned
        $sql = "SELECT 
            doc_id, 
            doc_first_name, 
            doc_middle_initial, 
            doc_last_name, 
            doc_email, 
            doc_phone, 
            doc_license_number, 
            doc_specialization_id, 
            doc_experience_years, 
            doc_consultation_fee, 
            doc_qualification, 
            doc_bio, 
            doc_status, 
            created_at, 
            updated_at 
        FROM doctors 
        WHERE doc_id = :id";
        
        $result = $this->db->fetchOne($sql, ['id' => $id]);
        
        // If result has fewer columns than expected, try alternative approach
        if ($result !== null && count($result) < 5) {
            try {
                $conn = $this->db->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute(['id' => $id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Doctor::getById() alternative fetch error: " . $e->getMessage());
            }
        }
        
        return $result;
    }

    // Create new doctor (maintains backward compatibility)
    public function create($data) {
        $this->fromArray($data);
        return $this->save();
    }

    // Update doctor (maintains backward compatibility)
    public function update($id, $data) {
        $data['doc_id'] = $id;
        $this->fromArray($data);
        return $this->save();
    }

    // Delete doctor (maintains backward compatibility)
    public function delete($id = null): array {
        if ($id !== null) {
            $this->doc_id = $id;
        }
        return parent::delete($id);
    }

    public function getDetailsById(int $doctorId) {
        return $this->db->fetchOne("
            SELECT d.*, s.spec_name, u.profile_picture_url
            FROM doctors d
            LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
            LEFT JOIN users u ON d.doc_id = u.doc_id
            WHERE d.doc_id = :doctor_id
        ", ['doctor_id' => $doctorId]);
    }

    public function searchDoctors(array $options = []): array {
        $search = $options['search'] ?? '';
        $specialization = $options['specialization'] ?? null;
        $appointment_date = $options['appointment_date'] ?? null;
        $appointment_time = $options['appointment_time'] ?? null;

        $where = ["d.doc_status = 'active'"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(d.doc_first_name ILIKE :search OR d.doc_middle_initial ILIKE :search OR d.doc_last_name ILIKE :search OR s.spec_name ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($specialization)) {
            $where[] = "d.doc_specialization_id = :specialization";
            $params['specialization'] = $specialization;
        }

        // Filter by availability if date and time are provided
        if (!empty($appointment_date) && !empty($appointment_time)) {
            // Only show doctors who have an available schedule slot for the requested date/time
            $where[] = "EXISTS (
                SELECT 1 FROM schedules sch
                WHERE sch.doc_id = d.doc_id
                AND sch.schedule_date = :appointment_date
                AND sch.start_time <= :appointment_time
                AND sch.end_time > :appointment_time
            )";
            $params['appointment_date'] = $appointment_date;
            $params['appointment_time'] = $appointment_time;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        return $this->db->fetchAll("
            SELECT DISTINCT ON (d.doc_id) d.*, s.spec_name, u.profile_picture_url
            FROM doctors d
            LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
            LEFT JOIN users u ON d.doc_id = u.doc_id
            $whereClause
            ORDER BY d.doc_id, d.doc_first_name, d.doc_last_name
        ", $params);
    }

    /**
     * Search doctors with full details including specialization description (optimized single query)
     * @param array $options Search and filter options
     * @return array Array of doctors with all details
     */
    public function searchDoctorsWithDetails(array $options = []): array {
        $search = $options['search'] ?? '';
        $specialization = $options['specialization'] ?? null;
        $appointment_date = $options['appointment_date'] ?? null;
        $appointment_time = $options['appointment_time'] ?? null;

        $where = ["d.doc_status = 'active'"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(d.doc_first_name ILIKE :search OR d.doc_middle_initial ILIKE :search OR d.doc_last_name ILIKE :search OR s.spec_name ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($specialization)) {
            $where[] = "d.doc_specialization_id = :specialization";
            $params['specialization'] = $specialization;
        }

        // Filter by availability if date and time are provided
        if (!empty($appointment_date) && !empty($appointment_time)) {
            // Only show doctors who have an available schedule slot for the requested date/time
            $where[] = "EXISTS (
                SELECT 1 FROM schedules sch
                WHERE sch.doc_id = d.doc_id
                AND sch.schedule_date = :appointment_date
                AND sch.start_time <= :appointment_time
                AND sch.end_time > :appointment_time
            )";
            $params['appointment_date'] = $appointment_date;
            $params['appointment_time'] = $appointment_time;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        // Single query to get all doctor details including specialization description
        // Use DISTINCT ON to ensure no duplicate doctors (in case of multiple user records)
        return $this->db->fetchAll("
            SELECT DISTINCT ON (d.doc_id) d.*, 
                   s.spec_name, 
                   s.spec_description,
                   u.profile_picture_url
            FROM doctors d
            LEFT JOIN specializations s ON d.doc_specialization_id = s.spec_id
            LEFT JOIN users u ON d.doc_id = u.doc_id
            $whereClause
            ORDER BY d.doc_id, d.doc_first_name, d.doc_last_name
        ", $params);
    }
}
