-- SQL Script to randomly assign patients, doctors, dates, times, and services to each appointment
-- 
-- Usage: psql -U your_username -d your_database -f randomize-appointments.sql
-- Or in psql: \i randomize-appointments.sql
-- 
-- This script will:
-- 1. Verify that patients, doctors, and services exist
-- 2. Randomly assign a patient, doctor, date, time, and service to each appointment
-- 3. Update all appointments in a single transaction
-- 
-- Note: Dates are randomized between TODAY and 90 days in the future
--       Times are randomized between 08:00 and 17:00 (business hours)

-- Start transaction for atomicity
BEGIN;

-- Check if patients exist
DO $$
DECLARE
    patient_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO patient_count FROM patients;
    IF patient_count = 0 THEN
        RAISE EXCEPTION 'ERROR: No patients found in the database! Please add patients before running this script.';
    END IF;
    RAISE NOTICE 'Found % valid patient(s).', patient_count;
END $$;

-- Check if doctors exist
DO $$
DECLARE
    doctor_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO doctor_count FROM doctors;
    IF doctor_count = 0 THEN
        RAISE EXCEPTION 'ERROR: No doctors found in the database! Please add doctors before running this script.';
    END IF;
    RAISE NOTICE 'Found % valid doctor(s).', doctor_count;
END $$;

-- Check if services exist (optional but recommended)
DO $$
DECLARE
    service_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO service_count FROM services;
    IF service_count = 0 THEN
        RAISE NOTICE 'WARNING: No services found in the database. Service IDs will be set to NULL.';
    ELSE
        RAISE NOTICE 'Found % valid service(s).', service_count;
    END IF;
END $$;

-- Check if appointments exist
DO $$
DECLARE
    appointment_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO appointment_count FROM appointments;
    IF appointment_count = 0 THEN
        RAISE NOTICE 'No appointments found in the database. Nothing to update.';
        RAISE NOTICE 'Transaction will be rolled back.';
        -- Rollback transaction since there's nothing to do
        PERFORM 1/0; -- This will cause an exception and rollback
    END IF;
    RAISE NOTICE 'Found % appointment(s) to update.', appointment_count;
END $$;

-- Update all appointments with random patients, doctors, dates, times, and services
-- This uses arrays for optimal performance - shuffles once, then selects randomly
WITH 
-- Shuffle patients into an array
patient_array AS (
    SELECT ARRAY_AGG(pat_id ORDER BY RANDOM()) as pids
    FROM patients
),
-- Shuffle doctors into an array
doctor_array AS (
    SELECT ARRAY_AGG(doc_id ORDER BY RANDOM()) as dids
    FROM doctors
),
-- Shuffle services into an array (if services exist, otherwise empty array)
service_array AS (
    SELECT COALESCE(ARRAY_AGG(service_id ORDER BY RANDOM()), ARRAY[]::INTEGER[]) as sids
    FROM services
),
-- Assign random patient, doctor, date, time, and service to each appointment
appointment_assignments AS (
    SELECT 
        a.appointment_id,
        -- Random patient
        pa.pids[1 + floor(RANDOM() * array_length(pa.pids, 1))::int] as new_pat_id,
        -- Random doctor
        da.dids[1 + floor(RANDOM() * array_length(da.dids, 1))::int] as new_doc_id,
        -- Random date between today and 90 days in the future (inclusive)
        CURRENT_DATE + (floor(RANDOM() * 91)::int || ' days')::INTERVAL as new_appointment_date,
        -- Random time between 08:00 and 17:00 (business hours, 15-minute intervals, inclusive)
        (TIME '08:00' + (floor(RANDOM() * 37)::int * INTERVAL '15 minutes')) as new_appointment_time,
        -- Random service (NULL if no services exist)
        CASE 
            WHEN array_length(sa.sids, 1) > 0 
            THEN sa.sids[1 + floor(RANDOM() * array_length(sa.sids, 1))::int]
            ELSE NULL
        END as new_service_id
    FROM appointments a
    CROSS JOIN patient_array pa
    CROSS JOIN doctor_array da
    CROSS JOIN service_array sa
)
UPDATE appointments a
SET 
    pat_id = aa.new_pat_id,
    doc_id = aa.new_doc_id,
    appointment_date = aa.new_appointment_date::DATE,
    appointment_time = aa.new_appointment_time::TIME,
    service_id = aa.new_service_id,
    updated_at = CURRENT_TIMESTAMP
FROM appointment_assignments aa
WHERE a.appointment_id = aa.appointment_id;

-- Show summary
DO $$
DECLARE
    updated_count INTEGER;
    patient_count INTEGER;
    doctor_count INTEGER;
    service_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO updated_count FROM appointments;
    SELECT COUNT(*) INTO patient_count FROM patients;
    SELECT COUNT(*) INTO doctor_count FROM doctors;
    SELECT COUNT(*) INTO service_count FROM services;
    
    RAISE NOTICE '';
    RAISE NOTICE '========================================';
    RAISE NOTICE 'Summary';
    RAISE NOTICE '========================================';
    RAISE NOTICE '  Total appointments updated: %', updated_count;
    RAISE NOTICE '  Valid patients available: %', patient_count;
    RAISE NOTICE '  Valid doctors available: %', doctor_count;
    RAISE NOTICE '  Valid services available: %', service_count;
    RAISE NOTICE '';
    RAISE NOTICE 'Randomization details:';
    RAISE NOTICE '  - Dates: Random between TODAY and 90 days in the future';
    RAISE NOTICE '  - Times: Random between 08:00 and 17:00 (15-minute intervals)';
    RAISE NOTICE '  - Patients, Doctors, Services: Random selection from available records';
    RAISE NOTICE '';
    RAISE NOTICE 'Script completed successfully!';
END $$;

-- Commit the transaction
COMMIT;

