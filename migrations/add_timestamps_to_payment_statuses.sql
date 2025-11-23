-- Migration: Add created_at and updated_at columns to payment_statuses table
-- Date: 2025-01-XX

-- Add updated_at column to payment_statuses if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'payment_statuses' AND column_name = 'updated_at'
    ) THEN
        ALTER TABLE payment_statuses ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    END IF;
END $$;

-- Ensure created_at exists (it should already exist, but just in case)
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'payment_statuses' AND column_name = 'created_at'
    ) THEN
        ALTER TABLE payment_statuses ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    END IF;
END $$;

-- Update existing records to have created_at and updated_at timestamps
UPDATE payment_statuses 
SET created_at = CURRENT_TIMESTAMP 
WHERE created_at IS NULL;

UPDATE payment_statuses 
SET updated_at = CURRENT_TIMESTAMP 
WHERE updated_at IS NULL;

