<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const tabLinks = document.querySelectorAll('.tab-link');
        if (tabLinks.length > 0) {
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const tabId = this.getAttribute('data-tab');
                    
     
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                    });
       
                    tabLinks.forEach(tabLink => {
                        tabLink.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                        tabLink.classList.add('border-transparent', 'text-gray-500');
                    });
  
                    document.getElementById(tabId + '-tab').classList.remove('hidden');
   
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('active', 'border-indigo-500', 'text-indigo-600');
                });
            });
        }
    });

    
    const baseUrl = '<?php echo $base_url; ?>';
    
  
    function toggleMobileMenu() {
        const userMenu = document.getElementById('mobile-user-menu');
        if (userMenu) {
            userMenu.classList.toggle('hidden');
        }
    }

    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown-content');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    function toggleUserMenu() {
        const userMenu = document.getElementById('mobile-user-menu');
        if (userMenu) {
            userMenu.classList.toggle('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        
        if (mobileMenuButton && sidebar) {
            mobileMenuButton.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
        }
        
     
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 640 && 
                sidebar && 
                sidebar.classList.contains('open') && 
                !sidebar.contains(event.target) && 
                !mobileMenuButton.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        });

        document.querySelectorAll('a[href]:not([href^="http"]):not([href^="#"])').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.startsWith('/') && !href.includes('://')) {
                link.setAttribute('href', baseUrl + href.replace(/^\.\//, ''));
            }
        });
    });
</script>
<script>
   
    function navigateTo(url) {
        window.location.href = url;
        return false; 
    }
</script>

<?php if (isset($pageScripts)): ?>
    <?php echo $pageScripts; ?>
<?php endif; ?>

<script src="assets/js/script.js"></script>
</body>
</html>