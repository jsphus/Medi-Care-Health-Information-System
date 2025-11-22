-- SQL Script to populate medical records for completed appointments
-- 
-- Usage: psql -U your_username -d your_database -f populate-medical-records.sql
-- Or in psql: \i populate-medical-records.sql
-- 
-- This script will:
-- 1. Find all completed appointments that don't have medical records yet
-- 2. Create medical records with randomized diagnosis and prescription data
-- 3. Set visit_date to be within the week of the appointment date (0-6 days after)
-- 4. Randomize diagnosis and treatments for each appointment

-- Start transaction for atomicity
BEGIN;

-- Check if there are completed appointments without medical records
DO $$
DECLARE
    appointment_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO appointment_count
    FROM appointments a
    JOIN appointment_statuses st ON a.status_id = st.status_id
    WHERE LOWER(st.status_name) = 'completed'
    AND NOT EXISTS (
        SELECT 1 FROM medical_records mr 
        WHERE mr.appt_id = a.appointment_id
    );
    
    IF appointment_count = 0 THEN
        RAISE NOTICE 'No completed appointments found without medical records. Nothing to do.';
        RAISE NOTICE 'Transaction will be rolled back.';
        PERFORM 1/0; -- This will cause an exception and rollback
    END IF;
    
    RAISE NOTICE 'Found % completed appointment(s) without medical records.', appointment_count;
END $$;

-- Insert medical records with randomized diagnosis and prescriptions
-- Visit date will be within the week of appointment date (0-6 days after)
WITH 
-- Diagnosis templates array
diagnosis_array AS (
    SELECT ARRAY[
        'Hypertension (High Blood Pressure) - Stage 1. Blood pressure readings consistently elevated. Recommend lifestyle modifications and medication.',
        'Type 2 Diabetes Mellitus - Well controlled with current medication. Continue monitoring blood glucose levels.',
        'Upper Respiratory Tract Infection - Viral etiology suspected. Symptomatic treatment recommended.',
        'Acute Gastroenteritis - Likely viral. Advise oral rehydration and rest.',
        'Migraine Headache - Tension-type headache. Prescribed pain management and stress reduction techniques.',
        'Seasonal Allergic Rhinitis - Allergic reaction to environmental allergens. Antihistamine therapy initiated.',
        'Gastroesophageal Reflux Disease (GERD) - Acid reflux symptoms. Lifestyle modifications and medication prescribed.',
        'Urinary Tract Infection - Uncomplicated UTI. Antibiotic course prescribed.',
        'Bronchitis - Acute bronchitis, likely viral. Supportive care and monitoring recommended.',
        'Anxiety Disorder - Generalized anxiety. Counseling and medication management discussed.',
        'Atrial Fibrillation - Irregular heart rhythm detected. Anticoagulation therapy initiated.',
        'Coronary Artery Disease - Stable angina. Continue current medication regimen.',
        'Hypertension - Stage 2. Blood pressure management with combination therapy.',
        'Heart Failure - Congestive heart failure, well managed. Continue current treatment plan.',
        'Arrhythmia - Premature ventricular contractions. Monitoring recommended.',
        'Chronic Migraine - Frequent migraine episodes. Preventive medication prescribed.',
        'Epilepsy - Seizure disorder, well controlled. Continue anticonvulsant therapy.',
        'Peripheral Neuropathy - Diabetic neuropathy. Pain management and glycemic control emphasized.',
        'Parkinson''s Disease - Motor symptoms stable. Medication adjustment made.',
        'Multiple Sclerosis - Relapsing-remitting MS. Disease-modifying therapy continued.',
        'Acute Otitis Media - Middle ear infection. Antibiotic course prescribed.',
        'Asthma - Pediatric asthma, well controlled. Inhaler technique reviewed.',
        'Atopic Dermatitis - Eczema flare-up. Topical treatment and moisturizing regimen.',
        'Viral Pharyngitis - Sore throat, likely viral. Symptomatic treatment.',
        'Conjunctivitis - Pink eye, bacterial. Antibiotic eye drops prescribed.',
        'Cataract - Age-related cataract, mild. Annual monitoring recommended.',
        'Glaucoma - Open-angle glaucoma. Eye drops prescribed for pressure control.',
        'Diabetic Retinopathy - Early stage. Tight glycemic control emphasized.',
        'Dry Eye Syndrome - Chronic dry eyes. Artificial tears and lifestyle modifications.',
        'Refractive Error - Myopia progression. Updated prescription provided.',
        'Dental Caries - Multiple cavities detected. Fillings scheduled.',
        'Periodontal Disease - Gingivitis. Deep cleaning and improved oral hygiene recommended.',
        'Tooth Abscess - Periapical abscess. Root canal treatment recommended.',
        'Bruxism - Teeth grinding. Night guard recommended.',
        'Oral Thrush - Fungal infection. Antifungal treatment prescribed.',
        'Acne Vulgaris - Moderate acne. Topical and oral treatment prescribed.',
        'Psoriasis - Plaque psoriasis. Topical steroids and phototherapy discussed.',
        'Eczema - Atopic dermatitis. Emollient and corticosteroid treatment.',
        'Contact Dermatitis - Allergic reaction. Avoidance of allergen and topical treatment.',
        'Seborrheic Dermatitis - Scalp and facial dermatitis. Medicated shampoo prescribed.',
        'Osteoarthritis - Knee osteoarthritis. Pain management and physical therapy recommended.',
        'Lower Back Pain - Mechanical low back pain. NSAIDs and physical therapy.',
        'Rotator Cuff Tear - Shoulder injury. Conservative management with physical therapy.',
        'Plantar Fasciitis - Heel pain. Stretching exercises and orthotics recommended.',
        'Carpal Tunnel Syndrome - Wrist pain. Wrist splint and activity modification.',
        'Menstrual Irregularity - Irregular periods. Hormonal evaluation recommended.',
        'Vaginal Candidiasis - Yeast infection. Antifungal treatment prescribed.',
        'Polycystic Ovary Syndrome (PCOS) - Hormonal imbalance. Lifestyle modifications and medication.',
        'Menopause Symptoms - Perimenopausal symptoms. Hormone replacement therapy discussed.',
        'Major Depressive Disorder - Depression, moderate severity. Antidepressant and therapy recommended.',
        'Generalized Anxiety Disorder - Anxiety symptoms. Medication and cognitive behavioral therapy.',
        'Bipolar Disorder - Type II, stable. Mood stabilizer continued.',
        'Attention Deficit Hyperactivity Disorder (ADHD) - Adult ADHD. Stimulant medication prescribed.',
        'Insomnia - Sleep disorder. Sleep hygiene and medication discussed.'
    ] as diagnoses
),
-- Prescription templates array
prescription_array AS (
    SELECT ARRAY[
        'Paracetamol 500mg - Take 1-2 tablets every 4-6 hours as needed for pain/fever. Maximum 4g per day.',
        'Ibuprofen 400mg - Take 1 tablet three times daily with food. Continue for 5-7 days.',
        'Amoxicillin 500mg - Take 1 capsule three times daily for 7 days. Complete full course.',
        'Loratadine 10mg - Take 1 tablet once daily for allergy symptoms.',
        'Omeprazole 20mg - Take 1 capsule once daily before breakfast for acid reflux.',
        'Metformin 500mg - Take 1 tablet twice daily with meals for diabetes management.',
        'Amlodipine 5mg - Take 1 tablet once daily for blood pressure control.',
        'Atorvastatin 20mg - Take 1 tablet once daily at bedtime for cholesterol management.',
        'Salbutamol Inhaler - Use 2 puffs as needed for asthma symptoms. Maximum 8 puffs per day.',
        'Fluticasone Nasal Spray - 2 sprays in each nostril once daily for allergic rhinitis.',
        'Doxycycline 100mg - Take 1 capsule twice daily for 7 days. Take with food and avoid dairy.',
        'Ciprofloxacin 500mg - Take 1 tablet twice daily for 5 days. Complete full course.',
        'Prednisone 20mg - Take 2 tablets once daily for 5 days, then taper. Take with food.',
        'Gabapentin 300mg - Take 1 capsule three times daily for neuropathic pain.',
        'Sertraline 50mg - Take 1 tablet once daily in the morning for depression/anxiety.',
        'Topical Hydrocortisone 1% - Apply thin layer to affected area twice daily for 7 days.',
        'Artificial Tears - Use 1-2 drops in each eye as needed, up to 4 times daily.',
        'Multivitamin - Take 1 tablet once daily with food.',
        'Calcium + Vitamin D - Take 1 tablet twice daily with meals.',
        'Iron Supplement 65mg - Take 1 tablet once daily on empty stomach for anemia.'
    ] as prescriptions
),
-- Get appointments that need medical records
appointments_to_process AS (
    SELECT 
        a.appointment_id,
        a.appointment_date,
        -- Generate random seed based on appointment_id for varied but consistent results
        -- Use hashtext and convert to positive integer in safe range
        -- Cast to BIGINT first to handle ABS of minimum negative integer safely
        (ABS(hashtext(a.appointment_id)::BIGINT) % 1000000)::INTEGER as seed
    FROM appointments a
    JOIN appointment_statuses st ON a.status_id = st.status_id
    WHERE LOWER(st.status_name) = 'completed'
    AND NOT EXISTS (
        SELECT 1 FROM medical_records mr 
        WHERE mr.appt_id = a.appointment_id
    )
),
-- Generate randomized data for each appointment
medical_record_data AS (
    SELECT 
        apt.appointment_id,
        apt.appointment_date,
        apt.seed,
        -- Random diagnosis (using seed for consistency)
        da.diagnoses[1 + (apt.seed % array_length(da.diagnoses, 1))] as diagnosis,
        -- Random visit date within the week (0-6 days after appointment date)
        apt.appointment_date + ((apt.seed % 7)::int || ' days')::INTERVAL as visit_date,
        -- Determine number of prescriptions (70% chance of 1, 25% chance of 2, 5% chance of 3)
        -- Use modulo to avoid integer overflow
        CASE 
            WHEN ((apt.seed % 1000) * 7) % 100 < 70 THEN 1
            WHEN ((apt.seed % 1000) * 7) % 100 < 95 THEN 2
            ELSE 3
        END as prescription_count
    FROM appointments_to_process apt
    CROSS JOIN diagnosis_array da
),
-- Generate prescriptions (1-3 per record)
prescription_data AS (
    SELECT 
        mrd.appointment_id,
        mrd.visit_date,
        mrd.diagnosis,
        -- Build prescription string with 1-3 medications
        -- Use different multipliers to ensure variety in prescription selection
        -- Use modulo operations to prevent integer overflow
        STRING_AGG(
            pa.prescriptions[1 + (((mrd.seed % 1000) + ((idx * 17) % 100) + ((mrd.seed % 13))) % array_length(pa.prescriptions, 1))],
            E'\n\n'
            ORDER BY idx
        ) as prescription
    FROM medical_record_data mrd
    CROSS JOIN prescription_array pa
    CROSS JOIN LATERAL generate_series(0, mrd.prescription_count - 1) as idx
    GROUP BY mrd.appointment_id, mrd.visit_date, mrd.diagnosis
)
-- Insert medical records
INSERT INTO medical_records (
    appt_id,
    med_rec_diagnosis,
    med_rec_prescription,
    med_rec_visit_date,
    med_rec_created_at,
    med_rec_updated_at
)
SELECT 
    pd.appointment_id,
    pd.diagnosis,
    pd.prescription,
    pd.visit_date::DATE,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
FROM prescription_data pd;

-- Show summary
DO $$
DECLARE
    created_count INTEGER;
    total_appointments INTEGER;
BEGIN
    SELECT COUNT(*) INTO created_count FROM medical_records;
    SELECT COUNT(*) INTO total_appointments 
    FROM appointments a
    JOIN appointment_statuses st ON a.status_id = st.status_id
    WHERE LOWER(st.status_name) = 'completed';
    
    RAISE NOTICE '';
    RAISE NOTICE '========================================';
    RAISE NOTICE 'Summary';
    RAISE NOTICE '========================================';
    RAISE NOTICE '  Total medical records created: %', created_count;
    RAISE NOTICE '  Total completed appointments: %', total_appointments;
    RAISE NOTICE '';
    RAISE NOTICE 'Randomization details:';
    RAISE NOTICE '  - Diagnosis: Randomly selected from 55+ templates';
    RAISE NOTICE '  - Prescriptions: 1-3 medications per record (randomized)';
    RAISE NOTICE '  - Visit Date: Within the week of appointment date (0-6 days after)';
    RAISE NOTICE '';
    RAISE NOTICE 'Script completed successfully!';
END $$;

-- Commit the transaction
COMMIT;

