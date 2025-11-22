-- Medi-Care Database Schema
-- For Supabase PostgreSQL

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Users table (central authentication)
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    user_email VARCHAR(255) UNIQUE NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    user_is_superadmin BOOLEAN DEFAULT FALSE,
    pat_id INTEGER,
    staff_id INTEGER,
    doc_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Specializations table
CREATE TABLE specializations (
    spec_id SERIAL PRIMARY KEY,
    spec_name VARCHAR(100) UNIQUE NOT NULL,
    spec_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Staff table
CREATE TABLE staff (
    staff_id SERIAL PRIMARY KEY,
    staff_first_name VARCHAR(50) NOT NULL,
    staff_middle_initial VARCHAR(1),
    staff_last_name VARCHAR(50) NOT NULL,
    staff_email VARCHAR(255) UNIQUE NOT NULL,
    staff_phone VARCHAR(20),
    staff_position VARCHAR(100),
    staff_salary DECIMAL(10,2),
    staff_status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients table
CREATE TABLE patients (
    pat_id SERIAL PRIMARY KEY,
    pat_first_name VARCHAR(50) NOT NULL,
    pat_middle_initial VARCHAR(1),
    pat_last_name VARCHAR(50) NOT NULL,
    pat_email VARCHAR(255) UNIQUE NOT NULL,
    pat_phone VARCHAR(20),
    pat_date_of_birth DATE,
    pat_gender VARCHAR(10),
    pat_address TEXT,
    pat_emergency_contact VARCHAR(100),
    pat_emergency_phone VARCHAR(20),
    pat_medical_history TEXT,
    pat_allergies TEXT,
    pat_insurance_provider VARCHAR(100),
    pat_insurance_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctors table
CREATE TABLE doctors (
    doc_id SERIAL PRIMARY KEY,
    doc_first_name VARCHAR(50) NOT NULL,
    doc_middle_initial VARCHAR(1),
    doc_last_name VARCHAR(50) NOT NULL,
    doc_email VARCHAR(255) UNIQUE NOT NULL,
    doc_phone VARCHAR(20),
    doc_license_number VARCHAR(50) UNIQUE,
    doc_specialization_id INTEGER REFERENCES specializations(spec_id),
    doc_experience_years INTEGER,
    doc_consultation_fee DECIMAL(10,2),
    doc_qualification TEXT,
    doc_bio TEXT,
    doc_status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointment statuses table
CREATE TABLE appointment_statuses (
    status_id SERIAL PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE NOT NULL,
    status_description TEXT,
    status_color VARCHAR(20) DEFAULT '#3B82F6',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services table
CREATE TABLE services (
    service_id SERIAL PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    service_description TEXT,
    service_price DECIMAL(10,2),
    service_duration_minutes INTEGER DEFAULT 30,
    service_category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Schedules table
CREATE TABLE schedules (
    schedule_id SERIAL PRIMARY KEY,
    doc_id INTEGER REFERENCES doctors(doc_id),
    schedule_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(doc_id, schedule_date, start_time)
);

-- Appointments table
CREATE TABLE appointments (
    appointment_id VARCHAR(20) PRIMARY KEY,
    pat_id INTEGER REFERENCES patients(pat_id),
    doc_id INTEGER REFERENCES doctors(doc_id),
    service_id INTEGER REFERENCES services(service_id),
    status_id INTEGER REFERENCES appointment_statuses(status_id),
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    appointment_notes TEXT,
    appointment_duration INTEGER DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medical records table (REVAMPED - linked to appointments)
CREATE TABLE medical_records (
    med_rec_id SERIAL PRIMARY KEY,
    appt_id VARCHAR(20) NOT NULL REFERENCES appointments(appointment_id),
    med_rec_diagnosis TEXT,
    med_rec_prescription TEXT,
    med_rec_visit_date DATE NOT NULL,
    med_rec_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    med_rec_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payment methods table
CREATE TABLE payment_methods (
    method_id SERIAL PRIMARY KEY,
    method_name VARCHAR(50) UNIQUE NOT NULL,
    method_description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payment statuses table
CREATE TABLE payment_statuses (
    payment_status_id SERIAL PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE NOT NULL,
    status_description TEXT,
    status_color VARCHAR(20) DEFAULT '#3B82F6',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payments table
CREATE TABLE payments (
    payment_id SERIAL PRIMARY KEY,
    appointment_id VARCHAR(20) REFERENCES appointments(appointment_id),
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_method_id INTEGER REFERENCES payment_methods(method_id),
    payment_status_id INTEGER REFERENCES payment_statuses(payment_status_id),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_reference VARCHAR(100),
    payment_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add foreign key constraints for users table
ALTER TABLE users ADD CONSTRAINT fk_users_patient 
    FOREIGN KEY (pat_id) REFERENCES patients(pat_id);
ALTER TABLE users ADD CONSTRAINT fk_users_staff 
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id);
ALTER TABLE users ADD CONSTRAINT fk_users_doctor 
    FOREIGN KEY (doc_id) REFERENCES doctors(doc_id);

-- Insert default data
INSERT INTO appointment_statuses (status_name, status_description, status_color) VALUES
('Scheduled', 'Appointment is scheduled', '#3B82F6'),
('Completed', 'Appointment has been completed', '#10B981'),
('Cancelled', 'Appointment has been cancelled', '#EF4444');

INSERT INTO payment_statuses (status_name, status_description, status_color) VALUES
('Paid', 'Payment has been completed', '#10B981'),
('Pending', 'Payment is pending', '#F59E0B'),
('Refunded', 'Payment has been refunded', '#EF4444');

INSERT INTO payment_methods (method_name, method_description) VALUES
('Cash', 'Cash payment'),
('Debit Card', 'Debit card payment'),
('Credit Card', 'Credit card payment'),
('Mobile Payment', 'Mobile payment (GCash, PayMaya, etc.)'),
('Bank Transfer', 'Bank transfer payment'),
('Insurance', 'Insurance coverage');

INSERT INTO specializations (spec_name, spec_description) VALUES
('Family Medicine', 'General family practice and primary care'),
('Cardiology', 'Heart and cardiovascular system specialist'),
('Neurology', 'Brain and nervous system specialist'),
('Pediatrics', 'Medical care for infants, children, and adolescents'),
('Ophthalmology', 'Eye and vision care specialist'),
('Dentistry', 'Oral and dental health specialist'),
('Dermatology', 'Skin, hair, and nail specialist'),
('Orthopedics', 'Bone, joint, and muscle specialist'),
('Gynecology', 'Women''s reproductive health specialist'),
('Psychiatry', 'Mental health and behavioral disorders specialist');

-- Create indexes for better performance
CREATE INDEX idx_appointments_date ON appointments(appointment_date);
CREATE INDEX idx_appointments_doctor ON appointments(doc_id);
CREATE INDEX idx_appointments_patient ON appointments(pat_id);
CREATE INDEX idx_schedules_doctor_date ON schedules(doc_id, schedule_date);
CREATE INDEX idx_users_email ON users(user_email);
CREATE INDEX idx_patients_email ON patients(pat_email);
CREATE INDEX idx_doctors_email ON doctors(doc_email);
CREATE INDEX idx_staff_email ON staff(staff_email);
CREATE INDEX idx_medical_records_appointment ON medical_records(appt_id);
CREATE INDEX idx_medical_records_visit_date ON medical_records(med_rec_visit_date);

-- Create triggers for updated_at timestamps
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_patients_updated_at BEFORE UPDATE ON patients
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_doctors_updated_at BEFORE UPDATE ON doctors
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_staff_updated_at BEFORE UPDATE ON staff
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_appointments_updated_at BEFORE UPDATE ON appointments
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_medical_records_updated_at BEFORE UPDATE ON medical_records
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_payments_updated_at BEFORE UPDATE ON payments
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
