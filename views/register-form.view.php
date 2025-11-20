<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medi-Care - Register as <?= ucfirst($role) ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<style>
/* Page layout */
.register-page {
    display: flex;
    min-height: 100vh;
    width: 100%;
    margin: 0;
    padding: 0;
    background: var(--bg-gradient);
}

/* Left panel - form */
.register-left {
    flex: 0 0 50%;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
}

/* Right panel - welcome */
.register-right {
    flex: 1;
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    position: relative;
    overflow: hidden;
}

.register-right::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transform: rotate(15deg);
}

/* Form card */
.register-card {
    background: white;
    border-radius: 1.5rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    padding: 3rem;
    max-width: 500px;
    width: 100%;
    position: relative;
    z-index: 1;
}

.register-header {
    text-align: left;
    margin-bottom: 2rem;
}

.register-logo {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.register-logo-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.75rem;
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
}

.register-logo-text {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1f2937;
}

.register-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.register-subtitle {
    color: #6b7280;
    font-size: 0.9375rem;
}

/* Form field wrapper */
.form-icon-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 1.5rem;
}

/* Inputs, selects, textareas */
.form-icon-wrapper input,
.form-icon-wrapper select,
.form-icon-wrapper textarea {
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 0.75rem;
    font-size: 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-sizing: border-box;
    height: 3rem;
}

/* Textarea adjustments */
.form-icon-wrapper textarea {
    height: auto;
    min-height: 100px;
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}

/* Icons inside fields */
.form-icon-wrapper .form-icon {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    color: #6b7280;
    pointer-events: none;
    z-index: 1;
}

/* Focus effect */
.form-icon-wrapper input:focus,
.form-icon-wrapper select:focus,
.form-icon-wrapper textarea:focus {
    border-color: var(--primary-blue);
    outline: none;
}

.form-icon-wrapper input:focus + .form-icon,
.form-icon-wrapper select:focus + .form-icon,
.form-icon-wrapper textarea:focus + .form-icon {
    color: var(--primary-blue);
}

/* Remove default arrow for selects and date inputs */
.form-icon-wrapper select,
.form-icon-wrapper input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

/* Submit button */
.btn-register {
    margin-top: 2rem;
    height: 3rem;
    font-size: 1rem;
}

/* Login link */
.login-link {
    text-align: center;
    margin-top: 1.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.login-link a {
    color: var(--primary-blue);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
}

.login-link a:hover {
    color: var(--primary-blue-dark);
    text-decoration: underline;
}

/* Welcome card */
.welcome-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 1.5rem;
    padding: 3rem;
    max-width: 500px;
    width: 100%;
    position: relative;
    z-index: 1;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.welcome-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    line-height: 1.7;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-blue);
    text-decoration: none;
    font-weight: 500;
    margin-top: 1.5rem;
    transition: var(--transition);
    font-size: 0.875rem;
}

.back-link:hover {
    color: var(--primary-blue-dark);
    transform: translateX(-4px);
}

@media (max-width: 1024px) {
    .register-page {
        flex-direction: column;
    }
    .register-left, .register-right {
        flex: 0 0 auto;
        padding: 2rem;
    }
    .welcome-card {
        max-width: 100%;
    }
}
</style>

<div class="register-page">

    <!-- Left Panel -->
    <div class="register-left">
        <div class="register-card">
            <div class="register-header">
                <div class="register-logo">
                    <div class="register-logo-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="register-logo-text">Medi-Care</div>
                </div>
                <h1 class="register-title">Register as <?= ucfirst($role) ?></h1>
                <p class="register-subtitle">Fill in your details to create an account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" action="" class="register-form">
                <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                <!-- First Name -->
                <div class="form-icon-wrapper">
                    <input type="text" name="first_name" placeholder="First Name" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                    <i class="fas fa-user form-icon"></i>
                </div>

                <!-- Middle Initial -->
                <div class="form-icon-wrapper">
                    <input type="text" name="middle_initial" placeholder="Middle Initial (Optional)" maxlength="1" value="<?= htmlspecialchars($_POST['middle_initial'] ?? '') ?>">
                    <i class="fas fa-user form-icon"></i>
                </div>

                <!-- Last Name -->
                <div class="form-icon-wrapper">
                    <input type="text" name="last_name" placeholder="Last Name" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                    <i class="fas fa-user form-icon"></i>
                </div>

                <!-- Email -->
                <div class="form-icon-wrapper">
                    <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <i class="fas fa-envelope form-icon"></i>
                </div>

                <!-- Phone -->
                <div class="form-icon-wrapper">
                    <input type="tel" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    <i class="fas fa-phone form-icon"></i>
                </div>

                <!-- Password -->
                <div class="form-icon-wrapper">
                    <input type="password" name="password" placeholder="Password" required minlength="8">
                    <i class="fas fa-lock form-icon"></i>
                </div>

                <!-- Confirm Password -->
                <div class="form-icon-wrapper">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="8">
                    <i class="fas fa-lock form-icon"></i>
                </div>

                <!-- Patient Fields -->
                <?php if ($role === 'patient'): ?>
                    <div class="form-icon-wrapper">
                        <input type="date" name="date_of_birth" value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
                        <i class="fas fa-calendar form-icon"></i>
                    </div>

                    <div class="form-icon-wrapper">
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
                        </select>
                        <i class="fas fa-venus-mars form-icon"></i>
                    </div>

                    <div class="form-icon-wrapper">
                        <textarea name="address" placeholder="Enter your address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        <i class="fas fa-address-card form-icon"></i>
                    </div>

                    <div class="form-icon-wrapper">
                        <input type="text" name="emergency_contact" placeholder="Emergency Contact Name" value="<?= htmlspecialchars($_POST['emergency_contact'] ?? '') ?>">
                        <i class="fas fa-user form-icon"></i>
                    </div>

                    <div class="form-icon-wrapper">
                        <input type="tel" name="emergency_phone" placeholder="Emergency Contact Phone" value="<?= htmlspecialchars($_POST['emergency_phone'] ?? '') ?>">
                        <i class="fas fa-phone form-icon"></i>
                    </div>
                <?php endif; ?>

                <!-- Submit -->
                <button type="submit" class="btn-register">Create Account</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="/login">Sign in</a>
            </div>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="/register" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Role Selection
                </a>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="register-right">
        <div class="welcome-card">
            <h2 class="welcome-title">Welcome to Medi-Care</h2>
            <p class="welcome-text">
                <?php if ($role === 'patient'): ?>
                    Join thousands of patients who trust Medi-Care. Book appointments and manage health records.
                <?php elseif ($role === 'doctor'): ?>
                    Connect with patients and manage your practice efficiently.
                <?php else: ?>
                    Support our clinic operations and help provide excellent patient care.
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

</body>
</html>
