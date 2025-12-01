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
    <title>Lifesaver-Clinic - Register</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<style>
    .register-page {
        display: flex;
        min-height: 100vh;
        width: 100%;
        margin: 0;
        padding: 0;
        background: var(--bg-gradient);
    }
    
    .register-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
    }
    
    .register-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 3rem;
        max-width: 500px;
        width: 100%;
    }
    
    .register-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .register-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .register-logo-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
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
    
    .role-selection {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .role-card {
        border: 2px solid #e5e7eb;
        border-radius: var(--radius-md);
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        background: white;
        width: 100%;
        max-width: 280px;
    }
    
    .role-card:hover {
        border-color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 64, 175, 0.15);
    }
    
    .role-card.selected {
        border-color: var(--primary-blue);
        background: #eff6ff;
    }
    
    .role-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }
    
    .role-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .role-description {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.5;
    }
    
    .btn-continue {
        width: 100%;
        padding: 0.875rem;
        background: #10b981;
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 2rem;
    }
    
    .btn-continue:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
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
    
    @media (max-width: 768px) {
        .register-card {
            padding: 2rem;
        }
        
        .role-card {
            padding: 1.5rem;
        }
        
        .role-icon {
            width: 64px;
            height: 64px;
            font-size: 1.5rem;
        }
    }
</style>

<div class="register-page">
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-logo">
                    <div class="register-logo-icon">
                    <img src="/assets/images/Medi-Care.svg" alt="Medi-Care Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="register-logo-text" style="font-size: 1.875rem; font-weight: 700; color: #1f2937;">Lifesaver-Clinic</div>
                </div>
                <h1 class="register-title">Create Patient Account</h1>
                <p class="register-subtitle">Join our healthcare community today</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>
            
            <form method="get" action="/register" id="roleForm">
                <input type="hidden" name="role" id="selectedRole" value="patient">
                <div class="role-selection">
                    <div class="role-card patient selected" data-role="patient">
                        <div class="role-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="role-name">Patient Registration</div>
                        <div class="role-description">Book appointments, access your health records, and manage your healthcare journey with ease.</div>
                    </div>
                </div>
                
                <button type="submit" class="btn-continue" id="continueBtn">
                    Continue to Registration
                </button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="/login">Sign in</a>
            </div>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="/" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Home</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-select patient role and enable continue button on page load
    document.addEventListener('DOMContentLoaded', function() {
        const patientCard = document.querySelector('.role-card.patient');
        const continueBtn = document.getElementById('continueBtn');
        
        // Patient is already pre-selected via HTML
        document.getElementById('selectedRole').value = 'patient';
        
        // Optional: Still allow clicking for visual feedback
        patientCard.addEventListener('click', function() {
            // Already selected, but we can add some feedback
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
</script>

</body>
</html>

