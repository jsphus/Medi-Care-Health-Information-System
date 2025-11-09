<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MediCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="assets/images/logo.png" alt="MediCare Logo" class="h-16 w-16">
                </div>
                <h1 class="text-2xl font-bold text-gray-900">MediCare</h1>
                <p class="text-gray-500 mt-2">Sign in to your account</p>
            </div>

            <!-- Error Message (if any) -->
            <?php if (isset($error) && !empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="post" action="" class="space-y-4">
                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Enter your email" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 focus:outline-none transition"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 focus:outline-none transition"
                    >
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full bg-cyan-400 hover:bg-cyan-500 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 mt-6"
                >
                    Login
                </button>
            </form>

            <!-- Footer Link -->
            <div class="mt-6 text-center">
                <a href="/" class="text-sm text-gray-600 hover:text-cyan-500 transition">Back to Home</a>
            </div>
        </div>
    </div>

</body>
</html>