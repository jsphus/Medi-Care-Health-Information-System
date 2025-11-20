<?php
require_once __DIR__ . '/Entity.php';
require_once __DIR__ . '/../config/Database.php';

class User extends Entity {
    // Private properties - Encapsulation
    private $user_id;
    private $user_email;
    private $user_password;
    private $user_is_superadmin;
    private $pat_id;
    private $staff_id;
    private $doc_id;
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
        return 'users';
    }

    protected function getPrimaryKey(): string {
        return 'user_id';
    }

    protected function getColumns(): array {
        return [
            'user_id', 'user_email', 'user_password', 'user_is_superadmin',
            'pat_id', 'staff_id', 'doc_id', 'created_at', 'updated_at'
        ];
    }

    protected function validate(array $data, bool $isNew = true): array {
        $errors = [];

        if (empty($data['user_email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['user_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        if ($isNew && empty($data['user_password'])) {
            $errors[] = 'Password is required.';
        }

        // Check email uniqueness
        if ($isNew || (isset($data['user_id']) && isset($data['user_email']))) {
            $existing = $this->db->fetchOne(
                "SELECT user_id FROM users WHERE user_email = :email" . 
                ($isNew ? '' : " AND user_id != :id"),
                $isNew ? ['email' => $data['user_email']] : ['email' => $data['user_email'], 'id' => $data['user_id']]
            );
            if ($existing) {
                $errors[] = 'Email already exists.';
            }
        }

        return $errors;
    }

    public function toArray(): array {
        return [
            'user_id' => $this->user_id,
            'user_email' => $this->user_email,
            'user_password' => $this->user_password,
            'user_is_superadmin' => $this->user_is_superadmin,
            'pat_id' => $this->pat_id,
            'staff_id' => $this->staff_id,
            'doc_id' => $this->doc_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function fromArray(array $data): self {
        $this->user_id = $data['user_id'] ?? null;
        $this->user_email = $data['user_email'] ?? null;
        $this->user_password = $data['user_password'] ?? null;
        $this->user_is_superadmin = $data['user_is_superadmin'] ?? false;
        $this->pat_id = $data['pat_id'] ?? null;
        $this->staff_id = $data['staff_id'] ?? null;
        $this->doc_id = $data['doc_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        return $this;
    }

    // Getters - Encapsulation
    public function getUserId() { return $this->user_id; }
    public function getUserEmail() { return $this->user_email; }
    public function getUserPassword() { return $this->user_password; }
    public function getUserIsSuperadmin() { return $this->user_is_superadmin; }
    public function getPatId() { return $this->pat_id; }
    public function getStaffId() { return $this->staff_id; }
    public function getDocId() { return $this->doc_id; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters - Encapsulation
    public function setUserId($value) { $this->user_id = $value; return $this; }
    public function setUserEmail($value) { $this->user_email = $value; return $this; }
    public function setUserPassword($value) { $this->user_password = $value; return $this; }
    public function setUserIsSuperadmin($value) { $this->user_is_superadmin = $value; return $this; }
    public function setPatId($value) { $this->pat_id = $value; return $this; }
    public function setStaffId($value) { $this->staff_id = $value; return $this; }
    public function setDocId($value) { $this->doc_id = $value; return $this; }
    public function setCreatedAt($value) { $this->created_at = $value; return $this; }
    public function setUpdatedAt($value) { $this->updated_at = $value; return $this; }

    // Get user by email
    public function getByEmail($email) {
        return $this->db->fetchOne("SELECT * FROM users WHERE user_email = :email", ['email' => $email]);
    }

    public function getByPatientId(int $patientId) {
        return $this->db->fetchOne("SELECT * FROM users WHERE pat_id = :patient_id", ['patient_id' => $patientId]);
    }

    public function updatePasswordForPatient(int $patientId, string $currentPassword, string $newPassword): array {
        $user = $this->getByPatientId($patientId);
        if (!$user) {
            return ['success' => false, 'error' => 'User account not found'];
        }

        if (!password_verify($currentPassword, $user['user_password'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->execute("UPDATE users SET user_password = :password WHERE pat_id = :patient_id", [
            'password' => $hashed,
            'patient_id' => $patientId
        ]);

        return ['success' => true];
    }

    public function setProfilePicture(int $userId, ?string $url): bool {
        return $this->db->execute("UPDATE users SET profile_picture_url = :url WHERE user_id = :user_id", [
            'url' => $url,
            'user_id' => $userId
        ]);
    }

    public function getProfilePictureByUserId(int $userId): ?string {
        $user = $this->db->fetchOne("SELECT profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $userId]);
        return $user['profile_picture_url'] ?? null;
    }

    /**
     * Get user profile picture URL or return null
     * Moved from functions.php
     * @param int $user_id User ID
     * @param array $options Image transformation options (for Cloudinary)
     * @return string|null Profile picture URL or null
     */
    public static function getProfilePicture($user_id, $options = []) {
        try {
            $db = Database::getInstance();
            $user = $db->fetchOne("SELECT profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
            
            if ($user && !empty($user['profile_picture_url'])) {
                // If options provided, transform the image
                if (!empty($options) && class_exists('CloudinaryUpload')) {
                    require_once __DIR__ . '/CloudinaryUpload.php';
                    $cloudinary = new CloudinaryUpload();
                    $publicId = $cloudinary->extractPublicId($user['profile_picture_url']);
                    if ($publicId) {
                        return $cloudinary->transformImage($publicId, $options);
                    }
                }
                return $user['profile_picture_url'];
            }
        } catch (Exception $e) {
            error_log("Error fetching profile picture: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Initialize profile picture URL for the current logged-in user
     * Moved from functions.php
     * @param Auth $auth Auth instance
     * @return string|null Profile picture URL or null
     */
    public static function initializeProfilePicture($auth) {
        try {
            $user_id = $auth->getUserId();
            if (!$user_id) {
                return null;
            }
            
            $db = Database::getInstance();
            $user = $db->fetchOne("SELECT profile_picture_url FROM users WHERE user_id = :user_id", ['user_id' => $user_id]);
            
            return $user['profile_picture_url'] ?? null;
        } catch (Exception $e) {
            error_log("Error fetching profile picture: " . $e->getMessage());
            return null;
        }
    }
}
