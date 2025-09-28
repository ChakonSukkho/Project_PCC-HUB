// Main JavaScript functionality for Running Certificates page
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all interactive features
    initializeNavigation();
    initializeAnimations();
    initializeButtonHandlers();
    initializeHoverEffects();
    initializeScrollAnimations();
    animateStatsOnLoad();

    console.log('Running Certificates page loaded successfully');

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 500);
        });
    }, 5000);
});

// ==========================
// Navigation functionality
// ==========================
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            handleNavClick(e, this);
        });
    });
}

function handleNavClick(e, clickedLink) {
    e.preventDefault();

    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });

    clickedLink.classList.add('active');
    createRippleEffect(e, clickedLink);

    const targetUrl = clickedLink.getAttribute('href');
    if (targetUrl && targetUrl !== '#') {
        setTimeout(() => {
            window.location.href = targetUrl;
        }, 200);
    }
}

function createRippleEffect(e, element) {
    const ripple = document.createElement('div');
    ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    `;

    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';

    element.appendChild(ripple);

    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// ==========================
// Background animations
// ==========================
function initializeAnimations() {
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
}

// ==========================
// Button interactions
// ==========================
function initializeButtonHandlers() {
    const buttons = document.querySelectorAll('.btn');

    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleButtonClick(this);
        });
    });
}

function handleButtonClick(button) {
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
        button.style.transform = '';
    }, 150);

    const text = button.textContent.toLowerCase();
    let message = '';
    let type = 'info';

    if (text.includes('download')) {
        message = 'Certificate download started!';
        type = 'success';
    } else if (text.includes('view')) {
        message = 'Opening detailed view...';
        type = 'info';
    } else if (text.includes('register')) {
        message = 'Registration form opened!';
        type = 'success';
    }

    if (message) {
        showNotification(message, type);
    }
}

// ==========================
// Certificate actions
// ==========================
function viewCertificate(certificateId) {
    showNotification('Opening certificate viewer...', 'info');
    setTimeout(() => {
        window.open('certificate_viewer.php?id=' + certificateId, '_blank', 'width=800,height=600');
    }, 500);
}

function shareCertificate(certificateId) {
    showNotification('Generating shareable link...', 'info');
    // TODO: Implement share logic (copy link, QR code, etc.)
}

function printCertificate(certificateId) {
    showNotification('Preparing certificate for printing...', 'info');
    // TODO: Implement print logic
}

// ==========================
// Notification system
// ==========================
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');

    const colors = {
        success: '#10b981',
        info: '#3b82f6',
        warning: '#f59e0b',
        error: '#ef4444'
    };

    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 1000;
        font-weight: 600;
        transform: translateX(300px);
        transition: transform 0.3s ease;
        max-width: 300px;
        backdrop-filter: blur(10px);
    `;

    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(300px)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// ==========================
// Hover effects
// ==========================
function initializeHoverEffects() {
    const cards = document.querySelectorAll('.stat-card, .event-card, .certificate-card');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    const profile = document.querySelector('.profile');
    if (profile) {
        profile.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        profile.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
}

// ==========================
// Scroll animations
// ==========================
function initializeScrollAnimations() {
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

    const animatedElements = document.querySelectorAll('.certificate-card, .event-card');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
}

// ==========================
// Animate statistics numbers
// ==========================
function animateStatsOnLoad() {
    setTimeout(() => {
        const numbers = document.querySelectorAll('.stat-number, .stat-value, .cert-value');

        numbers.forEach(numberElement => {
            const finalValue = numberElement.textContent;
            const numericValue = parseFloat(finalValue);

            if (!isNaN(numericValue) && numericValue > 0) {
                animateNumber(numberElement, numericValue, finalValue);
            }
        });
    }, 500);
}

function animateNumber(element, targetValue, finalText) {
    let current = 0;
    const increment = targetValue / 30;
    const isFloat = finalText.includes('.');

    const timer = setInterval(() => {
        current += increment;

        if (current >= targetValue) {
            element.textContent = finalText;
            clearInterval(timer);
        } else {
            if (isFloat) {
                element.textContent = current.toFixed(1);
            } else {
                element.textContent = Math.floor(current);
            }
        }
    }, 50);
}

// ==========================
// Utility functions
// ==========================
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

// ==========================
// Add CSS keyframes
// ==========================
function addAnimationStyles() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .animate-in {
            animation: slideInUp 0.6s ease forwards;
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease forwards;
        }
    `;

    if (!document.querySelector('#animation-styles')) {
        style.id = 'animation-styles';
        document.head.appendChild(style);
    }
}

addAnimationStyles();

// ==========================
// Performance optimization
// ==========================
const debouncedMouseMove = debounce(function(e) {
    // Handle any expensive mouse move operations here
}, 16);

// ==========================
// Export functions
// ==========================
window.RunningTracker = {
    showNotification,
    animateNumber,
    createRippleEffect,
    viewCertificate,
    shareCertificate,
    printCertificate
};
