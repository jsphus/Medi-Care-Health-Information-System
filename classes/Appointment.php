<?php
require_once __DIR__ . '/Entity.php';

class Appointment extends Entity {
    // Private properties - Encapsulation
    private $appointment_id;
    private $pat_id;
    private $doc_id;
    private $service_id;
    private $status_id;
    private $appointment_date;
    private $appointment_time;
    private $appointment_notes;
    private $appointment_duration;
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
        return 'appointments';
    }

    protected function getPrimaryKey(): string {
        return 'appointment_id';
    }

    protected function getColumns(): array {
        return [
            'appointment_id', 'pat_id', 'doc_id', 'service_id', 'status_id',
            'appointment_date', 'appointment_time', 'appointment_notes',
            'appointment_duration', 'created_at', 'updated_at'
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
        if (empty($data['appointment_date'])) {
            $errors[] = 'Appointment date is required.';
        }
        if (empty($data['appointment_time'])) {
            $errors[] = 'Appointment time is required.';
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'appointment_id' => $this->appointment_id,
            'pat_id' => $this->pat_id,
            'doc_id' => $this->doc_id,
            'service_id' => $this->service_id,
            'status_id' => $this->status_id,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'appointment_notes' => $this->appointment_notes,
            'appointment_duration' => $this->appointment_duration,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->appointment_id = $data['appointment_id'] ?? null;
        $this->pat_id = $data['pat_id'] ?? null;
        $this->doc_id = $data['doc_id'] ?? null;
        $this->service_id = $data['service_id'] ?? null;
        $this->status_id = $data['status_id'] ?? null;
        $this->appointment_date = $data['appointment_date'] ?? null;
        $this->appointment_time = $data['appointment_time'] ?? null;
        $this->appointment_notes = $data['appointment_notes'] ?? null;
        $this->appointment_duration = $data['appointment_duration'] ?? 30;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getAppointmentId() { return $this->appointment_id; }
    public function getPatId() { return $this->pat_id; }
    public function getDocId() { return $this->doc_id; }
    public function getServiceId() { return $this->service_id; }
    public function getStatusId() { return $this->status_id; }
    public function getAppointmentDate() { return $this->appointment_date; }
    public function getAppointmentTime() { return $this->appointment_time; }
    public function getAppointmentNotes() { return $this->appointment_notes; }
    public function getAppointmentDuration() { return $this->appointment_duration; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setAppointmentId($value) { $this->appointment_id = $value; return $this; }
    public function setPatId($value) { $this->pat_id = $value; return $this; }
    public function setDocId($value) { $this->doc_id = $value; return $this; }
    public function setServiceId($value) { $this->service_id = $value; return $this; }
    public function setStatusId($value) { $this->status_id = $value; return $this; }
    public function setAppointmentDate($value) { $this->appointment_date = $value; return $this; }
    public function setAppointmentTime($value) { $this->appointment_time = $value; return $this; }
    public function setAppointmentNotes($value) { $this->appointment_notes = $value; return $this; }
    public function setAppointmentDuration($value) { $this->appointment_duration = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    /**
     * Generate formatted appointment ID (e.g., 2025-10-0000010)
     * Moved from functions.php
     * @param Database|null $db Database instance (optional, uses singleton if not provided)
     * @return string Generated appointment ID
     */
    public static function generateId($db = null) {
        if ($db === null) {
            $db = Database::getInstance();
        }
        
        $year = date('Y');
        $month = date('m');
        $prefix = "$year-$month-";
        
        try {
            // Get the last appointment ID for this month
            $result = $db->fetchOne("SELECT appointment_id FROM appointments WHERE appointment_id LIKE :prefix ORDER BY appointment_id DESC LIMIT 1", ['prefix' => $prefix . '%']);
            
            if ($result) {
                $lastNum = (int)substr($result['appointment_id'], -7);
                $newNum = $lastNum + 1;
            } else {
                $newNum = 1;
            }
            
            return $prefix . str_pad($newNum, 7, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            return $prefix . str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Override insert to generate appointment ID
     */
    protected function insert(array $data): array {
        // Generate appointment ID if not provided
        if (empty($data['appointment_id'])) {
            $data['appointment_id'] = self::generateId($this->db);
        }
        
        return parent::insert($data);
    }

    // Get all appointments with joined details
    public function getAll($search = '') {
        $whereClause = 'WHERE 1=1';
        $params = [];

        if (!empty($search)) {
            $whereClause .= " AND a.appointment_id ILIKE :search";
            $params['search'] = "%$search%";
        }

        return $this->db->fetchAll("
            SELECT a.*, 
                   p.pat_first_name, p.pat_last_name,
                   d.doc_first_name, d.doc_last_name,
                   s.status_name, s.status_color,
                   srv.service_name
            FROM appointments a
            JOIN patients p ON a.pat_id = p.pat_id
            JOIN doctors d ON a.doc_id = d.doc_id
            JOIN appointment_statuses s ON a.status_id = s.status_id
            LEFT JOIN services srv ON a.service_id = srv.service_id
            $whereClause
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ", $params);
    }

    // Get appointment by ID with joined details
    public function getById($id) {
        return $this->db->fetchOne("
            SELECT a.*, 
                   p.pat_first_name, p.pat_last_name,
                   d.doc_first_name, d.doc_last_name,
                   s.status_name, s.status_color,
                   srv.service_name, srv.service_price
            FROM appointments a
            JOIN patients p ON a.pat_id = p.pat_id
            JOIN doctors d ON a.doc_id = d.doc_id
            JOIN appointment_statuses s ON a.status_id = s.status_id
            LEFT JOIN services srv ON a.service_id = srv.service_id
            WHERE a.appointment_id = :appointment_id
        ", ['appointment_id' => $id]);
    }

    // Create new appointment (maintains backward compatibility)
    public function create($data) {
        $this->fromArray($data);
        return $this->save();
    }

    // Update appointment (maintains backward compatibility)
    public function update($data) {
        if (isset($data['appointment_id'])) {
            $this->fromArray($data);
            return $this->save();
        }
        return ['success' => false, 'errors' => ['Appointment ID is required for update']];
    }

    // Delete appointment (maintains backward compatibility)
    public function delete($id = null) {
        if ($id !== null) {
            $this->appointment_id = $id;
        }
        return parent::delete($id);
    }

    // Cancel appointment (status_id = 3 = "Cancelled")
    public function cancel($id) {
        try {
            $this->db->execute("
                UPDATE appointments 
                SET status_id = 3, updated_at = NOW() 
                WHERE appointment_id = :id
            ", ['id' => $id]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to cancel appointment: ' . $e->getMessage()]];
        }
    }

    /**
     * Cancel an appointment owned by a patient
     */
    public function cancelForPatient(string $appointmentId, int $patientId): array {
        try {
            $appointment = $this->db->fetchOne("
                SELECT appointment_id, pat_id, status_id 
                FROM appointments 
                WHERE appointment_id = :appointment_id",
                ['appointment_id' => $appointmentId]
            );

            if (!$appointment) {
                return ['success' => false, 'error' => 'Appointment not found'];
            }

            if ((int)$appointment['pat_id'] !== $patientId) {
                return ['success' => false, 'error' => 'You do not have permission to cancel this appointment'];
            }

            $status = $this->db->fetchOne("
                SELECT status_name 
                FROM appointment_statuses 
                WHERE status_id = :status_id",
                ['status_id' => $appointment['status_id']]
            );

            if ($status && in_array(strtolower($status['status_name']), ['cancelled', 'completed'])) {
                return ['success' => false, 'error' => 'This appointment cannot be cancelled'];
            }

            $cancelledStatus = $this->db->fetchOne("
                SELECT status_id FROM appointment_statuses 
                WHERE LOWER(status_name) = 'cancelled' LIMIT 1");

            if (!$cancelledStatus) {
                return ['success' => false, 'error' => 'Cancelled status not found in system'];
            }

            $this->db->execute("
                UPDATE appointments 
                SET status_id = :status_id, updated_at = NOW() 
                WHERE appointment_id = :appointment_id",
                [
                    'status_id' => $cancelledStatus['status_id'],
                    'appointment_id' => $appointmentId
                ]
            );

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Failed to cancel appointment: ' . $e->getMessage()];
        }
    }

    /**
     * Get appointments for a patient with optional filters
     */
    public function getForPatient(int $patientId, array $options = []): array {
        $search = $options['search'] ?? '';
        $statusId = $options['status'] ?? null;

        $whereConditions = ['a.pat_id = :patient_id'];
        $params = ['patient_id' => $patientId];

        if (!empty($search)) {
            $whereConditions[] = "(d.doc_first_name ILIKE :search OR d.doc_last_name ILIKE :search OR s.service_name ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($statusId)) {
            $whereConditions[] = "a.status_id = :status_id";
            $params['status_id'] = $statusId;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

        $appointments = $this->db->fetchAll("
            SELECT a.*, 
                   d.doc_first_name, d.doc_last_name, d.doc_specialization_id,
                   s.service_name, s.service_price,
                   st.status_name, st.status_color,
                   sp.spec_name,
                   ud.profile_picture_url as doctor_profile_picture
            FROM appointments a
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
            LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
            LEFT JOIN users ud ON ud.doc_id = d.doc_id
            $whereClause
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ", $params);

        $today = date('Y-m-d');
        $upcoming = array_filter($appointments, fn($apt) => $apt['appointment_date'] >= $today);
        $past = array_filter($appointments, fn($apt) => $apt['appointment_date'] < $today);

        return [
            'all' => $appointments,
            'upcoming' => array_values($upcoming),
            'past' => array_values($past)
        ];
    }

    /**
     * Get distinct statuses used by a patient's appointments
     */
    public function getPatientStatusFilters(int $patientId): array {
        return $this->db->fetchAll("
            SELECT DISTINCT st.status_id, st.status_name 
            FROM appointments a 
            JOIN appointment_statuses st ON a.status_id = st.status_id 
            WHERE a.pat_id = :patient_id
            ORDER BY st.status_name
        ", ['patient_id' => $patientId]);
    }

    public function getUpcomingForPatient(int $patientId, int $limit = 5): array {
        $limit = max(1, (int)$limit);
        return $this->db->fetchAll("
            SELECT a.*, 
                   d.doc_first_name, d.doc_last_name, d.doc_specialization_id,
                   s.service_name, s.service_price,
                   st.status_name, st.status_color,
                   sp.spec_name,
                   ud.profile_picture_url as doctor_profile_picture
            FROM appointments a
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
            LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
            LEFT JOIN users ud ON ud.doc_id = d.doc_id
            WHERE a.pat_id = :patient_id AND a.appointment_date >= :today
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
            LIMIT {$limit}
        ", [
            'patient_id' => $patientId,
            'today' => date('Y-m-d')
        ]);
    }

    public function countTotalForPatient(int $patientId): int {
        return (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count FROM appointments WHERE pat_id = :patient_id",
            ['patient_id' => $patientId]
        )['count'];
    }

    public function countUpcomingForPatient(int $patientId): int {
        return (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count FROM appointments WHERE pat_id = :patient_id AND appointment_date >= :today",
            [
                'patient_id' => $patientId,
                'today' => date('Y-m-d')
            ]
        )['count'];
    }

    public function countCompletedForPatient(int $patientId): int {
        return (int)$this->db->fetchOne(
            "SELECT COUNT(*) as count 
             FROM appointments a
             JOIN appointment_statuses st ON a.status_id = st.status_id
             WHERE a.pat_id = :patient_id AND LOWER(st.status_name) = 'completed'",
            ['patient_id' => $patientId]
        )['count'];
    }

    private function resolveDuration(?int $serviceId): int {
        if (!$serviceId) {
            return 30;
        }

        $service = $this->db->fetchOne(
            "SELECT service_duration_minutes FROM services WHERE service_id = :service_id",
            ['service_id' => $serviceId]
        );

        return isset($service['service_duration_minutes']) ? (int)$service['service_duration_minutes'] : 30;
    }

    public function bookForPatient(array $data): array {
        $data['appointment_duration'] = $this->resolveDuration($data['service_id'] ?? null);
        $data['status_id'] = $data['status_id'] ?? 1;

        return $this->create([
            'pat_id' => $data['pat_id'],
            'doc_id' => $data['doc_id'],
            'service_id' => $data['service_id'] ?? null,
            'status_id' => $data['status_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'appointment_notes' => $data['appointment_notes'] ?? null,
            'appointment_duration' => $data['appointment_duration']
        ]);
    }

    public function getDetailedForPatient(string $appointmentId, int $patientId) {
        return $this->db->fetchOne("
            SELECT a.*, d.doc_first_name, d.doc_last_name, sp.spec_name, u.profile_picture_url
            FROM appointments a
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN specializations sp ON d.doc_specialization_id = sp.spec_id
            LEFT JOIN users u ON d.doc_id = u.doc_id
            WHERE a.appointment_id = :appointment_id AND a.pat_id = :patient_id
        ", ['appointment_id' => $appointmentId, 'patient_id' => $patientId]);
    }

    public function rescheduleForPatient(string $appointmentId, int $patientId, array $data): array {
        $appointment = $this->db->fetchOne("
            SELECT pat_id, status_id, service_id 
            FROM appointments 
            WHERE appointment_id = :appointment_id",
            ['appointment_id' => $appointmentId]
        );

        if (!$appointment) {
            return ['success' => false, 'error' => 'Appointment not found'];
        }

        if ((int)$appointment['pat_id'] !== $patientId) {
            return ['success' => false, 'error' => 'You do not have permission to reschedule this appointment'];
        }

        $status = $this->db->fetchOne("
            SELECT status_name 
            FROM appointment_statuses 
            WHERE status_id = :status_id",
            ['status_id' => $appointment['status_id']]
        );

        if ($status && in_array(strtolower($status['status_name']), ['cancelled', 'completed'])) {
            return ['success' => false, 'error' => 'This appointment cannot be rescheduled'];
        }

        $duration = $this->resolveDuration($appointment['service_id'] ?? null);

        $this->db->execute("
            UPDATE appointments 
            SET appointment_date = :appointment_date,
                appointment_time = :appointment_time,
                appointment_duration = :duration,
                appointment_notes = :notes,
                updated_at = NOW()
            WHERE appointment_id = :appointment_id AND pat_id = :patient_id
        ", [
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'duration' => $duration,
            'notes' => $data['appointment_notes'] ?? null,
            'appointment_id' => $appointmentId,
            'patient_id' => $patientId
        ]);

        return ['success' => true];
    }

    public function getForPatientById(string $appointmentId, int $patientId) {
        return $this->db->fetchOne("
            SELECT a.*, 
                   d.doc_first_name, d.doc_last_name, d.doc_consultation_fee,
                   s.service_name, s.service_price,
                   st.status_name
            FROM appointments a
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
            WHERE a.appointment_id = :appointment_id AND a.pat_id = :patient_id
        ", ['appointment_id' => $appointmentId, 'patient_id' => $patientId]);
    }

    public function getUnpaidForPatient(int $patientId): array {
        return $this->db->fetchAll("
            SELECT a.*, 
                   s.service_name, s.service_price,
                   d.doc_first_name, d.doc_last_name, d.doc_consultation_fee,
                   st.status_name
            FROM appointments a
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN doctors d ON a.doc_id = d.doc_id
            LEFT JOIN appointment_statuses st ON a.status_id = st.status_id
            WHERE a.pat_id = :patient_id
              AND NOT EXISTS (
                  SELECT 1 FROM payments p WHERE p.appointment_id = a.appointment_id
              )
              AND LOWER(st.status_name) != 'cancelled'
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ", ['patient_id' => $patientId]);
    }
}
