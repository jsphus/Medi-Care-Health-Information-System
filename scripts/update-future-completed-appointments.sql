-- SQL Script to update future appointments with "completed" status to "scheduled"
-- 
-- Usage: psql -U your_username -d your_database -f update-future-completed-appointments.sql
-- Or in psql: \i update-future-completed-appointments.sql
-- 
-- This script will:
-- 1. Find all appointments that are in the future (appointment_date > CURRENT_DATE)
-- 2. That have status "completed"
-- 3. Change their status to "scheduled"

-- Start transaction for atomicity
BEGIN;

-- Check if there are future completed appointments to update
DO $$
DECLARE
    appointment_count INTEGER;
    scheduled_status_id INTEGER;
    completed_status_id INTEGER;
BEGIN
    -- Get the status_id for "scheduled"
    SELECT status_id INTO scheduled_status_id
    FROM appointment_statuses
    WHERE LOWER(status_name) = 'scheduled'
    LIMIT 1;
    
    IF scheduled_status_id IS NULL THEN
        RAISE EXCEPTION 'ERROR: "Scheduled" status not found in appointment_statuses table!';
    END IF;
    
    -- Get the status_id for "completed"
    SELECT status_id INTO completed_status_id
    FROM appointment_statuses
    WHERE LOWER(status_name) = 'completed'
    LIMIT 1;
    
    IF completed_status_id IS NULL THEN
        RAISE EXCEPTION 'ERROR: "Completed" status not found in appointment_statuses table!';
    END IF;
    
    -- Count future completed appointments
    SELECT COUNT(*) INTO appointment_count
    FROM appointments a
    WHERE a.appointment_date > CURRENT_DATE
    AND a.status_id = completed_status_id;
    
    IF appointment_count = 0 THEN
        RAISE NOTICE 'No future appointments with "completed" status found. Nothing to update.';
        RAISE NOTICE 'Transaction will be rolled back.';
        PERFORM 1/0; -- This will cause an exception and rollback
    END IF;
    
    RAISE NOTICE 'Found % future appointment(s) with "completed" status to update.', appointment_count;
    RAISE NOTICE 'Scheduled status_id: %', scheduled_status_id;
    RAISE NOTICE 'Completed status_id: %', completed_status_id;
END $$;

-- Update future completed appointments to scheduled
DO $$
DECLARE
    scheduled_status_id INTEGER;
    completed_status_id INTEGER;
    updated_count INTEGER;
BEGIN
    -- Get the status_id for "scheduled"
    SELECT status_id INTO scheduled_status_id
    FROM appointment_statuses
    WHERE LOWER(status_name) = 'scheduled'
    LIMIT 1;
    
    -- Get the status_id for "completed"
    SELECT status_id INTO completed_status_id
    FROM appointment_statuses
    WHERE LOWER(status_name) = 'completed'
    LIMIT 1;
    
    -- Update appointments
    UPDATE appointments
    SET 
        status_id = scheduled_status_id,
        updated_at = CURRENT_TIMESTAMP
    WHERE appointment_date > CURRENT_DATE
    AND status_id = completed_status_id;
    
    GET DIAGNOSTICS updated_count = ROW_COUNT;
    
    RAISE NOTICE '';
    RAISE NOTICE '========================================';
    RAISE NOTICE 'Update Summary';
    RAISE NOTICE '========================================';
    RAISE NOTICE '  Appointments updated: %', updated_count;
    RAISE NOTICE '  Status changed: "Completed" -> "Scheduled"';
    RAISE NOTICE '  Criteria: appointment_date > CURRENT_DATE AND status = "Completed"';
    RAISE NOTICE '';
    RAISE NOTICE 'Script completed successfully!';
END $$;

-- Commit the transaction
COMMIT;

