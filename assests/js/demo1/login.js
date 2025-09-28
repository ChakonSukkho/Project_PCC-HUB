// Toggle between Student and Staff
const studentToggle = document.getElementById('student-toggle');
const staffToggle = document.getElementById('staff-toggle');
const userTypeInput = document.getElementById('user_type');
const emailInput = document.getElementById('email');

studentToggle.addEventListener('click', () => {
    studentToggle.classList.add('active');
    staffToggle.classList.remove('active');
    userTypeInput.value = 'student';
    emailInput.placeholder = 'Email Address';
    emailInput.type = 'email';
});

staffToggle.addEventListener('click', () => {
    staffToggle.classList.add('active');
    studentToggle.classList.remove('active');
    userTypeInput.value = 'staff';
    emailInput.placeholder = 'Staff ID';
    emailInput.type = 'text';
});

// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
            
        // Reset error messages
        document.querySelectorAll('.error-message').forEach(msg => {
            msg.style.display = 'none';
        });

        let isValid = true;

        if (!email.trim()) {
            document.getElementById('email-error').textContent = 'Email is required';
            document.getElementById('email-error').style.display = 'block';
            isValid = false;
        }

        if (!password.trim()) {
            document.getElementById('password-error').textContent = 'Password is required';
            document.getElementById('password-error').style.display = 'block';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
});