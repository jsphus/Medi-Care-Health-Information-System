<?php
/**
 * Combined script to create user accounts for all roles (doctors, patients, staff) that don't have one
 * 
 * Usage: php create-user-accounts-for-all-roles.php [--password=PASSWORD] [--dry-run] [--role=ROLE]
 * 
 * Options:
 *   --password=PASSWORD  Set a default password for all accounts (default: generates random passwords)
 *   --dry-run           Show what would be done without actually creating accounts
 *   --force             Skip email validation check (use if emails might already exist in users table for other roles)
 *   --role=ROLE         Only process specific role: 'doctor', 'patient', 'staff', or 'all' (default: 'all')
 * 
 * This script will:
 * 1. Find all users (doctors/patients/staff) without user accounts
 * 2. Check if their email already exists in users table
 * 3. Create user accounts for users without conflicts
 * 4. Generate secure passwords or use the provided password
 * 5. Link user accounts to their respective roles
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Parse command line arguments
$options = getopt('', ['password:', 'dry-run', 'force', 'role:']);
$defaultPassword = $options['password'] ?? null;
$dryRun = isset($options['dry-run']);
$force = isset($options['force']);
$role = isset($options['role']) ? strtolower($options['role']) : 'all';

// Validate role option
if (!in_array($role, ['doctor', 'patient', 'staff', 'all'])) {
    echo "ERROR: Invalid role. Use 'doctor', 'patient', 'staff', or 'all'.\n";
    exit(1);
}

// Function to generate a random secure password
function generateSecurePassword($length = 12) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $password;
}

// Function to create user account for a user
function createUserAccount($db, $email, $password, $idField, $idValue, $force, &$errors) {
    // Check if email already exists (unless forced)
    if (!$force) {
        $checkStmt = $db->prepare("SELECT user_id, pat_id, doc_id, staff_id FROM users WHERE user_email = :email");
        $checkStmt->execute(['email' => $email]);
        $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            // Check if it's already linked to the correct ID
            $fieldMap = ['pat_id' => 'pat_id', 'doc_id' => 'doc_id', 'staff_id' => 'staff_id'];
            if (isset($fieldMap[$idField]) && $existingUser[$idField] == $idValue) {
                return ['success' => false, 'skipped' => true, 'message' => 'User account already exists'];
            } else {
                return ['success' => false, 'skipped' => true, 'message' => "Email already exists (user_id: {$existingUser['user_id']})"];
            }
        }
    }
    
    // Hash password and create account
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $params = ['email' => $email, 'password' => $hashedPassword, $idField => $idValue];
        
        $stmt = $db->prepare("
            INSERT INTO users (user_email, user_password, $idField, user_is_superadmin, created_at) 
            VALUES (:email, :password, :$idField, false, NOW())
        ");
        $stmt->execute($params);
        
        return ['success' => true, 'user_id' => $db->lastInsertId()];
    } catch (PDOException $e) {
        $errors[] = $e->getMessage();
        return ['success' => false, 'skipped' => false, 'message' => $e->getMessage()];
    }
}

// Main execution
try {
    echo "========================================\n";
    echo "Create User Accounts for All Roles Script\n";
    echo "========================================\n\n";
    
    if ($dryRun) {
        echo "⚠️  DRY RUN MODE - No changes will be made\n\n";
    }
    
    $totalStats = ['doctors' => 0, 'patients' => 0, 'staff' => 0];
    $successStats = ['doctors' => 0, 'patients' => 0, 'staff' => 0];
    $skipStats = ['doctors' => 0, 'patients' => 0, 'staff' => 0];
    $errorStats = ['doctors' => 0, 'patients' => 0, 'staff' => 0];
    $allCreatedAccounts = ['doctors' => [], 'patients' => [], 'staff' => []];
    $allErrors = [];
    
    // Process Doctors
    if ($role === 'all' || $role === 'doctor') {
        echo "========================================\n";
        echo "PROCESSING DOCTORS\n";
        echo "========================================\n";
        $stmt = $db->query("
            SELECT d.doc_id, d.doc_first_name, d.doc_middle_initial, d.doc_last_name, d.doc_email, d.doc_status
            FROM doctors d
            LEFT JOIN users u ON d.doc_id = u.doc_id
            WHERE u.doc_id IS NULL
            ORDER BY d.doc_id
        ");
        $doctorsWithoutAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalStats['doctors'] = count($doctorsWithoutAccounts);
        
        if ($totalStats['doctors'] > 0 && !$dryRun) {
            foreach ($doctorsWithoutAccounts as $doctor) {
                $docId = $doctor['doc_id'];
                $email = $doctor['doc_email'] ?? '';
                $fullName = trim(($doctor['doc_first_name'] ?? '') . ' ' . 
                                ($doctor['doc_middle_initial'] ?? '') . ' ' . 
                                ($doctor['doc_last_name'] ?? ''));
                
                if (empty($email)) {
                    $skipStats['doctors']++;
                    echo "⚠️  Doctor ID $docId ($fullName): SKIPPED - No email\n";
                    continue;
                }
                
                $password = $defaultPassword ?: generateSecurePassword();
                $result = createUserAccount($db, $email, $password, 'doc_id', $docId, $force, $allErrors);
                
                if ($result['success']) {
                    $successStats['doctors']++;
                    $allCreatedAccounts['doctors'][] = [
                        'user_id' => $result['user_id'],
                        'id' => $docId,
                        'name' => $fullName,
                        'email' => $email,
                        'password' => $password
                    ];
                    echo "✅ Doctor ID $docId ($fullName): Created (user_id: {$result['user_id']})\n";
                } elseif ($result['skipped']) {
                    $skipStats['doctors']++;
                    echo "⚠️  Doctor ID $docId ($fullName): SKIPPED - {$result['message']}\n";
                } else {
                    $errorStats['doctors']++;
                    echo "❌ Doctor ID $docId ($fullName): ERROR - {$result['message']}\n";
                }
            }
        } elseif ($totalStats['doctors'] > 0) {
            echo "Found $totalStats[doctors] doctors without accounts (dry run)\n";
        } else {
            echo "✅ All doctors have user accounts\n";
        }
        echo "\n";
    }
    
    // Process Patients
    if ($role === 'all' || $role === 'patient') {
        echo "========================================\n";
        echo "PROCESSING PATIENTS\n";
        echo "========================================\n";
        $stmt = $db->query("
            SELECT p.pat_id, p.pat_first_name, p.pat_middle_initial, p.pat_last_name, p.pat_email
            FROM patients p
            LEFT JOIN users u ON p.pat_id = u.pat_id
            WHERE u.pat_id IS NULL
            ORDER BY p.pat_id
        ");
        $patientsWithoutAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalStats['patients'] = count($patientsWithoutAccounts);
        
        if ($totalStats['patients'] > 0 && !$dryRun) {
            foreach ($patientsWithoutAccounts as $patient) {
                $patId = $patient['pat_id'];
                $email = $patient['pat_email'] ?? '';
                $fullName = trim(($patient['pat_first_name'] ?? '') . ' ' . 
                                ($patient['pat_middle_initial'] ?? '') . ' ' . 
                                ($patient['pat_last_name'] ?? ''));
                
                if (empty($email)) {
                    $skipStats['patients']++;
                    echo "⚠️  Patient ID $patId ($fullName): SKIPPED - No email\n";
                    continue;
                }
                
                $password = $defaultPassword ?: generateSecurePassword();
                $result = createUserAccount($db, $email, $password, 'pat_id', $patId, $force, $allErrors);
                
                if ($result['success']) {
                    $successStats['patients']++;
                    $allCreatedAccounts['patients'][] = [
                        'user_id' => $result['user_id'],
                        'id' => $patId,
                        'name' => $fullName,
                        'email' => $email,
                        'password' => $password
                    ];
                    echo "✅ Patient ID $patId ($fullName): Created (user_id: {$result['user_id']})\n";
                } elseif ($result['skipped']) {
                    $skipStats['patients']++;
                    echo "⚠️  Patient ID $patId ($fullName): SKIPPED - {$result['message']}\n";
                } else {
                    $errorStats['patients']++;
                    echo "❌ Patient ID $patId ($fullName): ERROR - {$result['message']}\n";
                }
            }
        } elseif ($totalStats['patients'] > 0) {
            echo "Found $totalStats[patients] patients without accounts (dry run)\n";
        } else {
            echo "✅ All patients have user accounts\n";
        }
        echo "\n";
    }
    
    // Process Staff
    if ($role === 'all' || $role === 'staff') {
        echo "========================================\n";
        echo "PROCESSING STAFF\n";
        echo "========================================\n";
        $stmt = $db->query("
            SELECT s.staff_id, s.staff_first_name, s.staff_middle_initial, s.staff_last_name, s.staff_email, s.staff_position
            FROM staff s
            LEFT JOIN users u ON s.staff_id = u.staff_id
            WHERE u.staff_id IS NULL
            ORDER BY s.staff_id
        ");
        $staffWithoutAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalStats['staff'] = count($staffWithoutAccounts);
        
        if ($totalStats['staff'] > 0 && !$dryRun) {
            foreach ($staffWithoutAccounts as $staffMember) {
                $staffId = $staffMember['staff_id'];
                $email = $staffMember['staff_email'] ?? '';
                $fullName = trim(($staffMember['staff_first_name'] ?? '') . ' ' . 
                                ($staffMember['staff_middle_initial'] ?? '') . ' ' . 
                                ($staffMember['staff_last_name'] ?? ''));
                
                if (empty($email)) {
                    $skipStats['staff']++;
                    echo "⚠️  Staff ID $staffId ($fullName): SKIPPED - No email\n";
                    continue;
                }
                
                $password = $defaultPassword ?: generateSecurePassword();
                $result = createUserAccount($db, $email, $password, 'staff_id', $staffId, $force, $allErrors);
                
                if ($result['success']) {
                    $successStats['staff']++;
                    $allCreatedAccounts['staff'][] = [
                        'user_id' => $result['user_id'],
                        'id' => $staffId,
                        'name' => $fullName,
                        'email' => $email,
                        'password' => $password
                    ];
                    echo "✅ Staff ID $staffId ($fullName): Created (user_id: {$result['user_id']})\n";
                } elseif ($result['skipped']) {
                    $skipStats['staff']++;
                    echo "⚠️  Staff ID $staffId ($fullName): SKIPPED - {$result['message']}\n";
                } else {
                    $errorStats['staff']++;
                    echo "❌ Staff ID $staffId ($fullName): ERROR - {$result['message']}\n";
                }
            }
        } elseif ($totalStats['staff'] > 0) {
            echo "Found $totalStats[staff] staff members without accounts (dry run)\n";
        } else {
            echo "✅ All staff members have user accounts\n";
        }
        echo "\n";
    }
    
    // Final Summary
    echo "========================================\n";
    echo "FINAL SUMMARY\n";
    echo "========================================\n";
    
    $totalUsers = array_sum($totalStats);
    $totalSuccess = array_sum($successStats);
    $totalSkipped = array_sum($skipStats);
    $totalErrors = array_sum($errorStats);
    
    echo "Total users without accounts: $totalUsers\n";
    echo "  - Doctors: {$totalStats['doctors']} (Created: {$successStats['doctors']}, Skipped: {$skipStats['doctors']}, Errors: {$errorStats['doctors']})\n";
    echo "  - Patients: {$totalStats['patients']} (Created: {$successStats['patients']}, Skipped: {$skipStats['patients']}, Errors: {$errorStats['patients']})\n";
    echo "  - Staff: {$totalStats['staff']} (Created: {$successStats['staff']}, Skipped: {$skipStats['staff']}, Errors: {$errorStats['staff']})\n";
    echo "\n";
    echo "✅ Total successfully created: $totalSuccess\n";
    echo "⚠️  Total skipped: $totalSkipped\n";
    echo "❌ Total errors: $totalErrors\n";
    
    // Display created accounts if any
    if ($totalSuccess > 0 && !$dryRun) {
        echo "\n";
        echo "========================================\n";
        echo "CREATED ACCOUNTS\n";
        echo "========================================\n";
        
        if (!$defaultPassword) {
            echo "⚠️  IMPORTANT: Save the passwords below. These are plaintext and won't be shown again.\n\n";
        }
        
        foreach (['doctors', 'patients', 'staff'] as $roleType) {
            if (count($allCreatedAccounts[$roleType]) > 0) {
                echo ucfirst($roleType) . ":\n";
                echo str_repeat("-", 100) . "\n";
                if ($defaultPassword) {
                    echo sprintf("%-8s %-10s %-30s %-40s\n", "User ID", ucfirst(substr($roleType, 0, -1)) . " ID", "Name", "Email");
                } else {
                    echo sprintf("%-8s %-10s %-30s %-40s %-15s\n", "User ID", ucfirst(substr($roleType, 0, -1)) . " ID", "Name", "Email", "Password");
                }
                echo str_repeat("-", 100) . "\n";
                
                foreach ($allCreatedAccounts[$roleType] as $account) {
                    if ($defaultPassword) {
                        echo sprintf("%-8s %-10s %-30s %-40s\n", 
                            $account['user_id'], $account['id'],
                            substr($account['name'], 0, 30),
                            substr($account['email'], 0, 40)
                        );
                    } else {
                        echo sprintf("%-8s %-10s %-30s %-40s %-15s\n", 
                            $account['user_id'], $account['id'],
                            substr($account['name'], 0, 30),
                            substr($account['email'], 0, 40),
                            $account['password']
                        );
                    }
                }
                echo "\n";
            }
        }
        
        if ($defaultPassword) {
            echo "Default password used for all accounts: $defaultPassword\n";
        }
    }
    
    if ($totalSuccess > 0) {
        echo "\n✅ Script completed successfully!\n";
    } elseif ($totalUsers > 0) {
        echo "\n⚠️  No accounts were created. Check the messages above for details.\n";
    } else {
        echo "\n✅ All users already have accounts!\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

