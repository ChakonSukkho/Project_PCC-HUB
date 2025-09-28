console.log("✅ profile.js loaded");

// Profile page functionality
document.addEventListener("DOMContentLoaded", function() {
    // Existing profile logic...
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    const profileView = document.getElementById('profileView');
    const profileEdit = document.getElementById('profileEdit');
    const personalView = document.getElementById('personalView');
    const personalEdit = document.getElementById('personalEdit');
    const actionButtons = document.getElementById('actionButtons');
    const form = document.getElementById('profileForm');
    
    let isEditMode = false;
    
    // Store original values for cancel functionality
    let originalValues = {};
    
    // Initialize
    init();
    
    function init() {
        // Store original form values
        storeOriginalValues();
        
        // Add event listeners
        editProfileBtn.addEventListener('click', toggleEditMode);
        cancelBtn.addEventListener('click', cancelEdit);
        logoutBtn.addEventListener('click', logout);
        
        // Add form validation
        addFormValidation();
        
        // Add UI enhancements
        addUIEnhancements();
    }
    
    function storeOriginalValues() {
        const inputs = document.querySelectorAll('#profileForm input');
        inputs.forEach(input => {
            originalValues[input.name] = input.value;
        });
    }
    
    function toggleEditMode() {
        isEditMode = !isEditMode;
        
        if (isEditMode) {
            enterEditMode();
        } else {
            exitEditMode();
        }
    }
    
    function enterEditMode() {
        // Hide view sections
        profileView.style.display = 'none';
        personalView.style.display = 'none';
        
        // Show edit sections
        profileEdit.style.display = 'block';
        personalEdit.style.display = 'block';
        actionButtons.style.display = 'flex';
        
        // Update button text
        editProfileBtn.innerHTML = '👁️ View Profile';
        editProfileBtn.style.background = 'rgba(79, 70, 229, 0.2)';
        
        // Add edit mode class to sections
        document.querySelectorAll('.profile-section, .personal-section').forEach(section => {
            section.classList.add('edit-mode');
        });
        
        // Focus on first input
        const firstInput = document.querySelector('#profileEdit input:not([readonly])');
        if (firstInput) {
            firstInput.focus();
        }
        
        // Animate sections
        animateEditMode();
    }
    
    function exitEditMode() {
        // Show view sections
        profileView.style.display = 'block';
        personalView.style.display = 'block';
        
        // Hide edit sections
        profileEdit.style.display = 'none';
        personalEdit.style.display = 'none';
        actionButtons.style.display = 'none';
        
        // Update button text
        editProfileBtn.innerHTML = '✏️ Edit Profile';
        editProfileBtn.style.background = 'rgba(255, 255, 255, 0.2)';
        
        // Remove edit mode class
        document.querySelectorAll('.profile-section, .personal-section').forEach(section => {
            section.classList.remove('edit-mode');
        });
    }
    
    function cancelEdit() {
        // Restore original values
        Object.keys(originalValues).forEach(name => {
            const input = document.querySelector(`input[name="${name}"]`);
            if (input) {
                input.value = originalValues[name];
            }
        });
        
        // Exit edit mode
        isEditMode = true; // Set to true so toggleEditMode will set it to false
        toggleEditMode();
        
        // Show cancel animation
        showNotification('Changes cancelled', 'info');
    }
    
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            // Clear any stored data
            localStorage.removeItem("userData");
            localStorage.removeItem("loggedIn");
            
            // Show logout animation
            showNotification('Logging out...', 'info');
            
            // Redirect after animation
            setTimeout(() => {
                window.location.href = '../homepage.php';
            }, 1500);
        }
    }
    
    function addFormValidation() {
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', validateInput);
            input.addEventListener('input', clearValidationError);
        });
        
        // Email validation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', validateEmail);
        }
        
        // Phone validation
        const phoneInput = document.getElementById('user_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', formatPhoneNumber);
        }
    }
    
    function validateInput(e) {
        const input = e.target;
        const value = input.value.trim();
        
        if (input.hasAttribute('required') && !value) {
            showInputError(input, 'This field is required');
            return false;
        }
        
        clearInputError(input);
        return true;
    }
    
    function validateEmail(e) {
        const email = e.target.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            showInputError(e.target, 'Please enter a valid email address');
            return false;
        }
        
        clearInputError(e.target);
        return true;
    }
    
    function formatPhoneNumber(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Format Malaysian phone number
        if (value.startsWith('60')) {
            value = '+60 ' + value.substring(2);
        } else if (value.startsWith('0')) {
            value = '+60 ' + value.substring(1);
        }
        
        // Add formatting
        if (value.length > 7) {
            value = value.replace(/(\+60 \d{2})(\d{3})(\d{4})/, '$1-$2 $3');
        }
        
        e.target.value = value;
    }
    
    function showInputError(input, message) {
        clearInputError(input);
        
        input.style.borderColor = '#ef4444';
        input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'input-error';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            animation: slideDown 0.3s ease;
        `;
        
        input.parentNode.appendChild(errorDiv);
    }
    
    function clearInputError(input) {
        const existingError = input.parentNode.querySelector('.input-error');
        if (existingError) {
            existingError.remove();
        }
        
        input.style.borderColor = 'rgba(79, 70, 229, 0.2)';
        input.style.boxShadow = 'none';
    }
    
    function clearValidationError(e) {
        clearInputError(e.target);
    }
    
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        const styles = {
            success: 'background: rgba(16, 185, 129, 0.9); color: white;',
            error: 'background: rgba(239, 68, 68, 0.9); color: white;',
            info: 'background: rgba(59, 130, 246, 0.9); color: white;'
        };
        
        notification.style.cssText = `
            position: fixed;
            top: 2rem;
            right: 2rem;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 500;
            z-index: 1000;
            animation: slideInRight 0.5s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            ${styles[type]}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.5s ease forwards';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }
    
    function animateEditMode() {
        const sections = document.querySelectorAll('.profile-section, .personal-section');
        sections.forEach((section, index) => {
            section.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s both`;
        });
    }
    
    function addUIEnhancements() {
        // Add ripple effect to buttons
        const buttons = document.querySelectorAll('button');
        buttons.forEach(addRippleEffect);
        
        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        
        // Add intersection observer for animations
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        // Observe sections for scroll animations
        document.querySelectorAll('.profile-section, .personal-section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'all 0.6s ease';
            observer.observe(section);
        });
    }
    
    function addRippleEffect(button) {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    }
    
    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            // Debug: Log form data before submission
            console.log('Form submission data:');
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            // Validate all inputs before submission
            const inputs = this.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateInput({ target: input })) {
                    isValid = false;
                }
            });
            
            if (!validateEmail({ target: document.getElementById('email') })) {
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fix the errors before submitting', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '💾 Saving...';
                submitBtn.disabled = true;
                
                // Re-enable after form submission (in case of validation errors)
                setTimeout(() => {
                    if (submitBtn) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 3000);
            }
        });
    }
});

// Add CSS animations
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideOutRight {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes ripple {
        to { transform: scale(4); opacity: 0; }
    }
    
    .edit-mode {
        transform: scale(1.02);
        box-shadow: 0 20px 60px rgba(79, 70, 229, 0.15) !important;
    }
    
    .form-group input:focus {
        transform: translateY(-2px);
    }
    
    .profile-section, .personal-section {
        transition: all 0.3s ease;
    }
`;

document.head.appendChild(styleSheet);



    // ✅ Profile picture upload
    const fileInput = document.getElementById("profileUpload");
    const preview = document.getElementById("profilePreview");
    const picForm = document.getElementById("profilePicForm"); // <-- form for pic only

    if (fileInput) {
        fileInput.addEventListener("change", function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview.tagName.toLowerCase() === "img") {
                        preview.src = e.target.result; // live preview
                    } else {
                        preview.outerHTML = `<img src="${e.target.result}" alt="Profile" id="profilePreview">`;
                    }
                };
                reader.readAsDataURL(file);

                // ✅ Auto-submit after selecting
                if (picForm) {
                    picForm.submit();
                }
            }
        });
    };

