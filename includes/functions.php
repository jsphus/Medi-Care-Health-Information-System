<?php
// Helper functions

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// generateAppointmentId() moved to Appointment::generateId()

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

// getUserProfilePicture() moved to User::getProfilePicture()

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

// initializeProfilePicture() moved to User::initializeProfilePicture()

