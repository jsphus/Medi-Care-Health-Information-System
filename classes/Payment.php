<?php
require_once __DIR__ . '/Entity.php';

class Payment extends Entity {
    // Private properties - Encapsulation
    private $payment_id;
    private $appointment_id;
    private $payment_amount;
    private $payment_method_id;
    private $payment_status_id;
    private $payment_date;
    private $payment_reference;
    private $payment_notes;
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
        return 'payments';
    }

    protected function getPrimaryKey(): string {
        return 'payment_id';
    }

    protected function getColumns(): array {
        return [
            'payment_id', 'appointment_id', 'payment_amount', 'payment_method_id',
            'payment_status_id', 'payment_date', 'payment_reference', 'payment_notes',
            'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['appointment_id'])) {
            $errors[] = 'Appointment is required.';
        }
        if (empty($data['payment_amount']) || !is_numeric($data['payment_amount'])) {
            $errors[] = 'Payment amount is required and must be a valid number.';
        }
        if (empty($data['payment_method_id'])) {
            $errors[] = 'Payment method is required.';
        }
        if (empty($data['payment_status_id'])) {
            $errors[] = 'Payment status is required.';
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'payment_id' => $this->payment_id,
            'appointment_id' => $this->appointment_id,
            'payment_amount' => $this->payment_amount,
            'payment_method_id' => $this->payment_method_id,
            'payment_status_id' => $this->payment_status_id,
            'payment_date' => $this->payment_date,
            'payment_reference' => $this->payment_reference,
            'payment_notes' => $this->payment_notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->payment_id = $data['payment_id'] ?? null;
        $this->appointment_id = $data['appointment_id'] ?? null;
        $this->payment_amount = $data['payment_amount'] ?? null;
        $this->payment_method_id = $data['payment_method_id'] ?? null;
        $this->payment_status_id = $data['payment_status_id'] ?? null;
        $this->payment_date = $data['payment_date'] ?? null;
        $this->payment_reference = $data['payment_reference'] ?? null;
        $this->payment_notes = $data['payment_notes'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getPaymentId() { return $this->payment_id; }
    public function getAppointmentId() { return $this->appointment_id; }
    public function getPaymentAmount() { return $this->payment_amount; }
    public function getPaymentMethodId() { return $this->payment_method_id; }
    public function getPaymentStatusId() { return $this->payment_status_id; }
    public function getPaymentDate() { return $this->payment_date; }
    public function getPaymentReference() { return $this->payment_reference; }
    public function getPaymentNotes() { return $this->payment_notes; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setPaymentId($value) { $this->payment_id = $value; return $this; }
    public function setAppointmentId($value) { $this->appointment_id = $value; return $this; }
    public function setPaymentAmount($value) { $this->payment_amount = $value; return $this; }
    public function setPaymentMethodId($value) { $this->payment_method_id = $value; return $this; }
    public function setPaymentStatusId($value) { $this->payment_status_id = $value; return $this; }
    public function setPaymentDate($value) { $this->payment_date = $value; return $this; }
    public function setPaymentReference($value) { $this->payment_reference = $value; return $this; }
    public function setPaymentNotes($value) { $this->payment_notes = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get payments by appointment
    public function getByAppointment($appointmentId) {
        return $this->db->fetchAll(
            "SELECT * FROM payments WHERE appointment_id = :appointment_id ORDER BY payment_date DESC",
            ['appointment_id' => $appointmentId]
        );
    }

    public function getRecentForPatient(int $patientId, int $limit = 5): array {
        $limit = max(1, (int)$limit);
        return $this->db->fetchAll("
            SELECT p.*, 
                   a.appointment_id, a.appointment_date,
                   pm.method_name,
                   ps.status_name, ps.status_color
            FROM payments p
            LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
            LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
            WHERE a.pat_id = :patient_id
            ORDER BY p.payment_date DESC
            LIMIT {$limit}
        ", ['patient_id' => $patientId]);
    }

    public function getTotalAmountForPatient(int $patientId): float {
        $result = $this->db->fetchOne("
            SELECT COALESCE(SUM(p.payment_amount), 0) as total
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.appointment_id
            WHERE a.pat_id = :patient_id
        ", ['patient_id' => $patientId]);

        return (float)($result['total'] ?? 0);
    }

    public function getPendingCountForPatient(int $patientId): int {
        $result = $this->db->fetchOne("
            SELECT COUNT(*) as count
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.appointment_id
            JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
            WHERE a.pat_id = :patient_id AND LOWER(ps.status_name) = 'pending'
        ", ['patient_id' => $patientId]);

        return (int)($result['count'] ?? 0);
    }

    public function getLatestByAppointment(string $appointmentId) {
        return $this->db->fetchOne("
            SELECT * FROM payments WHERE appointment_id = :appointment_id ORDER BY payment_date DESC LIMIT 1
        ", ['appointment_id' => $appointmentId]);
    }

    public function getLatestDetailsByAppointment(string $appointmentId) {
        return $this->db->fetchOne("
            SELECT p.*, 
                   pm.method_name,
                   ps.status_name, ps.status_color
            FROM payments p
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
            LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
            WHERE p.appointment_id = :appointment_id
            ORDER BY p.payment_date DESC
            LIMIT 1
        ", ['appointment_id' => $appointmentId]);
    }

    public function createPayment(array $data): array {
        $this->db->execute("
            INSERT INTO payments (
                appointment_id,
                payment_amount,
                payment_method_id,
                payment_status_id,
                payment_date,
                payment_reference,
                payment_notes,
                created_at
            ) VALUES (
                :appointment_id,
                :payment_amount,
                :payment_method_id,
                :payment_status_id,
                NOW(),
                :payment_reference,
                :payment_notes,
                NOW()
            )
        ", [
            'appointment_id' => $data['appointment_id'],
            'payment_amount' => $data['payment_amount'],
            'payment_method_id' => $data['payment_method_id'],
            'payment_status_id' => $data['payment_status_id'],
            'payment_reference' => $data['payment_reference'] ?? null,
            'payment_notes' => $data['payment_notes'] ?? null
        ]);

        $id = $this->db->lastInsertId('payments_payment_id_seq');
        return ['success' => true, 'id' => $id];
    }

    public function hasPaymentForAppointment(string $appointmentId): bool {
        $result = $this->db->fetchOne(
            "SELECT payment_id FROM payments WHERE appointment_id = :appointment_id LIMIT 1",
            ['appointment_id' => $appointmentId]
        );
        return !empty($result);
    }

    public function getForPatient(int $patientId, string $search = ''): array {
        $where = ['a.pat_id = :patient_id'];
        $params = ['patient_id' => $patientId];

        if (!empty($search)) {
            $where[] = "(p.payment_id::TEXT ILIKE :search OR a.appointment_id ILIKE :search OR pm.method_name ILIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        return $this->db->fetchAll("
            SELECT p.*, 
                   a.appointment_id, a.appointment_date, a.appointment_time,
                   pm.method_name,
                   ps.status_name, ps.status_color
            FROM payments p
            LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.method_id
            LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
            $whereClause
            ORDER BY p.payment_date DESC
        ", $params);
    }

    public function getStatsForPatient(int $patientId): array {
        $total = (int)$this->db->fetchOne("
            SELECT COUNT(*) as count
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.appointment_id
            WHERE a.pat_id = :patient_id
        ", ['patient_id' => $patientId])['count'];

        $paid = (int)$this->db->fetchOne("
            SELECT COUNT(*) as count
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.appointment_id
            JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
            WHERE a.pat_id = :patient_id AND LOWER(ps.status_name) = 'paid'
        ", ['patient_id' => $patientId])['count'];

        $pending = (int)$this->db->fetchOne("
            SELECT COUNT(*) as count
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.appointment_id
            JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
            WHERE a.pat_id = :patient_id AND LOWER(ps.status_name) = 'pending'
        ", ['patient_id' => $patientId])['count'];

        $totalAmount = $this->db->fetchOne("
            SELECT COALESCE(SUM(p.payment_amount), 0) as total
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.appointment_id
            WHERE a.pat_id = :patient_id
        ", ['patient_id' => $patientId]);

        return [
            'total' => $total,
            'paid' => $paid,
            'pending' => $pending,
            'total_amount' => (float)($totalAmount['total'] ?? 0)
        ];
    }
}
