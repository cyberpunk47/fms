<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality for faculty view
        const tabLinks = document.querySelectorAll('.tab-link');
        if (tabLinks.length > 0) {
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Get the tab to show
                    const tabId = this.getAttribute('data-tab');
                    
                    // Hide all tab contents
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Remove active class from all links
                    tabLinks.forEach(tabLink => {
                        tabLink.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                        tabLink.classList.add('border-transparent', 'text-gray-500');
                    });
                    
                    // Show the selected tab
                    document.getElementById(tabId + '-tab').classList.remove('hidden');
                    
                    // Mark this link as active
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('active', 'border-indigo-500', 'text-indigo-600');
                });
            });
        }
    });

    // Add base URL for JavaScript navigation if needed
    const baseUrl = '<?php echo $base_url; ?>';
    
    // Toggle mobile menu 
    function toggleMobileMenu() {
        const userMenu = document.getElementById('mobile-user-menu');
        if (userMenu) {
            userMenu.classList.toggle('hidden');
        }
    }

    // Toggle user dropdown
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown-content');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    // Toggle user menu on mobile
    function toggleUserMenu() {
        const userMenu = document.getElementById('mobile-user-menu');
        if (userMenu) {
            userMenu.classList.toggle('hidden');
        }
    }

    // Mobile sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        
        if (mobileMenuButton && sidebar) {
            mobileMenuButton.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 640 && 
                sidebar && 
                sidebar.classList.contains('open') && 
                !sidebar.contains(event.target) && 
                !mobileMenuButton.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Make all navigation links use absolute paths
        document.querySelectorAll('a[href]:not([href^="http"]):not([href^="#"])').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.startsWith('/') && !href.includes('://')) {
                link.setAttribute('href', baseUrl + href.replace(/^\.\//, ''));
            }
        });
    });
</script>
<script>
    // Keep existing footer JavaScript here
    function navigateTo(url) {
        window.location.href = url;
        return false; // Prevent default link behavior
    }
</script>

<?php if (isset($pageScripts)): ?>
    <?php echo $pageScripts; ?>
<?php endif; ?>

<script src="assets/js/script.js"></script>
</body>
</html>