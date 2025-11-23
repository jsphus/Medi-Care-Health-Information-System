-- Migration: Remove staff_status column from staff table
-- Date: 2024
-- Description: This migration removes the staff_status field from the staff table

-- Drop the staff_status column from the staff table
ALTER TABLE staff DROP COLUMN IF EXISTS staff_status;

