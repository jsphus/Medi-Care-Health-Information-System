-- SQL Script to seed doctor schedules with random days and times
-- 
-- Usage: psql -U your_username -d your_database -f seed-doctor-schedules.sql
-- Or in psql: \i seed-doctor-schedules.sql
-- 
-- This script will:
-- 1. Get all active doctors from the database
-- 2. Generate random schedules starting from TODAY
-- 3. Create full-day availability schedules for each doctor
-- 4. Spread schedules across many days (up to 90 days in the future)
-- 5. Randomly assign working hours (start_time and end_time) for each day
-- 6. Each schedule represents a full day of availability
-- 
-- Note: The script will skip duplicate schedules (same doc_id, schedule_date, start_time)
--       due to the UNIQUE constraint, so it's safe to run multiple times

-- Start transaction for atomicity
BEGIN;

-- Check if doctors exist
DO $$
DECLARE
    doctor_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO doctor_count FROM doctors WHERE doc_status = 'active';
    IF doctor_count = 0 THEN
        RAISE EXCEPTION 'ERROR: No active doctors found in the database! Please add doctors before running this script.';
    END IF;
    RAISE NOTICE 'Found % active doctor(s).', doctor_count;
END $$;

-- Generate random schedules for all doctors using set-based approach
-- This creates full-day availability schedules per doctor across many days
WITH 
-- Get all active doctors
active_doctors AS (
    SELECT doc_id FROM doctors WHERE doc_status = 'active'
),
-- Generate date range: from today up to 90 days ahead
date_range AS (
    SELECT CURRENT_DATE + (generate_series(0, 90) || ' days')::INTERVAL as schedule_date
),
-- Cross join to create all possible doctor-date combinations
all_combinations AS (
    SELECT 
        d.doc_id,
        dr.schedule_date,
        -- Use random seed based on combination for consistent randomization
        (ABS(hashtext(d.doc_id::TEXT || dr.schedule_date::TEXT)) % 1000000)::INTEGER as seed
    FROM active_doctors d
    CROSS JOIN date_range dr
),
-- Add random working hours for each day (full day availability)
schedules_with_hours AS (
    SELECT 
        doc_id,
        schedule_date,
        seed,
        -- Random start time between 07:00 and 10:00 (morning start)
        -- Using seed to ensure consistency per doctor-date combination
        (TIME '07:00' + ((seed % 4) * INTERVAL '1 hour')) as start_time,
        -- Random end time between 16:00 and 18:00 (evening end)
        -- Use different part of seed to ensure variety and that end > start
        (TIME '16:00' + (((seed * 7) % 3) * INTERVAL '1 hour')) as end_time
    FROM all_combinations
),
-- Ensure valid full-day schedules (start < end, reasonable hours)
-- Note: Since start_time is 07:00-10:00 and end_time is 16:00-18:00, end_time is always after start_time
adjusted_schedules AS (
    SELECT 
        doc_id,
        schedule_date,
        -- Start time: clamp between 07:00 and 10:00
        CASE 
            WHEN start_time < '07:00'::TIME THEN '07:00'::TIME
            WHEN start_time > '10:00'::TIME THEN '10:00'::TIME
            ELSE start_time
        END as start_time,
        -- End time: clamp between 16:00 and 18:00
        CASE 
            WHEN end_time < '16:00'::TIME THEN '16:00'::TIME
            WHEN end_time > '18:00'::TIME THEN '18:00'::TIME
            ELSE end_time
        END as end_time,
        seed
    FROM schedules_with_hours
),
-- Randomly select which days each doctor is available (approximately 40-70% of days)
selected_schedules AS (
    SELECT 
        doc_id,
        schedule_date,
        start_time,
        end_time
    FROM adjusted_schedules
    WHERE 
        -- Randomly select approximately 40-70% of days per doctor
        -- This gives roughly 36-64 days of availability per doctor
        (seed % 100) < (40 + ((seed * 11) % 31))  -- Random percentage between 40-70%
        -- Duplicates are handled by the UNIQUE constraint
)
-- Insert schedules (ON CONFLICT will skip duplicates)
INSERT INTO schedules (doc_id, schedule_date, start_time, end_time, created_at, updated_at)
SELECT 
    doc_id,
    schedule_date,
    start_time,
    end_time,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
FROM selected_schedules
ON CONFLICT (doc_id, schedule_date, start_time) DO NOTHING;

-- Show summary
DO $$
DECLARE
    schedules_created INTEGER;
    schedules_skipped INTEGER;
    total_doctors INTEGER;
BEGIN
    -- Count newly created schedules (those created in this transaction)
    -- Note: This is approximate since we can't easily track what was just inserted
    SELECT COUNT(*) INTO schedules_created FROM schedules;
    SELECT COUNT(*) INTO total_doctors FROM doctors WHERE doc_status = 'active';
    
    RAISE NOTICE '';
    RAISE NOTICE '========================================';
    RAISE NOTICE 'Summary';
    RAISE NOTICE '========================================';
    RAISE NOTICE '  Total schedules in database: %', schedules_created;
    RAISE NOTICE '  Total active doctors: %', total_doctors;
    RAISE NOTICE '  (Note: Duplicate schedules were automatically skipped)';
    RAISE NOTICE '';
    RAISE NOTICE 'Randomization details:';
    RAISE NOTICE '  - Start date: TODAY';
    RAISE NOTICE '  - Date range: Up to 90 days in the future';
    RAISE NOTICE '  - Start time: Random between 07:00 and 10:00';
    RAISE NOTICE '  - End time: Random between 16:00 and 18:00';
    RAISE NOTICE '  - Schedule type: Full day availability per schedule';
    RAISE NOTICE '  - Selection: Randomly selects 40-70%% of days per doctor';
    RAISE NOTICE '  - Approximate schedules per doctor: 36-64 days';
    RAISE NOTICE '';
    RAISE NOTICE 'Script completed successfully!';
END $$;

-- Show final statistics
DO $$
DECLARE
    total_schedules INTEGER;
    total_doctors INTEGER;
    schedules_per_doctor_avg NUMERIC;
    earliest_date DATE;
    latest_date DATE;
BEGIN
    SELECT COUNT(*) INTO total_schedules FROM schedules;
    SELECT COUNT(*) INTO total_doctors FROM doctors WHERE doc_status = 'active';
    SELECT AVG(doctor_schedules) INTO schedules_per_doctor_avg
    FROM (
        SELECT doc_id, COUNT(*) as doctor_schedules
        FROM schedules
        GROUP BY doc_id
    ) subquery;
    SELECT MIN(schedule_date) INTO earliest_date FROM schedules;
    SELECT MAX(schedule_date) INTO latest_date FROM schedules;
    
    RAISE NOTICE '';
    RAISE NOTICE '========================================';
    RAISE NOTICE 'Final Statistics';
    RAISE NOTICE '========================================';
    RAISE NOTICE '  Total schedules in database: %', total_schedules;
    RAISE NOTICE '  Total active doctors: %', total_doctors;
    RAISE NOTICE '  Average schedules per doctor: %', schedules_per_doctor_avg;
    RAISE NOTICE '  Earliest schedule date: %', earliest_date;
    RAISE NOTICE '  Latest schedule date: %', latest_date;
    RAISE NOTICE '';
END $$;

-- Commit the transaction
COMMIT;

