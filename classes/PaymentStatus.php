<?php
require_once __DIR__ . '/Entity.php';

class PaymentStatus extends Entity {
    // Private properties - Encapsulation
    private $payment_status_id;
    private $status_name;
    private $status_description;
    private $status_color;
    private $created_at;

    public function __construct($data = []) {
        parent::__construct();
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    // Abstract method implementations
    protected function getTableName(): string {
        return 'payment_statuses';
    }

    protected function getPrimaryKey(): string {
        return 'payment_status_id';
    }

    protected function getColumns(): array {
        return [
            'payment_status_id', 'status_name', 'status_description', 'status_color', 'created_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['status_name'])) {
            $errors[] = 'Status name is required.';
        }

        // Check name uniqueness
        if ($isNew || (isset($data['payment_status_id']) && isset($data['status_name']))) {
            $existing = $this->db->fetchOne(
                "SELECT payment_status_id FROM payment_statuses WHERE status_name = :name" . 
                ($isNew ? '' : " AND payment_status_id != :id"),
                $isNew ? ['name' => $data['status_name']] : ['name' => $data['status_name'], 'id' => $data['payment_status_id']]
            );
            if ($existing) {
                $errors[] = 'Status name already exists.';
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'payment_status_id' => $this->payment_status_id,
            'status_name' => $this->status_name,
            'status_description' => $this->status_description,
            'status_color' => $this->status_color,
            'created_at' => $this->created_at
        ];
    }

    public function fromArray(array $data): self {
        $this->payment_status_id = $data['payment_status_id'] ?? null;
        $this->status_name = $data['status_name'] ?? null;
        $this->status_description = $data['status_description'] ?? null;
        $this->status_color = $data['status_color'] ?? '#3B82F6';
        $this->created_at = $data['created_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getPaymentStatusId() { return $this->payment_status_id; }
    public function getStatusName() { return $this->status_name; }
    public function getStatusDescription() { return $this->status_description; }
    public function getStatusColor() { return $this->status_color; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters - Encapsulation
    public function setPaymentStatusId($value) { $this->payment_status_id = $value; return $this; }
    public function setStatusName($value) { $this->status_name = $value; return $this; }
    public function setStatusDescription($value) { $this->status_description = $value; return $this; }
    public function setStatusColor($value) { $this->status_color = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }

    public function getByName(string $name) {
        return $this->db->fetchOne(
            "SELECT * FROM payment_statuses WHERE LOWER(status_name) = LOWER(:name) LIMIT 1",
            ['name' => $name]
        );
    }
}
