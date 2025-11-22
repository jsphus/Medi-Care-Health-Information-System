<?php
/**
 * Script to populate medical records for completed appointments
 * 
 * Usage: php populate-medical-records.php
 * 
 * This script will:
 * 1. Find all completed appointments that don't have medical records yet
 * 2. Create medical records with realistic diagnosis and prescription data
 * 3. Link records to appointments and set visit_date to match appointment_date
 */

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/config.php';

// Initialize database connection
$db = Database::getInstance();

// Medical diagnosis templates based on common conditions
$diagnosisTemplates = [
    // General/Family Medicine
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
    
    // Cardiology
    'Atrial Fibrillation - Irregular heart rhythm detected. Anticoagulation therapy initiated.',
    'Coronary Artery Disease - Stable angina. Continue current medication regimen.',
    'Hypertension - Stage 2. Blood pressure management with combination therapy.',
    'Heart Failure - Congestive heart failure, well managed. Continue current treatment plan.',
    'Arrhythmia - Premature ventricular contractions. Monitoring recommended.',
    
    // Neurology
    'Chronic Migraine - Frequent migraine episodes. Preventive medication prescribed.',
    'Epilepsy - Seizure disorder, well controlled. Continue anticonvulsant therapy.',
    'Peripheral Neuropathy - Diabetic neuropathy. Pain management and glycemic control emphasized.',
    'Parkinson\'s Disease - Motor symptoms stable. Medication adjustment made.',
    'Multiple Sclerosis - Relapsing-remitting MS. Disease-modifying therapy continued.',
    
    // Pediatrics
    'Acute Otitis Media - Middle ear infection. Antibiotic course prescribed.',
    'Asthma - Pediatric asthma, well controlled. Inhaler technique reviewed.',
    'Atopic Dermatitis - Eczema flare-up. Topical treatment and moisturizing regimen.',
    'Viral Pharyngitis - Sore throat, likely viral. Symptomatic treatment.',
    'Conjunctivitis - Pink eye, bacterial. Antibiotic eye drops prescribed.',
    
    // Ophthalmology
    'Cataract - Age-related cataract, mild. Annual monitoring recommended.',
    'Glaucoma - Open-angle glaucoma. Eye drops prescribed for pressure control.',
    'Diabetic Retinopathy - Early stage. Tight glycemic control emphasized.',
    'Dry Eye Syndrome - Chronic dry eyes. Artificial tears and lifestyle modifications.',
    'Refractive Error - Myopia progression. Updated prescription provided.',
    
    // Dentistry
    'Dental Caries - Multiple cavities detected. Fillings scheduled.',
    'Periodontal Disease - Gingivitis. Deep cleaning and improved oral hygiene recommended.',
    'Tooth Abscess - Periapical abscess. Root canal treatment recommended.',
    'Bruxism - Teeth grinding. Night guard recommended.',
    'Oral Thrush - Fungal infection. Antifungal treatment prescribed.',
    
    // Dermatology
    'Acne Vulgaris - Moderate acne. Topical and oral treatment prescribed.',
    'Psoriasis - Plaque psoriasis. Topical steroids and phototherapy discussed.',
    'Eczema - Atopic dermatitis. Emollient and corticosteroid treatment.',
    'Contact Dermatitis - Allergic reaction. Avoidance of allergen and topical treatment.',
    'Seborrheic Dermatitis - Scalp and facial dermatitis. Medicated shampoo prescribed.',
    
    // Orthopedics
    'Osteoarthritis - Knee osteoarthritis. Pain management and physical therapy recommended.',
    'Lower Back Pain - Mechanical low back pain. NSAIDs and physical therapy.',
    'Rotator Cuff Tear - Shoulder injury. Conservative management with physical therapy.',
    'Plantar Fasciitis - Heel pain. Stretching exercises and orthotics recommended.',
    'Carpal Tunnel Syndrome - Wrist pain. Wrist splint and activity modification.',
    
    // Gynecology
    'Menstrual Irregularity - Irregular periods. Hormonal evaluation recommended.',
    'Urinary Tract Infection - Uncomplicated UTI. Antibiotic course prescribed.',
    'Vaginal Candidiasis - Yeast infection. Antifungal treatment prescribed.',
    'Polycystic Ovary Syndrome (PCOS) - Hormonal imbalance. Lifestyle modifications and medication.',
    'Menopause Symptoms - Perimenopausal symptoms. Hormone replacement therapy discussed.',
    
    // Psychiatry
    'Major Depressive Disorder - Depression, moderate severity. Antidepressant and therapy recommended.',
    'Generalized Anxiety Disorder - Anxiety symptoms. Medication and cognitive behavioral therapy.',
    'Bipolar Disorder - Type II, stable. Mood stabilizer continued.',
    'Attention Deficit Hyperactivity Disorder (ADHD) - Adult ADHD. Stimulant medication prescribed.',
    'Insomnia - Sleep disorder. Sleep hygiene and medication discussed.',
];

// Prescription templates
$prescriptionTemplates = [
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
    'Iron Supplement 65mg - Take 1 tablet once daily on empty stomach for anemia.',
];

// Function to get a random diagnosis
function getRandomDiagnosis($seed = null) {
    global $diagnosisTemplates;
    if ($seed === null) {
        $seed = rand(0, count($diagnosisTemplates) - 1);
    } else {
        $seed = abs($seed) % count($diagnosisTemplates);
    }
    return $diagnosisTemplates[$seed];
}

// Function to get a random prescription
function getRandomPrescription($seed = null) {
    global $prescriptionTemplates;
    if ($seed === null) {
        $seed = rand(0, count($prescriptionTemplates) - 1);
    } else {
        $seed = abs($seed) % count($prescriptionTemplates);
    }
    return $prescriptionTemplates[$seed];
}

// Function to get multiple prescriptions (sometimes patients need multiple medications)
function getPrescriptions($seed = null, $count = 1) {
    $prescriptions = [];
    $baseSeed = $seed !== null ? abs($seed) : rand(0, 999);
    
    // 70% chance of 1 prescription, 25% chance of 2, 5% chance of 3
    if ($count === 1) {
        $rand = ($baseSeed * 7) % 100;
        $count = $rand < 70 ? 1 : ($rand < 95 ? 2 : 3);
    }
    
    for ($i = 0; $i < $count; $i++) {
        $prescriptions[] = getRandomPrescription($baseSeed + $i);
    }
    
    return implode("\n\n", $prescriptions);
}

// Main execution
try {
    echo "========================================\n";
    echo "Medical Records Population Script\n";
    echo "========================================\n\n";
    
    // Find completed appointments without medical records
    echo "Finding completed appointments without medical records...\n";
    echo "----------------------------------------\n";
    
    $query = "
        SELECT 
            a.appointment_id,
            a.pat_id,
            a.doc_id,
            a.appointment_date,
            a.appointment_time,
            st.status_name,
            p.pat_first_name,
            p.pat_last_name,
            d.doc_first_name,
            d.doc_last_name
        FROM appointments a
        JOIN appointment_statuses st ON a.status_id = st.status_id
        JOIN patients p ON a.pat_id = p.pat_id
        JOIN doctors d ON a.doc_id = d.doc_id
        WHERE LOWER(st.status_name) = 'completed'
        AND NOT EXISTS (
            SELECT 1 FROM medical_records mr 
            WHERE mr.appt_id = a.appointment_id
        )
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";
    
    $appointments = $db->fetchAll($query);
    $appointmentCount = count($appointments);
    
    echo "Found {$appointmentCount} completed appointment(s) without medical records.\n\n";
    
    if ($appointmentCount === 0) {
        echo "No appointments need medical records. Exiting.\n";
        exit(0);
    }
    
    // Ask for confirmation before creating records (if running interactively)
    if (php_sapi_name() === 'cli') {
        echo "This will create {$appointmentCount} medical record(s).\n";
        echo "Do you want to continue? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($line) !== 'yes' && strtolower($line) !== 'y') {
            echo "Operation cancelled.\n";
            exit(0);
        }
        echo "\n";
    }
    
    // Create medical records
    echo "Creating medical records...\n";
    echo "----------------------------------------\n";
    
    $created = 0;
    $failed = 0;
    
    foreach ($appointments as $appointment) {
        $appointment_id = $appointment['appointment_id'];
        $appointment_date = $appointment['appointment_date'];
        $pat_name = $appointment['pat_first_name'] . ' ' . $appointment['pat_last_name'];
        $doc_name = $appointment['doc_first_name'] . ' ' . $appointment['doc_last_name'];
        
        // Generate diagnosis and prescription based on appointment_id for consistency
        // Use hash of appointment_id to get deterministic but varied results
        $seed = crc32($appointment_id);
        $diagnosis = getRandomDiagnosis($seed);
        $prescription = getPrescriptions($seed, 1);
        
        try {
            $insertQuery = "
                INSERT INTO medical_records (
                    appt_id,
                    med_rec_diagnosis,
                    med_rec_prescription,
                    med_rec_visit_date,
                    med_rec_created_at,
                    med_rec_updated_at
                ) VALUES (
                    :appt_id,
                    :diagnosis,
                    :prescription,
                    :visit_date,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )
            ";
            
            $db->execute($insertQuery, [
                'appt_id' => $appointment_id,
                'diagnosis' => $diagnosis,
                'prescription' => $prescription,
                'visit_date' => $appointment_date
            ]);
            
            echo "✓ Created medical record for appointment {$appointment_id}\n";
            echo "  Patient: {$pat_name}\n";
            echo "  Doctor: {$doc_name}\n";
            echo "  Visit Date: {$appointment_date}\n";
            echo "  Diagnosis: " . substr($diagnosis, 0, 60) . "...\n";
            echo "\n";
            
            $created++;
        } catch (Exception $e) {
            echo "✗ Failed to create medical record for appointment {$appointment_id}: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    // Final Summary
    echo "========================================\n";
    echo "Final Summary\n";
    echo "========================================\n";
    echo "  Total appointments found: {$appointmentCount}\n";
    echo "  Successfully created: {$created}\n";
    echo "  Failed: {$failed}\n";
    
    if ($created > 0) {
        echo "\nScript completed successfully!\n";
    } else {
        echo "\nNo medical records were created.\n";
    }
    
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

