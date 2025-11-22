<?php
require_once __DIR__ . '/Entity.php';

class Schedule extends Entity {
    // Private properties - Encapsulation
    private $schedule_id;
    private $doc_id;
    private $schedule_date;
    private $start_time;
    private $end_time;
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
        return 'schedules';
    }

    protected function getPrimaryKey(): string {
        return 'schedule_id';
    }

    protected function getColumns(): array {
        return [
            'schedule_id', 'doc_id', 'schedule_date', 'start_time', 'end_time',
            'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['doc_id'])) {
            $errors[] = 'Doctor is required.';
        }
        if (empty($data['schedule_date'])) {
            $errors[] = 'Schedule date is required.';
        }
        if (empty($data['start_time'])) {
            $errors[] = 'Start time is required.';
        }
        if (empty($data['end_time'])) {
            $errors[] = 'End time is required.';
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'schedule_id' => $this->schedule_id,
            'doc_id' => $this->doc_id,
            'schedule_date' => $this->schedule_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->schedule_id = $data['schedule_id'] ?? null;
        $this->doc_id = $data['doc_id'] ?? null;
        $this->schedule_date = $data['schedule_date'] ?? null;
        $this->start_time = $data['start_time'] ?? null;
        $this->end_time = $data['end_time'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getScheduleId() { return $this->schedule_id; }
    public function getDocId() { return $this->doc_id; }
    public function getScheduleDate() { return $this->schedule_date; }
    public function getStartTime() { return $this->start_time; }
    public function getEndTime() { return $this->end_time; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setScheduleId($value) { $this->schedule_id = $value; return $this; }
    public function setDocId($value) { $this->doc_id = $value; return $this; }
    public function setScheduleDate($value) { $this->schedule_date = $value; return $this; }
    public function setStartTime($value) { $this->start_time = $value; return $this; }
    public function setEndTime($value) { $this->end_time = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get schedules by doctor
    public function getByDoctor($docId) {
        return $this->db->fetchAll(
            "SELECT * FROM schedules WHERE doc_id = :doc_id ORDER BY schedule_date, start_time",
            ['doc_id' => $docId]
        );
    }

    // Get available slots
    public function getAvailableSlots($docId, $date) {
        return $this->db->fetchAll(
            "SELECT * FROM schedules WHERE doc_id = :doc_id AND schedule_date = :date ORDER BY start_time",
            ['doc_id' => $docId, 'date' => $date]
        );
    }
}
