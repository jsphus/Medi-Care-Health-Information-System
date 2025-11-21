<?php
/**
 * Script to populate empty profile_picture_url columns with random pictures
 * 
 * Usage: php populate-profile-pictures.php
 * 
 * This script will:
 * 1. Find all users with empty or NULL profile_picture_url
 * 2. Assign random profile pictures from a placeholder image service
 * 3. Update the database records
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Function to get a random profile picture URL
function getRandomProfilePicture($seed = null) {
    // Use Picsum Photos (Lorem Picsum) for random images
    // You can also use other services like:
    // - https://i.pravatar.cc/300?img={number} (avatar service)
    // - https://randomuser.me/api/portraits/{gender}/{number}.jpg
    
    if ($seed === null) {
        $seed = rand(1, 1000);
    }
    
    // Using Picsum Photos - provides random high-quality images
    // Size: 300x300 for profile pictures
    return "https://picsum.photos/seed/{$seed}/300/300";
    
    // Alternative: Using Pravatar (avatar service)
    // return "https://i.pravatar.cc/300?img=" . rand(1, 70);
    
    // Alternative: Using Random User API
    // $gender = rand(0, 1) ? 'men' : 'women';
    // $number = rand(1, 99);
    // return "https://randomuser.me/api/portraits/{$gender}/{$number}.jpg";
}

// Function to check if profile_picture_url column exists
function ensureProfilePictureColumn($db) {
    try {
        // Check if column exists
        $checkQuery = "SELECT column_name 
                       FROM information_schema.columns 
                       WHERE table_name = 'users' 
                       AND column_name = 'profile_picture_url'";
        
        $result = $db->fetchOne($checkQuery);
        
        if (!$result) {
            echo "Creating profile_picture_url column...\n";
            $db->query("ALTER TABLE users ADD COLUMN profile_picture_url TEXT");
            echo "Column created successfully.\n";
        } else {
            echo "profile_picture_url column already exists.\n";
        }
    } catch (Exception $e) {
        echo "Error checking/creating column: " . $e->getMessage() . "\n";
        throw $e;
    }
}

// Main execution
try {
    echo "========================================\n";
    echo "Profile Picture Population Script\n";
    echo "========================================\n\n";
    
    // Ensure column exists
    ensureProfilePictureColumn($db);
    echo "\n";
    
    // Find all users with empty or NULL profile_picture_url
    echo "Finding users with empty profile pictures...\n";
    $query = "SELECT user_id, user_email, profile_picture_url 
              FROM users 
              WHERE profile_picture_url IS NULL 
                 OR profile_picture_url = '' 
                 OR TRIM(profile_picture_url) = ''";
    
    $users = $db->fetchAll($query);
    
    $count = count($users);
    echo "Found {$count} users with empty profile pictures.\n\n";
    
    if ($count === 0) {
        echo "No users need profile pictures. Exiting.\n";
        exit(0);
    }
    
    // Ask for confirmation (if running interactively)
    if (php_sapi_name() === 'cli') {
        echo "This will update {$count} user(s) with random profile pictures.\n";
        echo "Do you want to continue? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($line) !== 'yes' && strtolower($line) !== 'y') {
            echo "Operation cancelled.\n";
            exit(0);
        }
    }
    
    echo "\nUpdating profile pictures...\n";
    echo "----------------------------------------\n";
    
    $updated = 0;
    $failed = 0;
    
    // Update each user with a random profile picture
    foreach ($users as $user) {
        $user_id = $user['user_id'];
        $user_email = $user['user_email'];
        
        // Generate a unique seed based on user_id to get consistent images
        $seed = $user_id;
        $profile_url = getRandomProfilePicture($seed);
        
        try {
            $updateQuery = "UPDATE users 
                           SET profile_picture_url = :url 
                           WHERE user_id = :user_id";
            
            $db->execute($updateQuery, [
                'url' => $profile_url,
                'user_id' => $user_id
            ]);
            
            echo "✓ Updated user #{$user_id} ({$user_email})\n";
            $updated++;
        } catch (Exception $e) {
            echo "✗ Failed to update user #{$user_id} ({$user_email}): " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    echo "----------------------------------------\n";
    echo "\nSummary:\n";
    echo "  Total users found: {$count}\n";
    echo "  Successfully updated: {$updated}\n";
    echo "  Failed: {$failed}\n";
    echo "\nScript completed successfully!\n";
    
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

