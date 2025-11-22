<?php
/**
 * Script to populate payment statuses with random values from available payment_statuses
 * 
 * Usage: php populate-payment-statuses.php
 * 
 * This script will:
 * 1. Get all available payment status IDs from payment_statuses table
 * 2. Find all payments in the payments table
 * 3. Randomly assign a valid payment_status_id to each payment
 * 4. Update the database records
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Function to get a random payment status ID from available statuses
function getRandomPaymentStatusId(array $statusIds) {
    if (empty($statusIds)) {
        return null;
    }
    return $statusIds[array_rand($statusIds)];
}

// Main execution
try {
    echo "========================================\n";
    echo "Payment Status Population Script\n";
    echo "========================================\n\n";
    
    // First, get all available payment statuses from the database
    echo "Fetching available payment statuses...\n";
    echo "----------------------------------------\n";
    $statusesQuery = "SELECT payment_status_id, status_name FROM payment_statuses ORDER BY payment_status_id";
    $statuses = $db->fetchAll($statusesQuery);
    
    if (empty($statuses)) {
        echo "ERROR: No payment statuses found in the database!\n";
        echo "Please ensure payment_statuses table has at least one status.\n";
        exit(1);
    }
    
    $statusIds = array_column($statuses, 'payment_status_id');
    $statusNames = [];
    foreach ($statuses as $status) {
        $statusNames[$status['payment_status_id']] = $status['status_name'];
    }
    
    echo "Found " . count($statuses) . " payment status(es):\n";
    foreach ($statuses as $status) {
        echo "  - ID: {$status['payment_status_id']}, Name: {$status['status_name']}\n";
    }
    echo "\n";
    
    // Get all payments
    echo "Fetching all payments...\n";
    echo "----------------------------------------\n";
    $paymentsQuery = "SELECT payment_id, appointment_id, payment_amount, payment_status_id 
                     FROM payments 
                     ORDER BY payment_id";
    
    $payments = $db->fetchAll($paymentsQuery);
    $paymentsCount = count($payments);
    
    echo "Found {$paymentsCount} payment(s) to update.\n\n";
    
    if ($paymentsCount === 0) {
        echo "No payments found in the database. Nothing to update.\n";
        exit(0);
    }
    
    // Show current payment status distribution
    if ($paymentsCount > 0) {
        echo "Current payment status distribution:\n";
        echo "----------------------------------------\n";
        $currentStatusQuery = "SELECT ps.payment_status_id, ps.status_name, COUNT(p.payment_id) as count
                              FROM payments p
                              LEFT JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
                              GROUP BY ps.payment_status_id, ps.status_name
                              ORDER BY ps.payment_status_id";
        $currentDistribution = $db->fetchAll($currentStatusQuery);
        foreach ($currentDistribution as $dist) {
            $statusName = $dist['status_name'] ?? 'NULL';
            echo "  - {$statusName}: {$dist['count']} payment(s)\n";
        }
        echo "\n";
    }
    
    // Ask for confirmation before updating (if running interactively)
    if ($paymentsCount > 0 && php_sapi_name() === 'cli') {
        echo "This will randomly assign payment statuses to {$paymentsCount} payment(s).\n";
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
    
    // Update payments with random statuses
    echo "Updating PAYMENTS...\n";
    echo "----------------------------------------\n";
    $updated = 0;
    $failed = 0;
    $statusCounts = array_fill_keys($statusIds, 0);
    
    foreach ($payments as $payment) {
        $payment_id = $payment['payment_id'];
        $appointment_id = $payment['appointment_id'] ?? 'N/A';
        $old_status_id = $payment['payment_status_id'];
        $old_status_name = $old_status_id ? ($statusNames[$old_status_id] ?? 'Unknown') : 'NULL';
        
        // Get a random payment status ID
        $new_status_id = getRandomPaymentStatusId($statusIds);
        $new_status_name = $statusNames[$new_status_id];
        
        try {
            $updateQuery = "UPDATE payments 
                           SET payment_status_id = :payment_status_id, updated_at = NOW()
                           WHERE payment_id = :payment_id";
            
            $db->execute($updateQuery, [
                'payment_status_id' => $new_status_id,
                'payment_id' => $payment_id
            ]);
            
            $statusCounts[$new_status_id]++;
            echo "✓ Updated payment #{$payment_id} (Appointment: {$appointment_id}) - ";
            echo "Status: {$old_status_name} → {$new_status_name}\n";
            $updated++;
        } catch (Exception $e) {
            echo "✗ Failed to update payment #{$payment_id} (Appointment: {$appointment_id}): ";
            echo $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    echo "\nPayments Summary: {$updated} updated, {$failed} failed\n\n";
    
    // Show new payment status distribution
    if ($updated > 0) {
        echo "New payment status distribution:\n";
        echo "----------------------------------------\n";
        foreach ($statusIds as $statusId) {
            $statusName = $statusNames[$statusId];
            $count = $statusCounts[$statusId];
            $percentage = $paymentsCount > 0 ? round(($count / $paymentsCount) * 100, 2) : 0;
            echo "  - {$statusName}: {$count} payment(s) ({$percentage}%)\n";
        }
        echo "\n";
    }
    
    // Final Summary
    echo "========================================\n";
    echo "Final Summary\n";
    echo "========================================\n";
    echo "  Total payments found: {$paymentsCount}\n";
    echo "  Successfully updated: {$updated}\n";
    echo "  Failed: {$failed}\n";
    
    if ($paymentsCount === 0) {
        echo "\nNo payments found. Nothing to update.\n";
    } else {
        echo "\nScript completed successfully!\n";
    }
    
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

