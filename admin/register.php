<?php
include('../config.php');

if(isset($_POST['register']))
{
    // Get form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $staff_id = trim($_POST['staff_id']);
    $admin_phone = trim($_POST['admin_phone']); // <-- new field
    
    // Validation
    $errors = array();
    
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    // Check email uniqueness
    $check_email = $connect->prepare("SELECT admin_email FROM admins WHERE admin_email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result_check = $check_email->get_result();
    if($result_check->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    
    // Check staff ID uniqueness
    if(!empty($staff_id)) {
        $check_staff = $connect->prepare("SELECT staff_id FROM admins WHERE staff_id = ?");
        $check_staff->bind_param("s", $staff_id);
        $check_staff->execute();
        $result_staff = $check_staff->get_result();
        if($result_staff->num_rows > 0) {
            $errors[] = "Staff ID already exists";
        }
    }
    
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // secure hash

        // Insert into database
        $sql_register = $connect->prepare("INSERT INTO admins (admin_name, admin_email, admin_password, staff_id, admin_phone) VALUES (?, ?, ?, ?, ?)");
        $sql_register->bind_param("sssss", $full_name, $email, $hashed_password, $staff_id, $admin_phone);

        if($sql_register->execute()) {
            $_SESSION['success_message'] = 'Registration successful! You can now log in.';
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['message'] = 'Registration failed. Please try again.';
        }
    } else {
        $_SESSION['message'] = implode(', ', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - PCC Hub</title>
    <link rel="stylesheet" href="../assests/css/demo1/register.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <h1>Admin Portal</h1>
            <p>Create your admin account to manage the system</p>
            <a href="../homepage.php" class="back-btn">← Back to Home</a>
        </div>
        
        <div class="auth-right">
            <div class="form-header">
                <h2>Admin Registration</h2>
                <p>Create your administrative account</p>
            </div>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="error-message" style="display: block; text-align: center; margin-bottom: 1rem;">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="registerForm">
                <div class="form-group">
                    <input type="text" class="form-input" name="full_name" id="full_name" placeholder="Full Name" required>
                    <div class="error-message" id="name-error"></div>
                </div>

                <div class="form-group">
                    <input type="email" class="form-input" name="email" id="email" placeholder="Official Email Address" required>
                    <div class="error-message" id="email-error"></div>
                </div>

                <div class="form-group">
                    <input type="text" class="form-input" name="staff_id" id="staff_id" placeholder="Staff ID" required>
                    <div class="error-message" id="staff-error"></div>
                </div>

                <div class="form-group">
                    <input type="text" class="form-input" name="admin_phone" id="admin_phone" placeholder="Phone Number">
                    <div class="error-message" id="phone-error"></div>
                </div>

                <div class="form-group">
                    <input type="password" class="form-input" name="password" id="password" placeholder="Password (min 6 characters)" required minlength="6">
                    <div class="error-message" id="password-error"></div>
                </div>

                <div class="form-group">
                    <input type="password" class="form-input" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <div class="error-message" id="confirm-password-error"></div>
                </div>

                <button type="submit" name="register" class="submit-btn">Create Account</button>

                <div class="form-footer">
                    Already have an account? <a href="login.php">Sign in here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            document.querySelectorAll('.error-message').forEach(msg => msg.style.display = 'none');

            let isValid = true;
            const fullName = document.getElementById('full_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const staffId = document.getElementById('staff_id').value.trim();
            const phone = document.getElementById('admin_phone').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if(!fullName){ document.getElementById('name-error').textContent='Full name is required'; document.getElementById('name-error').style.display='block'; isValid=false;}
            if(!email){ document.getElementById('email-error').textContent='Email is required'; document.getElementById('email-error').style.display='block'; isValid=false;}
            else if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){ document.getElementById('email-error').textContent='Enter a valid email'; document.getElementById('email-error').style.display='block'; isValid=false;}
            if(!staffId){ document.getElementById('staff-error').textContent='Staff ID is required'; document.getElementById('staff-error').style.display='block'; isValid=false;}
            if(phone && !/^\d{8,15}$/.test(phone)){ document.getElementById('phone-error').textContent='Enter a valid phone number (8-15 digits)'; document.getElementById('phone-error').style.display='block'; isValid=false;}
            if(!password){ document.getElementById('password-error').textContent='Password is required'; document.getElementById('password-error').style.display='block'; isValid=false;}
            else if(password.length<6){ document.getElementById('password-error').textContent='Password must be at least 6 characters'; document.getElementById('password-error').style.display='block'; isValid=false;}
            if(!confirmPassword){ document.getElementById('confirm-password-error').textContent='Confirm password is required'; document.getElementById('confirm-password-error').style.display='block'; isValid=false;}
            else if(password!==confirmPassword){ document.getElementById('confirm-password-error').textContent='Passwords do not match'; document.getElementById('confirm-password-error').style.display='block'; isValid=false;}

            if(!isValid) e.preventDefault();
        });
    </script>
</body>
</html>
