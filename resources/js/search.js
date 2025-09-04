// Enhanced search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="q"]');
    const searchForm = document.querySelector('form[action*="search"]');
    
    if (searchInput && searchForm) {
        // Add search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value.trim()) {
                    searchForm.submit();
                }
            }
        });

        // Add search icon click functionality
        const searchIcon = searchForm.querySelector('.bx-search');
        if (searchIcon) {
            searchIcon.style.cursor = 'pointer';
            searchIcon.addEventListener('click', function() {
                if (searchInput.value.trim()) {
                    searchForm.submit();
                }
            });
        }

        // Auto-focus search input with Ctrl+K
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });

        // Add placeholder animation
        let placeholderTexts = [
            'Search users...',
            'Find by name...',
            'Search by email...',
            'Press Ctrl+K to focus'
        ];
        let currentIndex = 0;

        setInterval(() => {
            if (!searchInput.matches(':focus') && !searchInput.value) {
                searchInput.placeholder = placeholderTexts[currentIndex];
                currentIndex = (currentIndex + 1) % placeholderTexts.length;
            }
        }, 3000);
    }
});
