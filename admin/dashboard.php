<?php
include('../config.php');

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin info
$user_id = $_SESSION['admin_id'];
$stmt = $connect->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$admin = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC Connect - Enhanced Dashboard</title>
    <link rel="stylesheet" href="../assests/css/user-css/dashbord.css">
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <div class="container">
        <aside class="sidebar">
            <div class="logo">PCC Hub</div>
            

            <!-- Update database by Register/ Login Page -->
            <div class="profile">
                <div class="profile-pic">👨‍💻</div>
                <div class="profile-info">
                    <h3>User: <?php echo htmlspecialchars($admin['admin_name']); ?> </h3>

                    <!-- Tukar sini !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!  -->
                    <div class="student-id">Admin ID: <?php echo htmlspecialchars($admin['staff_id']); ?> </div>
                </div>
            </div>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <span class="nav-icon">📊</span>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../chatbox/index.html" class="nav-link">
                            <span class="nav-icon">💬</span>
                            Group Chat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="running.php" class="nav-link">
                            <span class="nav-icon">🏃‍♂️</span>
                            Go Fitness
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="statis.php" class="nav-link">
                            <span class="nav-icon">📈</span>
                            Student Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="announcement2.php" class="nav-link">
                            <span class="nav-icon">📢</span>
                            Announcements
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="certificate.php" class="nav-link">
                            <span class="nav-icon">🏆</span>
                            Certificate
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="profile.php" class="nav-link">
                            <span class="nav-icon">👤</span>
                            Profile
                        </a>
                    </li>
                    
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div></div>
                <a href="announcement.php" class="view-all-btn">🔔 View All</a>
            </header>

            <section class="welcome-section">
                <h1>Welcome back,  <?php echo htmlspecialchars($admin['admin_name']); ?> <span class="wave-emoji">👋</span></h1>
                <p>Ready to make today productive? Check your latest updates below.</p>
            </section>

            <section class="quick-access">
                <h2 class="section-title">Quick Access</h2>
                <div class="quick-access-grid">
                    <div class="access-card">
                        <div class="card-header">
                            <div class="card-icon group-chat">
                                💬
                            </div>
                        </div>
                        <h3 class="card-title">Group Chat</h3>
                        <p class="card-description">Connect with classmates</p>
                        <a href="../my-chat-app/index.html" class="access-link">Access Now →</a>
                    </div>

                    <div class="access-card">
                        <div class="card-header">
                            <div class="card-icon jogging">🏃‍♂️</div>
                        </div>
                        <h3 class="card-title">Go Fitness</h3>
                        <p class="card-description">Track your RUN</p>
                        <a href="http://127.0.0.1:5501/RunningPage/index.html" class="access-link">Access Now →</a>
                    </div>

                    <div class="access-card">
                        <div class="card-header">
                            <div class="card-icon statistics">
                                📊
                            </div>
                        </div>
                        <h3 class="card-title">Student Statistics</h3>
                        <p class="card-description">Check Your Merits</p>
                        <a href="#" class="access-link">Access Now →</a>
                    </div>
                </div>
            </section>

            <section class="announcements">
                <div class="announcements-header">
                    <h2 class="announcements-title">Latest Announcements</h2>
                    <a href="../Announcements/index.html" class="view-all-link">View All</a>
                </div>

                <div class="announcement-item academic">
                    <div class="announcement-meta">
                        <span class="announcement-type academic">Academic</span>
                        <span class="announcement-priority">URGENT</span>
                    </div>
                    <h3 class="announcement-title">Mid-Semester Break Schedule</h3>
                    <p class="announcement-content">Mid-semester break will begin on March 15th. Classes resume on March 25th.</p>
                    <div class="announcement-time">2 hours ago</div>
                </div>

                <div class="announcement-item facility">
                    <div class="announcement-meta">
                        <span class="announcement-type facility">Facility</span>
                    </div>
                    <h3 class="announcement-title">Library Closure</h3>
                    <p class="announcement-content">Library closed for maintenance on March 10th.</p>
                    <div class="announcement-time">5 hours ago</div>
                </div>

                <div class="announcement-item event">
                    <div class="announcement-meta">
                        <span class="announcement-type event">Event</span>
                    </div>
                    <h3 class="announcement-title">Career Fair</h3>
                    <p class="announcement-content">Career fair next Wednesday at Block A. Bring your resume!</p>
                    <div class="announcement-time">1 day ago</div>
                </div>

                <div class="footer-info">
                    Showing latest announcements • 3 items
                </div>
            </section>

            <div class="footer-info" style="margin-top: 2rem;">
                © PCC Connect • v1.0
            </div>
        </main>
    </div>

    <script type="module" src="main.js"></script>
</body>
</html>