// Enhanced search functionality with menu autocomplete
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="q"]');
    const searchForm = document.querySelector('form[action*="search"]');
    
    if (searchInput && searchForm) {
        // Create autocomplete dropdown
        const autocompleteContainer = document.createElement('div');
        autocompleteContainer.className = 'search-autocomplete';
        autocompleteContainer.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        `;
        
        // Make search container relative
        const searchContainer = searchInput.closest('.input-group') || searchInput.parentElement;
        searchContainer.style.position = 'relative';
        searchContainer.appendChild(autocompleteContainer);

        let debounceTimer;
        
        // Add autocomplete functionality
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(debounceTimer);
            
            if (query.length < 2) {
                autocompleteContainer.style.display = 'none';
                return;
            }
            
            debounceTimer = setTimeout(() => {
                fetch(`/search/autocomplete?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displayAutocomplete(data);
                    })
                    .catch(error => {
                        console.error('Autocomplete error:', error);
                    });
            }, 300);
        });

        function displayAutocomplete(data) {
            autocompleteContainer.innerHTML = '';
            
            let hasResults = false;
            
            // Display menu items
            if (data.menus && data.menus.length > 0) {
                hasResults = true;
                const menuHeader = document.createElement('div');
                menuHeader.className = 'autocomplete-header';
                menuHeader.innerHTML = '<small class="text-muted px-3 py-2 d-block"><i class="bx bx-menu me-1"></i>Menu Items</small>';
                autocompleteContainer.appendChild(menuHeader);
                
                data.menus.forEach(menu => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.style.cssText = 'padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0;';
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="${menu.icon} me-2 text-primary"></i>
                            <div>
                                <div class="fw-medium">${menu.name}</div>
                                ${menu.parent ? `<small class="text-muted">${menu.parent}</small>` : ''}
                            </div>
                        </div>
                    `;
                    
                    item.addEventListener('click', () => {
                        window.open(menu.url, menu.target);
                        autocompleteContainer.style.display = 'none';
                        searchInput.value = '';
                    });
                    
                    item.addEventListener('mouseenter', () => {
                        item.style.backgroundColor = '#f8f9fa';
                    });
                    
                    item.addEventListener('mouseleave', () => {
                        item.style.backgroundColor = 'white';
                    });
                    
                    autocompleteContainer.appendChild(item);
                });
            }
            
            // Display users
            if (data.users && data.users.length > 0) {
                hasResults = true;
                const userHeader = document.createElement('div');
                userHeader.className = 'autocomplete-header';
                userHeader.innerHTML = '<small class="text-muted px-3 py-2 d-block"><i class="bx bx-user me-1"></i>Users</small>';
                autocompleteContainer.appendChild(userHeader);
                
                data.users.forEach(user => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.style.cssText = 'padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0;';
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <img src="/assets/img/avatars/1.png" alt="Avatar" class="rounded-circle">
                            </div>
                            <div>
                                <div class="fw-medium">${user.name}</div>
                                <small class="text-muted">${user.email}</small>
                            </div>
                        </div>
                    `;
                    
                    item.addEventListener('click', () => {
                        searchInput.value = user.name;
                        searchForm.submit();
                    });
                    
                    item.addEventListener('mouseenter', () => {
                        item.style.backgroundColor = '#f8f9fa';
                    });
                    
                    item.addEventListener('mouseleave', () => {
                        item.style.backgroundColor = 'white';
                    });
                    
                    autocompleteContainer.appendChild(item);
                });
            }
            
            if (hasResults) {
                // Add "See all results" option
                const seeAllItem = document.createElement('div');
                seeAllItem.className = 'autocomplete-item';
                seeAllItem.style.cssText = 'padding: 8px 12px; cursor: pointer; background: #f8f9fa; text-align: center;';
                seeAllItem.innerHTML = '<small class="text-primary"><i class="bx bx-search me-1"></i>See all results</small>';
                seeAllItem.addEventListener('click', () => {
                    searchForm.submit();
                });
                autocompleteContainer.appendChild(seeAllItem);
                
                autocompleteContainer.style.display = 'block';
            } else {
                autocompleteContainer.style.display = 'none';
            }
        }

        // Hide autocomplete when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchContainer.contains(e.target)) {
                autocompleteContainer.style.display = 'none';
            }
        });

        // Add search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (this.value.trim()) {
                    autocompleteContainer.style.display = 'none';
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
            'Search menus & users...',
            'Find pages...',
            'Search by name...',
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
