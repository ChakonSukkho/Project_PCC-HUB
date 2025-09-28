// enroll_program.js
// Fetch and render programs, handle enroll/complete actions

// API Endpoints Configuration
const api = {
    getPrograms: 'api/get_programs.php',
    enroll: 'api/enroll.php',
    complete: 'api/complete.php',
    myEnrollments: 'api/my_enrollments.php'
};

// Initialize application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('enroll_program.js loaded');
    
    // Load initial data
    loadPrograms();
    loadMyEnrollments();

    // Setup event listeners
    document.getElementById('filterCategory').addEventListener('change', loadPrograms);
    document.getElementById('searchInput').addEventListener('input', debounce(loadPrograms, 300));
});

// Utility Functions
function debounce(fn, delay) {
    let t;
    return function() {
        clearTimeout(t);
        t = setTimeout(() => fn(), delay);
    };
}

function escapeHtml(s) {
    if (!s) return '';
    return s.replace(/[&<>"]/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;'
    }[c]));
}

// Program Management Functions
async function loadPrograms() {
    const cat = document.getElementById('filterCategory').value;
    const q = document.getElementById('searchInput').value.trim();
    
    const res = await fetch(`${api.getPrograms}?category=${encodeURIComponent(cat)}&q=${encodeURIComponent(q)}`);
    const programs = await res.json();
    
    renderPrograms(programs);
}

function renderPrograms(programs) {
    const container = document.getElementById('programsGrid');
    container.innerHTML = '';
    
    if (!programs.length) {
        container.innerHTML = '<p>No programs found.</p>';
        return;
    }
    
    programs.forEach(p => {
        const card = document.createElement('div');
        card.className = 'program-card';
        card.innerHTML = `
            <h3>${escapeHtml(p.name)}</h3>
            <div class="program-meta">Type: ${escapeHtml(p.category)} • ${escapeHtml(p.duration_text)} • ${escapeHtml(p.difficulty || '')}</div>
            <p>${escapeHtml(p.description)}</p>
            <div class="program-actions">
                <button class="btn btn-primary" data-id="${p.id}" onclick="onEnroll(${p.id})">Enroll</button>
                <button class="btn btn-ghost" onclick="showDetails(${p.id})">Details</button>
            </div>
        `;
        container.appendChild(card);
    });
}

function showDetails(programId) {
    // Function placeholder for showing program details
    console.log('Show details for program:', programId);
}

// Enrollment Management Functions
async function onEnroll(programId) {
    if (!confirm('Confirm enroll to this program?')) return;
    
    const res = await fetch(api.enroll, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ program_id: programId })
    });
    
    const data = await res.json();
    
    if (data.success) {
        alert('Enrolled successfully');
        loadMyEnrollments();
    } else {
        alert(data.message || 'Enroll failed');
    }
}

async function loadMyEnrollments() {
    const res = await fetch(api.myEnrollments);
    const list = await res.json();
    
    const container = document.getElementById('enrollmentsList');
    container.innerHTML = '';
    
    if (!list.length) {
        container.innerHTML = '<p>No enrollments yet.</p>';
        return;
    }
    
    list.forEach(e => {
        const el = document.createElement('div');
        el.className = 'enrollment-item';
        el.innerHTML = `
            <strong>${escapeHtml(e.name)}</strong>
            <div class="program-meta">${escapeHtml(e.category)} • ${escapeHtml(e.duration_text)}</div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:${e.progress_percent}%"></div>
            </div>
            <div style="margin-top:8px; display:flex; gap:8px;">
                <button class="btn btn-primary" onclick="markComplete(${e.enroll_id})">Mark as Completed</button>
                <a class="btn btn-ghost" href="certificate.php?enroll_id=${e.enroll_id}">View Certificate</a>
            </div>
        `;
        container.appendChild(el);
    });
}

async function markComplete(enrollId) {
    if (!confirm('Mark this program as completed?')) return;
    
    const res = await fetch(api.complete, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ enroll_id: enrollId })
    });
    
    const data = await res.json();
    
    if (data.success) {
        alert('Program marked as completed');
        loadMyEnrollments();
    } else {
        alert(data.message || 'Failed to mark as completed');
    }
}