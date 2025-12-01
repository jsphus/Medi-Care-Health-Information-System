# Lifesaver-Clinic Health Information System

A comprehensive healthcare management system built with PHP, PostgreSQL, and modern web technologies. This system manages patients, doctors, staff, appointments, medical records, payments, and schedules for healthcare facilities.

## Table of Contents

- [Overview](#overview)
- [Technology Stack](#technology-stack)
- [Database Schema](#database-schema)
- [User Roles & Permissions](#user-roles--permissions)
- [Features by Role](#features-by-role)
- [Project Structure](#project-structure)
- [Key Components](#key-components)
- [Routes & URLs](#routes--urls)
- [Setup Instructions](#setup-instructions)
- [Development Guidelines](#development-guidelines)

---

## Overview

Lifesaver-Clinic is a full-featured Health Information System (HIS) designed to manage all aspects of a healthcare facility's operations. The system supports multiple user roles with role-based access control, ensuring secure and efficient management of healthcare data.

### Core Capabilities

- **Multi-role Authentication**: Super Admin, Staff, Doctor, and Patient roles
- **Patient Management**: Complete patient registration, profiles, and medical history
- **Appointment System**: Booking, scheduling, and management of appointments
- **Medical Records**: Secure storage and management of patient medical records
- **Payment Processing**: Payment tracking, methods, and status management
- **Schedule Management**: Doctor availability and schedule management
- **Service Catalog**: Healthcare services with pricing and duration
- **Analytics & Reporting**: Dashboard with statistics and charts

---

## Technology Stack

### Backend
- **PHP 7.4+**: Server-side scripting
- **PostgreSQL**: Database (via Supabase)
- **PDO**: Database abstraction layer
- **Session Management**: PHP native sessions

### Frontend
- **HTML5/CSS3**: Structure and styling
- **JavaScript (Vanilla)**: Client-side interactivity
- **Chart.js**: Data visualization
- **Font Awesome 6.4.0**: Icons
- **Responsive Design**: Mobile-friendly layouts

### Architecture
- **MVC Pattern**: Controllers, Views, and Models separation
- **Custom Router**: Simple routing system
- **Role-Based Access Control (RBAC)**: Permission-based access
- **Prepared Statements**: SQL injection prevention
- **XSS Protection**: `htmlspecialchars()` for output escaping

---

## Database Schema

### Core Tables

#### Users (Authentication)
- `user_id` (Primary Key)
- `user_email` (Unique)
- `user_password` (Hashed)
- `user_is_superadmin` (Boolean)
- `pat_id`, `staff_id`, `doc_id` (Foreign Keys to respective tables)
- `created_at`, `updated_at` (Timestamps)

#### Patients
- `pat_id` (Primary Key)
- Personal info: `pat_first_name`, `pat_middle_initial`, `pat_last_name`, `pat_email`, `pat_phone`
- Demographics: `pat_date_of_birth`, `pat_gender`, `pat_address`
- Medical: `pat_medical_history`, `pat_allergies`
- Insurance: `pat_insurance_provider`, `pat_insurance_number`
- Emergency: `pat_emergency_contact`, `pat_emergency_phone`

#### Doctors
- `doc_id` (Primary Key)
- Personal: `doc_first_name`, `doc_middle_initial`, `doc_last_name`, `doc_email`, `doc_phone`
- Professional: `doc_license_number` (Unique), `doc_specialization_id`
- Experience: `doc_experience_years`, `doc_consultation_fee`
- Details: `doc_qualification`, `doc_bio`, `doc_status`

#### Staff
- `staff_id` (Primary Key)
- Personal: `staff_first_name`, `staff_middle_initial`, `staff_last_name`, `staff_email`, `staff_phone`
- Employment: `staff_position`, `staff_hire_date`, `staff_salary`

#### Appointments
- `appointment_id` (Primary Key, VARCHAR)
- Relations: `pat_id`, `doc_id`, `service_id`, `status_id`
- Scheduling: `appointment_date`, `appointment_time`, `appointment_duration`
- Notes: `appointment_notes`

#### Medical Records
- `record_id` (Primary Key)
- Relations: `pat_id`, `doc_id`, `appointment_id`
- Medical: `record_date`, `diagnosis`, `treatment`, `prescription`
- Follow-up: `follow_up_date`, `notes`

#### Payments
- `payment_id` (Primary Key)
- Relations: `appointment_id`, `payment_method_id`, `payment_status_id`
- Financial: `payment_amount`, `payment_date`
- Details: `payment_reference`, `payment_notes`

#### Schedules
- `schedule_id` (Primary Key)
- Relation: `doc_id`
- Timing: `schedule_date`, `start_time`, `end_time`
- Availability: `max_appointments`, `is_available`
- Unique constraint on `(doc_id, schedule_date, start_time)`

#### Services
- `service_id` (Primary Key)
- Details: `service_name`, `service_description`, `service_category`
- Pricing: `service_price`, `service_duration_minutes`

#### Specializations
- `spec_id` (Primary Key)
- `spec_name` (Unique), `spec_description`

#### Appointment Statuses
- `status_id` (Primary Key)
- `status_name` (Unique), `status_description`, `status_color`
- Default: Scheduled, Completed, Cancelled

#### Payment Methods
- `method_id` (Primary Key)
- `method_name` (Unique), `method_description`, `is_active`
- Default: Cash, Debit Card, Credit Card, Mobile Payment, Bank Transfer, Insurance

#### Payment Statuses
- `payment_status_id` (Primary Key)
- `status_name` (Unique), `status_description`, `status_color`
- Default: Paid, Pending, Refunded

### Relationships

- Users → Patients/Doctors/Staff (One-to-One)
- Doctors → Specializations (Many-to-One)
- Appointments → Patients, Doctors, Services, Statuses (Many-to-One)
- Medical Records → Patients, Doctors, Appointments (Many-to-One)
- Payments → Appointments, Payment Methods, Payment Statuses (Many-to-One)
- Schedules → Doctors (Many-to-One)

---

## User Roles & Permissions

### 1. Super Admin
**Full system access and management**

- Complete CRUD operations on all entities
- User management (create, edit, delete users)
- System configuration
- View all data across all roles
- Manage all appointments, records, and payments
- Access to all dashboards and reports

### 2. Staff
**Operational management and support**

- Manage services, specializations, and payment methods
- View and manage appointments
- Access medical records (view-only)
- Manage payment records
- View staff members
- Cannot manage users or doctors directly

### 3. Doctor
**Patient care and medical records**

- View own appointments (today, future, previous)
- Manage own schedule
- Create and edit medical records for patients
- View patient information
- View other doctors (read-only)
- Cannot manage payments or services

### 4. Patient
**Personal health management**

- View own appointments
- Book new appointments
- View own medical records
- View payment history
- Manage personal profile
- View notifications
- Cannot access other patients' data

---

## Features by Role

### Super Admin Features

#### Dashboard (`/superadmin/dashboard`)
- **KPIs**: Total Patients, New Appointments, Medical Records, Patients Today
- **Payment Cards**: Payments This Month, Total Amount, Paid, Pending
- **Charts**: 
  - Patient Statistics (12-month line chart)
  - Users by Role (donut chart)
  - Top Services (bar chart)
  - Completion Rate (12-month line chart)
- **Lists**: Top Services, Top Staff (doctors), Today's Appointments
- Personalized greeting with user name

#### Users Management (`/superadmin/users`)
- View all users with role badges
- Create new users (link to patient/doctor/staff)
- Edit user details
- Delete users
- View user profiles
- Summary cards: Total Users, Staff Users, Doctor Users, Patient Users
- Action buttons: Edit, View, Delete (consistent order)

#### Patients Management (`/superadmin/patients`)
- View all patients with avatars
- Create, edit, delete patients
- View patient profiles
- Filter and search capabilities
- Summary cards: Total Patients, New This Month, Active Patients
- Action buttons: Edit, View, Delete

#### Doctors Management (`/superadmin/doctors`)
- View all doctors with specialization
- Create, edit, delete doctors
- View doctor profiles
- Summary cards: Total Doctors, Active Doctors, Inactive Doctors
- Action buttons: Edit, View, Delete

#### Staff Management (`/superadmin/staff`)
- View all staff members
- Create, edit, delete staff
- View staff profiles
- Summary cards: Total Staff, Active Staff, Inactive Staff
- Action buttons: Edit, View, Delete

#### Appointments (`/superadmin/appointments`)
- View all appointments
- Create, edit, delete appointments
- Filter by status, date, patient, doctor
- Summary cards: Upcoming, Completed, Cancelled
- Action buttons: Edit, View, Delete

#### Medical Records (`/superadmin/medical-records`)
- View all medical records
- Create, edit, delete records
- Filter by patient, doctor, date
- Summary cards: Total Records, Records This Month, Pending Follow-up
- Action buttons: View, Delete

#### Services (`/superadmin/services`)
- Manage healthcare services
- Set pricing and duration
- Categorize services
- Summary cards: Total Services, Service Appointments, Total Revenue
- Action buttons: Edit, View, Delete

#### Specializations (`/superadmin/specializations`)
- Manage doctor specializations
- View doctors per specialization
- Summary cards: Total Specializations, With Doctors, Total Doctors
- Action buttons: Edit, View, Delete

#### Schedules (`/superadmin/schedules`)
- View all doctor schedules
- Create, edit, delete schedules
- Filter by doctor and date
- Action buttons: Edit, Delete

#### Payments (`/superadmin/payments`)
- View all payment records
- Create, edit, delete payments
- Filter by status, method, date
- Summary cards: Payments This Month, Paid, Pending, Total Amount
- Action buttons: Edit, View, Delete

#### Payment Methods (`/superadmin/payment-methods`)
- Manage payment methods (Cash, Cards, Mobile, etc.)
- Activate/deactivate methods
- Summary cards: Total Methods, Active, Inactive, Total Payments
- Action buttons: Edit, View, Delete

#### Payment Statuses (`/superadmin/payment-statuses`)
- Manage payment statuses (Paid, Pending, Refunded)
- Customize status colors
- Summary cards: Total Statuses, Total Payments
- Action buttons: Edit, View, Delete

#### Appointment Statuses (`/superadmin/statuses`)
- Manage appointment statuses (Scheduled, Completed, Cancelled)
- Customize status colors
- Summary cards: Total Statuses, Total Appointments
- Action buttons: Edit, View, Delete

#### Account Settings (`/superadmin/account`, `/superadmin/settings`, `/superadmin/privacy`)
- Manage personal account
- Update settings
- Privacy preferences

### Staff Features

#### Dashboard (`/staff/dashboard`)
- Statistics: Total Staff, Services, Specializations, Payment Methods
- Services Overview chart
- Quick Stats
- Recent Services table
- Personalized greeting

#### Staff Management (`/staff/staff`)
- View staff members
- Edit own profile
- View staff profiles
- Summary cards: New This Month, Active Staff, Inactive Staff
- Action buttons: Edit, View

#### Services (`/staff/services`)
- Manage services (create, edit, delete)
- View service details
- Summary cards: Total Services, Service Appointments, Total Revenue
- Action buttons: Edit, View, Delete

#### Service Appointments (`/staff/service-appointments`)
- View appointments by service
- Filter appointments

#### Specializations (`/staff/specializations`)
- View all specializations
- View doctors per specialization
- Summary cards: Total Specializations, With Doctors, Total Doctors
- Action buttons: View

#### Specialization Doctors (`/staff/specialization-doctors`)
- View doctors by specialization
- Doctor details and statistics

#### Payment Methods (`/staff/payment-methods`)
- Manage payment methods
- Activate/deactivate
- Summary cards: Total Methods, Active, Inactive, Total Payments
- Action buttons: Edit, View

#### Payment Statuses (`/staff/payment-statuses`)
- Manage payment statuses
- Summary cards: Total Statuses, Total Payments
- Action buttons: Edit, View

#### Payments (`/staff/payments`)
- View and manage payments
- Create, edit payments
- Summary cards: Payments This Month, Paid, Pending, Total Amount
- Action buttons: Edit, View, Delete

#### Appointment Statuses (`/staff/statuses`)
- Manage appointment statuses
- Summary cards: Total Statuses, Total Appointments
- Action buttons: Edit, View

#### Medical Records (`/staff/medical-records`)
- View all medical records (read-only)
- Filter by patient, doctor
- Summary cards: Total Records, Records This Month, Pending Follow-up
- Action buttons: View

#### Account Settings (`/staff/account`, `/staff/settings`, `/staff/privacy`)
- Manage personal account
- Update settings
- Privacy preferences

### Doctor Features

#### Dashboard (`/doctor/dashboard`)
- Statistics: Total Patients, Active Doctors, Today Appointments, Pending Lab Results
- Recent Appointments list
- Quick Actions: Schedule Appointment, View Appointments, View Doctors, Create Medical Record
- Patient Analytics section
- Personalized greeting: "Welcome back, Dr. [First Name]!"

#### Appointments - Today (`/doctor/appointments/today`)
- View today's appointments
- Patient table with status badges
- Filter and search

#### Appointments - Future (`/doctor/appointments/future`)
- View upcoming appointments
- Patient table with status badges

#### Appointments - Previous (`/doctor/appointments/previous`)
- View past appointments
- Patient table with status badges

#### Schedules (`/doctor/schedules`)
- View own schedules
- Summary cards: Total Schedules, Today's Schedules, Upcoming Schedules
- Action buttons: Edit, View, Delete

#### Manage Schedules (`/doctor/schedules-manage`)
- Create new schedules
- Set availability and max appointments

#### Doctors (`/doctor/doctors`)
- View all doctors
- Edit own profile
- View doctor profiles
- Summary cards: Total Doctors, Active Doctors, Inactive Doctors
- Action buttons: Edit, View

#### Medical Records (`/doctor/medical-records`)
- Create medical records for patients
- Edit own records
- View all records
- Filter by patient
- Summary cards: Total Records, Records This Month, Pending Follow-up
- Action buttons: Edit, View

#### Profile (`/doctor/profile`)
- Edit doctor profile
- Update specialization, license, experience, fees
- View profile details

#### Account Settings (`/doctor/account`, `/doctor/settings`, `/doctor/privacy`)
- Manage personal account
- Update settings
- Privacy preferences

### Patient Features

#### Dashboard (`/patient/dashboard`)
- Welcome section with personalized greeting
- Statistics: Total Appointments, Upcoming, Total Payments, Pending Payments
- Quick Actions: Book, My Appointments, Medical Records, Payments
- Upcoming Appointments list
- Recent Medical Records
- Recent Payments
- Personalized greeting: "Welcome, [First Name]! 👋"

#### Appointments (`/patient/appointments`)
- View all appointments (upcoming and past)
- Filter by status and category
- Search appointments
- View appointment details
- Cancel appointments
- Summary cards: Total Appointments, Upcoming, Completed

#### Book Appointment (`/patient/book`)
- Browse available doctors
- Search by name or specialization
- View doctor details
- Select doctor and proceed to booking

#### Create Appointment (`/patient/create-appointment`)
- Select doctor and service
- Choose date and time
- Add appointment notes
- Reschedule existing appointments

#### Doctor Details (`/doctor-detail`)
- View doctor profile
- See specialization, experience, fees
- View availability

#### Medical Records (`/patient/medical-records`)
- View own medical records
- Search records
- View full record details
- View prescriptions
- Summary cards: Total Records, Records This Month, Pending Follow-up

#### Payments (`/patient/payments`)
- View payment history
- Search payments
- View payment details
- Download receipts (planned)
- Summary cards: Total Payments, Paid, Pending, Total Amount

#### Notifications (`/patient/notifications`)
- View system notifications
- Appointment reminders
- Payment notifications

#### Profile (`/patient/profile`)
- Edit personal information
- Update medical history and allergies
- Manage insurance information

#### Account Settings (`/patient/account`, `/patient/settings`, `/patient/privacy`)
- Manage personal account
- Update settings
- Privacy preferences

---

## Project Structure

```
Lifesaver-Clinic-Health-Information-System/
│
├── index.php                    # Main entry point, routes requests
├── schema.sql                   # Database schema (PostgreSQL)
│
├── config/
│   ├── config.php              # Application configuration, env loading
│   └── Database.php            # Database singleton connection class
│
├── classes/                     # Business logic classes
│   ├── Auth.php                # Authentication & authorization
│   ├── User.php                # User management
│   ├── Patient.php             # Patient operations
│   ├── Doctor.php              # Doctor operations
│   ├── Staff.php               # Staff operations
│   ├── Appointment.php         # Appointment operations
│   ├── MedicalRecord.php        # Medical record operations
│   ├── Payment.php             # Payment operations
│   ├── Service.php             # Service operations
│   ├── Schedule.php            # Schedule operations
│   ├── Specialization.php      # Specialization operations
│   ├── AppointmentStatus.php  # Appointment status operations
│   ├── PaymentMethod.php      # Payment method operations
│   ├── PaymentStatus.php      # Payment status operations
│   └── Status.php              # Generic status operations
│
├── controllers/                 # Request handlers (MVC Controllers)
│   ├── index.php               # Landing page
│   ├── login.php               # Login handler
│   ├── logout.php              # Logout handler
│   ├── register.php            # Registration handler
│   │
│   ├── superadmin/             # Super Admin controllers
│   │   ├── dashboard.php
│   │   ├── users.php
│   │   ├── patients.php
│   │   ├── doctors.php
│   │   ├── staff.php
│   │   ├── appointments.php
│   │   ├── services.php
│   │   ├── specializations.php
│   │   ├── schedules.php
│   │   ├── statuses.php
│   │   ├── payment-methods.php
│   │   ├── payment-statuses.php
│   │   ├── payments.php
│   │   ├── medical-records.php
│   │   ├── account.php
│   │   ├── settings.php
│   │   └── privacy.php
│   │
│   ├── staff/                  # Staff controllers
│   │   ├── dashboard.php
│   │   ├── staff.php
│   │   ├── services.php
│   │   ├── service-appointments.php
│   │   ├── specializations.php
│   │   ├── specialization-doctors.php
│   │   ├── statuses.php
│   │   ├── payment-methods.php
│   │   ├── payment-statuses.php
│   │   ├── payments.php
│   │   ├── medical-records.php
│   │   ├── account.php
│   │   ├── settings.php
│   │   └── privacy.php
│   │
│   ├── doctor/                 # Doctor controllers
│   │   ├── dashboard.php
│   │   ├── appointments-today.php
│   │   ├── appointments-future.php
│   │   ├── appointments-previous.php
│   │   ├── schedules.php
│   │   ├── schedules-manage.php
│   │   ├── doctors.php
│   │   ├── medical-records.php
│   │   ├── profile.php
│   │   ├── account.php
│   │   ├── settings.php
│   │   └── privacy.php
│   │
│   └── patient/                # Patient controllers
│       ├── dashboard.php
│       ├── appointments.php
│       ├── book.php
│       ├── create-appointment.php
│       ├── doctor-detail.php
│       ├── medical-records.php
│       ├── payments.php
│       ├── notifications.php
│       ├── profile.php
│       ├── account.php
│       ├── settings.php
│       └── privacy.php
│
├── views/                       # Presentation layer (MVC Views)
│   ├── landing.php             # Landing page
│   ├── login.view.php          # Login page
│   ├── register-role.view.php  # Role selection
│   ├── register-form.view.php   # Registration form
│   │
│   ├── partials/               # Reusable components
│   │   ├── header.php          # Page header, navigation
│   │   ├── footer.php          # Page footer
│   │   ├── sidebar.php         # Sidebar navigation (role-based)
│   │   └── filter-sidebar.php  # Filter sidebar component
│   │
│   ├── superadmin/             # Super Admin views
│   │   ├── dashboard.view.php
│   │   ├── users.view.php
│   │   ├── patients.view.php
│   │   ├── doctors.view.php
│   │   ├── staff.view.php
│   │   ├── appointments.view.php
│   │   ├── services.view.php
│   │   ├── specializations.view.php
│   │   ├── schedules.view.php
│   │   ├── statuses.view.php
│   │   ├── payment-methods.view.php
│   │   ├── payment-statuses.view.php
│   │   ├── payments.view.php
│   │   ├── medical-records.view.php
│   │   ├── account.view.php
│   │   ├── settings.view.php
│   │   └── privacy.view.php
│   │
│   ├── staff/                  # Staff views
│   │   ├── dashboard.view.php
│   │   ├── staff.view.php
│   │   ├── services.view.php
│   │   ├── service-appointments.view.php
│   │   ├── specializations.view.php
│   │   ├── specialization-doctors.view.php
│   │   ├── statuses.view.php
│   │   ├── payment-methods.view.php
│   │   ├── payment-statuses.view.php
│   │   ├── payments.view.php
│   │   ├── medical-records.view.php
│   │   ├── account.view.php
│   │   ├── settings.view.php
│   │   └── privacy.view.php
│   │
│   ├── doctor/                 # Doctor views
│   │   ├── dashboard.view.php
│   │   ├── appointments-today.view.php
│   │   ├── appointments-future.view.php
│   │   ├── appointments-previous.view.php
│   │   ├── schedules.view.php
│   │   ├── schedules-manage.view.php
│   │   ├── doctors.view.php
│   │   ├── medical-records.view.php
│   │   ├── profile.view.php
│   │   ├── account.view.php
│   │   ├── settings.view.php
│   │   └── privacy.view.php
│   │
│   └── patient/                # Patient views
│       ├── dashboard.view.php
│       ├── appointments.view.php
│       ├── book.view.php
│       ├── create-appointment.view.php
│       ├── doctor-detail.view.php
│       ├── medical-records.view.php
│       ├── payments.view.php
│       ├── notifications.view.php
│       ├── profile.view.php
│       ├── account.view.php
│       ├── settings.view.php
│       └── privacy.view.php
│
├── includes/                    # Shared utilities
│   ├── router.php              # Routing logic
│   ├── routes.php              # Route definitions
│   └── functions.php           # Helper functions (sanitize, validate, etc.)
│
└── public/                      # Public assets
    ├── css/
    │   ├── style.css           # Main stylesheet (2350+ lines)
    │   └── confirm-modal.css    # Confirmation modal styles
    └── js/                      # JavaScript files (if any)
```

---

## Key Components

### Authentication System (`classes/Auth.php`)

**Methods:**
- `login($email, $password)` - Authenticate user
- `logout()` - Destroy session
- `isLoggedIn()` - Check login status
- `isSuperAdmin()`, `isStaff()`, `isDoctor()`, `isPatient()` - Role checks
- `getRole()` - Get user role
- `getUserId()`, `getPatientId()`, `getDoctorId()`, `getStaffId()` - Get IDs
- `requireLogin()`, `requireSuperAdmin()`, etc. - Access control

**Session Variables:**
- `user_id`, `user_email`, `is_superadmin`
- `pat_id`, `staff_id`, `doc_id`
- `logged_in` (boolean)

### Database Connection (`config/Database.php`)

**Singleton Pattern:**
- `getInstance()` - Get database connection
- Uses PDO with PostgreSQL
- Connection via Supabase or local PostgreSQL

### Routing System (`includes/router.php`)

**Features:**
- Custom routing based on `routes.php`
- 404 handling
- Base directory detection
- Clean URLs

### UI Components

**Common Patterns:**
- Summary cards with statistics
- Data tables with action buttons (Edit, View, Delete)
- Modals for create/edit forms
- Confirmation modals for delete actions
- Filter sidebars
- Pagination (where applicable)
- Status badges with colors
- Avatar displays with initials

**Action Button Order:**
- Consistent across all tables: **Edit → View → Delete**
- View button uses eye icon (`fa-eye`)
- Edit button uses edit icon (`fa-edit`)
- Delete button uses trash icon (`fa-trash`)

---

## Routes & URLs

### Public Routes
- `/` - Landing page
- `/login` - Login page
- `/register` - Registration (role selection)
- `/logout` - Logout handler

### Super Admin Routes
- `/superadmin/dashboard` - Dashboard
- `/superadmin/users` - User management
- `/superadmin/patients` - Patient management
- `/superadmin/doctors` - Doctor management
- `/superadmin/staff` - Staff management
- `/superadmin/appointments` - Appointment management
- `/superadmin/services` - Service management
- `/superadmin/specializations` - Specialization management
- `/superadmin/schedules` - Schedule management
- `/superadmin/statuses` - Appointment status management
- `/superadmin/payment-methods` - Payment method management
- `/superadmin/payment-statuses` - Payment status management
- `/superadmin/payments` - Payment management
- `/superadmin/medical-records` - Medical record management
- `/superadmin/account` - Account settings
- `/superadmin/settings` - Application settings
- `/superadmin/privacy` - Privacy settings

### Staff Routes
- `/staff/dashboard` - Dashboard
- `/staff/staff` - Staff management
- `/staff/services` - Service management
- `/staff/service-appointments` - Service appointments
- `/staff/specializations` - Specialization view
- `/staff/specialization-doctors` - Doctors by specialization
- `/staff/statuses` - Appointment status management
- `/staff/payment-methods` - Payment method management
- `/staff/payment-statuses` - Payment status management
- `/staff/payments` - Payment management
- `/staff/medical-records` - Medical record view
- `/staff/account` - Account settings
- `/staff/settings` - Application settings
- `/staff/privacy` - Privacy settings

### Doctor Routes
- `/doctor/dashboard` - Dashboard
- `/doctor/appointments/today` - Today's appointments
- `/doctor/appointments/future` - Future appointments
- `/doctor/appointments/previous` - Previous appointments
- `/doctor/schedules` - Schedule view
- `/doctor/schedules-manage` - Schedule management
- `/doctor/doctors` - Doctor directory
- `/doctor/medical-records` - Medical record management
- `/doctor/profile` - Profile management
- `/doctor/account` - Account settings
- `/doctor/settings` - Application settings
- `/doctor/privacy` - Privacy settings

### Patient Routes
- `/patient/dashboard` - Dashboard
- `/patient/appointments` - Appointment management
- `/patient/book` - Browse doctors
- `/patient/create-appointment` - Book appointment
- `/patient/doctor-detail` - Doctor details
- `/patient/medical-records` - Medical records
- `/patient/payments` - Payment history
- `/patient/notifications` - Notifications
- `/patient/profile` - Profile management
- `/patient/account` - Account settings
- `/patient/settings` - Application settings
- `/patient/privacy` - Privacy settings

---

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- PostgreSQL database (or Supabase)
- Web server (Apache/Nginx) or PHP built-in server
- Composer (optional, for dependencies)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Lifesaver-Clinic-Health-Information-System
   ```

2. **Configure environment**
   - Copy `.env.example` to `.env` (if exists)
   - Or create `.env` file with:
     ```
     DB_HOST=your-db-host
     DB_PORT=5432
     DB_NAME=your-db-name
     DB_USER=your-db-user
     DB_PASSWORD=your-db-password
     APP_NAME=Medi-Care Health Information System
     APP_URL=http://localhost
     ```

3. **Database setup**
   ```bash
   # Connect to PostgreSQL
   psql -U your-user -d your-database
   
   # Run schema
   \i schema.sql
   ```

4. **Configure web server**
   - Point document root to project directory
   - Ensure `index.php` is the default file
   - Enable URL rewriting (for clean URLs)

5. **Set permissions**
   ```bash
   chmod 755 -R .
   chmod 777 -R public/uploads  # If uploads directory exists
   ```

6. **Access the application**
   - Navigate to `http://localhost` or your configured URL
   - Register a new user or use existing credentials

### Default Data

The schema includes default data:
- **Appointment Statuses**: Scheduled, Completed, Cancelled
- **Payment Statuses**: Paid, Pending, Refunded
- **Payment Methods**: Cash, Debit Card, Credit Card, Mobile Payment, Bank Transfer, Insurance
- **Specializations**: Family Medicine, Cardiology, Neurology, Pediatrics, Ophthalmology, Dentistry, Dermatology, Orthopedics, Gynecology, Psychiatry

---

## Development Guidelines

### Code Style

**PHP:**
- Use PSR-12 coding standards
- Use prepared statements for all SQL queries
- Escape output with `htmlspecialchars()`
- Use null coalescing operator (`??`) for default values
- Validate and sanitize all user input

**JavaScript:**
- Use vanilla JavaScript (no frameworks)
- Use `const` and `let` (avoid `var`)
- Use arrow functions where appropriate
- Handle errors gracefully

**CSS:**
- Use CSS variables for theming
- Follow BEM naming convention where applicable
- Use flexbox/grid for layouts
- Mobile-first responsive design

### Security Best Practices

1. **SQL Injection Prevention**
   - Always use prepared statements
   - Never concatenate user input into SQL

2. **XSS Prevention**
   - Escape all output with `htmlspecialchars()`
   - Use `json_encode()` for JSON data

3. **Authentication**
   - Use `password_hash()` for passwords
   - Verify with `password_verify()`
   - Implement session timeout

4. **Authorization**
   - Check permissions on every request
   - Use `requireRole()` methods in controllers

5. **Input Validation**
   - Validate on both client and server side
   - Sanitize all user input
   - Use type checking

### Adding New Features

1. **Create Controller**
   - Add to appropriate role folder
   - Include authentication check
   - Handle GET and POST requests
   - Pass data to view

2. **Create View**
   - Follow existing naming convention (`*.view.php`)
   - Include header and footer
   - Use consistent UI components
   - Add action buttons in consistent order

3. **Add Route**
   - Update `includes/routes.php`
   - Follow URL naming convention

4. **Update Navigation**
   - Add menu item to `views/partials/sidebar.php`
   - Use appropriate icon and label

### Database Changes

1. **Schema Updates**
   - Update `schema.sql`
   - Create migration script if needed
   - Update related models/classes

2. **Data Integrity**
   - Add foreign key constraints
   - Use transactions for multi-table operations
   - Handle cascade deletes appropriately

### Testing Checklist

- [ ] Authentication works for all roles
- [ ] CRUD operations work correctly
- [ ] Data validation prevents invalid input
- [ ] Error handling displays user-friendly messages
- [ ] Responsive design works on mobile
- [ ] All links and buttons function correctly
- [ ] Forms submit and validate properly
- [ ] Modals open and close correctly
- [ ] Charts display data accurately
- [ ] Search and filter functions work

---

## Common Patterns & Conventions

### Table Views
- Summary cards at the top (3-4 cards with statistics)
- Table with consistent styling
- Action buttons: Edit, View, Delete (in that order)
- Pagination for large datasets
- Filter sidebar (where applicable)
- No checkboxes or bulk actions
- No sort arrows on headers

### Forms
- Use `sanitize()` function for input
- Validate required fields
- Show error messages clearly
- Use modals for create/edit
- Confirmation modals for delete

### Data Display
- Use avatars with initials for users
- Status badges with colors
- Format dates: `date('d M Y', strtotime($date))`
- Format times: `date('g:i A', strtotime($time))`
- Format currency: `₱<?= number_format($amount, 2) ?>`

### Phone Number Format
- Format: `XXXX-XXX-XXXX` (Philippine format)
- Applied on both client and server side
- Stored in database as formatted string

### Date Handling
- Empty date strings converted to `NULL` for database
- Use `DATE()` function in SQL for date comparisons
- Default timezone: `Asia/Manila`

---

## Future Enhancements (Planned)

- Email notifications
- SMS reminders
- PDF report generation
- Receipt download/printing
- File uploads for medical documents
- Advanced analytics and reporting
- API endpoints for mobile app
- Real-time notifications
- Appointment reminders
- Payment gateway integration

---

## Support & Documentation

For detailed information about specific features, refer to:
- Controller files for business logic
- View files for UI implementation
- Schema file for database structure
- Class files for reusable operations

---

**Last Updated**: 2024
**Version**: 1.0
**License**: [Specify License]
