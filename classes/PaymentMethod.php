<?php
require_once __DIR__ . '/Entity.php';

class PaymentMethod extends Entity {
    // Private properties - Encapsulation
    private $method_id;
    private $method_name;
    private $method_description;
    private $is_active;
    private $created_at;

    public function __construct($data = []) {
        parent::__construct();
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    // Abstract method implementations
    protected function getTableName(): string {
        return 'payment_methods';
    }

    protected function getPrimaryKey(): string {
        return 'method_id';
    }

    protected function getColumns(): array {
        return [
            'method_id', 'method_name', 'method_description', 'is_active', 'created_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['method_name'])) {
            $errors[] = 'Method name is required.';
        }

        // Check name uniqueness
        if ($isNew || (isset($data['method_id']) && isset($data['method_name']))) {
            $existing = $this->db->fetchOne(
                "SELECT method_id FROM payment_methods WHERE method_name = :name" . 
                ($isNew ? '' : " AND method_id != :id"),
                $isNew ? ['name' => $data['method_name']] : ['name' => $data['method_name'], 'id' => $data['method_id']]
            );
            if ($existing) {
                $errors[] = 'Method name already exists.';
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'method_id' => $this->method_id,
            'method_name' => $this->method_name,
            'method_description' => $this->method_description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }

    public function fromArray(array $data): self {
        $this->method_id = $data['method_id'] ?? null;
        $this->method_name = $data['method_name'] ?? null;
        $this->method_description = $data['method_description'] ?? null;
        $this->is_active = $data['is_active'] ?? true;
        $this->created_at = $data['created_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getMethodId() { return $this->method_id; }
    public function getMethodName() { return $this->method_name; }
    public function getMethodDescription() { return $this->method_description; }
    public function getIsActive() { return $this->is_active; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters - Encapsulation
    public function setMethodId($value) { $this->method_id = $value; return $this; }
    public function setMethodName($value) { $this->method_name = $value; return $this; }
    public function setMethodDescription($value) { $this->method_description = $value; return $this; }
    public function setIsActive($value) { $this->is_active = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }

    // Get active payment methods
    public function getActive() {
        return $this->db->fetchAll(
            "SELECT * FROM payment_methods WHERE is_active = TRUE ORDER BY method_name"
        );
    }

    public function getAllActive(): array {
        return $this->getActive();
    }
}
