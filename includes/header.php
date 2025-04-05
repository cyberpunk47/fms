<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Faculty Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="./src/output.css">

    <style>
        /* Core layout fixes */
        body {
            padding-top: 4rem; /* Space for navbar */
        }
        
        /* Fixed navbar */
        nav.fixed-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4rem;
            z-index: 50;
        }
        
        /* Sidebar positioning */
        #sidebar {
            position: fixed;
            top: 4rem; /* Start below navbar */
            height: calc(100vh - 4rem);
            z-index: 40;
            transition: transform 0.3s ease;
        }
        
        /* Main content padding */
        .main-content {
            min-height: calc(100vh - 4rem);
        }
        
        /* Mobile adjustments */
        @media (max-width: 640px) {
            #sidebar {
                transform: translateX(-100%);
                width: 16rem;
            }
            
            #sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="fixed-nav bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex gap-1 items-center">
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-button" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <!-- Logo -->
                    <a href="<?php echo $base_url; ?>dashboard.php" class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-gray-800">FMS</span>
                    </a>
                </div>

                <!-- Desktop User Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <div class="ml-3 relative">
                        <div>
                            <button onclick="toggleDropdown()" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="sr-only">Open user menu</span>
                                <div class="bg-gray-100 rounded-full p-2">
                                    <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <!-- Dropdown menu -->
                        <div id="dropdown-content" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="px-4 py-2 text-xs text-gray-400">
                                Logged in as <span class="font-medium text-gray-900"><?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                            </div>
                            <a href="<?php echo $base_url; ?>profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                            <a href="<?php echo $base_url; ?>settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <div class="border-t border-gray-100"></div>
                            <a href="<?php echo $base_url; ?>logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a>
                        </div>
                    </div>
                </div>

                <!-- Mobile user menu button -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button onclick="toggleUserMenu()" class="bg-white inline-flex items-center justify-center p-2 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Open user menu</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-user-menu" class="sm:hidden hidden">
            <div class="pt-2 pb-3 border-t border-gray-200">
                <div class="px-4 py-2 text-sm font-medium text-gray-800">
                    <?php echo $_SESSION['user_name'] ?? 'User'; ?>
                </div>
                <a href="<?php echo $base_url; ?>profile.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Profile</a>
                <a href="<?php echo $base_url; ?>settings.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Settings</a>
                <a href="<?php echo $base_url; ?>logout.php" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Sign out</a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar" class="bg-gray-800 text-white w-64 hidden sm:block">
        <div class="px-4 py-2">
            <a href="<?php echo $base_url; ?>dashboard.php" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Dashboard</a>
            <a href="<?php echo $base_url; ?>faculty.php" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Faculty</a>
            <a href="<?php echo $base_url; ?>departments.php" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Departments</a>
            <a href="<?php echo $base_url; ?>evaluations.php" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Evaluations</a>
            <a href="<?php echo $base_url; ?>development.php" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Development</a>
            <a href="<?php echo $base_url; ?>reports.php" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Reports</a>
            <a href="<?php echo $base_url; ?>settings.php" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">Settings</a>
        </div>
    </div>
</body>
</html>