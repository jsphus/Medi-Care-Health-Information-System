<?php
// Helper functions

function sanitize($data) {
    if ($data === null) {
        return '';
    }
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

/**
 * Format full name with middle initial
 * @param string $first_name First name
 * @param string|null $middle_initial Middle initial (optional)
 * @param string $last_name Last name
 * @return string Formatted full name
 */
function formatFullName($first_name, $middle_initial = null, $last_name = '') {
    $name = trim($first_name ?? '');
    if (!empty($middle_initial)) {
        $name .= ' ' . strtoupper(substr($middle_initial, 0, 1)) . '.';
    }
    if (!empty($last_name)) {
        $name .= ' ' . trim($last_name);
    }
    return trim($name);
}

