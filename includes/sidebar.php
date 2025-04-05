<!-- Sidebar -->
<aside id="sidebar" class="hidden sm:flex sm:flex-col w-64 bg-white shadow-md fixed h-full">
    <nav class="p-4 flex-1 overflow-y-auto">
        <ul class="space-y-2">
            <li>
                <a href="<?php echo $base_url; ?>dashboard.php"
                    class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                        </path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>faculty.php"
                    class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'faculty.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                        </path>
                    </svg>
                    Faculty
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>departments.php"
                    class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z">
                        </path>
                    </svg>
                    Departments
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>evaluations.php"
                    class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'evaluations.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z">
                        </path>
                    </svg>
                    Evaluations
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>development.php"
                    class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'development.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Development
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>reports.php"
                    class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z">
                        </path>
                    </svg>
                    Reports
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>settings.php"
                    class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 text-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Settings
                </a>
            </li>
        </ul>
    </nav>
</aside>