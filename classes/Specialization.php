<?php
require_once __DIR__ . '/Entity.php';

class Specialization extends Entity {
    // Private properties - Encapsulation
    private $spec_id;
    private $spec_name;
    private $spec_description;
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
        return 'specializations';
    }

    protected function getPrimaryKey(): string {
        return 'spec_id';
    }

    protected function getColumns(): array {
        return [
            'spec_id', 'spec_name', 'spec_description', 'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['spec_name'])) {
            $errors[] = 'Specialization name is required.';
        }

        // Check name uniqueness
        if ($isNew || (isset($data['spec_id']) && isset($data['spec_name']))) {
            $existing = $this->db->fetchOne(
                "SELECT spec_id FROM specializations WHERE spec_name = :name" . 
                ($isNew ? '' : " AND spec_id != :id"),
                $isNew ? ['name' => $data['spec_name']] : ['name' => $data['spec_name'], 'id' => $data['spec_id']]
            );
            if ($existing) {
                $errors[] = 'Specialization name already exists.';
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'spec_id' => $this->spec_id,
            'spec_name' => $this->spec_name,
            'spec_description' => $this->spec_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->spec_id = $data['spec_id'] ?? null;
        $this->spec_name = $data['spec_name'] ?? null;
        $this->spec_description = $data['spec_description'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getSpecId() { return $this->spec_id; }
    public function getSpecName() { return $this->spec_name; }
    public function getSpecDescription() { return $this->spec_description; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setSpecId($value) { $this->spec_id = $value; return $this; }
    public function setSpecName($value) { $this->spec_name = $value; return $this; }
    public function setSpecDescription($value) { $this->spec_description = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    public function getAllSpecializations(): array {
        return $this->db->fetchAll("SELECT * FROM specializations ORDER BY spec_name");
    }
}
