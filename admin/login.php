<?php
include('../config.php');

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['message'] = 'Email and password are required';
    } else {
        $stmt = $connect->prepare("SELECT admin_id, admin_name, admin_email, staff_id, admin_password, is_active FROM admins WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            if ($admin['is_active'] != 1) {
                $_SESSION['message'] = 'Your account is inactive';
            } else {
                if (password_verify($password, $admin['admin_password'])) {
                    $_SESSION['admin_id']    = $admin['admin_id'];
                    $_SESSION['admin_name']  = $admin['admin_name'];
                    $_SESSION['admin_email'] = $admin['admin_email'];
                    $_SESSION['staff_id']    = $admin['staff_id'];

                    session_regenerate_id(true);

                    // Use relative path instead of absolute URL
                    header("Location: http://localhost/PCC-PROJECT/admin/dashboard.php");
                    exit();
                } else {
                    $_SESSION['message'] = 'Invalid email or password';
                }
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
    <title>Admin Login - PCC Hub</title>
    <link rel="stylesheet" href="../assests/css/demo1/login.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <h1>Admin Portal</h1>
            <p>Sign in to access the administrative dashboard</p>
            <a href="../homepage.php" class="back-btn">← Back to Home</a>
        </div>
        
        <div class="auth-right">
            <div class="form-header">
                <h2>Admin & Staff Login</h2>
                <p>Enter your credentials to access the admin panel</p>
            </div>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="error-message" style="display: block; text-align: center; margin-bottom: 1rem; color: red; background: #ffe6e6; padding: 10px; border-radius: 5px;">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <!-- PHP Form Submission -->
            <form action="" method="POST" id="loginForm">
                <div class="form-group">
                    <input type="email" class="form-input" name="email" id="email" placeholder="Admin/Staff Email Address" required>
                    <div class="error-message" id="email-error"></div>
                </div>

                <div class="form-group">
                    <input type="password" class="form-input" name="password" id="password" placeholder="Password" required>
                    <div class="error-message" id="password-error"></div>
                </div>

                <div class="forgot-password">
                    <a href="forgot_password.html">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="submit-btn">Sign In</button>

                <div class="form-footer">
                    Need access? <a href="register.php">Create one here</a>
                </div>
            </form>
        </div>
    </div>

   
</body>
</html>