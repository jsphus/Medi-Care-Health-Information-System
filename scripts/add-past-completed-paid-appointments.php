<?php
/**
 * Script to add past appointments that have been completed and paid
 * 
 * Usage: php add-past-completed-paid-appointments.php [count] [days_back]
 * 
 * Parameters:
 *   count (optional): Number of appointments to create (default: 10)
 *   days_back (optional): How many days back to create appointments (default: 1825 = 5 years)
 * 
 * This script will:
 * 1. Fetch all valid patient IDs from the patients table
 * 2. Fetch all valid doctor IDs from the doctors table
 * 3. Get status IDs for "Completed" and "Paid"
 * 4. Get payment method IDs
 * 5. Create past appointments with "Completed" status randomly distributed over 5 years
 * 6. Create payments with "Paid" status for each appointment
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Appointment.php';

// Initialize database connection
$db = Database::getInstance();

// Get command line arguments
$count = isset($argv[1]) ? (int)$argv[1] : 10;
$daysBack = isset($argv[2]) ? (int)$argv[2] : 1825; // Default: 5 years (1825 days)

// Validate parameters
if ($count <= 0) {
    echo "ERROR: Count must be greater than 0.\n";
    exit(1);
}

if ($daysBack <= 0) {
    echo "ERROR: Days back must be greater than 0.\n";
    exit(1);
}

// Main execution
try {
    echo "========================================\n";
    echo "Add Past Completed & Paid Appointments Script\n";
    echo "========================================\n\n";
    
    // Step 1: Get all valid patient IDs
    echo "Fetching valid patient IDs...\n";
    $patients = $db->fetchAll("SELECT pat_id FROM patients ORDER BY pat_id");
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
    $doctors = $db->fetchAll("SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY doc_id");
    $doctorIds = array_column($doctors, 'doc_id');
    $doctorCount = count($doctorIds);
    
    if ($doctorCount === 0) {
        echo "ERROR: No active doctors found in the database!\n";
        echo "Please add doctors before running this script.\n";
        exit(1);
    }
    
    echo "Found {$doctorCount} valid doctor(s).\n\n";
    
    // Step 3: Get status IDs
    echo "Fetching status IDs...\n";
    $completedStatus = $db->fetchOne("SELECT status_id FROM appointment_statuses WHERE LOWER(status_name) = 'completed' LIMIT 1");
    if (!$completedStatus) {
        echo "ERROR: 'Completed' status not found in appointment_statuses table!\n";
        exit(1);
    }
    $completedStatusId = $completedStatus['status_id'];
    echo "Found 'Completed' status (ID: {$completedStatusId}).\n";
    
    $paidStatus = $db->fetchOne("SELECT payment_status_id FROM payment_statuses WHERE LOWER(status_name) = 'paid' LIMIT 1");
    if (!$paidStatus) {
        echo "ERROR: 'Paid' status not found in payment_statuses table!\n";
        exit(1);
    }
    $paidStatusId = $paidStatus['payment_status_id'];
    echo "Found 'Paid' payment status (ID: {$paidStatusId}).\n\n";
    
    // Step 4: Get payment methods
    echo "Fetching payment methods...\n";
    $paymentMethods = $db->fetchAll("SELECT method_id FROM payment_methods WHERE is_active = TRUE ORDER BY method_id");
    $paymentMethodIds = array_column($paymentMethods, 'method_id');
    $paymentMethodCount = count($paymentMethodIds);
    
    if ($paymentMethodCount === 0) {
        echo "ERROR: No active payment methods found in the database!\n";
        exit(1);
    }
    
    echo "Found {$paymentMethodCount} payment method(s).\n\n";
    
    // Step 5: Get services (optional)
    echo "Fetching services...\n";
    $services = $db->fetchAll("SELECT service_id, service_price FROM services ORDER BY service_id");
    $serviceCount = count($services);
    echo "Found {$serviceCount} service(s).\n\n";
    
    // Step 6: Get doctor consultation fees
    echo "Fetching doctor consultation fees...\n";
    $doctorFees = $db->fetchAll("SELECT doc_id, doc_consultation_fee FROM doctors WHERE doc_status = 'active'");
    $feesByDoctor = [];
    foreach ($doctorFees as $fee) {
        $feesByDoctor[$fee['doc_id']] = $fee['doc_consultation_fee'] ?? 0;
    }
    echo "Fetched consultation fees for {$doctorCount} doctor(s).\n\n";
    
    // Display configuration
    $years = round($daysBack / 365, 1);
    $startDate = date('Y-m-d', strtotime("-{$daysBack} days"));
    $endDate = date('Y-m-d', strtotime('-1 day'));
    
    echo "Configuration:\n";
    echo "----------------------------------------\n";
    echo "  Appointments to create: {$count}\n";
    echo "  Days back: {$daysBack} ({$years} years)\n";
    echo "  Date range: {$startDate} to {$endDate}\n";
    echo "\n";
    
    // Ask for confirmation before creating (if running interactively)
    if (php_sapi_name() === 'cli') {
        echo "This will create {$count} past appointment(s) with 'Completed' status and 'Paid' payment records.\n";
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
    
    // Step 7: Create appointments and payments
    echo "Creating appointments and payments...\n";
    echo "----------------------------------------\n";
    
    $created = 0;
    $failed = 0;
    $totalPaymentAmount = 0;
    
    // Start transaction for atomicity
    $db->beginTransaction();
    
    try {
        for ($i = 0; $i < $count; $i++) {
            // Randomly select patient, doctor, and service
            $randomPatientId = $patientIds[array_rand($patientIds)];
            $randomDoctorId = $doctorIds[array_rand($doctorIds)];
            $randomServiceId = $serviceCount > 0 ? $services[array_rand($services)]['service_id'] : null;
            $randomPaymentMethodId = $paymentMethodIds[array_rand($paymentMethodIds)];
            
            // Generate random past date (within days_back range)
            $randomDaysAgo = rand(1, $daysBack);
            $appointmentDate = date('Y-m-d', strtotime("-{$randomDaysAgo} days"));
            
            // Generate random time (between 8:00 AM and 5:00 PM)
            $randomHour = rand(8, 17);
            $randomMinute = rand(0, 59);
            $appointmentTime = sprintf('%02d:%02d:00', $randomHour, $randomMinute);
            
            // Calculate payment amount - service price corresponds directly to payment
            $paymentAmount = 0;
            $servicePrice = 0;
            $consultationFee = $feesByDoctor[$randomDoctorId] ?? 0;
            
            // Get service price if service is selected
            if ($randomServiceId) {
                foreach ($services as $service) {
                    if ($service['service_id'] == $randomServiceId) {
                        $servicePrice = $service['service_price'] ?? 0;
                        break;
                    }
                }
            }
            
            // Payment amount corresponds directly to service price if service is selected
            // Otherwise, use consultation fee
            if ($servicePrice > 0) {
                // Service has a corresponding payment - use the service price exactly
                $paymentAmount = $servicePrice;
            } else {
                // No service selected - use consultation fee with small variation for realism
                $paymentAmount = $consultationFee > 0 ? $consultationFee : 500; // Default to 500 if no fee set
                
                // Add small random variation (±5%) only for consultation-only appointments
                $variation = rand(-5, 5) / 100; // -5% to +5%
                $paymentAmount = round($paymentAmount * (1 + $variation), 2);
            }
            
            // Ensure minimum payment amount
            if ($paymentAmount < 100) {
                $paymentAmount = 100;
            }
            
            // Generate appointment ID
            $year = date('Y', strtotime($appointmentDate));
            $month = date('m', strtotime($appointmentDate));
            $prefix = "$year-$month-";
            
            // Get the last appointment ID for this month
            $lastAppointment = $db->fetchOne(
                "SELECT appointment_id FROM appointments WHERE appointment_id LIKE :prefix ORDER BY appointment_id DESC LIMIT 1",
                ['prefix' => $prefix . '%']
            );
            
            if ($lastAppointment) {
                $lastNum = (int)substr($lastAppointment['appointment_id'], -7);
                $newNum = $lastNum + 1;
            } else {
                $newNum = 1;
            }
            
            $appointmentId = $prefix . str_pad($newNum, 7, '0', STR_PAD_LEFT);
            
            // Insert appointment directly (bypassing validation)
            $createdAt = date('Y-m-d H:i:s', strtotime($appointmentDate . ' ' . $appointmentTime));
            $appointmentDuration = 30; // Default duration
            
            $db->execute("
                INSERT INTO appointments (
                    appointment_id, pat_id, doc_id, service_id, status_id,
                    appointment_date, appointment_time, appointment_duration,
                    created_at, updated_at
                ) VALUES (
                    :appointment_id, :pat_id, :doc_id, :service_id, :status_id,
                    :appointment_date, :appointment_time, :appointment_duration,
                    :created_at, :updated_at
                )
            ", [
                'appointment_id' => $appointmentId,
                'pat_id' => $randomPatientId,
                'doc_id' => $randomDoctorId,
                'service_id' => $randomServiceId,
                'status_id' => $completedStatusId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'appointment_duration' => $appointmentDuration,
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);
            
            // Insert payment
            $paymentDate = date('Y-m-d H:i:s', strtotime($appointmentDate . ' ' . $appointmentTime . ' +30 minutes'));
            
            $db->execute("
                INSERT INTO payments (
                    appointment_id, payment_amount, payment_method_id, payment_status_id,
                    payment_date, created_at, updated_at
                ) VALUES (
                    :appointment_id, :payment_amount, :payment_method_id, :payment_status_id,
                    :payment_date, :created_at, :updated_at
                )
            ", [
                'appointment_id' => $appointmentId,
                'payment_amount' => $paymentAmount,
                'payment_method_id' => $randomPaymentMethodId,
                'payment_status_id' => $paidStatusId,
                'payment_date' => $paymentDate,
                'created_at' => $paymentDate,
                'updated_at' => $paymentDate
            ]);
            
            $serviceInfo = $randomServiceId ? " (Service ID: {$randomServiceId})" : " (Consultation only)";
            echo "✓ Created appointment {$appointmentId} for patient {$randomPatientId} with doctor {$randomDoctorId} on {$appointmentDate} at {$appointmentTime}{$serviceInfo}\n";
            echo "  → Payment: ₱" . number_format($paymentAmount, 2) . " (Status: Paid)\n";
            $created++;
            $totalPaymentAmount += $paymentAmount;
        }
        
        // Commit the transaction
        $db->commit();
        
        echo "\n----------------------------------------\n";
        echo "Summary: {$created} appointment(s) and payment(s) created successfully.\n";
        echo "Total payment amount: ₱" . number_format($totalPaymentAmount, 2) . "\n";
        echo "Average payment amount: ₱" . number_format($totalPaymentAmount / max($created, 1), 2) . "\n";
        
    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        echo "\nERROR: " . $e->getMessage() . "\n";
        echo "All changes have been rolled back.\n";
        $failed = $count;
        throw $e;
    }
    
    // Final Summary
    echo "\n========================================\n";
    echo "Final Summary\n";
    echo "========================================\n";
    echo "  Appointments requested: {$count}\n";
    echo "  Successfully created: {$created}\n";
    echo "  Failed: {$failed}\n";
    echo "  Total payment amount: ₱" . number_format($totalPaymentAmount, 2) . "\n";
    echo "  Average payment amount: ₱" . number_format($totalPaymentAmount / max($created, 1), 2) . "\n";
    echo "  Valid patients available: {$patientCount}\n";
    echo "  Valid doctors available: {$doctorCount}\n";
    echo "  Services available: {$serviceCount}\n";
    echo "  Payment methods available: {$paymentMethodCount}\n";
    
    if ($created > 0) {
        echo "\nScript completed successfully!\n";
    }
    
} catch (Exception $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

