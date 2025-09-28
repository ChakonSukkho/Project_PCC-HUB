<?php
include('../config.php');

if(isset($_POST['register']))
{
    // Get form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $matric_number = $_POST['matric_number'];
    $phone = $_POST['phone'];
    
    // Validation
    $errors = array();
    
    // Check if passwords match
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check password length
    if(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    // Check if email already exists
    $check_email = "SELECT user_email FROM users WHERE user_email = '$email'";
    $result_check = $connect->query($check_email);
    if($result_check->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    
    // Check if matric number already exists
    if(!empty($matric_number)) {
        $check_matric = "SELECT * FROM users WHERE matric_number = '$matric_number'";
        $result_matric = $connect->query($check_matric);
        if($result_matric->num_rows > 0) {
            $errors[] = "Matric number already exists";
        }
    }
    
    // If no errors, proceed with registration
    if(empty($errors)) {
        // Hash the password
        $hashed_password = md5($password);
        
        // Insert new user
        $sql_register = "INSERT INTO users (user_name, user_email, user_password, matric_number, user_phone) 
                         VALUES ('$full_name', '$email', '$hashed_password', '$matric_number', '$phone')";
        
        if($connect->query($sql_register)) {
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
    <title>Student Registration - PCC Hub</title>
    <link rel="stylesheet" href="../assests/css/demo1/register.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <h1>Student Portal</h1>
            <p>Create your student account</p>
            <a href="../homepage.php" class="back-btn">← Back to Home</a>
        </div>
        
        <div class="auth-right">
            <div class="form-header">
                <h2>Student Registration</h2>
                <p>Fill in your details to create an account</p>
            </div>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="error-message" style="display: block; text-align: center; margin-bottom: 1rem;">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form action="" method="POST" id="registerForm">
                <div class="form-group">
                    <input type="text" class="form-input" name="full_name" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <input type="email" class="form-input" name="email" placeholder="Email Address" required>
                </div>

                <div class="form-group">
                    <input type="text" class="form-input" name="matric_number" placeholder="Matric Number" required>
                </div>

                <div class="form-group">
                    <input type="text" class="form-input" name="phone" placeholder="Phone Number" required>
                </div>

                <div class="form-group">
                    <input type="password" class="form-input" name="password" placeholder="Password (min 6 characters)" required>
                </div>

                <div class="form-group">
                    <input type="password" class="form-input" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <button type="submit" name="register" class="submit-btn">Create Account</button>

                <div class="form-footer">
                    Already have an account? <a href="login.php">Sign in here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
