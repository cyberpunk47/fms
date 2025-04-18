<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Faculty Management System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="./src/output.css">

    <style>
 
        body {
            padding-top: 4rem; 
        }
    
        nav.fixed-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4rem;
            z-index: 50;
        }
        
  
        #sidebar {
            position: fixed;
            top: 4rem; 
            height: calc(100vh - 4rem);
            z-index: 40;
            transition: transform 0.3s ease;
        }
        
     
        .main-content {
            min-height: calc(100vh - 4rem);
        }
       
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
    <nav class="fixed-nav bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex gap-1 items-center">
                    <button id="mobile-menu-button" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <a href="<?php echo $base_url; ?>dashboard.php" class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-gray-800">FMS</span>
                    </a>
                </div>
                <a href="<?php echo $base_url; ?>faculty_login.php" class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-gray-800">Logout</span>
                    </a>
            </div>
        </div>

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

</body>
</html>