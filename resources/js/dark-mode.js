// Dark Mode Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('dark_mode');
    const body = document.body;
    const html = document.documentElement;

    // Check for saved dark mode preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply the saved theme on page load
    if (currentTheme === 'dark') {
        enableDarkMode();
        if (darkModeToggle) {
            darkModeToggle.checked = true;
        }
    }

    // Add event listener to dark mode toggle
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', function() {
            if (this.checked) {
                enableDarkMode();
                localStorage.setItem('theme', 'dark');
            } else {
                disableDarkMode();
                localStorage.setItem('theme', 'light');
            }
        });
    }

    function enableDarkMode() {
        html.setAttribute('data-theme', 'dark');
        body.classList.add('dark-mode');
    }

    function disableDarkMode() {
        html.setAttribute('data-theme', 'light');
        body.classList.remove('dark-mode');
    }
});
