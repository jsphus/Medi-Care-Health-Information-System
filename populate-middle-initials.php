<?php
/**
 * Script to populate empty middle_initial columns with random letters
 * 
 * Usage: php populate-middle-initials.php
 * 
 * This script will:
 * 1. Find all staff, patients, and doctors with no middle_initial value (NULL, empty, or whitespace)
 * 2. Assign random middle initials (A-Z)
 * 3. Update the database records
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Function to generate a random middle initial (A-Z)
function getRandomMiddleInitial($seed = null) {
    if ($seed === null) {
        $seed = rand(0, 25);
    } else {
        // Use seed to get consistent results (modulo 26 for A-Z)
        $seed = abs($seed) % 26;
    }
    
    return chr(65 + $seed); // 65 is ASCII for 'A', 90 is 'Z'
}

// Main execution
try {
    echo "========================================\n";
    echo "Middle Initial Population Script\n";
    echo "========================================\n\n";
    
    $totalFound = 0;
    $totalUpdated = 0;
    $totalFailed = 0;
    
    // Process Staff
    echo "Processing STAFF table...\n";
    echo "----------------------------------------\n";
    $staffQuery = "SELECT staff_id, staff_first_name, staff_last_name, staff_email, staff_middle_initial 
                   FROM staff 
                   WHERE COALESCE(TRIM(staff_middle_initial), '') = ''";
    
    $staff = $db->fetchAll($staffQuery);
    $staffCount = count($staff);
    echo "Found {$staffCount} staff members with empty middle initials.\n";
    
    // Note: Actual updates will happen after confirmation
    
    $totalFound += $staffCount;
    echo "\n";
    
    // Process Patients
    echo "Processing PATIENTS table...\n";
    echo "----------------------------------------\n";
    $patientsQuery = "SELECT pat_id, pat_first_name, pat_last_name, pat_email, pat_middle_initial 
                      FROM patients 
                      WHERE COALESCE(TRIM(pat_middle_initial), '') = ''";
    
    $patients = $db->fetchAll($patientsQuery);
    $patientsCount = count($patients);
    echo "Found {$patientsCount} patients with empty middle initials.\n";
    
    // Note: Actual updates will happen after confirmation
    
    $totalFound += $patientsCount;
    echo "\n";
    
    // Process Doctors
    echo "Processing DOCTORS table...\n";
    echo "----------------------------------------\n";
    $doctorsQuery = "SELECT doc_id, doc_first_name, doc_last_name, doc_email, doc_middle_initial 
                     FROM doctors 
                     WHERE COALESCE(TRIM(doc_middle_initial), '') = ''";
    
    $doctors = $db->fetchAll($doctorsQuery);
    $doctorsCount = count($doctors);
    echo "Found {$doctorsCount} doctors with empty middle initials.\n";
    
    // Note: Actual updates will happen after confirmation
    
    $totalFound += $doctorsCount;
    echo "\n";
    
    // Ask for confirmation before updating (if running interactively)
    if ($totalFound > 0 && php_sapi_name() === 'cli') {
        echo "This will update {$totalFound} record(s) with random middle initials.\n";
        echo "Do you want to continue? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($line) !== 'yes' && strtolower($line) !== 'y') {
            echo "Operation cancelled.\n";
            exit(0);
        }
        echo "\n";
    }
    
    // Reset counters for actual updates
    $totalUpdated = 0;
    $totalFailed = 0;
    
    // Process Staff Updates
    if ($staffCount > 0) {
        echo "Updating STAFF...\n";
        echo "----------------------------------------\n";
        $updated = 0;
        $failed = 0;
        
        foreach ($staff as $member) {
            $staff_id = $member['staff_id'];
            $staff_email = $member['staff_email'];
            
            // Generate a consistent middle initial based on staff_id
            $middle_initial = getRandomMiddleInitial($staff_id);
            
            try {
                $updateQuery = "UPDATE staff 
                               SET staff_middle_initial = :middle_initial 
                               WHERE staff_id = :staff_id";
                
                $db->execute($updateQuery, [
                    'middle_initial' => $middle_initial,
                    'staff_id' => $staff_id
                ]);
                
                echo "✓ Updated staff #{$staff_id} ({$staff_email}) - Middle Initial: {$middle_initial}\n";
                $updated++;
            } catch (Exception $e) {
                echo "✗ Failed to update staff #{$staff_id} ({$staff_email}): " . $e->getMessage() . "\n";
                $failed++;
            }
        }
        
        echo "Staff Summary: {$updated} updated, {$failed} failed\n";
        $totalUpdated += $updated;
        $totalFailed += $failed;
        echo "\n";
    }
    
    // Process Patients Updates
    if ($patientsCount > 0) {
        echo "Updating PATIENTS...\n";
        echo "----------------------------------------\n";
        $updated = 0;
        $failed = 0;
        
        foreach ($patients as $patient) {
            $pat_id = $patient['pat_id'];
            $pat_email = $patient['pat_email'];
            
            // Generate a consistent middle initial based on pat_id
            $middle_initial = getRandomMiddleInitial($pat_id);
            
            try {
                $updateQuery = "UPDATE patients 
                               SET pat_middle_initial = :middle_initial 
                               WHERE pat_id = :pat_id";
                
                $db->execute($updateQuery, [
                    'middle_initial' => $middle_initial,
                    'pat_id' => $pat_id
                ]);
                
                echo "✓ Updated patient #{$pat_id} ({$pat_email}) - Middle Initial: {$middle_initial}\n";
                $updated++;
            } catch (Exception $e) {
                echo "✗ Failed to update patient #{$pat_id} ({$pat_email}): " . $e->getMessage() . "\n";
                $failed++;
            }
        }
        
        echo "Patients Summary: {$updated} updated, {$failed} failed\n";
        $totalUpdated += $updated;
        $totalFailed += $failed;
        echo "\n";
    }
    
    // Process Doctors Updates
    if ($doctorsCount > 0) {
        echo "Updating DOCTORS...\n";
        echo "----------------------------------------\n";
        $updated = 0;
        $failed = 0;
        
        foreach ($doctors as $doctor) {
            $doc_id = $doctor['doc_id'];
            $doc_email = $doctor['doc_email'];
            
            // Generate a consistent middle initial based on doc_id
            $middle_initial = getRandomMiddleInitial($doc_id);
            
            try {
                $updateQuery = "UPDATE doctors 
                               SET doc_middle_initial = :middle_initial 
                               WHERE doc_id = :doc_id";
                
                $db->execute($updateQuery, [
                    'middle_initial' => $middle_initial,
                    'doc_id' => $doc_id
                ]);
                
                echo "✓ Updated doctor #{$doc_id} ({$doc_email}) - Middle Initial: {$middle_initial}\n";
                $updated++;
            } catch (Exception $e) {
                echo "✗ Failed to update doctor #{$doc_id} ({$doc_email}): " . $e->getMessage() . "\n";
                $failed++;
            }
        }
        
        echo "Doctors Summary: {$updated} updated, {$failed} failed\n";
        $totalUpdated += $updated;
        $totalFailed += $failed;
        echo "\n";
    }
    
    // Final Summary
    echo "========================================\n";
    echo "Final Summary\n";
    echo "========================================\n";
    echo "  Total records found: {$totalFound}\n";
    echo "    - Staff: {$staffCount}\n";
    echo "    - Patients: {$patientsCount}\n";
    echo "    - Doctors: {$doctorsCount}\n";
    echo "  Successfully updated: {$totalUpdated}\n";
    echo "  Failed: {$totalFailed}\n";
    
    if ($totalFound === 0) {
        echo "\nNo records need middle initials. Exiting.\n";
    } else {
        echo "\nScript completed successfully!\n";
    }
    
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

