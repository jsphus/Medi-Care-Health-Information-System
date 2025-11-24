<?php
/**
 * Script to create user accounts for patients that don't have one
 * 
 * Usage: php create-user-accounts-for-patients.php [--password=PASSWORD] [--dry-run]
 * 
 * Options:
 *   --password=PASSWORD  Set a default password for all accounts (default: generates random passwords)
 *   --dry-run           Show what would be done without actually creating accounts
 *   --force             Skip email validation check (use if emails might already exist in users table for other roles)
 * 
 * This script will:
 * 1. Find all patients without user accounts (no entry in users table with matching pat_id)
 * 2. Check if the patient's email already exists in users table
 * 3. Create user accounts for patients without conflicts
 * 4. Generate secure passwords or use the provided password
 * 5. Link user accounts to patients via pat_id
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Parse command line arguments
$options = getopt('', ['password:', 'dry-run', 'force']);
$defaultPassword = $options['password'] ?? null;
$dryRun = isset($options['dry-run']);
$force = isset($options['force']);

// Function to generate a random secure password
function generateSecurePassword($length = 12) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $password;
}

// Main execution
try {
    echo "========================================\n";
    echo "Create User Accounts for Patients Script\n";
    echo "========================================\n\n";
    
    if ($dryRun) {
        echo "⚠️  DRY RUN MODE - No changes will be made\n\n";
    }
    
    // Step 1: Find all patients without user accounts
    echo "Step 1: Finding patients without user accounts...\n";
    $stmt = $db->query("
        SELECT p.pat_id, p.pat_first_name, p.pat_middle_initial, p.pat_last_name, p.pat_email
        FROM patients p
        LEFT JOIN users u ON p.pat_id = u.pat_id
        WHERE u.pat_id IS NULL
        ORDER BY p.pat_id
    ");
    $patientsWithoutAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $totalPatients = count($patientsWithoutAccounts);
    echo "Found $totalPatients patient(s) without user accounts.\n\n";
    
    if ($totalPatients === 0) {
        echo "✅ All patients already have user accounts!\n";
        exit(0);
    }
    
    // Display patients that need accounts
    echo "Patients needing user accounts:\n";
    echo str_repeat("-", 100) . "\n";
    echo sprintf("%-8s %-30s %-40s\n", "ID", "Name", "Email");
    echo str_repeat("-", 100) . "\n";
    foreach ($patientsWithoutAccounts as $patient) {
        $fullName = trim(($patient['pat_first_name'] ?? '') . ' ' . 
                        ($patient['pat_middle_initial'] ?? '') . ' ' . 
                        ($patient['pat_last_name'] ?? ''));
        echo sprintf("%-8s %-30s %-40s\n", 
            $patient['pat_id'], 
            substr($fullName, 0, 30), 
            substr($patient['pat_email'] ?? 'N/A', 0, 40)
        );
    }
    echo str_repeat("-", 100) . "\n\n";
    
    if ($dryRun) {
        echo "Dry run complete. Use without --dry-run to create accounts.\n";
        exit(0);
    }
    
    // Step 2: Process each patient
    echo "Step 2: Creating user accounts...\n\n";
    
    $successCount = 0;
    $skippedCount = 0;
    $errorCount = 0;
    $errors = [];
    $createdAccounts = [];
    
    foreach ($patientsWithoutAccounts as $patient) {
        $patId = $patient['pat_id'];
        $email = $patient['pat_email'] ?? '';
        $fullName = trim(($patient['pat_first_name'] ?? '') . ' ' . 
                        ($patient['pat_middle_initial'] ?? '') . ' ' . 
                        ($patient['pat_last_name'] ?? ''));
        
        // Skip if no email
        if (empty($email)) {
            $skippedCount++;
            $errors[] = "Patient ID $patId ($fullName): No email address - skipped";
            echo "⚠️  Patient ID $patId ($fullName): SKIPPED - No email address\n";
            continue;
        }
        
        // Check if email already exists in users table (unless forced)
        if (!$force) {
            $checkStmt = $db->prepare("SELECT user_id, pat_id, doc_id, staff_id FROM users WHERE user_email = :email");
            $checkStmt->execute(['email' => $email]);
            $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingUser) {
                // Check if it's already linked to this patient
                if ($existingUser['pat_id'] == $patId) {
                    $skippedCount++;
                    echo "⚠️  Patient ID $patId ($fullName): SKIPPED - User account already exists with this email and pat_id\n";
                    continue;
                } else {
                    // Email exists but linked to different patient or role
                    $skippedCount++;
                    $errors[] = "Patient ID $patId ($fullName): Email '$email' already exists in users table (linked to user_id: {$existingUser['user_id']}) - skipped. Use --force to override.";
                    echo "⚠️  Patient ID $patId ($fullName): SKIPPED - Email already exists (user_id: {$existingUser['user_id']})\n";
                    continue;
                }
            }
        }
        
        // Generate or use provided password
        if ($defaultPassword) {
            $password = $defaultPassword;
            echo "📝 Patient ID $patId ($fullName): Using provided password\n";
        } else {
            $password = generateSecurePassword();
            echo "📝 Patient ID $patId ($fullName): Generated random password\n";
        }
        
        // Create user account
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("
                INSERT INTO users (user_email, user_password, pat_id, user_is_superadmin, created_at) 
                VALUES (:email, :password, :pat_id, false, NOW())
            ");
            $stmt->execute([
                'email' => $email,
                'password' => $hashedPassword,
                'pat_id' => $patId
            ]);
            
            $userId = $db->lastInsertId();
            $successCount++;
            $createdAccounts[] = [
                'user_id' => $userId,
                'pat_id' => $patId,
                'name' => $fullName,
                'email' => $email,
                'password' => $password
            ];
            
            echo "✅ Patient ID $patId ($fullName): User account created (user_id: $userId)\n";
            if (!$defaultPassword) {
                echo "   Password: $password\n";
            }
            
        } catch (PDOException $e) {
            $errorCount++;
            $errorMsg = "Patient ID $patId ($fullName): " . $e->getMessage();
            $errors[] = $errorMsg;
            echo "❌ Patient ID $patId ($fullName): ERROR - {$e->getMessage()}\n";
        }
    }
    
    // Step 3: Summary
    echo "\n";
    echo str_repeat("=", 100) . "\n";
    echo "SUMMARY\n";
    echo str_repeat("=", 100) . "\n";
    echo "Total patients without accounts: $totalPatients\n";
    echo "✅ Successfully created: $successCount\n";
    echo "⚠️  Skipped: $skippedCount\n";
    echo "❌ Errors: $errorCount\n";
    echo "\n";
    
    // Display created accounts
    if (count($createdAccounts) > 0) {
        echo "Created Accounts:\n";
        echo str_repeat("-", 100) . "\n";
        if ($defaultPassword) {
            echo sprintf("%-8s %-10s %-30s %-40s\n", "User ID", "Patient ID", "Name", "Email");
            echo str_repeat("-", 100) . "\n";
            foreach ($createdAccounts as $account) {
                echo sprintf("%-8s %-10s %-30s %-40s\n", 
                    $account['user_id'], 
                    $account['pat_id'],
                    substr($account['name'], 0, 30),
                    substr($account['email'], 0, 40)
                );
            }
            echo "\nDefault password used for all accounts: $defaultPassword\n";
        } else {
            echo sprintf("%-8s %-10s %-30s %-40s %-15s\n", "User ID", "Patient ID", "Name", "Email", "Password");
            echo str_repeat("-", 130) . "\n";
            foreach ($createdAccounts as $account) {
                echo sprintf("%-8s %-10s %-30s %-40s %-15s\n", 
                    $account['user_id'], 
                    $account['pat_id'],
                    substr($account['name'], 0, 30),
                    substr($account['email'], 0, 40),
                    $account['password']
                );
            }
        }
        echo str_repeat("-", 100) . "\n\n";
    }
    
    // Display errors if any
    if (count($errors) > 0) {
        echo "Skipped/Error Details:\n";
        echo str_repeat("-", 100) . "\n";
        foreach ($errors as $error) {
            echo "  • $error\n";
        }
        echo "\n";
    }
    
    if ($successCount > 0) {
        echo "✅ Script completed successfully!\n";
        if (!$defaultPassword) {
            echo "\n⚠️  IMPORTANT: Save the passwords above. These are plaintext and won't be shown again.\n";
            echo "   Patients will need to reset their passwords or you can update them manually.\n";
        }
    } else {
        echo "⚠️  No accounts were created. Check the errors above.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

