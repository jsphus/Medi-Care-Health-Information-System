<?php
require_once __DIR__ . '/Entity.php';

class Service extends Entity {
    // Private properties - Encapsulation
    private $service_id;
    private $service_name;
    private $service_description;
    private $service_price;
    private $service_duration_minutes;
    private $service_category;
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
        return 'services';
    }

    protected function getPrimaryKey(): string {
        return 'service_id';
    }

    protected function getColumns(): array {
        return [
            'service_id', 'service_name', 'service_description', 'service_price',
            'service_duration_minutes', 'service_category', 'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['service_name'])) {
            $errors[] = 'Service name is required.';
        }
        if (!empty($data['service_price']) && !is_numeric($data['service_price'])) {
            $errors[] = 'Service price must be a valid number.';
        }
        if (!empty($data['service_duration_minutes']) && !is_numeric($data['service_duration_minutes'])) {
            $errors[] = 'Service duration must be a valid number.';
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'service_id' => $this->service_id,
            'service_name' => $this->service_name,
            'service_description' => $this->service_description,
            'service_price' => $this->service_price,
            'service_duration_minutes' => $this->service_duration_minutes,
            'service_category' => $this->service_category,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->service_id = $data['service_id'] ?? null;
        $this->service_name = $data['service_name'] ?? null;
        $this->service_description = $data['service_description'] ?? null;
        $this->service_price = $data['service_price'] ?? null;
        $this->service_duration_minutes = $data['service_duration_minutes'] ?? 30;
        $this->service_category = $data['service_category'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getServiceId() { return $this->service_id; }
    public function getServiceName() { return $this->service_name; }
    public function getServiceDescription() { return $this->service_description; }
    public function getServicePrice() { return $this->service_price; }
    public function getServiceDurationMinutes() { return $this->service_duration_minutes; }
    public function getServiceCategory() { return $this->service_category; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setServiceId($value) { $this->service_id = $value; return $this; }
    public function setServiceName($value) { $this->service_name = $value; return $this; }
    public function setServiceDescription($value) { $this->service_description = $value; return $this; }
    public function setServicePrice($value) { $this->service_price = $value; return $this; }
    public function setServiceDurationMinutes($value) { $this->service_duration_minutes = $value; return $this; }
    public function setServiceCategory($value) { $this->service_category = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get all services (maintains backward compatibility)
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM services ORDER BY service_name");
    }

    // Get service by ID (maintains backward compatibility)
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM services WHERE service_id = :id", ['id' => $id]);
    }

    // Create new service (maintains backward compatibility)
    public function create($data) {
        $this->fromArray($data);
        return $this->save();
    }

    // Update service (maintains backward compatibility)
    public function update($id, $data) {
        $data['service_id'] = $id;
        $this->fromArray($data);
        return $this->save();
    }

    // Delete service (maintains backward compatibility)
    public function delete($id = null) {
        if ($id !== null) {
            $this->service_id = $id;
        }
        return parent::delete($id);
    }
}
