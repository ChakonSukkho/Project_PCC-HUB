const modal = document.getElementById("announcementModal");
const form = document.getElementById("announcementForm");
const addBtn = document.getElementById("addAnnouncementBtn");
const closeBtn = document.getElementById("closeModal");

// ... all your other JS code ...

// ✅ Place the submit event listener here
form.addEventListener("submit", (e) => {
  e.preventDefault();

  const formData = new FormData(form);

  fetch("save_announcement.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("✅ Announcement added successfully!");
      form.reset();
      modal.style.display = "none";
      loadAnnouncements();
    } else {
      alert("❌ " + data.message);
    }
  })
  .catch(err => console.error(err));
});
      
      // Integrated JavaScript with your existing PHP structure
        let displayedAnnouncements = [];
        let currentPage = 1;
        const itemsPerPage = 6;
        let currentFilters = {
            category: '<?php echo $category_filter; ?>',
            search: '<?php echo htmlspecialchars($search_query); ?>',
            sort: '<?php echo $sort_by; ?>'
        };
        let hasMoreData = true;
        let isLoading = false;

        // DOM Elements
        const announcementsGrid = document.getElementById('announcementsGrid');
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const sortSelect = document.getElementById('sortBy');
        const loadMoreBtn = document.getElementById('loadMoreBtn');

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Remove no-js class if it exists
            document.body.classList.remove('no-js');
            
            initializeNavigation();
            loadAnnouncements(true); // Load fresh data
            setupEventListeners();
            addInteractiveEffects();
        });

        // Navigation effects
        function initializeNavigation() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href === '#') {
                        e.preventDefault();
                        return;
                    }
                    
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    // Create ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.3);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;

                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        }

        // Load announcements from server
        async function loadAnnouncements(reset = false) {
            if (reset) {
                currentPage = 1;
                displayedAnnouncements = [];
                announcementsGrid.innerHTML = '';
                hasMoreData = true;
            }

            if (!hasMoreData || isLoading) return;

            try {
                isLoading = true;
                showLoadingState();
                
                const params = new URLSearchParams({
                    ajax: '1',
                    category: currentFilters.category,
                    search: currentFilters.search,
                    sort: currentFilters.sort,
                    page: currentPage,
                    limit: itemsPerPage
                });

                const response = await fetch(`announcement.php?${params}`);
                const data = await response.json();

                hideLoadingState();

                if (data.success && data.data.length > 0) {
                    data.data.forEach(announcement => {
                        displayedAnnouncements.push(announcement);
                        renderAnnouncement(announcement);
                    });
                    
                    currentPage++;
                    hasMoreData = data.pagination.has_more;
                    
                    if (!hasMoreData) {
                        loadMoreBtn.style.display = 'none';
                    } else {
                        loadMoreBtn.style.display = 'block';
                    }
                } else if (displayedAnnouncements.length === 0) {
                    showEmptyState();
                }
            } catch (error) {
                console.error('Error loading announcements:', error);
                hideLoadingState();
                showErrorState();
            } finally {
                isLoading = false;
            }
        }

        // Show loading state
        function showLoadingState() {
            if (currentPage === 1) {
                announcementsGrid.innerHTML = `
                    <div class="loading-state">
                        <div class="loading-spinner"></div>
                        <p>Loading announcements...</p>
                    </div>
                `;
            }
            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Loading...';
        }

        // Hide loading state
        function hideLoadingState() {
            const loadingState = document.querySelector('.loading-state');
            if (loadingState) {
                loadingState.remove();
            }
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = 'Load More Announcements';
        }

        // Render single announcement
        function renderAnnouncement(announcement) {
            const card = document.createElement('div');
            card.className = `announcement-card ${announcement.category} fade-in`;
            card.dataset.id = announcement.announcement_id;
            
            const categoryIcons = {
                'general': '📢',
                'academic': '🎓',
                'co-curricular': '🏆',
                'event': '📅',
                'urgent': '🚨'
            };
            
            const priorityBadge = () => {
                switch(announcement.priority) {
                    case 'urgent': return '<span class="announcement-priority urgent">🚨 URGENT</span>';
                    case 'high': return '<span class="announcement-priority high">⚠️ HIGH</span>';
                    case 'medium': return '<span class="announcement-priority medium">ℹ️ Medium</span>';
                    case 'low': return '<span class="announcement-priority low">⬇️ Low</span>';
                    default: return '';
                }
            };
            
            card.innerHTML = `
                <div class="announcement-header">
                    <div class="announcement-meta">
                        <span class="announcement-category ${announcement.category}">
                            ${(categoryIcons[announcement.category] || '📢')} ${announcement.category.charAt(0).toUpperCase() + announcement.category.slice(1).replace('-', ' ')}
                        </span>
                        ${priorityBadge()}
                    </div>
                    <div class="announcement-time">${announcement.time_ago}</div>
                </div>
                
                <div class="announcement-content">
                    <h3 class="announcement-title">${escapeHtml(announcement.title)}</h3>
                    <p class="announcement-description">
                        ${escapeHtml(announcement.content).replace(/\n/g, '<br>')}
                    </p>
                </div>
                
                ${announcement.image ? `
                    <div class="announcement-image">
                        <img src="../uploads/announcements/${escapeHtml(announcement.image)}" 
                             alt="Announcement Image" 
                             loading="lazy">
                    </div>
                ` : ''}
                
                <div class="announcement-footer">
                    <div class="announcement-author">
                        <span class="author-icon">👤</span>
                        <span class="author-name">
                            ${escapeHtml(announcement.created_by_name || 'PCC Admin')}
                        </span>
                    </div>
                </div>
            `;
            
            announcementsGrid.appendChild(card);
            
            // Trigger animation
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }

        // Setup event listeners
        function setupEventListeners() {
            // Search functionality
            searchInput.addEventListener('input', debounce(handleSearch, 300));
            
            // Category filter
            categoryFilter.addEventListener('change', handleFilter);
            
            // Sort functionality
            sortSelect.addEventListener('change', handleSort);
            
            // Load more button
            loadMoreBtn.addEventListener('click', () => loadAnnouncements(false));
        }

        // Search handler
        function handleSearch() {
            currentFilters.search = searchInput.value.toLowerCase().trim();
            loadAnnouncements(true);
        }

        // Filter handler
        function handleFilter() {
            currentFilters.category = categoryFilter.value;
            loadAnnouncements(true);
        }

        // Sort handler
        function handleSort() {
            currentFilters.sort = sortSelect.value;
            loadAnnouncements(true);
        }

        // Show empty state
        function showEmptyState() {
            announcementsGrid.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>No announcements found</h3>
                    <p>No announcements match your current criteria.</p>
                    ${(currentFilters.search !== '' || currentFilters.category !== 'all') ? 
                        '<button onclick="clearFilters()" class="retry-btn">Clear Filters</button>' : ''
                    }
                </div>
            `;
            loadMoreBtn.style.display = 'none';
        }

        // Show error state
        function showErrorState() {
            announcementsGrid.innerHTML = `
                <div class="error-state">
                    <div class="error-state-icon">⚠️</div>
                    <p>Failed to load announcements. Please try again later.</p>
                    <button onclick="loadAnnouncements(true)" class="retry-btn">Retry</button>
                </div>
            `;
            loadMoreBtn.style.display = 'none';
        }

        // Clear filters function
        function clearFilters() {
            currentFilters = { category: 'all', search: '', sort: 'newest' };
            searchInput.value = '';
            categoryFilter.value = 'all';
            sortSelect.value = 'newest';
            loadAnnouncements(true);
        }

        // Utility function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Interactive effects
        function addInteractiveEffects() {
            // Parallax effect for background decorations
            window.addEventListener('mousemove', function(e) {
                const decorations = document.querySelectorAll('.bg-decoration');
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                
                decorations.forEach((decoration, index) => {
                    const moveX = (x - 0.5) * 20 * (index + 1);
                    const moveY = (y - 0.5) * 20 * (index + 1);
                    decoration.style.transform = `translate(${moveX}px, ${moveY}px)`;
                });
            });

            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe new cards as they're added
            const originalAppendChild = announcementsGrid.appendChild.bind(announcementsGrid);
            announcementsGrid.appendChild = function(child) {
                if (child.classList && child.classList.contains('announcement-card')) {
                    child.style.opacity = '0';
                    child.style.transform = 'translateY(20px)';
                    child.style.transition = 'all 0.6s ease';
                    observer.observe(child);
                }
                return originalAppendChild(child);
            };

            // Add hover effects to announcement cards
            document.addEventListener('mouseenter', function(e) {
                if (e.target.classList.contains('announcement-card')) {
                    e.target.style.transform = 'translateY(-5px) scale(1.02)';
                }
            }, true);

            document.addEventListener('mouseleave', function(e) {
                if (e.target.classList.contains('announcement-card')) {
                    e.target.style.transform = 'translateY(0) scale(1)';
                }
            }, true);
        }

        // Initialize page load animations
        window.addEventListener('load', function() {
            // Add smooth entrance animation
            document.querySelector('.main-content').style.animation = 'fadeInUp 0.8s ease';
            
            // Stagger animation for sidebar items
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, index * 100);
            });
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(event) {
            if (event.state) {
                currentFilters = event.state.filters;
                currentPage = event.state.page;
                
                // Update form elements
                searchInput.value = currentFilters.search;
                categoryFilter.value = currentFilters.category;
                sortSelect.value = currentFilters.sort;
                
                loadAnnouncements(true);
            }
        });

        // Push state when filters change
        function updateURL() {
            const params = new URLSearchParams();
            if (currentFilters.search) params.set('search', currentFilters.search);
            if (currentFilters.category !== 'all') params.set('category', currentFilters.category);
            if (currentFilters.sort !== 'newest') params.set('sort', currentFilters.sort);
            
            const newURL = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            
            history.pushState({
                filters: currentFilters,
                page: currentPage
            }, '', newURL);
        }

        // Enhanced search with URL updates
        function handleSearchEnhanced() {
            currentFilters.search = searchInput.value.toLowerCase().trim();
            updateURL();
            loadAnnouncements(true);
        }

        function handleFilterEnhanced() {
            currentFilters.category = categoryFilter.value;
            updateURL();
            loadAnnouncements(true);
        }

        function handleSortEnhanced() {
            currentFilters.sort = sortSelect.value;
            updateURL();
            loadAnnouncements(true);
        }

        // Update event listeners to use enhanced functions
        function setupEventListeners() {
            searchInput.addEventListener('input', debounce(handleSearchEnhanced, 300));
            categoryFilter.addEventListener('change', handleFilterEnhanced);
            sortSelect.addEventListener('change', handleSortEnhanced);
            loadMoreBtn.addEventListener('click', () => loadAnnouncements(false));
            
            // Enter key support for search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    handleSearchEnhanced();
                }
            });
        }

        // Add keyboard navigation support
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
            
            // Escape to clear search
            if (e.key === 'Escape' && document.activeElement === searchInput) {
                searchInput.value = '';
                handleSearchEnhanced();
                searchInput.blur();
            }
        });

        // Add touch/swipe support for mobile
        let touchStartX = 0;
        let touchStartY = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', function(e) {
            if (!e.changedTouches) return;
            
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const diffX = touchStartX - touchEndX;
            const diffY = touchStartY - touchEndY;
            
            // Horizontal swipe detected
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    // Swipe left - could trigger some action
                } else {
                    // Swipe right - could trigger some action
                }
            }
        });

        // Performance optimization: Intersection Observer for lazy loading
        const lazyImageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        lazyImageObserver.unobserve(img);
                    }
                }
            });
        });

        // Observe all lazy images
        document.addEventListener('DOMNodeInserted', function(e) {
            if (e.target.tagName === 'IMG' && e.target.classList.contains('lazy')) {
                lazyImageObserver.observe(e.target);
            }
        });