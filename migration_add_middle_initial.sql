-- Migration Script: Add Middle Initial to Patients, Doctors, and Staff Tables
-- Date: 2024
-- Description: Adds middle_initial VARCHAR(1) column to patients, doctors, and staff tables

-- Add middle_initial column to patients table
ALTER TABLE patients 
ADD COLUMN IF NOT EXISTS pat_middle_initial VARCHAR(1);

-- Add middle_initial column to doctors table
ALTER TABLE doctors 
ADD COLUMN IF NOT EXISTS doc_middle_initial VARCHAR(1);

-- Add middle_initial column to staff table
ALTER TABLE staff 
ADD COLUMN IF NOT EXISTS staff_middle_initial VARCHAR(1);

-- Verify the columns were added (optional - for checking)
-- SELECT column_name, data_type, is_nullable 
-- FROM information_schema.columns 
-- WHERE table_name IN ('patients', 'doctors', 'staff') 
-- AND column_name LIKE '%middle_initial%';

