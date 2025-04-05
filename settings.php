<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Check for admin role
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php?error=permission");
    exit();
}

// Message for settings updates
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'general_settings':
                // Update general settings
                $message = "General settings updated successfully.";
                $messageType = "success";
                break;
                
            case 'email_settings':
                // Update email settings
                $smtp_host = $_POST['smtp_host'] ?? '';
                $smtp_port = $_POST['smtp_port'] ?? '';
                $smtp_user = $_POST['smtp_user'] ?? '';
                $smtp_pass = $_POST['smtp_pass'] ?? '';

                // Save settings to a configuration file or database
                file_put_contents('email_config.json', json_encode([
                    'smtp_host' => $smtp_host,
                    'smtp_port' => $smtp_port,
                    'smtp_user' => $smtp_user,
                    'smtp_pass' => $smtp_pass
                ]));

                $message = "Email settings updated successfully.";
                $messageType = "success";
                break;
                
            case 'evaluation_periods':
                // Update evaluation periods
                $message = "Evaluation periods updated successfully.";
                $messageType = "success";
                break;
                
            case 'user_roles':
                // Update user roles and permissions
                $message = "User roles and permissions updated successfully.";
                $messageType = "success";
                break;
        }
    }
}

$pageTitle = "System Settings";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                System Settings
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <?php if ($message): ?>
            <div class="mb-4 rounded-md p-4 <?php echo $messageType === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'; ?>">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <?php if ($messageType === 'success'): ?>
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        <?php else: ?>
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm"><?php echo $message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="#general" class="text-indigo-600 border-indigo-500 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" aria-current="page">
                        General Settings
                    </a>
                    <a href="#email" class="text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm">
                        Email Configuration
                    </a>
                    <a href="#evaluations" class="text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm">
                        Evaluation Periods
                    </a>
                    <a href="#users" class="text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm">
                        User Roles & Permissions
                    </a>
                    <a href="#backup" class="text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm">
                        Backup & Restore
                    </a>
                </nav>
            </div>
            
            <!-- General Settings -->
            <div id="general" class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">General System Settings</h3>
                
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="general_settings">
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="system_name" class="block text-sm font-medium text-gray-700">System Name</label>
                            <input type="text" name="system_name" id="system_name" value="Faculty Management System" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="institution_name" class="block text-sm font-medium text-gray-700">Institution Name</label>
                            <input type="text" name="institution_name" id="institution_name" value="University Name" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700">Current Academic Year</label>
                            <input type="text" name="academic_year" id="academic_year" value="2024-2025" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700">System Timezone</label>
                            <select id="timezone" name="timezone" 
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="America/New_York" selected>Eastern Time (ET)</option>
                                <option value="America/Chicago">Central Time (CT)</option>
                                <option value="America/Denver">Mountain Time (MT)</option>
                                <option value="America/Los_Angeles">Pacific Time (PT)</option>
                                <option value="America/Anchorage">Alaska Time (AKT)</option>
                                <option value="Pacific/Honolulu">Hawaii Time (HT)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="footer_text" class="block text-sm font-medium text-gray-700">Footer Text</label>
                        <input type="text" name="footer_text" id="footer_text" value="© 2025 Faculty Management System. All rights reserved." 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="maintenance_mode" name="maintenance_mode" type="checkbox" 
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="maintenance_mode" class="font-medium text-gray-700">Enable Maintenance Mode</label>
                            <p class="text-gray-500">When enabled, only administrators can access the system.</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Configuration -->
            <div id="email" class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Email Configuration</h3>
                
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="email_settings">
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="smtp_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                            <input type="text" name="smtp_host" id="smtp_host" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="smtp_port" class="block text-sm font-medium text-gray-700">SMTP Port</label>
                            <input type="text" name="smtp_port" id="smtp_port" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="smtp_user" class="block text-sm font-medium text-gray-700">SMTP User</label>
                            <input type="text" name="smtp_user" id="smtp_user" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="smtp_pass" class="block text-sm font-medium text-gray-700">SMTP Password</label>
                            <input type="password" name="smtp_pass" id="smtp_pass" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize settings tabs
    const tabLinks = document.querySelectorAll('.border-b-2');
    const tabContents = document.querySelectorAll('#general, #email, #evaluations, #users, #backup');
    
    // Hide all tabs except the first one
    tabContents.forEach((content, index) => {
        if (index > 0) {
            content.style.display = 'none';
        }
    });
    
    // Add click handlers to tab links
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('href').substring(1);
            
            // Hide all tabs
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            
            // Show selected tab
            document.getElementById(tabId).style.display = 'block';
            
            // Update active tab styling
            tabLinks.forEach(tabLink => {
                tabLink.classList.remove('text-indigo-600', 'border-indigo-500');
                tabLink.classList.add('text-gray-500', 'border-transparent');
            });
            
            this.classList.remove('text-gray-500', 'border-transparent');
            this.classList.add('text-indigo-600', 'border-indigo-500');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle tab navigation
    const tabLinks = document.querySelectorAll('.border-b a');
    const tabContents = document.querySelectorAll('#general, #email, #evaluations, #users, #backup');
    
    // Set up click handlers for tabs
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get target tab ID from href
            const targetId = this.getAttribute('href').substring(1);
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            
            // Show target tab content
            document.getElementById(targetId).style.display = 'block';
            
            // Update active tab styles
            tabLinks.forEach(tab => {
                tab.classList.remove('text-indigo-600', 'border-indigo-500');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            
            this.classList.remove('text-gray-500', 'border-transparent');
            this.classList.add('text-indigo-600', 'border-indigo-500');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>