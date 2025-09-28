<?php
// enroll_program.php
include_once 'config.php';
include_once 'header.php';
include_once 'sidebar.php';
?>

<div class="main-content">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <h1>Enroll in Programs 🎓</h1>
            <p>Discover and enroll in high-quality programs across fitness, study sessions, and workshops. Track your progress and earn certificates upon completion.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-number" id="availablePrograms">6</div>
            <div class="stat-label">Available Programs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number" id="enrolledPrograms">2</div>
            <div class="stat-label">Enrolled Programs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📈</div>
            <div class="stat-number" id="completedPrograms">1</div>
            <div class="stat-label">Completed Programs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-number" id="certificatesEarned">0</div>
            <div class="stat-label">Certificates Earned</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-navigation">
        <button class="tab-btn active" data-tab="browse">🔍 Browse Programs</button>
        <button class="tab-btn" data-tab="enrollments">📝 My Enrollments (<span id="enrollmentCount">2</span>)</button>
    </div>

    <!-- Browse Programs Tab -->
    <div class="tab-content active" id="browse-tab">
        <!-- Search and Filter -->
        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search programs...">
                <span class="search-icon">🔍</span>
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-category="all">All</button>
                <button class="filter-btn" data-category="fitness">Fitness</button>
                <button class="filter-btn" data-category="study">Study</button>
                <button class="filter-btn" data-category="workshop">Workshop</button>
            </div>
        </div>

        <!-- Programs Grid -->
        <div class="programs-grid" id="programsGrid">
            <!-- Programs will be loaded here by JavaScript -->
        </div>
    </div>

    <!-- My Enrollments Tab -->
    <div class="tab-content" id="enrollments-tab">
        <div class="enrollments-container" id="enrollmentsList">
            <!-- Enrollments will be loaded here by JavaScript -->
        </div>
    </div>
</div>

<style>
/* Main Layout */
.main-content {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

/* Header Section */
.header-section {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.header-content h1 {
    color: white;
    font-size: 2rem;
    margin-bottom: 10px;
    font-weight: 600;
}

.header-content p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-weight: 500;
}

/* Tab Navigation */
.tab-navigation {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
}

.tab-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.tab-btn:hover, .tab-btn.active {
    background: rgba(255, 255, 255, 0.9);
    color: #667eea;
    transform: translateY(-2px);
}

/* Tab Content */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Search and Filter Section */
.search-filter-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.search-box {
    position: relative;
    margin-bottom: 20px;
}

.search-box input {
    width: 100%;
    padding: 15px 50px 15px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #667eea;
}

.search-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
}

.filter-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    background: #f0f0f0;
    color: #666;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-btn:hover, .filter-btn.active {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

/* Programs Grid */
.programs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.program-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.program-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
}

.program-image {
    width: 100%;
    height: 200px;
    border-radius: 12px;
    margin-bottom: 20px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
}

.program-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.program-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #666;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.program-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
}

.program-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    font-size: 0.9rem;
    color: #666;
}

.program-rating {
    color: #ffa726;
}

.program-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 12px 24px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-secondary:hover {
    background: #667eea;
    color: white;
}

/* Enrollments Container */
.enrollments-container {
    display: grid;
    gap: 20px;
}

.enrollment-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.enrollment-card:hover {
    transform: translateY(-3px);
}

.enrollment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.enrollment-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.enrollment-meta {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.progress-section {
    margin-bottom: 20px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #666;
}

.progress-bar {
    height: 10px;
    background: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4caf50, #8bc34a);
    border-radius: 5px;
    transition: width 0.3s ease;
}

.enrollment-actions {
    display: flex;
    gap: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-content {
        padding: 15px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .programs-grid {
        grid-template-columns: 1fr;
    }
    
    .search-filter-section {
        padding: 20px;
    }
    
    .filter-buttons {
        justify-content: center;
    }
    
    .tab-navigation {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .program-actions {
        flex-direction: column;
    }
    
    .enrollment-actions {
        flex-direction: column;
    }
}

/* Loading Animation */
.loading {
    text-align: center;
    padding: 40px;
    color: white;
    font-size: 1.1rem;
}

.loading::after {
    content: '⏳';
    margin-left: 10px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
// enroll_program.js - Enhanced version matching your design

const api = {
    getPrograms: 'api/get_programs.php',
    enroll: 'api/enroll.php',
    complete: 'api/complete.php',
    myEnrollments: 'api/my_enrollments.php'
};

let currentCategory = 'all';
let currentTab = 'browse';

document.addEventListener('DOMContentLoaded', () => {
    console.log('Enroll Program page loaded');
    
    // Initialize
    loadPrograms();
    loadMyEnrollments();
    loadStats();
    
    // Setup event listeners
    setupEventListeners();
});

function setupEventListeners() {
    // Search input
    document.getElementById('searchInput').addEventListener('input', debounce(loadPrograms, 300));
    
    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            currentCategory = e.target.dataset.category;
            loadPrograms();
        });
    });
    
    // Tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const tabName = e.target.dataset.tab;
            switchTab(tabName);
        });
    });
}

function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`${tabName}-tab`).classList.add('active');
    
    currentTab = tabName;
}

function debounce(fn, delay) {
    let t;
    return function() {
        clearTimeout(t);
        t = setTimeout(() => fn(), delay);
    };
}

async function loadStats() {
    try {
        // Load available programs count
        const programsRes = await fetch(`${api.getPrograms}?category=all&q=`);
        const programs = await programsRes.json();
        document.getElementById('availablePrograms').textContent = programs.length;
        
        // Load enrollments for other stats
        const enrollmentsRes = await fetch(api.myEnrollments);
        const enrollments = await enrollmentsRes.json();
        
        const enrolled = enrollments.length;
        const completed = enrollments.filter(e => e.progress_percent >= 100).length;
        
        document.getElementById('enrolledPrograms').textContent = enrolled;
        document.getElementById('completedPrograms').textContent = completed;
        document.getElementById('certificatesEarned').textContent = completed; // Assuming certificates = completed
        document.getElementById('enrollmentCount').textContent = enrolled;
        
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadPrograms() {
    try {
        const container = document.getElementById('programsGrid');
        container.innerHTML = '<div class="loading">Loading programs...</div>';
        
        const query = document.getElementById('searchInput').value.trim();
        const res = await fetch(`${api.getPrograms}?category=${encodeURIComponent(currentCategory)}&q=${encodeURIComponent(query)}`);
        const programs = await res.json();
        
        renderPrograms(programs);
    } catch (error) {
        console.error('Error loading programs:', error);
        document.getElementById('programsGrid').innerHTML = '<p style="color: white; text-align: center;">Error loading programs</p>';
    }
}

function renderPrograms(programs) {
    const container = document.getElementById('programsGrid');
    
    if (!programs.length) {
        container.innerHTML = '<p style="color: white; text-align: center; grid-column: 1/-1;">No programs found matching your criteria.</p>';
        return;
    }
    
    container.innerHTML = '';
    programs.forEach(program => {
        const card = createProgramCard(program);
        container.appendChild(card);
    });
}

function createProgramCard(program) {
    const card = document.createElement('div');
    card.className = 'program-card';
    
    // Get program icon based on category
    const categoryIcons = {
        fitness: '💪',
        study: '📚',
        workshop: '🛠️'
    };
    
    const icon = categoryIcons[program.category] || '📋';
    const difficulty = program.difficulty || 'Beginner';
    
    card.innerHTML = `
        <div class="program-image">
            ${icon}
        </div>
        <div class="program-title">${escapeHtml(program.name)}</div>
        <div class="program-meta">
            <div class="meta-item">
                <span>📂</span>
                <span>${escapeHtml(program.category)}</span>
            </div>
            <div class="meta-item">
                <span>⏱️</span>
                <span>${escapeHtml(program.duration_text || 'N/A')}</span>
            </div>
            <div class="meta-item">
                <span>📊</span>
                <span>${escapeHtml(difficulty)}</span>
            </div>
        </div>
        <div class="program-description">
            ${escapeHtml(program.description || 'No description available.')}
        </div>
        <div class="program-stats">
            <div class="program-rating">
                ⭐ 4.8 (324 reviews)
            </div>
            <div class="enrolled-count">
                2,847 enrolled
            </div>
        </div>
        <div class="program-actions">
            <button class="btn btn-primary" onclick="enrollInProgram(${program.id})">
                Enroll Now
            </button>
            <button class="btn btn-secondary" onclick="showProgramDetails(${program.id})">
                Details
            </button>
        </div>
    `;
    
    return card;
}

async function loadMyEnrollments() {
    try {
        const container = document.getElementById('enrollmentsList');
        container.innerHTML = '<div class="loading">Loading enrollments...</div>';
        
        const res = await fetch(api.myEnrollments);
        const enrollments = await res.json();
        
        renderEnrollments(enrollments);
    } catch (error) {
        console.error('Error loading enrollments:', error);
        document.getElementById('enrollmentsList').innerHTML = '<p style="color: white; text-align: center;">Error loading enrollments</p>';
    }
}

function renderEnrollments(enrollments) {
    const container = document.getElementById('enrollmentsList');
    
    if (!enrollments.length) {
        container.innerHTML = '<p style="color: white; text-align: center;">You haven\'t enrolled in any programs yet. Start by browsing available programs!</p>';
        return;
    }
    
    container.innerHTML = '';
    enrollments.forEach(enrollment => {
        const card = createEnrollmentCard(enrollment);
        container.appendChild(card);
    });
}

function createEnrollmentCard(enrollment) {
    const card = document.createElement('div');
    card.className = 'enrollment-card';
    
    const progress = enrollment.progress_percent || 0;
    const isCompleted = progress >= 100;
    
    card.innerHTML = `
        <div class="enrollment-header">
            <div>
                <div class="enrollment-title">${escapeHtml(enrollment.name)}</div>
                <div class="enrollment-meta">
                    ${escapeHtml(enrollment.category)} • ${escapeHtml(enrollment.duration_text || 'N/A')}
                </div>
            </div>
            <div style="font-size: 2rem;">
                ${enrollment.category === 'fitness' ? '💪' : enrollment.category === 'study' ? '📚' : '🛠️'}
            </div>
        </div>
        
        <div class="progress-section">
            <div class="progress-label">
                <span>Progress</span>
                <span>${progress}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: ${progress}%"></div>
            </div>
        </div>
        
        <div class="enrollment-actions">
            ${!isCompleted ? `
                <button class="btn btn-primary" onclick="markAsComplete(${enrollment.enroll_id})">
                    Mark as Completed
                </button>
                <button class="btn btn-secondary" onclick="continueProgram(${enrollment.enroll_id})">
                    Continue Learning
                </button>
            ` : `
                <button class="btn btn-primary" onclick="viewCertificate(${enrollment.enroll_id})">
                    View Certificate
                </button>
                <button class="btn btn-secondary" onclick="reviewProgram(${enrollment.id})">
                    Review Program
                </button>
            `}
        </div>
    `;
    
    return card;
}

async function enrollInProgram(programId) {
    if (!confirm('Are you sure you want to enroll in this program?')) {
        return;
    }
    
    try {
        const res = await fetch(api.enroll, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ program_id: programId })
        });
        
        const data = await res.json();
        
        if (data.success) {
            showNotification('Successfully enrolled! 🎉', 'success');
            loadMyEnrollments();
            loadStats();
            // Switch to enrollments tab
            switchTab('enrollments');
        } else {
            showNotification(data.message || 'Enrollment failed', 'error');
        }
    } catch (error) {
        console.error('Error enrolling:', error);
        showNotification('An error occurred while enrolling', 'error');
    }
}

async function markAsComplete(enrollId) {
    if (!confirm('Mark this program as completed?')) {
        return;
    }
    
    try {
        const res = await fetch(api.complete, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ enroll_id: enrollId })
        });
        
        const data = await res.json();
        
        if (data.success) {
            showNotification('Program completed! Certificate generated 🏆', 'success');
            loadMyEnrollments();
            loadStats();
        } else {
            showNotification(data.message || 'Failed to mark as completed', 'error');
        }
    } catch (error) {
        console.error('Error completing program:', error);
        showNotification('An error occurred', 'error');
    }
}

function showProgramDetails(programId) {
    // Placeholder - you can implement modal or redirect
    window.location.href = `program_detail.php?id=${programId}`;
}

function continueProgram(enrollId) {
    // Placeholder - redirect to learning interface
    window.location.href = `program_learning.php?enroll_id=${enrollId}`;
}

function viewCertificate(enrollId) {
    window.open(`certificate.php?enroll_id=${enrollId}`, '_blank');
}

function reviewProgram(programId) {
    // Placeholder - open review modal or redirect
    console.log('Review program:', programId);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add styles for notification
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        .notification-success { border-left: 4px solid #4caf50; }
        .notification-error { border-left: 4px solid #f44336; }
        .notification-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        .notification button {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    
    if (!document.getElementById('notification-styles')) {
        style.id = 'notification-styles';
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>

<?php include_once 'footer.php'; ?>