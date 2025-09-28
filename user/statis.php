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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Statistics - PCC Hub</title>
    <link rel="stylesheet" href="../assests/css/user-css/userstatistic.css">
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <div class="container">
        <aside class="sidebar">
            <div class="logo">PCC Hub</div>
            
            <a href="profile.php" class="profile-link" style="text-decoration: none; color: inherit;">
                <div class="profile">
                    <div class="profile-pic">👨‍💻</div>
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
                        <a href="userdashboard.php" class="nav-link">
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
                        <a href="userRunning.php" class="nav-link">
                            <span class="nav-icon">🏃‍♂️</span>
                            Go Fitness
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
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
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div></div>
                <a href="#" class="view-all-btn">🏆 View All Awards</a>
            </header>

            <section class="welcome-section">
                <h1>Student Merit Rankings <span class="trophy-emoji">🏆</span></h1>
                <p>Track academic excellence and student achievements across all programs.</p>
            </section>

            <section class="podium-section">
                <h2 class="section-title white">Top Performers</h2>
                
                <div class="podium-container">
                    <div class="podium-place second">
                        <div class="empty-avatar">?</div>
                        <div class="podium-base">
                            <div class="position-number">2</div>
                            <div class="student-name">No Student</div>
                            <div class="merit-score">0 pts</div>
                        </div>
                    </div>

                    <div class="podium-place first">
                        <div class="crown">👑</div>
                        <div class="empty-avatar">?</div>
                        <div class="podium-base">
                            <div class="position-number">1</div>
                            <div class="student-name">No Student</div>
                            <div class="merit-score">0 pts</div>
                        </div>
                    </div>

                    <div class="podium-place third">
                        <div class="empty-avatar">?</div>
                        <div class="podium-base">
                            <div class="position-number">3</div>
                            <div class="student-name">No Student</div>
                            <div class="merit-score">0 pts</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rankings-section">
                <h2 class="section-title">Merit Rankings</h2>
                
                <div class="controls">
                    <button class="filter-btn active" onclick="filterRankings('all')">All Students</button>
                    <button class="filter-btn" onclick="filterRankings('semester')">This Semester</button>
                    <button class="filter-btn" onclick="filterRankings('year')">This Year</button>
                    <button class="refresh-btn" onclick="refreshRankings()">🔄 Refresh</button>
                </div>

                <div class="rankings-list">
                    <div class="ranking-header">
                        <div>Rank</div>
                        <div>Student</div>
                        <div>Merit Points</div>
                    </div>
                    <div id="rankings-body">
                        <div class="empty-state">
                            <div class="empty-state-icon">📊</div>
                            <h3>No Rankings Available</h3>
                            <p>Student rankings will appear here once merit points are recorded.</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="footer-info">
                © PCC Connect • Student Statistics v1.0
            </div>
        </main>
    </div>
    <script src="../assests/js/user-js/statistic.js"></script>
</body>
</html>