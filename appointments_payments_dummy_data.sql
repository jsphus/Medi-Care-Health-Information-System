-- ============================================================================
-- Comprehensive Appointment & Payment Dummy Data Generator
-- Medi-Care Health Information System
-- ============================================================================
-- This script generates 3000+ appointments with corresponding payment records
-- Date Range: 12 months past to 6 months future
-- All foreign key relationships are maintained
-- Highly varied data with realistic scenarios
-- ============================================================================

-- Part 1: Ensure Reference Data Exists
-- ============================================================================
-- Note: This assumes you have existing patients, doctors, and services
-- If not, you'll need to create them first or modify this script

-- Check existing reference data counts
DO $$
DECLARE
    patient_count INTEGER;
    doctor_count INTEGER;
    service_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO patient_count FROM patients;
    SELECT COUNT(*) INTO doctor_count FROM doctors;
    SELECT COUNT(*) INTO service_count FROM services;
    
    IF patient_count = 0 THEN
        RAISE NOTICE 'WARNING: No patients found. Please create patients first.';
    END IF;
    
    IF doctor_count = 0 THEN
        RAISE NOTICE 'WARNING: No doctors found. Please create doctors first.';
    END IF;
    
    IF service_count = 0 THEN
        RAISE NOTICE 'WARNING: No services found. Please create services first.';
    END IF;
    
    RAISE NOTICE 'Found % patients, % doctors, % services', patient_count, doctor_count, service_count;
END $$;

-- ============================================================================
-- Part 2: Generate Appointments (3000+ records)
-- ============================================================================
-- Appointment ID Format: YYYY-MM-0000001
-- Date Range: 12 months past to 6 months future
-- Status Distribution: Varied by time period
-- Highly varied: notes, times, durations, scenarios
-- ============================================================================

-- Helper function to generate appointment ID
CREATE OR REPLACE FUNCTION generate_appointment_id_seq(
    year_month TEXT,
    seq_num BIGINT
) RETURNS VARCHAR(20) AS $$
BEGIN
    RETURN year_month || '-' || LPAD(seq_num::TEXT, 7, '0');
END;
$$ LANGUAGE plpgsql;

-- Generate appointments using a CTE approach
-- We'll create appointments in batches by month

-- ============================================================================
-- Appointments for Past 12 Months (January 2024 - December 2024)
-- ============================================================================

-- January 2024 Appointments (200 appointments, mostly Completed)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-01', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.90 THEN 2  -- 90% Completed
        WHEN random() < 0.97 THEN 1
        ELSE 3
    END,
    '2024-01-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.15 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.70 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.90 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Regular checkup - patient reports no issues',
        'Follow-up visit for hypertension management',
        'New patient consultation - initial assessment',
        'Routine health screening - annual physical',
        'Prescription refill consultation',
        'Emergency consultation - chest pain evaluation',
        'Diabetes monitoring and medication adjustment',
        'Post-operative follow-up visit',
        'Vaccination appointment - flu shot',
        'Mental health consultation - anxiety management',
        'Pediatric checkup - child wellness exam',
        'Women''s health consultation - gynecological exam',
        'Cardiac evaluation - ECG and stress test',
        'Dermatology consultation - skin condition',
        'Orthopedic consultation - joint pain assessment',
        'Eye examination - vision check',
        'Dental consultation - oral health check',
        'Nutrition counseling - weight management',
        'Physical therapy follow-up',
        'Lab results review and discussion'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.10 THEN 15
        WHEN random() < 0.20 THEN 20
        WHEN random() < 0.50 THEN 30
        WHEN random() < 0.75 THEN 45
        WHEN random() < 0.90 THEN 60
        WHEN random() < 0.97 THEN 90
        ELSE 120
    END,
    '2024-01-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-01-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- February 2024 Appointments (200 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-02', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.88 THEN 2
        WHEN random() < 0.96 THEN 1
        ELSE 3
    END,
    '2024-02-01'::DATE + (random() * 28)::INTEGER,
    CASE 
        WHEN random() < 0.12 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.68 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.88 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'General consultation - annual checkup',
        'Blood pressure monitoring follow-up',
        'Lab results review and discussion',
        'Medication adjustment consultation',
        'Preventive care screening',
        'Chronic condition management',
        'Respiratory infection treatment',
        'Allergy consultation and testing',
        'Thyroid function evaluation',
        'Kidney function assessment',
        'Liver function test review',
        'Cholesterol level monitoring',
        'Bone density scan consultation',
        'Mammography follow-up',
        'Prostate health screening',
        'Sleep disorder consultation',
        'Migraine management',
        'Arthritis treatment plan',
        'Asthma control assessment',
        'Gastrointestinal consultation'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.08 THEN 15
        WHEN random() < 0.18 THEN 20
        WHEN random() < 0.48 THEN 30
        WHEN random() < 0.73 THEN 45
        WHEN random() < 0.88 THEN 60
        WHEN random() < 0.96 THEN 90
        ELSE 120
    END,
    '2024-02-01'::TIMESTAMP + (random() * 28 * INTERVAL '1 day'),
    '2024-02-01'::TIMESTAMP + (random() * 28 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- March 2024 Appointments (200 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-03', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.87 THEN 2
        WHEN random() < 0.95 THEN 1
        ELSE 3
    END,
    '2024-03-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.10 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.65 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.85 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Vaccination appointment - COVID-19 booster',
        'Wellness consultation - lifestyle modification',
        'Symptom evaluation and diagnosis',
        'Post-surgery follow-up',
        'Mental health consultation',
        'Specialist referral consultation',
        'Second opinion consultation',
        'Pre-employment medical examination',
        'Travel health consultation',
        'Sports physical examination',
        'School health clearance',
        'Driver''s license medical exam',
        'Insurance medical assessment',
        'Work-related injury evaluation',
        'Disability evaluation',
        'Prenatal care consultation',
        'Postnatal checkup',
        'Menopause consultation',
        'Elderly care assessment',
        'Family planning consultation'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.12 THEN 15
        WHEN random() < 0.22 THEN 20
        WHEN random() < 0.52 THEN 30
        WHEN random() < 0.77 THEN 45
        WHEN random() < 0.92 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    '2024-03-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-03-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- April 2024 Appointments (200 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-04', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.85 THEN 2
        WHEN random() < 0.94 THEN 1
        ELSE 3
    END,
    '2024-04-01'::DATE + (random() * 29)::INTEGER,
    CASE 
        WHEN random() < 0.13 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.70 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.90 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Routine physical examination',
        'Treatment plan review',
        'Pain management consultation',
        'Nutrition and diet counseling',
        'Family planning consultation',
        'Hormone level testing',
        'Vitamin deficiency assessment',
        'Immune system evaluation',
        'Autoimmune disorder consultation',
        'Neurological examination',
        'Endocrine system assessment',
        'Metabolic disorder evaluation',
        'Infectious disease consultation',
        'Wound care follow-up',
        'Suture removal appointment',
        'Cast removal and follow-up',
        'Physical therapy assessment',
        'Occupational therapy consultation',
        'Speech therapy evaluation',
        'Rehabilitation program review'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.09 THEN 15
        WHEN random() < 0.19 THEN 20
        WHEN random() < 0.49 THEN 30
        WHEN random() < 0.74 THEN 45
        WHEN random() < 0.89 THEN 60
        WHEN random() < 0.97 THEN 90
        ELSE 120
    END,
    '2024-04-01'::TIMESTAMP + (random() * 29 * INTERVAL '1 day'),
    '2024-04-01'::TIMESTAMP + (random() * 29 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- May 2024 Appointments (200 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-05', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.83 THEN 2
        WHEN random() < 0.93 THEN 1
        ELSE 3
    END,
    '2024-05-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.11 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.67 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.87 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Annual health checkup',
        'Chronic disease monitoring',
        'Medication review and adjustment',
        'Health education session',
        'Preventive screening test',
        'Cancer screening consultation',
        'Genetic counseling',
        'Fertility consultation',
        'Pregnancy confirmation',
        'Ultrasound consultation',
        'X-ray results review',
        'CT scan follow-up',
        'MRI results discussion',
        'Biopsy results consultation',
        'Pathology report review',
        'Blood work interpretation',
        'Urine analysis review',
        'Stool test results',
        'Culture and sensitivity results',
        'Allergy test interpretation'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.10 THEN 15
        WHEN random() < 0.20 THEN 20
        WHEN random() < 0.50 THEN 30
        WHEN random() < 0.75 THEN 45
        WHEN random() < 0.90 THEN 60
        WHEN random() < 0.97 THEN 90
        ELSE 120
    END,
    '2024-05-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-05-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- June 2024 Appointments (200 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-06', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.82 THEN 2
        WHEN random() < 0.92 THEN 1
        ELSE 3
    END,
    '2024-06-01'::DATE + (random() * 29)::INTEGER,
    CASE 
        WHEN random() < 0.14 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.71 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.91 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Summer health check',
        'Heat-related illness consultation',
        'Dehydration treatment',
        'Sunburn evaluation',
        'Allergy season consultation',
        'Asthma exacerbation management',
        'Respiratory infection treatment',
        'Swimming-related ear infection',
        'Sports injury evaluation',
        'Fracture assessment',
        'Sprain and strain treatment',
        'Concussion evaluation',
        'Heat stroke management',
        'Food poisoning treatment',
        'Gastroenteritis consultation',
        'Travel health consultation',
        'Vaccination update',
        'Tetanus shot administration',
        'Hepatitis vaccination',
        'Typhoid vaccination'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.11 THEN 15
        WHEN random() < 0.21 THEN 20
        WHEN random() < 0.51 THEN 30
        WHEN random() < 0.76 THEN 45
        WHEN random() < 0.91 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    '2024-06-01'::TIMESTAMP + (random() * 29 * INTERVAL '1 day'),
    '2024-06-01'::TIMESTAMP + (random() * 29 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- July 2024 Appointments (250 appointments, mostly Completed)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-07', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.85 THEN 2
        WHEN random() < 0.95 THEN 1
        ELSE 3
    END,
    '2024-07-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.13 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.69 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.89 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Regular checkup - patient reports no issues',
        'Follow-up visit for previous condition',
        'New patient consultation',
        'Routine health screening',
        'Prescription refill consultation',
        'Emergency consultation - urgent care needed',
        'Monsoon season health check',
        'Dengue fever evaluation',
        'Leptospirosis consultation',
        'Typhoid fever assessment',
        'Malaria screening',
        'Waterborne illness treatment',
        'Skin infection consultation',
        'Fungal infection treatment',
        'Heat exhaustion management',
        'Dehydration treatment',
        'Food safety consultation',
        'Vector-borne disease screening',
        'Tropical disease evaluation',
        'Monsoon-related health issues'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.12 THEN 15
        WHEN random() < 0.22 THEN 20
        WHEN random() < 0.52 THEN 30
        WHEN random() < 0.77 THEN 45
        WHEN random() < 0.92 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    '2024-07-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-07-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- August 2024 Appointments (250 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-08', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.84 THEN 2
        WHEN random() < 0.94 THEN 1
        ELSE 3
    END,
    '2024-08-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.14 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.70 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.90 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'General consultation - annual checkup',
        'Blood pressure monitoring follow-up',
        'Lab results review and discussion',
        'Medication adjustment consultation',
        'Preventive care screening',
        'Chronic condition management',
        'Back-to-school health clearance',
        'Student health examination',
        'Sports participation clearance',
        'School vaccination update',
        'Child development assessment',
        'Adolescent health consultation',
        'Growth and development check',
        'Immunization catch-up',
        'Vision and hearing screening',
        'Dental health check',
        'Nutritional assessment',
        'Behavioral health evaluation',
        'Learning disability assessment',
        'ADHD evaluation'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.11 THEN 15
        WHEN random() < 0.21 THEN 20
        WHEN random() < 0.51 THEN 30
        WHEN random() < 0.76 THEN 45
        WHEN random() < 0.91 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    '2024-08-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-08-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- September 2024 Appointments (250 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-09', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.80 THEN 2
        WHEN random() < 0.92 THEN 1
        ELSE 3
    END,
    '2024-09-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.12 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.68 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.88 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Vaccination appointment',
        'Wellness consultation',
        'Symptom evaluation and diagnosis',
        'Post-surgery follow-up',
        'Mental health consultation',
        'Specialist referral consultation',
        'Second opinion consultation',
        'Flu season preparation',
        'Respiratory health check',
        'Allergy management',
        'Asthma control review',
        'COPD management',
        'Pneumonia follow-up',
        'Bronchitis treatment',
        'Sinusitis consultation',
        'Ear infection treatment',
        'Throat infection evaluation',
        'Cough evaluation',
        'Seasonal allergy consultation',
        'Immune system boost consultation'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.13 THEN 15
        WHEN random() < 0.23 THEN 20
        WHEN random() < 0.53 THEN 30
        WHEN random() < 0.78 THEN 45
        WHEN random() < 0.93 THEN 60
        WHEN random() < 0.99 THEN 90
        ELSE 120
    END,
    '2024-09-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-09-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- October 2024 Appointments (250 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-10', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.75 THEN 2
        WHEN random() < 0.90 THEN 1
        ELSE 3
    END,
    '2024-10-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.15 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.71 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.91 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Routine physical examination',
        'Treatment plan review',
        'Pain management consultation',
        'Nutrition and diet counseling',
        'Family planning consultation',
        'Breast cancer awareness month screening',
        'Mammography consultation',
        'Prostate health screening',
        'Colon cancer screening',
        'Cervical cancer screening',
        'Skin cancer check',
        'Cancer prevention consultation',
        'Oncology follow-up',
        'Chemotherapy consultation',
        'Radiation therapy follow-up',
        'Cancer survivor checkup',
        'Genetic cancer risk assessment',
        'Early detection screening',
        'Cancer support consultation',
        'Palliative care consultation'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.10 THEN 15
        WHEN random() < 0.20 THEN 20
        WHEN random() < 0.50 THEN 30
        WHEN random() < 0.75 THEN 45
        WHEN random() < 0.90 THEN 60
        WHEN random() < 0.97 THEN 90
        ELSE 120
    END,
    '2024-10-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-10-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- November 2024 Appointments (250 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-11', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.70 THEN 2
        WHEN random() < 0.88 THEN 1
        ELSE 3
    END,
    '2024-11-01'::DATE + (random() * 29)::INTEGER,
    CASE 
        WHEN random() < 0.13 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.69 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.89 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Annual health checkup',
        'Chronic disease monitoring',
        'Medication review and adjustment',
        'Health education session',
        'Preventive screening test',
        'Diabetes awareness month check',
        'Men''s health screening',
        'Prostate health evaluation',
        'Erectile dysfunction consultation',
        'Testosterone level check',
        'Menopause management',
        'Hormone replacement therapy',
        'Bone health assessment',
        'Osteoporosis screening',
        'Vitamin D deficiency check',
        'Thyroid function test',
        'Metabolic syndrome evaluation',
        'Cardiovascular risk assessment',
        'Stroke prevention consultation',
        'Heart health check'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.11 THEN 15
        WHEN random() < 0.21 THEN 20
        WHEN random() < 0.51 THEN 30
        WHEN random() < 0.76 THEN 45
        WHEN random() < 0.91 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    '2024-11-01'::TIMESTAMP + (random() * 29 * INTERVAL '1 day'),
    '2024-11-01'::TIMESTAMP + (random() * 29 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- December 2024 Appointments (250 appointments)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2024-12', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.65 THEN 2
        WHEN random() < 0.85 THEN 1
        ELSE 3
    END,
    '2024-12-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.14 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.70 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.90 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'End of year health assessment',
        'Holiday season wellness check',
        'Treatment continuation consultation',
        'Health goal review and planning',
        'Specialist consultation referral',
        'Holiday stress management',
        'Weight management consultation',
        'New Year resolution health planning',
        'Insurance year-end checkup',
        'Medication refill before holidays',
        'Travel health consultation',
        'Holiday food safety',
        'Alcohol consumption counseling',
        'Stress and anxiety management',
        'Sleep disorder consultation',
        'Holiday depression screening',
        'Family health planning',
        'Year-end health summary',
        'Next year health goals',
        'Comprehensive health review'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.12 THEN 15
        WHEN random() < 0.22 THEN 20
        WHEN random() < 0.52 THEN 30
        WHEN random() < 0.77 THEN 45
        WHEN random() < 0.92 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    '2024-12-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day'),
    '2024-12-01'::TIMESTAMP + (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- ============================================================================
-- Current Month Appointments (January 2025 - mix of statuses)
-- ============================================================================

-- January 2025 Appointments (300 appointments - mix of past and future)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2025-01', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.40 THEN 2
        WHEN random() < 0.90 THEN 1
        ELSE 3
    END,
    CASE 
        WHEN random() < 0.4 THEN CURRENT_DATE - (random() * 30)::INTEGER
        ELSE CURRENT_DATE + (random() * 30)::INTEGER
    END,
    CASE 
        WHEN random() < 0.15 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.70 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.90 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'New year health resolution consultation',
        'Post-holiday health check',
        'Regular scheduled appointment',
        'Follow-up from previous visit',
        'Preventive care appointment',
        'Urgent care consultation',
        'New Year fitness consultation',
        'Weight loss program start',
        'Smoking cessation consultation',
        'Alcohol reduction counseling',
        'Exercise program planning',
        'Nutrition plan development',
        'Wellness program enrollment',
        'Health coaching session',
        'Lifestyle modification consultation',
        'Stress reduction program',
        'Mental wellness check',
        'New Year health goals',
        'Annual health planning',
        'Comprehensive wellness assessment'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.12 THEN 15
        WHEN random() < 0.22 THEN 20
        WHEN random() < 0.52 THEN 30
        WHEN random() < 0.77 THEN 45
        WHEN random() < 0.92 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    CURRENT_TIMESTAMP - (random() * 60 * INTERVAL '1 day'),
    CURRENT_TIMESTAMP - (random() * 60 * INTERVAL '1 day')
FROM generate_series(1, 300) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- ============================================================================
-- Future Appointments (February 2025 - April 2025)
-- ============================================================================

-- February 2025 Appointments (250 appointments - mostly Scheduled)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2025-02', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.85 THEN 1
        WHEN random() < 0.95 THEN 2
        ELSE 3
    END,
    '2025-02-01'::DATE + (random() * 27)::INTEGER,
    CASE 
        WHEN random() < 0.13 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.69 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.89 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Upcoming routine checkup',
        'Scheduled follow-up appointment',
        'Preventive screening scheduled',
        'Treatment continuation plan',
        'Specialist consultation scheduled',
        'Valentine''s health check',
        'Heart health month screening',
        'Cardiovascular assessment',
        'Love your heart consultation',
        'Cardiac risk evaluation',
        'Heart disease prevention',
        'Blood pressure management',
        'Cholesterol check',
        'Cardiac stress test',
        'EKG consultation',
        'Echocardiogram follow-up',
        'Heart health education',
        'Cardiac rehabilitation',
        'Heart medication review',
        'Cardiovascular wellness'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.11 THEN 15
        WHEN random() < 0.21 THEN 20
        WHEN random() < 0.51 THEN 30
        WHEN random() < 0.76 THEN 45
        WHEN random() < 0.91 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    CURRENT_TIMESTAMP - (random() * 30 * INTERVAL '1 day'),
    CURRENT_TIMESTAMP - (random() * 30 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- March 2025 Appointments (250 appointments - mostly Scheduled)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2025-03', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    CASE 
        WHEN random() < 0.88 THEN 1
        WHEN random() < 0.96 THEN 2
        ELSE 3
    END,
    '2025-03-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.14 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.70 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.90 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Spring health assessment',
        'Quarterly health review',
        'Scheduled consultation',
        'Wellness program follow-up',
        'Health maintenance appointment',
        'Kidney health month screening',
        'Renal function evaluation',
        'Kidney disease prevention',
        'Urinary tract health',
        'Bladder health check',
        'Prostate health screening',
        'Nephrology consultation',
        'Dialysis consultation',
        'Kidney stone prevention',
        'Hydration counseling',
        'Renal diet consultation',
        'Kidney function test',
        'Urine analysis review',
        'Renal ultrasound follow-up',
        'Kidney transplant evaluation'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.12 THEN 15
        WHEN random() < 0.22 THEN 20
        WHEN random() < 0.52 THEN 30
        WHEN random() < 0.77 THEN 45
        WHEN random() < 0.92 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    CURRENT_TIMESTAMP - (random() * 20 * INTERVAL '1 day'),
    CURRENT_TIMESTAMP - (random() * 20 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- April 2025 Appointments (250 appointments - all Scheduled)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2025-04', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    1,
    '2025-04-01'::DATE + (random() * 29)::INTEGER,
    CASE 
        WHEN random() < 0.15 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.71 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.91 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Future scheduled appointment',
        'Upcoming health check',
        'Planned consultation',
        'Scheduled follow-up',
        'Future wellness appointment',
        'Summer preparation health check',
        'Pre-summer health screening',
        'Heat safety consultation',
        'Sun protection counseling',
        'Skin cancer prevention',
        'Summer fitness planning',
        'Travel health preparation',
        'Vaccination before travel',
        'Tropical disease prevention',
        'Heat illness prevention',
        'Hydration planning',
        'Summer activity clearance',
        'Beach health safety',
        'Outdoor activity preparation',
        'Summer wellness planning'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.11 THEN 15
        WHEN random() < 0.21 THEN 20
        WHEN random() < 0.51 THEN 30
        WHEN random() < 0.76 THEN 45
        WHEN random() < 0.91 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    CURRENT_TIMESTAMP - (random() * 10 * INTERVAL '1 day'),
    CURRENT_TIMESTAMP - (random() * 10 * INTERVAL '1 day')
FROM generate_series(1, 250) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- May 2025 Appointments (200 appointments - all Scheduled)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2025-05', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    1,
    '2025-05-01'::DATE + (random() * 30)::INTEGER,
    CASE 
        WHEN random() < 0.13 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.69 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.89 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Scheduled health consultation',
        'Upcoming medical checkup',
        'Future appointment',
        'Planned health screening',
        'Scheduled wellness visit',
        'Summer health maintenance',
        'Pre-vacation health check',
        'Travel medical clearance',
        'Summer activity preparation',
        'Heat safety consultation',
        'Sun protection review',
        'Skin health check',
        'Summer fitness assessment',
        'Hydration counseling',
        'Outdoor safety consultation',
        'Summer nutrition planning',
        'Beach health preparation',
        'Camping health clearance',
        'Summer sports clearance',
        'Seasonal health planning'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.10 THEN 15
        WHEN random() < 0.20 THEN 20
        WHEN random() < 0.50 THEN 30
        WHEN random() < 0.75 THEN 45
        WHEN random() < 0.90 THEN 60
        WHEN random() < 0.97 THEN 90
        ELSE 120
    END,
    CURRENT_TIMESTAMP - (random() * 5 * INTERVAL '1 day'),
    CURRENT_TIMESTAMP - (random() * 5 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- June 2025 Appointments (200 appointments - all Scheduled)
INSERT INTO appointments (appointment_id, pat_id, doc_id, service_id, status_id, appointment_date, appointment_time, appointment_notes, appointment_duration, created_at, updated_at)
SELECT 
    generate_appointment_id_seq('2025-06', seq),
    (SELECT pat_id FROM patients ORDER BY random() LIMIT 1),
    (SELECT doc_id FROM doctors WHERE doc_status = 'active' ORDER BY random() LIMIT 1),
    (SELECT service_id FROM services ORDER BY random() LIMIT 1),
    1,
    '2025-06-01'::DATE + (random() * 29)::INTEGER,
    CASE 
        WHEN random() < 0.14 THEN TIME '07:00:00' + (random() * 1 * INTERVAL '1 hour')
        WHEN random() < 0.70 THEN TIME '08:00:00' + (random() * 8 * INTERVAL '1 hour')
        WHEN random() < 0.90 THEN TIME '16:00:00' + (random() * 2 * INTERVAL '1 hour')
        ELSE TIME '19:00:00' + (random() * 1 * INTERVAL '1 hour')
    END,
    (ARRAY[
        'Future health consultation',
        'Scheduled medical appointment',
        'Upcoming wellness check',
        'Planned health screening',
        'Future follow-up visit',
        'Mid-year health assessment',
        'Summer wellness check',
        'Pre-summer health screening',
        'Heat illness prevention',
        'Summer safety consultation',
        'Sun protection planning',
        'Hydration management',
        'Summer fitness program',
        'Outdoor activity clearance',
        'Travel health preparation',
        'Vacation health planning',
        'Summer nutrition counseling',
        'Seasonal allergy management',
        'Summer skin care',
        'Heat safety education'
    ])[1 + floor(random() * 20)::int],
    CASE 
        WHEN random() < 0.12 THEN 15
        WHEN random() < 0.22 THEN 20
        WHEN random() < 0.52 THEN 30
        WHEN random() < 0.77 THEN 45
        WHEN random() < 0.92 THEN 60
        WHEN random() < 0.98 THEN 90
        ELSE 120
    END,
    CURRENT_TIMESTAMP - (random() * 3 * INTERVAL '1 day'),
    CURRENT_TIMESTAMP - (random() * 3 * INTERVAL '1 day')
FROM generate_series(1, 200) seq
ON CONFLICT (appointment_id) DO NOTHING;

-- ============================================================================
-- Part 3: Generate Payments (1 payment per appointment)
-- ============================================================================
-- Payment Logic:
-- - Completed appointments: 90% Paid, 10% Refunded
-- - Scheduled appointments: 60% Pending, 40% Paid
-- - Cancelled appointments: 50% Refunded, 50% Pending
-- Payment Methods: 30% Cash, 25% Mobile Payment, 20% Credit Card, 15% Debit Card, 5% Bank Transfer, 5% Insurance
-- ============================================================================

INSERT INTO payments (appointment_id, payment_amount, payment_method_id, payment_status_id, payment_date, payment_reference, payment_notes, created_at, updated_at)
SELECT 
    a.appointment_id,
    -- Payment amount: use service price if available, otherwise doctor consultation fee, or default 1500
    COALESCE(
        (SELECT service_price FROM services WHERE service_id = a.service_id),
        (SELECT doc_consultation_fee FROM doctors WHERE doc_id = a.doc_id),
        1500.00
    ) + (random() * 500 - 250),  -- Add some variation ±250
    -- Payment method distribution
    CASE 
        WHEN random() < 0.30 THEN 1  -- Cash
        WHEN random() < 0.55 THEN 4  -- Mobile Payment (GCash)
        WHEN random() < 0.75 THEN 3  -- Credit Card
        WHEN random() < 0.90 THEN 2  -- Debit Card
        WHEN random() < 0.95 THEN 5  -- Bank Transfer
        ELSE 6  -- Insurance
    END,
    -- Payment status based on appointment status
    CASE 
        WHEN a.status_id = 2 THEN  -- Completed
            CASE WHEN random() < 0.90 THEN 1 ELSE 3 END  -- 90% Paid, 10% Refunded
        WHEN a.status_id = 1 THEN  -- Scheduled
            CASE WHEN random() < 0.60 THEN 2 ELSE 1 END  -- 60% Pending, 40% Paid
        ELSE  -- Cancelled (status_id = 3)
            CASE WHEN random() < 0.50 THEN 3 ELSE 2 END  -- 50% Refunded, 50% Pending
    END,
    -- Payment date: same as appointment date or slightly before/after
    CASE 
        WHEN a.status_id = 2 THEN  -- Completed: payment usually before or on appointment date
            a.appointment_date::TIMESTAMP + a.appointment_time::INTERVAL - (random() * 2 * INTERVAL '1 day')
        WHEN a.status_id = 1 THEN  -- Scheduled: payment can be before or after
            CASE 
                WHEN random() < 0.7 THEN a.appointment_date::TIMESTAMP + a.appointment_time::INTERVAL - (random() * 7 * INTERVAL '1 day')
                ELSE a.appointment_date::TIMESTAMP + a.appointment_time::INTERVAL + (random() * 1 * INTERVAL '1 day')
            END
        ELSE  -- Cancelled: payment usually before cancellation
            a.appointment_date::TIMESTAMP + a.appointment_time::INTERVAL - (random() * 5 * INTERVAL '1 day')
    END,
    -- Payment reference (transaction ID, receipt number, etc.)
    CASE 
        WHEN random() < 0.3 THEN 'TXN-' || LPAD((random() * 999999)::INTEGER::TEXT, 6, '0')
        WHEN random() < 0.6 THEN 'RCP-' || LPAD((random() * 999999)::INTEGER::TEXT, 6, '0')
        WHEN random() < 0.8 THEN 'PAY-' || LPAD((random() * 999999)::INTEGER::TEXT, 6, '0')
        WHEN random() < 0.9 THEN 'GCASH-' || LPAD((random() * 999999)::INTEGER::TEXT, 6, '0')
        ELSE 'REF-' || LPAD((random() * 999999)::INTEGER::TEXT, 6, '0')
    END,
    -- Payment notes
    CASE 
        WHEN random() < 0.4 THEN NULL
        WHEN random() < 0.6 THEN 'Payment received in full'
        WHEN random() < 0.75 THEN 'Advance payment for scheduled appointment'
        WHEN random() < 0.85 THEN 'Payment processed successfully'
        WHEN random() < 0.92 THEN 'Partial payment received'
        WHEN random() < 0.97 THEN 'Payment refunded due to cancellation'
        ELSE 'Payment pending confirmation'
    END,
    a.created_at,
    a.updated_at
FROM appointments a
WHERE NOT EXISTS (
    SELECT 1 FROM payments p WHERE p.appointment_id = a.appointment_id
)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- Part 4: Validation Queries
-- ============================================================================

-- Summary Statistics
SELECT 
    '=== APPOINTMENT & PAYMENT DATA SUMMARY ===' as summary;

SELECT 
    'Total Appointments' as metric,
    COUNT(*) as count
FROM appointments;

SELECT 
    'Total Payments' as metric,
    COUNT(*) as count
FROM payments;

SELECT 
    'Appointments without Payments' as metric,
    COUNT(*) as count
FROM appointments a
LEFT JOIN payments p ON a.appointment_id = p.appointment_id
WHERE p.payment_id IS NULL;

SELECT 
    'Payments without Appointments' as metric,
    COUNT(*) as count
FROM payments p
LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
WHERE a.appointment_id IS NULL;

-- Appointment Status Distribution
SELECT 
    'Appointment Status Distribution' as metric,
    s.status_name,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM appointments), 2) as percentage
FROM appointments a
JOIN appointment_statuses s ON a.status_id = s.status_id
GROUP BY s.status_name, s.status_id
ORDER BY s.status_id;

-- Payment Status Distribution
SELECT 
    'Payment Status Distribution' as metric,
    ps.status_name,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM payments), 2) as percentage
FROM payments p
JOIN payment_statuses ps ON p.payment_status_id = ps.payment_status_id
GROUP BY ps.status_name, ps.payment_status_id
ORDER BY ps.payment_status_id;

-- Payment Method Distribution
SELECT 
    'Payment Method Distribution' as metric,
    pm.method_name,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM payments), 2) as percentage
FROM payments p
JOIN payment_methods pm ON p.payment_method_id = pm.method_id
GROUP BY pm.method_name, pm.method_id
ORDER BY pm.method_id;

-- Date Range Check
SELECT 
    'Appointment Date Range' as metric,
    MIN(appointment_date) as earliest_date,
    MAX(appointment_date) as latest_date,
    COUNT(*) as total_appointments
FROM appointments;

-- Payment Amount Statistics
SELECT 
    'Payment Amount Statistics' as metric,
    COUNT(*) as total_payments,
    ROUND(MIN(payment_amount), 2) as min_amount,
    ROUND(MAX(payment_amount), 2) as max_amount,
    ROUND(AVG(payment_amount), 2) as avg_amount,
    ROUND(SUM(payment_amount), 2) as total_amount
FROM payments;

-- Foreign Key Integrity Check
SELECT 
    'Foreign Key Integrity - Invalid Patient IDs' as metric,
    COUNT(*) as count
FROM appointments a
WHERE NOT EXISTS (SELECT 1 FROM patients p WHERE p.pat_id = a.pat_id);

SELECT 
    'Foreign Key Integrity - Invalid Doctor IDs' as metric,
    COUNT(*) as count
FROM appointments a
WHERE NOT EXISTS (SELECT 1 FROM doctors d WHERE d.doc_id = a.doc_id);

SELECT 
    'Foreign Key Integrity - Invalid Service IDs' as metric,
    COUNT(*) as count
FROM appointments a
WHERE a.service_id IS NOT NULL 
  AND NOT EXISTS (SELECT 1 FROM services s WHERE s.service_id = a.service_id);

SELECT 
    'Foreign Key Integrity - Invalid Appointment IDs in Payments' as metric,
    COUNT(*) as count
FROM payments p
WHERE NOT EXISTS (SELECT 1 FROM appointments a WHERE a.appointment_id = p.appointment_id);

-- Appointment-Payment Relationship Check
SELECT 
    'Appointments with Multiple Payments' as metric,
    COUNT(*) as count
FROM (
    SELECT appointment_id, COUNT(*) as payment_count
    FROM payments
    GROUP BY appointment_id
    HAVING COUNT(*) > 1
) multiple_payments;

-- Monthly Appointment Distribution
SELECT 
    'Monthly Appointment Distribution' as metric,
    TO_CHAR(appointment_date, 'YYYY-MM') as month,
    COUNT(*) as appointment_count
FROM appointments
GROUP BY TO_CHAR(appointment_date, 'YYYY-MM')
ORDER BY month;

-- Clean up helper function
DROP FUNCTION IF EXISTS generate_appointment_id_seq(TEXT, BIGINT);

-- Final Summary
SELECT 
    '=== DATA GENERATION COMPLETE ===' as summary,
    (SELECT COUNT(*) FROM appointments) as total_appointments,
    (SELECT COUNT(*) FROM payments) as total_payments,
    (SELECT COUNT(*) FROM appointments a LEFT JOIN payments p ON a.appointment_id = p.appointment_id WHERE p.payment_id IS NULL) as appointments_without_payments;

