<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare - Your Trusted Healthcare Partner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="public/css/landing.css">
</head>
<body class="bg-gray-50">

    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img src="assets/images/logo.png" alt="MediCare Logo" class="h-10 w-10">
                    <span class="ml-2 text-xl font-semibold text-gray-900">MediCare</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium transition">About</a>
                    <a href="#services" class="text-gray-700 hover:text-blue-600 font-medium transition">Services</a>
                    <a href="#departments" class="text-gray-700 hover:text-blue-600 font-medium transition">Departments</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 font-medium transition">Contact</a>
                    <a href="/login" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition font-medium">Login</a>
                </div>

                <button class="md:hidden text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-20 pb-12 hero-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="pt-16 pb-20">
                <div class="max-w-2xl">
                    <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight mb-4">
                        Your Health,<br>Our Priority
                    </h1>
                    <p class="text-lg text-white mb-8 leading-relaxed opacity-95">
                        Book appointments with top medical specialists instantly. Quality healthcare made accessible, convenient, and patient-centered.
                    </p>
                    <a href="/login" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-md hover:bg-blue-50 transition font-medium shadow-md">
                        Book an Appointment
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="stat-number text-blue-600">200+</div>
                    <p class="text-gray-600 font-medium mt-2">Doctors</p>
                </div>
                <div class="text-center">
                    <div class="stat-number text-blue-600">50k+</div>
                    <p class="text-gray-600 font-medium mt-2">Patients</p>
                </div>
                <div class="text-center">
                    <div class="stat-number text-blue-600">30+</div>
                    <p class="text-gray-600 font-medium mt-2">Clinics</p>
                </div>
                <div class="text-center">
                    <div class="stat-number text-blue-600">4.9</div>
                    <p class="text-gray-600 font-medium mt-2">Rating</p>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">About</h2>
                <p class="text-gray-600 max-w-4xl mx-auto leading-relaxed">
                    Welcome to MediCare, a modern, secure, and user-friendly booking system designed to streamline operations in medical clinics. Developed to bridge the gap between patients and healthcare providers by offering a seamless experience in appointment scheduling, medical record management, and clinic administration.
                </p>
            </div>
        </div>
    </section>

    <section id="services" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-3">Our Services</h2>
                <p class="text-gray-600">Comprehensive healthcare services tailored to your needs</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">General Consultation</h3>
                    <p class="text-gray-600 text-sm mb-4">Comprehensive health assessments and expert care planning for all family members.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Cardiology Services</h3>
                    <p class="text-gray-600 text-sm mb-4">Advanced heart care including diagnostics and post-operative rehabilitation programs.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Laboratory Testing</h3>
                    <p class="text-gray-600 text-sm mb-4">Complete range of diagnostic tests with quick turnaround times and accurate results.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Emergency Care</h3>
                    <p class="text-gray-600 text-sm mb-4">Round-the-clock emergency services with trauma-certified staff and critical care resources.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Women's Health</h3>
                    <p class="text-gray-600 text-sm mb-4">Comprehensive maternal care from pre-pregnancy planning to delivery and postnatal support.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Vaccination Services</h3>
                    <p class="text-gray-600 text-sm mb-4">Complete immunization programs for all ages including routine and seasonal shots.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="departments" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-3">Departments</h2>
                <p class="text-gray-600">Expert care across every medical specialty</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Cardiology</h3>
                    <p class="text-gray-600 text-sm mb-3">Expert heart care with advanced diagnostic and treatment options for all cardiovascular conditions.</p>
                    <a href="/login" class="text-blue-600 font-medium text-sm hover:text-blue-700">Book Now →</a>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">General Practice</h3>
                    <p class="text-gray-600 text-sm mb-3">Comprehensive primary care for all ages, from routine checkups to chronic disease management.</p>
                    <a href="/login" class="text-blue-600 font-medium text-sm hover:text-blue-700">Book Now →</a>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Pediatrics</h3>
                    <p class="text-gray-600 text-sm mb-3">Specialized care for children from infancy through adolescence in a kid-friendly environment.</p>
                    <a href="/login" class="text-blue-600 font-medium text-sm hover:text-blue-700">Book Now →</a>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ophthalmology</h3>
                    <p class="text-gray-600 text-sm mb-3">Eye care services including examinations, treatments, and surgical procedures.</p>
                    <a href="/login" class="text-blue-600 font-medium text-sm hover:text-blue-700">Book Now →</a>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Orthopedics</h3>
                    <p class="text-gray-600 text-sm mb-3">Treatment for bones, joints, ligaments, tendons and muscles with cutting-edge techniques.</p>
                    <a href="/login" class="text-blue-600 font-medium text-sm hover:text-blue-700">Book Now →</a>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Dermatology</h3>
                    <p class="text-gray-600 text-sm mb-3">Expert skin care for medical, surgical and cosmetic dermatology needs.</p>
                    <a href="/login" class="text-blue-600 font-medium text-sm hover:text-blue-700">Book Now →</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Ready to Book Your Appointment?</h2>
            <p class="text-lg text-blue-100 mb-8">Join thousands of satisfied patients who trust MediCare for their healthcare needs.</p>
            <a href="/login" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-md hover:bg-gray-100 transition font-medium shadow-lg">
                Book an Appointment
            </a>
        </div>
    </section>

    <footer id="contact" class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="assets/images/logo.png" alt="MediCare Logo" class="h-8 w-8">
                        <span class="ml-2 text-lg font-bold">MediCare</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">Your trusted partner in healthcare, connecting patients with top medical professionals.</p>
                </div>

                <div>
                    <h4 class="font-bold text-base mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#services" class="text-gray-400 hover:text-white transition">Services</a></li>
                        <li><a href="#departments" class="text-gray-400 hover:text-white transition">Departments</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-base mb-4">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-base mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>(555) 123-4567</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>contact@medicare.com</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>123 Medical Plaza, Health City</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400 text-sm">&copy; 2025 MediCare. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>