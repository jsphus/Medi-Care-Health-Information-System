<?php
// Helper functions

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateAppointmentId($db) {
    $year = date('Y');
    $month = date('m');
    $prefix = "$year-$month-";
    
    try {
        // Get the last appointment ID for this month
        $stmt = $db->prepare("SELECT appointment_id FROM appointments WHERE appointment_id LIKE :prefix ORDER BY appointment_id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $lastNum = (int)substr($result['appointment_id'], -7);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }
        
        return $prefix . str_pad($newNum, 7, '0', STR_PAD_LEFT);
    } catch (PDOException $e) {
        return $prefix . str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
    }
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function formatPhoneNumber($phone) {
    // Remove all non-digit characters
    $digits = preg_replace('/\D/', '', $phone);

    // Ensure it has 11 digits (Philippine format)
    if (strlen($digits) === 11) {
        return preg_replace('/(\d{4})(\d{3})(\d{4})/', '$1-$2-$3', $digits);
    }

    // If not 11 digits, just return the cleaned version
    return $digits;
}

/**
 * Get user profile picture URL or return default avatar
 * @param int $user_id User ID
 * @param object $db Database connection
 * @param string $name Name for default avatar
 * @param array $options Image transformation options
 * @return string Profile picture URL or default avatar HTML
 */
function getUserProfilePicture($user_id, $db, $name = '', $options = []) {
    try {
        $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && !empty($user['profile_picture_url'])) {
            // If options provided, transform the image
            if (!empty($options) && class_exists('CloudinaryUpload')) {
                $cloudinary = new CloudinaryUpload();
                $publicId = $cloudinary->extractPublicId($user['profile_picture_url']);
                if ($publicId) {
                    return $cloudinary->transformImage($publicId, $options);
                }
            }
            return $user['profile_picture_url'];
        }
    } catch (PDOException $e) {
        // Fall through to default avatar
    }
    
    // Return null to indicate no profile picture (view will handle default)
    return null;
}

/**
 * Generate default avatar HTML with first letter
 * @param string $name Name to extract first letter from
 * @param int $size Size in pixels (default 80)
 * @return string HTML for default avatar
 */
function getDefaultAvatar($name, $size = 80) {
    $firstLetter = strtoupper(substr(trim($name), 0, 1));
    if (empty($firstLetter)) {
        $firstLetter = '?';
    }
    
    return $firstLetter;
}

/**
 * Initialize profile picture URL for the current logged-in user
 * This function should be called in all controllers to ensure consistent profile picture display
 * @param Auth $auth Auth instance
 * @param PDO $db Database connection
 * @return string|null Profile picture URL or null
 */
function initializeProfilePicture($auth, $db) {
    try {
        $user_id = $auth->getUserId();
        if (!$user_id) {
            return null;
        }
        
        $stmt = $db->prepare("SELECT profile_picture_url FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user['profile_picture_url'] ?? null;
    } catch (PDOException $e) {
        error_log("Error fetching profile picture: " . $e->getMessage());
        return null;
    }
}

