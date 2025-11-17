<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../includes/functions.php'; // for generateAppointmentId()

class Appointment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ✅ Get all appointments with joined details
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

    // ✅ Get appointment by ID
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

    // ✅ Create new appointment
    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            // Generate formatted appointment_id (e.g., 2025-10-0000010)
            $appointmentId = generateAppointmentId($this->db);

            $sql = "INSERT INTO appointments (
                        appointment_id,
                        pat_id,
                        doc_id,
                        service_id,
                        status_id,
                        appointment_date,
                        appointment_time,
                        appointment_notes,
                        appointment_duration,
                        created_at,
                        updated_at
                    ) VALUES (
                        :appointment_id,
                        :pat_id,
                        :doc_id,
                        :service_id,
                        :status_id,
                        :appointment_date,
                        :appointment_time,
                        :appointment_notes,
                        :appointment_duration,
                        NOW(),
                        NOW()
                    )";

            $this->db->execute($sql, [
                'appointment_id' => $appointmentId,
                'pat_id' => $data['pat_id'],
                'doc_id' => $data['doc_id'],
                'service_id' => $data['service_id'] ?: null,
                'status_id' => $data['status_id'] ?? 1, // Default = Scheduled
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'appointment_notes' => $data['appointment_notes'] ?? null,
                'appointment_duration' => $data['appointment_duration'] ?? 30
            ]);

            return ['success' => true, 'id' => $appointmentId];

        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to create appointment: ' . $e->getMessage()]];
        }
    }

    // ✅ Update appointment
    public function update($data) {
        $errors = $this->validate($data, false);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $sql = "UPDATE appointments SET
                        pat_id = :pat_id,
                        doc_id = :doc_id,
                        service_id = :service_id,
                        status_id = :status_id,
                        appointment_date = :appointment_date,
                        appointment_time = :appointment_time,
                        appointment_notes = :appointment_notes,
                        appointment_duration = :appointment_duration,
                        updated_at = NOW()
                    WHERE appointment_id = :appointment_id";

            $this->db->execute($sql, [
                'appointment_id' => $data['appointment_id'],
                'pat_id' => $data['pat_id'],
                'doc_id' => $data['doc_id'],
                'service_id' => $data['service_id'] ?: null,
                'status_id' => $data['status_id'] ?? 1,
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'appointment_notes' => $data['appointment_notes'] ?? null,
                'appointment_duration' => $data['appointment_duration'] ?? 30
            ]);

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to update appointment: ' . $e->getMessage()]];
        }
    }

    // ✅ Delete appointment
    public function delete($id) {
        try {
            $this->db->execute("DELETE FROM appointments WHERE appointment_id = :id", ['id' => $id]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete appointment: ' . $e->getMessage()]];
        }
    }

    // ✅ Cancel appointment (status_id = 3 = "Cancelled")
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

    // ✅ Validation
    private function validate($data, $isNew = true) {
        $errors = [];

        if (empty($data['pat_id'])) $errors[] = 'Patient is required.';
        if (empty($data['doc_id'])) $errors[] = 'Doctor is required.';
        if (empty($data['appointment_date'])) $errors[] = 'Appointment date is required.';
        if (empty($data['appointment_time'])) $errors[] = 'Appointment time is required.';

        return $errors;
    }
}