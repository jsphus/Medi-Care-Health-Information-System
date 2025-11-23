-- SQL Script to add updated_at column to appointment_statuses table
-- 
-- Usage: psql -U your_username -d your_database -f add-updated-at-to-appointment-statuses.sql
-- Or in psql: \i add-updated-at-to-appointment-statuses.sql
-- 
-- This script will:
-- 1. Add the updated_at column to appointment_statuses table if it doesn't exist
-- 2. Set a default value of CURRENT_TIMESTAMP for new rows
-- 3. Update existing rows to set updated_at = created_at (if created_at exists) or CURRENT_TIMESTAMP

-- Start transaction for atomicity
BEGIN;

-- Check if column exists and add it if it doesn't
DO $$
BEGIN
    -- Check if updated_at column already exists
    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.columns 
        WHERE table_name = 'appointment_statuses' 
        AND column_name = 'updated_at'
    ) THEN
        -- Add the updated_at column
        ALTER TABLE appointment_statuses 
        ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
        
        -- Update existing rows: set updated_at = created_at if created_at exists, otherwise CURRENT_TIMESTAMP
        UPDATE appointment_statuses 
        SET updated_at = COALESCE(created_at, CURRENT_TIMESTAMP)
        WHERE updated_at IS NULL;
        
        -- Add default value constraint (PostgreSQL allows setting default after column creation)
        ALTER TABLE appointment_statuses 
        ALTER COLUMN updated_at SET DEFAULT CURRENT_TIMESTAMP;
        
        RAISE NOTICE 'Successfully added updated_at column to appointment_statuses table.';
        RAISE NOTICE 'Existing rows updated with appropriate timestamp values.';
    ELSE
        RAISE NOTICE 'Column updated_at already exists in appointment_statuses table.';
        RAISE NOTICE 'No changes made.';
    END IF;
END $$;

-- Commit the transaction
COMMIT;

