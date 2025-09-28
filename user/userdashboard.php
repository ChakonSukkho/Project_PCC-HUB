<?php
include('../config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Get user information
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    session_destroy();
    header("Location: ../user/login.php");
    exit();
}

$user = $result->fetch_assoc();
$profile_image = $user['profile_image'] ?? '';
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

            <!-- Updated Profile Section -->
            <a href="profile.php" class="profile-link">
                <div class="profile">
                    <div class="profile-pic" id="img-profile">
                        <!-- <?php if (!empty($profile_image) && file_exists('../uploads/profiles/' . $profile_image)): ?>
                            <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
                        <?php else: ?>
                            <div class="placeholder">👤</div>
                        <?php endif; ?> -->

                    </div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($user['user_name']); ?></h3>
                        <div class="student-id">
                            Student ID: <?php echo !empty($user['matric_number']) ? htmlspecialchars($user['matric_number']) : "N/A"; ?>
                        </div>
                    </div>
                </div>
            </a>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <span class="nav-icon">📊</span>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../my-chat-box-main/index.html" class="nav-link">
                            <span class="nav-icon">💬</span>
                            Group Chat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="userRunning.php" class="nav-link">
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
                        <a href="announcement.php" class="nav-link">
                            <span class="nav-icon">📢</span>
                            Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="usercertificate.php" class="nav-link">
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

                    <li class="nav-item">
                        <a href="enroll_program.php" class="nav-link">
                            <span class="nav-icon">👤</span>
                            Enroll Programm
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
                <h1>Welcome back, <?php echo htmlspecialchars($user['user_name']); ?>! <span class="wave-emoji">👋</span></h1>
                <p>Ready to make today productive? Check your latest updates below.</p>
            </section>

            <section class="quick-access">
                <h2 class="section-title">Quick Access</h2>
                <div class="quick-access-grid">
                    <div class="access-card">
                        <div class="card-header">
                            <div class="card-icon group-chat">💬</div>
                        </div>
                        <h3 class="card-title">Group Chat</h3>
                        <p class="card-description">Connect with classmates</p>
                        <a href="../my-chat-box-main/index.html" class="access-link">Access Now →</a>
                    </div>

                    <div class="access-card">
                        <div class="card-header">
                            <div class="card-icon jogging">🏃‍♂️</div>
                        </div>
                        <h3 class="card-title">Go Fitness</h3>
                        <p class="card-description">Track your RUN</p>
                        <a href="userRunning.php" class="access-link">Access Now →</a>
                    </div>

                    <div class="access-card">
                        <div class="card-header">
                            <div class="card-icon statistics">📊</div>
                        </div>
                        <h3 class="card-title">Student Statistics</h3>
                        <p class="card-description">Check Your Merits</p>
                        <a href="statis.php" class="access-link">Access Now →</a>
                    </div>
                </div>
            </section>



            

            <!-- This section for real time update the announcement when admin update the announcement -->
            <section class="announcements">
                <div class="announcements-header">
                    <h2 class="announcements-title">Latest Announcements</h2>
                    <a href="../user/announcement.php" class="view-all-link">View All</a>
                </div>

                <?php if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-item <?php echo $announcement['category']; ?>">
                            <div class="announcement-meta">
                                <span class="announcement-type <?php echo $announcement['category']; ?>">
                                    <?php echo ucfirst($announcement['category']); ?>
                                </span>
                                <?php if ($announcement['priority'] === 'urgent'): ?>
                                    <span class="announcement-priority">URGENT</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                            <p class="announcement-content">
                                <?php 
                                // Truncate content for dashboard display
                                $content = htmlspecialchars($announcement['content']);
                                echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content; 
                                ?>
                            </p>
                            <div class="announcement-time"><?php echo $announcement['time_ago']; ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="announcement-item">
                        <div class="announcement-meta">
                            <span class="announcement-type academic">System</span>
                        </div>
                        <h3 class="announcement-title">No Announcements</h3>
                        <p class="announcement-content">There are currently no announcements available.</p>
                        <div class="announcement-time">Just now</div>
                    </div>
                <?php endif; ?>

                <div class="footer-info">
                    Showing latest announcements • <?php echo count($announcements); ?> items
                </div>
            </section>

            <div class="footer-info" style="margin-top: 2rem;">
                © PCC Connect • v1.0
            </div>
        </main>
    </div>

    <script src="../assests/js/user-js/dashboard.js"></script>
</body>
</html>