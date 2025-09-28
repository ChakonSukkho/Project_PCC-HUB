<?php
include('../config.php');

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Input validation
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = 'Email and password are required';
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $connect->prepare("SELECT user_id, user_name, user_email, matric_number, user_password FROM users WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            $hashed_password = md5($password);
            
            if ($user['user_password'] === $hashed_password) {
                // Login successful
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['matric_number'] = $user['matric_number'];
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                header("Location: http://localhost/PCC-PROJECT/user/userdashboard.php");
                exit();
            } else {
                $_SESSION['message'] = 'Invalid email or password';
            }
        } else {
            $_SESSION['message'] = 'Invalid email or password';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - PCC Hub</title>
    <link rel="stylesheet" href="../assests/css/demo1/login.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <h1>Welcome Back</h1>
            <p>Sign in to access your student dashboard</p>
            <a href="../homepage.php" class="back-btn">← Back to Home</a>
        </div>
        
        <div class="auth-right">
            <div class="form-header">
                <h2>Student Login</h2>
                <p>Enter your credentials to access your student account</p>
            </div>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="error-message" style="display: block; text-align: center; margin-bottom: 1rem; color: #e74c3c; background: #fdf2f2; padding: 10px; border-radius: 4px;">
                    <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" id="loginForm">
                <div class="form-group">
                    <input type="email" class="form-input" name="email" id="email" placeholder="Student Email Address" required>
                    <div class="error-message" id="email-error"></div>
                </div>

                <div class="form-group">
                    <input type="password" class="form-input" name="password" id="password" placeholder="Password" required>
                    <div class="error-message" id="password-error"></div>
                </div>

                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="submit-btn">Sign In</button>

                <div class="form-footer">
                    Don't have an account? <a href="../user/register.php">Create one here</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/user-js/login.js"></script>
</body>
</html>