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
    <title>Medi-Care - Sign In</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<style>
    .login-page {
        display: flex;
        min-height: 100vh;
        width: 100%;
        margin: 0;
        padding: 0;
        background: var(--bg-gradient);
    }
    
    .login-left {
        flex: 0 0 50%;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        position: relative;
    }
    
    .login-right {
        flex: 1;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        position: relative;
        overflow: hidden;
    }
    
    .login-right::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: rotate(15deg);
    }
    
    .login-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 3rem;
        max-width: 450px;
        width: 100%;
        position: relative;
        z-index: 1;
    }
    
    .login-header {
        text-align: left;
        margin-bottom: 2.5rem;
    }
    
    .login-logo {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .login-logo-icon {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
    }
    
    .login-logo-text {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        letter-spacing: -0.02em;
    }
    
    .login-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .login-welcome {
        color: #6b7280;
        font-size: 0.9375rem;
    }
    
    .login-form {
        margin-top: 2rem;
    }
    
.form-icon-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem; /* space between icon and input */
    border: 1px solid #d1d5db; /* optional, for visible input border */
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #fff;
}

.form-icon-wrapper input {
    border: none;
    outline: none;
    flex: 1; /* fill remaining space */
    font-size: 1rem;
    background: transparent;
}

.form-icon {
    font-size: 1.25rem;
    color: #6b7280;
}



    
    .form-group input:focus + .form-icon,
    .form-group input:not(:placeholder-shown) + .form-icon {
        color: var(--primary-blue);
    }
    
    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1.5rem 0;
    }
    
    .remember-me {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .remember-me input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary-blue);
    }
    
    .remember-me label {
        font-size: 0.875rem;
        color: #4b5563;
        cursor: pointer;
        margin: 0;
    }
    
    .forgot-password {
        color: var(--primary-blue);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: var(--transition);
    }
    
    .forgot-password:hover {
        color: var(--primary-blue-dark);
        text-decoration: underline;
    }
    
    .btn-login {
        width: 100%;
        padding: 0.875rem;
        background: #1f2937;
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 0.5rem;
    }
    
    .btn-login:hover {
        background: #111827;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(31, 41, 55, 0.3);
    }
    
    .signup-link {
        text-align: center;
        margin-top: 1.5rem;
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .signup-link a {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }
    
    .signup-link a:hover {
        color: var(--primary-blue-dark);
        text-decoration: underline;
    }
    
    .social-login {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .social-login-title {
        text-align: center;
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    
    .social-icons {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }
    
    .social-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .social-icon.google {
        background: #4285f4;
    }
    
    .social-icon.google:hover {
        background: #357ae8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
    }
    
    .social-icon.github {
        background: #24292e;
    }
    
    .social-icon.github:hover {
        background: #1a1e22;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(36, 41, 46, 0.3);
    }
    
    .social-icon.facebook {
        background: #1877f2;
    }
    
    .social-icon.facebook:hover {
        background: #166fe5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3);
    }
    
    /* Welcome Section */
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
    
    .welcome-logo {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .welcome-logo-icon {
        width: 64px;
        height: 64px;
        background: white;
        border-radius: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-blue);
        font-size: 2rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    
    .welcome-logo-text {
        font-size: 2rem;
        font-weight: 700;
        color: white;
        letter-spacing: -0.02em;
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
        margin-bottom: 1rem;
    }
    
    .welcome-stats {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9375rem;
        margin-bottom: 2rem;
    }
    
    .cta-card {
        background: rgba(31, 41, 55, 0.6);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .cta-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.75rem;
    }
    
    .cta-text {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    
    .cta-users {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: -0.5rem;
    }
    
    .user-avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: -8px;
        background-size: cover;
        background-position: center;
    }
    
    .user-avatar-small.profile-1 {
        background-image: url('https://res.cloudinary.com/dc3cbupaq/image/upload/v1763266716/image_3_johjuh.jpg');
    }
    
    .user-avatar-small.profile-2 {
        background-image: url('https://res.cloudinary.com/dc3cbupaq/image/upload/v1763266710/image_1_nlmscj.jpg');
    }
    
    .user-avatar-small.profile-3 {
        background-image: url('https://res.cloudinary.com/dc3cbupaq/image/upload/v1763265883/240_F_621987811_ZU0yEkrhkosvwQ7lQO7EQI8fQYPRSHIw_dwkbat.jpg');
    }
    
    .user-avatar-small.more {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
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
        .login-page {
            flex-direction: column;
        }
        
        .login-left {
            flex: 0 0 auto;
            min-height: auto;
            padding: 2rem;
        }
        
        .login-right {
            flex: 0 0 auto;
            padding: 2rem;
        }
        
        .welcome-card {
            max-width: 100%;
        }
    }
    
    @media (max-width: 768px) {
        .login-card {
            padding: 2rem;
        }
        
        .welcome-card {
            padding: 2rem;
        }
        
        .welcome-title {
            font-size: 2rem;
        }
    }
</style>

<div class="login-page">
    <!-- Left Panel - Sign In Form -->
    <div class="login-left">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <div class="login-logo-icon">
                        <img src="/assets/images/Medi-Care.svg" alt="Medi-Care Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="login-logo-text">Medi-Care</div>
                </div>
                <h1 class="login-title">Sign in</h1>
                <p class="login-welcome">Welcome back! Please login to continue</p>
            </div>
            
            <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>
            
            <form method="post" action="" class="login-form">
                <div class="form-group form-icon-wrapper email">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <i class="fas fa-envelope form-icon"></i>
                </div>
                
                <div class="form-group form-icon-wrapper password">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-lock form-icon"></i>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot Password</a>
                </div>
                
                <button type="submit" class="btn-login">Sign in</button>
            </form>
            
            <div class="signup-link">
                Don't have an account? <a href="/register">Sign up</a>
            </div>
            
            <div class="social-login">
                <div class="social-login-title">Or continue with</div>
                <div class="social-icons">
                    <div class="social-icon google" title="Sign in with Google">
                        <i class="fab fa-google"></i>
                    </div>
                    <div class="social-icon github" title="Sign in with GitHub">
                        <i class="fab fa-github"></i>
                    </div>
                    <div class="social-icon facebook" title="Sign in with Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="/" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Home</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Right Panel - Welcome Section -->
    <div class="login-right">
        <div class="welcome-card">
            <div class="welcome-logo">
                <div class="welcome-logo-icon">
                    <img src="/assets/images/Medi-Care.svg" alt="Medi-Care Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="welcome-logo-text">Medi-Care</div>
            </div>
            
            <h2 class="welcome-title">Welcome to Medi-Care</h2>
            
            <p class="welcome-text">
                Medi-Care helps healthcare providers and patients manage appointments, medical records, and health information efficiently. Join us and experience seamless healthcare management today.
            </p>
            
            <p class="welcome-stats">
                More than 50k people trust us, it's your turn
            </p>
            
            <div class="cta-card">
                <h3 class="cta-title">Get the right care at the right place</h3>
                <p class="cta-text">
                    Be among the first to experience the easiest way to manage your healthcare needs and connect with trusted medical professionals.
                </p>
                <div class="cta-users">
                    <div class="user-avatar-small profile-1"></div>
                    <div class="user-avatar-small profile-2"></div>
                    <div class="user-avatar-small profile-3"></div>
                    <div class="user-avatar-small more">+2</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
