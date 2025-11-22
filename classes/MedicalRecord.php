<?php
require_once __DIR__ . '/Entity.php';

class MedicalRecord extends Entity {
    // Private properties - Encapsulation
    private $med_rec_id;
    private $appt_id;
    private $med_rec_diagnosis;
    private $med_rec_prescription;
    private $med_rec_visit_date;
    private $med_rec_created_at;
    private $med_rec_updated_at;

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
        return 'med_rec_id';
    }

    protected function getColumns(): array {
        return [
            'med_rec_id', 'appt_id', 'med_rec_diagnosis', 'med_rec_prescription',
            'med_rec_visit_date', 'med_rec_created_at', 'med_rec_updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['appt_id'])) {
            $errors[] = 'Appointment is required.';
        }
        if (empty($data['med_rec_visit_date'])) {
            $errors[] = 'Visit date is required.';
        }

        // Validate appointment exists
        if (!empty($data['appt_id'])) {
            $appointment = $this->db->fetchOne(
                "SELECT appointment_id FROM appointments WHERE appointment_id = :appt_id",
                ['appt_id' => $data['appt_id']]
            );
            if (!$appointment) {
                $errors[] = 'Invalid appointment ID.';
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'med_rec_id' => $this->med_rec_id,
            'appt_id' => $this->appt_id,
            'med_rec_diagnosis' => $this->med_rec_diagnosis,
            'med_rec_prescription' => $this->med_rec_prescription,
            'med_rec_visit_date' => $this->med_rec_visit_date,
            'med_rec_created_at' => $this->med_rec_created_at,
            'med_rec_updated_at' => $this->med_rec_updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->med_rec_id = $data['med_rec_id'] ?? null;
        $this->appt_id = $data['appt_id'] ?? null;
        $this->med_rec_diagnosis = $data['med_rec_diagnosis'] ?? null;
        $this->med_rec_prescription = $data['med_rec_prescription'] ?? null;
        $this->med_rec_visit_date = $data['med_rec_visit_date'] ?? null;
        $this->med_rec_created_at = $data['med_rec_created_at'] ?? null;
        $this->med_rec_updated_at = $data['med_rec_updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getMedRecId() { return $this->med_rec_id; }
    public function getApptId() { return $this->appt_id; }
    public function getMedRecDiagnosis() { return $this->med_rec_diagnosis; }
    public function getMedRecPrescription() { return $this->med_rec_prescription; }
    public function getMedRecVisitDate() { return $this->med_rec_visit_date; }
    public function getMedRecCreatedAt() { return $this->med_rec_created_at; }
    public function getMedRecUpdatedAt() { return $this->med_rec_updated_at; }

    // Setters - Encapsulation
    public function setMedRecId($value) { $this->med_rec_id = $value; return $this; }
    public function setApptId($value) { $this->appt_id = $value; return $this; }
    public function setMedRecDiagnosis($value) { $this->med_rec_diagnosis = $value; return $this; }
    public function setMedRecPrescription($value) { $this->med_rec_prescription = $value; return $this; }
    public function setMedRecVisitDate($value) { $this->med_rec_visit_date = $value; return $this; }
    public function setMedRecCreatedAt($value) { $this->med_rec_created_at = $value; return $this; }
    public function setMedRecUpdatedAt($value) { $this->med_rec_updated_at = $value; return $this; }

    // Get medical records by appointment
    public function getByAppointment($apptId) {
        return $this->db->fetchAll(
            "SELECT * FROM medical_records WHERE appt_id = :appt_id ORDER BY med_rec_visit_date DESC",
            ['appt_id' => $apptId]
        );
    }

    // Get medical records by patient (via appointment)
    public function getByPatient($patId) {
        return $this->db->fetchAll(
            "SELECT mr.*, a.pat_id, a.doc_id, a.appointment_date, a.appointment_time
             FROM medical_records mr
             JOIN appointments a ON mr.appt_id = a.appointment_id
             WHERE a.pat_id = :pat_id 
             ORDER BY mr.med_rec_visit_date DESC",
            ['pat_id' => $patId]
        );
    }

    // Get medical records by doctor (via appointment)
    public function getByDoctor($docId) {
        return $this->db->fetchAll(
            "SELECT mr.*, a.pat_id, a.doc_id, a.appointment_date, a.appointment_time
             FROM medical_records mr
             JOIN appointments a ON mr.appt_id = a.appointment_id
             WHERE a.doc_id = :doc_id 
             ORDER BY mr.med_rec_visit_date DESC",
            ['doc_id' => $docId]
        );
    }

    public function getRecentByPatient(int $patientId, int $limit = 5): array {
        $limit = max(1, (int)$limit);
        return $this->db->fetchAll("
            SELECT mr.med_rec_id,
                   mr.appt_id,
                   mr.med_rec_diagnosis as diagnosis,
                   mr.med_rec_prescription as prescription,
                   mr.med_rec_visit_date,
                   mr.med_rec_visit_date as record_date,
                   mr.med_rec_created_at,
                   mr.med_rec_updated_at,
                   NULL as treatment,
                   NULL as notes,
                   NULL as follow_up_date,
                   a.pat_id, a.doc_id, a.appointment_date, a.appointment_time, a.appointment_id,
                   p.pat_first_name, p.pat_last_name,
                   d.doc_first_name, d.doc_last_name,
                   ud.profile_picture_url as doctor_profile_picture
            FROM medical_records mr
            JOIN appointments a ON mr.appt_id = a.appointment_id
            JOIN patients p ON a.pat_id = p.pat_id
            JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN users ud ON ud.doc_id = d.doc_id
            WHERE a.pat_id = :patient_id
            ORDER BY mr.med_rec_visit_date DESC
            LIMIT {$limit}
        ", ['patient_id' => $patientId]);
    }

    public function searchByPatient(int $patientId, string $search = ''): array {
        $where = ['a.pat_id = :patient_id'];
        $params = ['patient_id' => $patientId];

        if (!empty($search)) {
            $where[] = "(d.doc_first_name ILIKE :search OR d.doc_last_name ILIKE :search OR mr.med_rec_diagnosis ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        return $this->db->fetchAll("
            SELECT mr.med_rec_id,
                   mr.appt_id,
                   mr.med_rec_diagnosis as diagnosis,
                   mr.med_rec_prescription as prescription,
                   mr.med_rec_visit_date,
                   mr.med_rec_visit_date as record_date,
                   mr.med_rec_created_at,
                   mr.med_rec_updated_at,
                   NULL as treatment,
                   NULL as notes,
                   NULL as follow_up_date,
                   a.pat_id, a.doc_id, a.appointment_date, a.appointment_time, a.appointment_id,
                   p.pat_first_name, p.pat_last_name,
                   d.doc_first_name, d.doc_last_name, d.doc_specialization_id,
                   sp.spec_name,
                   ud.profile_picture_url as doctor_profile_picture
            FROM medical_records mr
            JOIN appointments a ON mr.appt_id = a.appointment_id
            JOIN patients p ON a.pat_id = p.pat_id
            JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
            LEFT JOIN users ud ON ud.doc_id = d.doc_id
            $whereClause
            ORDER BY mr.med_rec_visit_date DESC
        ", $params);
    }

    public function getStatsForPatient(int $patientId): array {
        $total = (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count 
             FROM medical_records mr
             JOIN appointments a ON mr.appt_id = a.appointment_id
             WHERE a.pat_id = :patient_id",
            ['patient_id' => $patientId]
        )['count'];

        $thisMonth = (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count 
             FROM medical_records mr
             JOIN appointments a ON mr.appt_id = a.appointment_id
             WHERE a.pat_id = :patient_id 
             AND DATE_TRUNC('month', mr.med_rec_visit_date) = DATE_TRUNC('month', CURRENT_DATE)",
            ['patient_id' => $patientId]
        )['count'];

        return [
            'total' => $total,
            'this_month' => $thisMonth,
            'pending_followup' => 0 // Removed follow_up_date field
        ];
    }
}
