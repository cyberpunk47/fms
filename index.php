<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FMS Login</title>
    <link rel="stylesheet" href="./src/output.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl bg-gray-900 rounded-xl shadow-2xl overflow-hidden border border-gray-800">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- Login Form -->
            <div class="p-8 md:p-12">
                <div class="mb-10 text-center">
                    <div class="inline-block bg-indigo-600/20 p-4 rounded-2xl">
                        <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                    </div>
                    <h1 class="mt-6 text-3xl font-bold text-gray-100">Faculty Portal</h1>
                    <p class="mt-2 text-gray-400">Secure access to management system</p>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="space-y-6">
                    <?php if (isset($error)): ?>
                        <div class="p-3 bg-red-500/20 text-red-300 rounded-lg text-sm">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Institutional Email</label>
                        <input type="email" id="email" name="email" required
                            class="input-field w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-900"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div>
                        <div class="flex justify-between mb-2">
                            <label for="password" class="text-sm font-medium text-gray-300">Password</label>
                            <a href="forgot-password.php" class="text-sm text-indigo-400 hover:text-indigo-300">Need help?</a>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="input-field w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-gray-100 placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-900">
                    </div>

                    <button type="submit" 
                        class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        Access Portal
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-gray-400">New faculty member? 
                        <a href="registration.php" class="text-indigo-400 hover:text-indigo-300 font-medium">Request access</a>
                    </p>
                </div>
            </div>

            <!-- Graphic Side -->
            <div class="hidden md:block bg-gradient-to-br from-indigo-900/50 to-gray-900 p-12">
                <div class="h-full flex flex-col justify-center items-center text-center">
                    <div class="max-w-xs">
                        <svg class="w-full text-indigo-300" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M53.2 153.6C44.8 140.8 40 125.867 40 110C40 71.6 71.6 40 110 40C125.867 40 140.8 44.8 153.6 53.2M146.8 46.4C134.4 38.4 119.733 34 104 34C63.2 34 30 67.2 30 108C30 123.733 34.4 138.4 42.4 150.8M160 108C160 146.4 128.4 178 90 178C74.1333 178 59.2 173.2 46.4 164.8M153.6 162.8C141.2 170.8 126.267 175.2 110 175.2C69.2 175.2 36 142 36 101.2C36 85.3333 40.4 70.4 48.4 58" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                            <circle cx="100" cy="100" r="32" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="4"/>
                        </svg>
                        <h2 class="mt-8 text-xl font-semibold text-indigo-100">Faculty Excellence Portal</h2>
                        <p class="mt-2 text-indigo-200/80 text-sm">
                            Streamlined management for academic progression and development
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="absolute bottom-4 text-center w-full text-gray-500 text-sm">
        © <?php echo date('Y'); ?> Faculty Management System. Restricted access.
    </div>
</body>
</html>