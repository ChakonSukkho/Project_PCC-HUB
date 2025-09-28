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

// Update announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $connect->prepare("UPDATE announcements SET title=?, content=? WHERE announcement_id=?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    $stmt->close();
}

$admin = $result->fetch_assoc();

$result = $connect->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = [];

while ($row = $result->fetch_assoc()) {
    $announcements[] = [
        "id" => $row['announcement_id'],
        "title" => $row['title'],
        "category" => $row['category'],
        "priority" => $row['priority'],
        "content" => $row['content'],
        "time" => date("M d, Y H:i", strtotime($row['created_at'])),
        "timestamp" => strtotime($row['created_at']) * 1000
    ];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Announcements - PCC Hub</title>
    <link rel="stylesheet" href="../assests/css/admin-css/adminAnnouncement.css">
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <!-- Floating Add Announcement Button -->
<button id="add-announcement-btn" class="fab">+</button>

<!-- Add Announcement Modal -->
<div id="add-announcement-modal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h2>Add New Announcement</h2>
    <form id="announcement-form">
      <label>Title:</label>
      <input type="text" id="title" name="title" required>

      <label>Category:</label>
      <select id="category" name="category" required>
        <option value="academic">Academic</option>
        <option value="facility">Facility</option>
        <option value="event">Event</option>
        <option value="technical">Technical</option>
      </select>

      <label>Priority:</label>
      <select id="priority" name="priority">
        <option value="normal">Normal</option>
        <option value="urgent">Urgent</option>
      </select>

      <label>Content:</label>
      <textarea id="content" name="content" required></textarea>

      <button type="submit" class="submit-btn">Add Announcement</button>
    </form>
  </div>

</div>

<!--  Sini mir  -->

    <div class="container">
        <aside class="sidebar">
            <div class="logo">PCC Hub</div>
            
            <div class="profile">
                <div class="profile-pic">👨‍💻</div>
                <div class="profile-info">
                    <h3>Ahmad Rahman</h3>
                    <p>Computer Science</p>
                    <div class="student-id">Student ID: CS2023001</div>
                </div>
            </div>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <span class="nav-icon">📊</span>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span class="nav-icon">💬</span>
                            Group Chat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="running.php" class="nav-link">
                            <span class="nav-icon">🏃‍♂️</span>
                            Jogging Tracker
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="statis.php" class="nav-link">
                            <span class="nav-icon">📈</span>
                            Student Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="announcement2.php" class="nav-link active">
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
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="breadcrumb">
          <a class="breadcrumb-item" href="../dashboardWeb/index.html">Dashboard</a>
          <span class="breadcrumb-separator">›</span>
          <span class="breadcrumb-item active">Announcements</span>
        </div>
                <div class="header-content">
                    <h1 class="page-title">All Announcements</h1>
                    <p class="page-subtitle">Stay updated with the latest news and important information</p>
                </div>
                <div class="header-actions">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search announcements..." id="searchInput">
                        <span class="search-icon">🔍</span>
                    </div>
                    <div class="filter-container">
                        <select class="filter-select" id="categoryFilter">
                            <option value="all">Category</option>
                            <option value="academic">Academic</option>
                            <option value="facility">Facility</option>
                            <option value="event">Event</option>
                            <option value="technical">Technical</option>
                        </select>
                    </div>
                    <div class="sort-container">
                        <select class="sort-select" id="sortBy">
                            <option value="newest">Sort by Newest</option>
                            <option value="oldest">Sort by Oldest</option>
                            <option value="priority">Sort by Priority</option>
                        </select>
                    </div>
                </div>
            </header>

            <section class="announcements-grid" id="announcementsGrid">
                <!-- Announcements will be dynamically loaded here -->
                 <?php foreach ($announcements as $announcement) { ?>
                 <div class="announcement-card <?php if($announcement['category']=='academic') { echo 'academic'; } else if($announcement['category']=='facility') { echo 'facility'; } else if($announcement['category']=='event') { echo 'event'; } elseif ($announcement['category']=='technical') { echo 'technical'; } ?> fade-in"  data-id="ANN001" style="opacity: 1; transform: translateY(0px); transition: 0.6s;" >
        <div class="announcement-header">
            <div class="announcement-meta">
                <span class="announcement-type <?php if($announcement['category']=='academic') { echo 'academic'; } else if($announcement['category']=='facility') { echo 'facility'; } else if($announcement['category']=='event') { echo 'event'; } elseif ($announcement['category']=='technical') { echo 'technical'; } ?> "><?php echo $announcement['category'];?></span>
                
            </div>
            <div class="announcement-time"><?php echo $announcement['time'];?></div>
        </div>
        <h3 class="announcement-title"><?php echo $announcement['title'];?></h3>
        <p class="announcement-content"><?php echo $announcement['content'];?></p>
        <div class="announcement-actions">
            <!-- <span class="announcement-id"></span> -->
            <button class="delete-btn" data-id="<?php echo $announcement['id'];?>" onclick="deleteAnnouncement(${a.id})">Delete</button>
        </div>
    </div>
    <?php } ?>
            </section>

            <div class="load-more-container">
                <button class="load-more-btn" id="loadMoreBtn">Load More Announcements</button>
            </div>

            <div class="footer-info">
                © PCC Connect • v1.0
            </div>
        </main>
    </div>

    <script src="../assests/js/admin-js/adminAnnouncement.js"></script>
</body>
</html>