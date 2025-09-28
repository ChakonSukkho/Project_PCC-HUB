 // Toggle between Student and Staff
        const studentToggle = document.getElementById('student-toggle');
        const staffToggle = document.getElementById('staff-toggle');
        const userTypeInput = document.getElementById('user_type');
        const studentField = document.getElementById('student-field');
        const staffField = document.getElementById('staff-field');
        const matricInput = document.getElementById('matric_number');
        const staffIdInput = document.getElementById('staff_id');

        studentToggle.addEventListener('click', () => {
            studentToggle.classList.add('active');
            staffToggle.classList.remove('active');
            userTypeInput.value = 'student';
            
            studentField.classList.remove('hidden');
            staffField.classList.add('hidden');
            matricInput.setAttribute('required', '');
            staffIdInput.removeAttribute('required');
        });

        staffToggle.addEventListener('click', () => {
            staffToggle.classList.add('active');
            studentToggle.classList.remove('active');
            userTypeInput.value = 'staff';
            
            studentField.classList.add('hidden');
            staffField.classList.remove('hidden');
            matricInput.removeAttribute('required');
            staffIdInput.setAttribute('required', '');
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            // Reset error messages
            document.querySelectorAll('.error-message').forEach(msg => {
                msg.style.display = 'none';
            });

            let isValid = true;

            // Get form values
            const fullName = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const userType = document.getElementById('user_type').value;
            const matricNumber = document.getElementById('matric_number').value.trim();
            const staffId = document.getElementById('staff_id').value.trim();

            // Validate full name
            if (!fullName) {
                document.getElementById('name-error').textContent = 'Full name is required';
                document.getElementById('name-error').style.display = 'block';
                isValid = false;
            }

            // Validate email
            if (!email) {
                document.getElementById('email-error').textContent = 'Email is required';
                document.getElementById('email-error').style.display = 'block';
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('email-error').textContent = 'Please enter a valid email address';
                document.getElementById('email-error').style.display = 'block';
                isValid = false;
            }

            // Validate matric number or staff ID
            if (userType === 'student' && !matricNumber) {
                document.getElementById('matric-error').textContent = 'Matric number is required';
                document.getElementById('matric-error').style.display = 'block';
                isValid = false;
            } else if (userType === 'staff' && !staffId) {
                document.getElementById('staff-error').textContent = 'Staff ID is required';
                document.getElementById('staff-error').style.display = 'block';
                isValid = false;
            }

            // Validate password
            if (!password) {
                document.getElementById('password-error').textContent = 'Password is required';
                document.getElementById('password-error').style.display = 'block';
                isValid = false;
            } else if (password.length < 6) {
                document.getElementById('password-error').textContent = 'Password must be at least 6 characters';
                document.getElementById('password-error').style.display = 'block';
                isValid = false;
            }

            // Validate confirm password
            if (!confirmPassword) {
                document.getElementById('confirm-password-error').textContent = 'Please confirm your password';
                document.getElementById('confirm-password-error').style.display = 'block';
                isValid = false;
            } else if (password !== confirmPassword) {
                document.getElementById('confirm-password-error').textContent = 'Passwords do not match';
                document.getElementById('confirm-password-error').style.display = 'block';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });