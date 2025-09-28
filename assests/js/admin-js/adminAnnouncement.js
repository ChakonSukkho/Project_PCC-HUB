        // Elements

const addBtn = document.getElementById("add-announcement-btn");
const modal = document.getElementById("add-announcement-modal");
const closeBtn = document.querySelector(".close-btn");
const form = document.getElementById("announcement-form");

// ✅ Handle Form Submit - Save to PHP/MySQL
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
      window.location.reload(); // reload page after submit
    } else {
      alert("❌ " + data.message);
    }
  })
  .catch(err => console.error(err));
});


// TEMP: show button (later restrict to admin only)
addBtn.style.display = "block";

// Open modal
addBtn.addEventListener("click", () => {
  modal.style.display = "flex";
});

// Close modal
closeBtn.addEventListener("click", () => {
  modal.style.display = "none";
});

// Close on outside click
window.addEventListener("click", (e) => {
  if (e.target === modal) modal.style.display = "none";
});

// Submit form
/*
form.addEventListener("submit", (e) => {
  e.preventDefault();

  const newAnnouncement = {
    id: "ANN" + String(allAnnouncements.length + 1).padStart(3, "0"),
    title: document.getElementById("title").value,
    category: document.getElementById("category").value,
    priority: document.getElementById("priority").value,
    content: document.getElementById("content").value,
    time: "Just now",
    timestamp: new Date()
  };

  // Add to announcements array (simulate saving)
  allAnnouncements.unshift(newAnnouncement);
  filteredAnnouncements.unshift(newAnnouncement);

  // Reset and close
  form.reset();
  modal.style.display = "none";

  // Re-render announcements
  resetDisplay();
  loadAnnouncements();
});
*/


// Sample announcements data
const allAnnouncements = [
    
];

let displayedAnnouncements = [];
let currentPage = 0;
const itemsPerPage = 6;
let filteredAnnouncements = [...allAnnouncements];

// DOM Elements
const announcementsGrid = document.getElementById('announcementsGrid');
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const sortSelect = document.getElementById('sortBy');
const loadMoreBtn = document.getElementById('loadMoreBtn');

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    loadAnnouncements();
    setupEventListeners();
    addInteractiveEffects();
});

// Navigation effects (same as dashboard)
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') {
                e.preventDefault();
                return;
            }
            
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
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

// Load announcements
function loadAnnouncements() {
    const startIndex = currentPage * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, filteredAnnouncements.length);
    
    for (let i = startIndex; i < endIndex; i++) {
        const announcement = filteredAnnouncements[i];
        if (announcement) {
            displayedAnnouncements.push(announcement);
            renderAnnouncement(announcement);
        }
    }
    
    currentPage++;
    
    // Hide load more button if no more announcements
    if (endIndex >= filteredAnnouncements.length) {
        loadMoreBtn.style.display = 'none';
    } else {
        loadMoreBtn.style.display = 'block';
    }
}

function renderAnnouncement(announcement) {
    const card = document.createElement('div');
    card.className = `announcement-card ${announcement.category} fade-in`;
    card.dataset.id = announcement.id;
    
    card.innerHTML = `
        <div class="announcement-header">
            <div class="announcement-meta">
                <span class="announcement-type ${announcement.category}">${announcement.category}</span>
                ${announcement.priority === 'urgent' ? '<span class="announcement-priority">URGENT</span>' : ''}
            </div>
            <div class="announcement-time">${announcement.time}</div>
        </div>
        <h3 class="announcement-title">${announcement.title}</h3>
        <p class="announcement-content">${announcement.content}</p>
        <div class="announcement-actions">
            <a href="#" class="read-more-btn">Read More →</a>
            <span class="announcement-id">${announcement.id}</span>
            <button class="delete-btn" data-id="${announcement.id}">Delete</button>
        </div>
    `;
    
    announcementsGrid.appendChild(card);

    // Add delete functionality
    card.querySelector(".delete-btn").addEventListener("click", () => {
        deleteAnnouncement(announcement.id);
    });
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
    loadMoreBtn.addEventListener('click', loadAnnouncements);
}

// Search handler
function handleSearch() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    resetDisplay();
    
    if (searchTerm === '') {
        filteredAnnouncements = [...allAnnouncements];
    } else {
        filteredAnnouncements = allAnnouncements.filter(announcement =>
            announcement.title.toLowerCase().includes(searchTerm) ||
            announcement.content.toLowerCase().includes(searchTerm)
        );
    }
    
    applySortAndFilter();
}

// Filter handler
function handleFilter() {
    const selectedCategory = categoryFilter.value;
    resetDisplay();
    
    if (selectedCategory === 'all') {
        filteredAnnouncements = [...allAnnouncements];
    } else {
        filteredAnnouncements = allAnnouncements.filter(announcement =>
            announcement.category === selectedCategory
        );
    }
    
    // Apply current search term if exists
    const searchTerm = searchInput.value.toLowerCase().trim();
    if (searchTerm !== '') {
        filteredAnnouncements = filteredAnnouncements.filter(announcement =>
            announcement.title.toLowerCase().includes(searchTerm) ||
            announcement.content.toLowerCase().includes(searchTerm)
        );
    }
    
    applySortAndFilter();
}

// Sort handler
function handleSort() {
    const sortBy = sortSelect.value;
    
    filteredAnnouncements.sort((a, b) => {
        switch (sortBy) {
            case 'newest':
                return b.timestamp - a.timestamp;
            case 'oldest':
                return a.timestamp - b.timestamp;
            case 'priority':
                // Urgent first, then by newest
                if (a.priority === 'urgent' && b.priority !== 'urgent') return -1;
                if (b.priority === 'urgent' && a.priority !== 'urgent') return 1;
                return b.timestamp - a.timestamp;
            default:
                return b.timestamp - a.timestamp;
        }
    });
    
    resetDisplay();
    loadAnnouncements();
}

// Apply current sort and filter
function applySortAndFilter() {
    handleSort();
}

// Reset display
function resetDisplay() {
    announcementsGrid.innerHTML = '';
    displayedAnnouncements = [];
    currentPage = 0;
    
    if (filteredAnnouncements.length === 0) {
        showEmptyState();
    }
}

// Show empty state
function showEmptyState() {
    announcementsGrid.innerHTML = `
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <p>No announcements found matching your criteria.</p>
        </div>
    `;
    loadMoreBtn.style.display = 'none';
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
}

// Initialize page load
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

/*
if (currentUser && currentUser.role === "admin") {
  addBtn.style.display = "block";
}
  */

document.querySelectorAll(".delete-btn").forEach(btn => {
  btn.addEventListener("click", function() {
    const id = this.getAttribute("data-id");
    deleteAnnouncement(id);
  });
});

function deleteAnnouncement(id) {
  if (!confirm("Are you sure you want to delete this announcement?")) return;

  fetch("delete_announcement.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("🗑️ Deleted successfully!");
      window.location.reload(); // reload page after submit
    } else {
      alert("❌ Delete failed!");
    }
  })
  .catch(err => console.error(err));
}
