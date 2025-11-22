<?php
/**
 * Script to randomly assign patients and doctors to each appointment
 * 
 * Usage: php randomize-appointments.php
 * 
 * This script will:
 * 1. Fetch all appointments from the database
 * 2. Get all valid patient IDs from the patients table
 * 3. Get all valid doctor IDs from the doctors table
 * 4. Randomly assign a patient and doctor to each appointment
 * 5. Update the database records
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Main execution
try {
    echo "========================================\n";
    echo "Randomize Appointments Script\n";
    echo "========================================\n\n";
    
    // Step 1: Get all valid patient IDs
    echo "Fetching valid patient IDs...\n";
    $patientsQuery = "SELECT pat_id FROM patients ORDER BY pat_id";
    $patients = $db->fetchAll($patientsQuery);
    $patientIds = array_column($patients, 'pat_id');
    $patientCount = count($patientIds);
    
    if ($patientCount === 0) {
        echo "ERROR: No patients found in the database!\n";
        echo "Please add patients before running this script.\n";
        exit(1);
    }
    
    echo "Found {$patientCount} valid patient(s).\n\n";
    
    // Step 2: Get all valid doctor IDs
    echo "Fetching valid doctor IDs...\n";
    $doctorsQuery = "SELECT doc_id FROM doctors ORDER BY doc_id";
    $doctors = $db->fetchAll($doctorsQuery);
    $doctorIds = array_column($doctors, 'doc_id');
    $doctorCount = count($doctorIds);
    
    if ($doctorCount === 0) {
        echo "ERROR: No doctors found in the database!\n";
        echo "Please add doctors before running this script.\n";
        exit(1);
    }
    
    echo "Found {$doctorCount} valid doctor(s).\n\n";
    
    // Step 3: Get all appointments
    echo "Fetching all appointments...\n";
    $appointmentsQuery = "SELECT appointment_id, pat_id, doc_id, appointment_date, appointment_time 
                          FROM appointments 
                          ORDER BY appointment_id";
    $appointments = $db->fetchAll($appointmentsQuery);
    $appointmentCount = count($appointments);
    
    if ($appointmentCount === 0) {
        echo "No appointments found in the database.\n";
        echo "Nothing to update. Exiting.\n";
        exit(0);
    }
    
    echo "Found {$appointmentCount} appointment(s).\n\n";
    
    // Display preview
    echo "Preview of changes:\n";
    echo "----------------------------------------\n";
    $previewCount = min(5, $appointmentCount);
    for ($i = 0; $i < $previewCount; $i++) {
        $appt = $appointments[$i];
        $randomPatientId = $patientIds[array_rand($patientIds)];
        $randomDoctorId = $doctorIds[array_rand($doctorIds)];
        echo "Appointment: {$appt['appointment_id']}\n";
        echo "  Current -> Patient: {$appt['pat_id']}, Doctor: {$appt['doc_id']}\n";
        echo "  New     -> Patient: {$randomPatientId}, Doctor: {$randomDoctorId}\n";
        echo "\n";
    }
    
    if ($appointmentCount > 5) {
        echo "... and " . ($appointmentCount - 5) . " more appointment(s)\n\n";
    }
    
    // Ask for confirmation before updating (if running interactively)
    if (php_sapi_name() === 'cli') {
        echo "This will update {$appointmentCount} appointment(s) with random patients and doctors.\n";
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
    
    // Step 4: Update each appointment with random patient and doctor
    echo "Updating appointments...\n";
    echo "----------------------------------------\n";
    
    $updated = 0;
    $failed = 0;
    
    // Start transaction for atomicity
    $db->beginTransaction();
    
    try {
        foreach ($appointments as $appointment) {
            $appointment_id = $appointment['appointment_id'];
            $current_pat_id = $appointment['pat_id'];
            $current_doc_id = $appointment['doc_id'];
            
            // Randomly select a patient and doctor from valid IDs
            $randomPatientId = $patientIds[array_rand($patientIds)];
            $randomDoctorId = $doctorIds[array_rand($doctorIds)];
            
            // Update the appointment
            $updateQuery = "UPDATE appointments 
                           SET pat_id = :pat_id, 
                               doc_id = :doc_id,
                               updated_at = CURRENT_TIMESTAMP
                           WHERE appointment_id = :appointment_id";
            
            $db->execute($updateQuery, [
                'pat_id' => $randomPatientId,
                'doc_id' => $randomDoctorId,
                'appointment_id' => $appointment_id
            ]);
            
            echo "✓ Updated appointment {$appointment_id}: Patient {$current_pat_id} -> {$randomPatientId}, Doctor {$current_doc_id} -> {$randomDoctorId}\n";
            $updated++;
        }
        
        // Commit the transaction
        $db->commit();
        
        echo "\n----------------------------------------\n";
        echo "Summary: {$updated} appointment(s) updated successfully.\n";
        
    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        echo "\nERROR: " . $e->getMessage() . "\n";
        echo "All changes have been rolled back.\n";
        $failed = $appointmentCount;
        throw $e;
    }
    
    // Final Summary
    echo "\n========================================\n";
    echo "Final Summary\n";
    echo "========================================\n";
    echo "  Total appointments found: {$appointmentCount}\n";
    echo "  Successfully updated: {$updated}\n";
    echo "  Failed: {$failed}\n";
    echo "  Valid patients available: {$patientCount}\n";
    echo "  Valid doctors available: {$doctorCount}\n";
    
    if ($updated > 0) {
        echo "\nScript completed successfully!\n";
    }
    
} catch (Exception $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

