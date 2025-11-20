<?php
require_once __DIR__ . '/Entity.php';

class MedicalRecord extends Entity {
    // Private properties - Encapsulation
    private $record_id;
    private $pat_id;
    private $doc_id;
    private $appointment_id;
    private $record_date;
    private $diagnosis;
    private $treatment;
    private $prescription;
    private $notes;
    private $follow_up_date;
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
        return 'medical_records';
    }

    protected function getPrimaryKey(): string {
        return 'record_id';
    }

    protected function getColumns(): array {
        return [
            'record_id', 'pat_id', 'doc_id', 'appointment_id', 'record_date',
            'diagnosis', 'treatment', 'prescription', 'notes', 'follow_up_date',
            'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['pat_id'])) {
            $errors[] = 'Patient is required.';
        }
        if (empty($data['doc_id'])) {
            $errors[] = 'Doctor is required.';
        }
        if (empty($data['record_date'])) {
            $errors[] = 'Record date is required.';
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'record_id' => $this->record_id,
            'pat_id' => $this->pat_id,
            'doc_id' => $this->doc_id,
            'appointment_id' => $this->appointment_id,
            'record_date' => $this->record_date,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'prescription' => $this->prescription,
            'notes' => $this->notes,
            'follow_up_date' => $this->follow_up_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->record_id = $data['record_id'] ?? null;
        $this->pat_id = $data['pat_id'] ?? null;
        $this->doc_id = $data['doc_id'] ?? null;
        $this->appointment_id = $data['appointment_id'] ?? null;
        $this->record_date = $data['record_date'] ?? null;
        $this->diagnosis = $data['diagnosis'] ?? null;
        $this->treatment = $data['treatment'] ?? null;
        $this->prescription = $data['prescription'] ?? null;
        $this->notes = $data['notes'] ?? null;
        $this->follow_up_date = $data['follow_up_date'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getRecordId() { return $this->record_id; }
    public function getPatId() { return $this->pat_id; }
    public function getDocId() { return $this->doc_id; }
    public function getAppointmentId() { return $this->appointment_id; }
    public function getRecordDate() { return $this->record_date; }
    public function getDiagnosis() { return $this->diagnosis; }
    public function getTreatment() { return $this->treatment; }
    public function getPrescription() { return $this->prescription; }
    public function getNotes() { return $this->notes; }
    public function getFollowUpDate() { return $this->follow_up_date; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setRecordId($value) { $this->record_id = $value; return $this; }
    public function setPatId($value) { $this->pat_id = $value; return $this; }
    public function setDocId($value) { $this->doc_id = $value; return $this; }
    public function setAppointmentId($value) { $this->appointment_id = $value; return $this; }
    public function setRecordDate($value) { $this->record_date = $value; return $this; }
    public function setDiagnosis($value) { $this->diagnosis = $value; return $this; }
    public function setTreatment($value) { $this->treatment = $value; return $this; }
    public function setPrescription($value) { $this->prescription = $value; return $this; }
    public function setNotes($value) { $this->notes = $value; return $this; }
    public function setFollowUpDate($value) { $this->follow_up_date = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get medical records by patient
    public function getByPatient($patId) {
        return $this->db->fetchAll(
            "SELECT * FROM medical_records WHERE pat_id = :pat_id ORDER BY record_date DESC",
            ['pat_id' => $patId]
        );
    }

    // Get medical records by doctor
    public function getByDoctor($docId) {
        return $this->db->fetchAll(
            "SELECT * FROM medical_records WHERE doc_id = :doc_id ORDER BY record_date DESC",
            ['doc_id' => $docId]
        );
    }

    public function getRecentByPatient(int $patientId, int $limit = 5): array {
        $limit = max(1, (int)$limit);
        return $this->db->fetchAll("
            SELECT mr.*, 
                   d.doc_first_name, d.doc_last_name,
                   a.appointment_date, a.appointment_id,
                   ud.profile_picture_url as doctor_profile_picture
            FROM medical_records mr
            LEFT JOIN doctors d ON mr.doc_id = d.doc_id
            LEFT JOIN appointments a ON mr.appointment_id = a.appointment_id
            LEFT JOIN users ud ON ud.doc_id = d.doc_id
            WHERE mr.pat_id = :patient_id
            ORDER BY mr.record_date DESC
            LIMIT {$limit}
        ", ['patient_id' => $patientId]);
    }

    public function searchByPatient(int $patientId, string $search = ''): array {
        $where = ['mr.pat_id = :patient_id'];
        $params = ['patient_id' => $patientId];

        if (!empty($search)) {
            $where[] = "(d.doc_first_name ILIKE :search OR d.doc_last_name ILIKE :search OR mr.diagnosis ILIKE :search OR mr.treatment ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        return $this->db->fetchAll("
            SELECT mr.*, 
                   d.doc_first_name, d.doc_last_name, d.doc_specialization_id,
                   a.appointment_date, a.appointment_id, a.appointment_time,
                   sp.spec_name,
                   ud.profile_picture_url as doctor_profile_picture
            FROM medical_records mr
            LEFT JOIN doctors d ON mr.doc_id = d.doc_id
            LEFT JOIN appointments a ON mr.appointment_id = a.appointment_id
            LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
            LEFT JOIN users ud ON ud.doc_id = d.doc_id
            $whereClause
            ORDER BY mr.record_date DESC
        ", $params);
    }

    public function getStatsForPatient(int $patientId): array {
        $total = (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count FROM medical_records WHERE pat_id = :patient_id",
            ['patient_id' => $patientId]
        )['count'];

        $thisMonth = (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count FROM medical_records WHERE pat_id = :patient_id AND DATE_TRUNC('month', record_date) = DATE_TRUNC('month', CURRENT_DATE)",
            ['patient_id' => $patientId]
        )['count'];

        $pendingFollowup = (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count FROM medical_records WHERE pat_id = :patient_id AND follow_up_date IS NOT NULL AND follow_up_date >= CURRENT_DATE",
            ['patient_id' => $patientId]
        )['count'];

        return [
            'total' => $total,
            'this_month' => $thisMonth,
            'pending_followup' => $pendingFollowup
        ];
    }
}
