<?php
require_once __DIR__ . '/Entity.php';

class Staff extends Entity {
    // Private properties - Encapsulation
    private $staff_id;
    private $staff_first_name;
    private $staff_middle_initial;
    private $staff_last_name;
    private $staff_email;
    private $staff_phone;
    private $staff_position;
    private $staff_salary;
    private $staff_status;
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
        return 'staff';
    }

    protected function getPrimaryKey(): string {
        return 'staff_id';
    }

    protected function getColumns(): array {
        return [
            'staff_id', 'staff_first_name', 'staff_middle_initial', 'staff_last_name', 'staff_email', 'staff_phone',
            'staff_position', 'staff_salary', 'staff_status',
            'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['staff_first_name'])) {
            $errors[] = 'First name is required.';
        }
        if (empty($data['staff_last_name'])) {
            $errors[] = 'Last name is required.';
        }
        if (empty($data['staff_email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['staff_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        // Check email uniqueness
        if ($isNew || (isset($data['staff_id']) && isset($data['staff_email']))) {
            $existing = $this->db->fetchOne(
                "SELECT staff_id FROM staff WHERE staff_email = :email" . 
                ($isNew ? '' : " AND staff_id != :id"),
                $isNew ? ['email' => $data['staff_email']] : ['email' => $data['staff_email'], 'id' => $data['staff_id']]
            );
            if ($existing) {
                $errors[] = 'Email already exists.';
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'staff_id' => $this->staff_id,
            'staff_first_name' => $this->staff_first_name,
            'staff_middle_initial' => $this->staff_middle_initial,
            'staff_last_name' => $this->staff_last_name,
            'staff_email' => $this->staff_email,
            'staff_phone' => $this->staff_phone,
            'staff_position' => $this->staff_position,
            'staff_salary' => $this->staff_salary,
            'staff_status' => $this->staff_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->staff_id = $data['staff_id'] ?? null;
        $this->staff_first_name = $data['staff_first_name'] ?? null;
        $this->staff_middle_initial = $data['staff_middle_initial'] ?? null;
        $this->staff_last_name = $data['staff_last_name'] ?? null;
        $this->staff_email = $data['staff_email'] ?? null;
        $this->staff_phone = $data['staff_phone'] ?? null;
        $this->staff_position = $data['staff_position'] ?? null;
        $this->staff_salary = $data['staff_salary'] ?? null;
        $this->staff_status = $data['staff_status'] ?? 'active';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getStaffId() { return $this->staff_id; }
    public function getStaffFirstName() { return $this->staff_first_name; }
    public function getStaffMiddleInitial() { return $this->staff_middle_initial; }
    public function getStaffLastName() { return $this->staff_last_name; }
    public function getStaffEmail() { return $this->staff_email; }
    public function getStaffPhone() { return $this->staff_phone; }
    public function getStaffPosition() { return $this->staff_position; }
    public function getStaffSalary() { return $this->staff_salary; }
    public function getStaffStatus() { return $this->staff_status; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setStaffId($value) { $this->staff_id = $value; return $this; }
    public function setStaffFirstName($value) { $this->staff_first_name = $value; return $this; }
    public function setStaffMiddleInitial($value) { $this->staff_middle_initial = $value; return $this; }
    public function setStaffLastName($value) { $this->staff_last_name = $value; return $this; }
    public function setStaffEmail($value) { $this->staff_email = $value; return $this; }
    public function setStaffPhone($value) { $this->staff_phone = $value; return $this; }
    public function setStaffPosition($value) { $this->staff_position = $value; return $this; }
    public function setStaffSalary($value) { $this->staff_salary = $value; return $this; }
    public function setStaffStatus($value) { $this->staff_status = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get all staff with optional search (maintains backward compatibility)
    public function getAll($search = '') {
        $whereClause = '';
        $params = [];

        if (!empty($search)) {
            $whereClause = "WHERE staff_first_name ILIKE :search OR staff_middle_initial ILIKE :search OR staff_last_name ILIKE :search OR staff_email ILIKE :search";
            $params['search'] = "%$search%";
        }

        return $this->db->fetchAll("SELECT * FROM staff $whereClause ORDER BY staff_id DESC", $params);
    }

    // Get staff by ID (maintains backward compatibility)
    public function getById($id) {
        // Explicitly select all columns to ensure all fields are returned
        $sql = "SELECT 
            staff_id, 
            staff_first_name, 
            staff_middle_initial, 
            staff_last_name, 
            staff_email, 
            staff_phone, 
            staff_position, 
            staff_salary, 
            staff_status, 
            created_at, 
            updated_at 
        FROM staff 
        WHERE staff_id = :id";
        
        $result = $this->db->fetchOne($sql, ['id' => $id]);
        
        // If result is null or has fewer columns than expected, try alternative approach
        if ($result === null || count($result) < 5) {
            // Try using raw PDO connection with explicit column selection
            try {
                $conn = $this->db->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute(['id' => $id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // If still not working, try without prepared statement
                if ($result === false || count($result) < 5) {
                    $raw_sql = "SELECT staff_id, staff_first_name, staff_middle_initial, staff_last_name, staff_email, staff_phone, staff_position, staff_salary, staff_status, created_at, updated_at FROM staff WHERE staff_id = " . (int)$id;
                    $raw_result = $conn->query($raw_sql);
                    if ($raw_result) {
                        $result = $raw_result->fetch(PDO::FETCH_ASSOC);
                    }
                }
            } catch (Exception $e) {
                error_log("Staff::getById() alternative fetch error: " . $e->getMessage());
            }
        }
        
        return $result;
    }

    // Create new staff (maintains backward compatibility)
    public function create($data) {
        $this->fromArray($data);
        return $this->save();
    }

    // Update staff (maintains backward compatibility)
    public function update($id, $data) {
        $data['staff_id'] = $id;
        $this->fromArray($data);
        return $this->save();
    }

    // Delete staff (maintains backward compatibility)
    public function delete($id = null): array {
        if ($id !== null) {
            $this->staff_id = $id;
        }
        return parent::delete($id);
    }
}
