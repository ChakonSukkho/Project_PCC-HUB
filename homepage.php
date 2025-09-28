<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC Hub - Student Portal</title>
    <link rel="stylesheet" href="assests/css/demo1/homepage.css">
</head>
<body>
    <div class="main-page">
        <div class="container">
            <div class="hero-section">
                <h1 class="hero-title">Welcome to <strong>PPC Hub</strong></h1>
                <p class="hero-subtitle">
                    Your friendly student portal for academic excellence, community connection, 
                    and seamless campus life at Politeknik Sultan Mizan Zainal Abidin.
                </p>
                
                <!-- Add this to your existing HTML where the Get started button is -->
                <button class="btn btn-secondary" onclick="openLoginModal()">Get Start</button>

            <!-- Login Modal -->
            <div id="loginModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeLoginModal()">&times;</span>
                    <h3>Choose Login Type</h3>
                    <div class="login-options">
                        <a href="user/login.php" class="login-option">
                            <div class="login-icon">👨‍🎓</div>
                            <div class="login-text">
                                <h4>Student/Staff Login</h4>
                                <p>Access student portal and activities</p>
                            </div>
                        </a>
                        <a href="admin/login.php" class="login-option">
                            <div class="login-icon">👨‍💼</div>
                            <div class="login-text">
                                <h4>Admin Login</h4>
                                <p>Manage system and users</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div> 
                
                <div class="features">
                    <div class="feature-card">
                        <div class="feature-icon">📚</div>
                        <h3>Academic Excellence</h3>
                        <p>Track your courses, grades, and academic progress</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">👥</div>
                        <h3>Community</h3>
                        <p>Connect and collaborate with your peers</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">📖</div>
                        <h3>Resources</h3>
                        <p>Access study materials and campus information</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">👨‍💼</div>
                        <h3>Personal</h3>
                        <p>Manage your personal information and settings</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assests/js/demo1/homepage.js"></script>
</body>
</html>