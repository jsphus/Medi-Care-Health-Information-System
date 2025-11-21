<?php
/**
 * Script to populate empty profile_picture_url columns with random pictures (Auto mode - no confirmation)
 * 
 * Usage: php populate-profile-pictures-auto.php
 * 
 * This script will automatically update all users with empty profile pictures
 * without asking for confirmation. Use this for automated scripts or when you're sure.
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Function to get a random profile picture URL
function getRandomProfilePicture($seed = null) {
    if ($seed === null) {
        $seed = rand(1, 1000);
    }
    
    // Using Picsum Photos - provides random high-quality images
    // Size: 300x300 for profile pictures
    return "https://picsum.photos/seed/{$seed}/300/300";
}

// Function to check if profile_picture_url column exists
function ensureProfilePictureColumn($db) {
    try {
        $checkQuery = "SELECT column_name 
                       FROM information_schema.columns 
                       WHERE table_name = 'users' 
                       AND column_name = 'profile_picture_url'";
        
        $result = $db->fetchOne($checkQuery);
        
        if (!$result) {
            $db->query("ALTER TABLE users ADD COLUMN profile_picture_url TEXT");
            return true;
        }
        return false;
    } catch (Exception $e) {
        throw new Exception("Error checking/creating column: " . $e->getMessage());
    }
}

// Main execution
try {
    echo "========================================\n";
    echo "Profile Picture Population Script (Auto)\n";
    echo "========================================\n\n";
    
    // Ensure column exists
    $created = ensureProfilePictureColumn($db);
    if ($created) {
        echo "Created profile_picture_url column.\n\n";
    }
    
    // Find all users with empty or NULL profile_picture_url
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
    
    echo "Updating profile pictures...\n";
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
    exit(1);
}

