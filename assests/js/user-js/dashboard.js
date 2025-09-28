function updateUserProfile(userData) {
    const profileName = document.querySelector('.profile-info h3');
    const profileProgram = document.querySelector('.profile-info p');
    const studentId = document.querySelector('.student-id');
    const welcomeMessage = document.querySelector('.welcome-section h1');

    if (userData) {
        if (profileName) profileName.textContent = userData.fullName || 'User';
        if (profileProgram) profileProgram.textContent = userData.program || 'Computer Science';
        if (studentId) studentId.textContent = `Student ID: ${userData.matric || userData.staffId || 'N/A'}`;

        if (welcomeMessage) {
            welcomeMessage.innerHTML = `Welcome back, ${userData.fullName || 'User'}! <span class="wave-emoji">👋</span>`;
        }
    }
}

// ------------------------------
// LocalStorage helpers
// ------------------------------
function getUserData() {
    const data = localStorage.getItem("userData");
    return data ? JSON.parse(data) : null;
}

function saveUserData(userData) {
    localStorage.setItem("userData", JSON.stringify(userData));
    console.log("User data saved locally");
}

// ------------------------------
// Logout button
// ------------------------------
function addLogoutButton() {
    const logoutBtn = document.createElement('button');
    logoutBtn.innerHTML = '<span class="nav-icon">🚪</span> Logout';
    logoutBtn.className = 'nav-link logout-btn';
    logoutBtn.style.cssText = `
        margin-top: 20px;
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
        cursor: pointer;
        width: 100%;
    `;

    logoutBtn.addEventListener('click', () => {
        localStorage.removeItem("userData");
        localStorage.removeItem("loggedIn");
        window.location.href = '../login/index.html'; // adjust as needed
    });

    const navMenu = document.querySelector('.nav-menu');
    if (navMenu) {
        const logoutItem = document.createElement('li');
        logoutItem.className = 'nav-item';
        logoutItem.appendChild(logoutBtn);
        navMenu.appendChild(logoutItem);
    }
}

// ------------------------------
// Main auth check
// ------------------------------
document.addEventListener("DOMContentLoaded", () => {
    const loggedIn = localStorage.getItem("loggedIn");

    if (loggedIn === "true") {
        console.log("User is signed in locally");

        const userData = getUserData();
        if (userData) {
            updateUserProfile(userData);
        } else {
            updateUserProfile({
                fullName: "Guest User",
                program: "Computer Science",
                matric: "N/A"
            });
        }

        addLogoutButton();
    } else {
        console.log("No user signed in, redirecting to login");
        window.location.href = '../login/index.html'; // adjust path
    }
});

// ------------------------------
// UI animations + effects
// ------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Card hover
    const cards = document.querySelectorAll('.access-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Ripple on nav links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.classList.contains('logout-btn')) return;

            e.preventDefault();
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

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
            setTimeout(() => ripple.remove(), 600);

            const targetUrl = this.getAttribute("href");
            if (targetUrl && targetUrl !== "#") {
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 200);
            }
        });
    });

    // Parallax decorations
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

    // Announcements animation
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.announcement-item').forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'all 0.6s ease';
        observer.observe(item);
    });
});

// Ripple animation style
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
