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

// ✅ Handle AJAX requests for dynamic loading
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    $offset = ($page - 1) * $per_page;
    
    $category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
    
    // Build WHERE clause
    $where_conditions = ["a.is_active = 1"];
    $params = [];
    $types = '';
    
    if ($category_filter !== 'all') {
        $where_conditions[] = "a.category = ?";
        $params[] = $category_filter;
        $types .= 's';
    }
    
    if (!empty($search_query)) {
        $where_conditions[] = "(a.title LIKE ? OR a.content LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
        $types .= 'ss';
    }
    
    $where_clause = !empty($where_conditions) ? implode(' AND ', $where_conditions) : '1=1';
    
    // Build ORDER BY clause
    $order_by = '';
    switch ($sort_by) {
        case 'oldest':
            $order_by = 'ORDER BY a.created_at ASC';
            break;
        case 'priority':
            $order_by = "ORDER BY FIELD(a.priority, 'urgent', 'high', 'medium', 'low'), a.created_at DESC";
            break;
        case 'newest':
        default:
            $order_by = 'ORDER BY a.created_at DESC';
            break;
    }
    
    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM announcements a WHERE $where_clause";
    $count_stmt = $connect->prepare($count_sql);
    if (!empty($params)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $total_announcements = $count_stmt->get_result()->fetch_assoc()['total'];
    
    // Get announcements
    $sql = "SELECT a.*, ad.admin_name as created_by_name,
            CASE 
                WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN CONCAT(TIMESTAMPDIFF(MINUTE, a.created_at, NOW()), ' minutes ago')
                WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN CONCAT(TIMESTAMPDIFF(HOUR, a.created_at, NOW()), ' hours ago')
                WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK) THEN CONCAT(TIMESTAMPDIFF(DAY, a.created_at, NOW()), ' days ago')
                ELSE DATE_FORMAT(a.created_at, '%d %b %Y')
            END as time_ago
            FROM announcements a 
            LEFT JOIN admins ad ON a.created_by = ad.admin_id 
            WHERE $where_clause 
            $order_by 
            LIMIT $per_page OFFSET $offset";
    
    $stmt = $connect->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $announcements_result = $stmt->get_result();
    $announcements = $announcements_result->fetch_all(MYSQLI_ASSOC);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'data' => $announcements,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total_announcements / $per_page),
            'total_items' => $total_announcements,
            'has_more' => ($page * $per_page) < $total_announcements
        ]
    ]);
    exit();
}

// ✅ Regular page load - get initial announcements for non-JS fallback
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build WHERE clause - FIXED
$where_conditions = ["a.is_active = 1"];
$params = [];
$types = '';

// Only add category filter if it's not 'all'
if ($category_filter !== 'all') {
    $where_conditions[] = "a.category = ?";
    $params[] = $category_filter;
    $types .= 's';
}

if (!empty($search_query)) {
    $where_conditions[] = "(a.title LIKE ? OR a.content LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $types .= 'ss';
}

// Create WHERE clause or default to 1=1 if no conditions
$where_clause = !empty($where_conditions) ? implode(' AND ', $where_conditions) : '1=1';

// Build ORDER BY clause
$order_by = '';
switch ($sort_by) {
    case 'oldest':
        $order_by = 'ORDER BY a.created_at ASC';
        break;
    case 'priority':
        $order_by = "ORDER BY FIELD(a.priority, 'urgent', 'high', 'medium', 'low'), a.created_at DESC";
        break;
    case 'newest':
    default:
        $order_by = 'ORDER BY a.created_at DESC';
        break;
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM announcements a WHERE $where_clause";
$count_stmt = $connect->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_announcements = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_announcements / $per_page);

// Get announcements
$sql = "SELECT a.*, ad.admin_name as created_by_name,
        CASE 
            WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN CONCAT(TIMESTAMPDIFF(MINUTE, a.created_at, NOW()), ' minutes ago')
            WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN CONCAT(TIMESTAMPDIFF(HOUR, a.created_at, NOW()), ' hours ago')
            WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK) THEN CONCAT(TIMESTAMPDIFF(DAY, a.created_at, NOW()), ' days ago')
            ELSE DATE_FORMAT(a.created_at, '%d %b %Y')
        END as time_ago
        FROM announcements a 
        LEFT JOIN admins ad ON a.created_by = ad.admin_id 
        WHERE $where_clause 
        $order_by 
        LIMIT $per_page OFFSET $offset";

$stmt = $connect->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$announcements_result = $stmt->get_result();
$announcements = $announcements_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Announcements - PCC Hub</title>
    <link rel="stylesheet" href="../assests/css/user-css/announcement.css">
</head>
<body>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    
    <div class="container">
        <aside class="sidebar">
            <div class="logo">PCC Hub</div>
            
            <!-- Profile section -->
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
                        <a href="statis.php" class="nav-link">
                            <span class="nav-icon">📈</span>
                            Student Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
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
                <div class="breadcrumb">
                    <a class="breadcrumb-item" href="userdashboard.php">Dashboard</a>
                    <span class="breadcrumb-separator">›</span>
                    <span class="breadcrumb-item active">Announcements</span>
                </div>
                <div class="header-content">
                    <h1 class="page-title">All Announcements</h1>
                    <p class="page-subtitle">Stay updated with the latest news and important information</p>
                </div>
                <div class="header-actions">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search announcements..." 
                               id="searchInput" value="<?php echo htmlspecialchars($search_query); ?>">
                        <span class="search-icon">🔍</span>
                    </div>
                    <div class="filter-container">
                        <select class="filter-select" id="categoryFilter">
                            <option value="all" <?php echo $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <option value="general" <?php echo $category_filter === 'general' ? 'selected' : ''; ?>>General</option>
                            <option value="academic" <?php echo $category_filter === 'academic' ? 'selected' : ''; ?>>Academic</option>
                            <option value="co-curricular" <?php echo $category_filter === 'co-curricular' ? 'selected' : ''; ?>>Co-curricular</option>
                            <option value="event" <?php echo $category_filter === 'event' ? 'selected' : ''; ?>>Event</option>
                            <option value="urgent" <?php echo $category_filter === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                        </select>
                    </div>
                    <div class="sort-container">
                        <select class="sort-select" id="sortBy">
                            <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Sort by Newest</option>
                            <option value="oldest" <?php echo $sort_by === 'oldest' ? 'selected' : ''; ?>>Sort by Oldest</option>
                            <option value="priority" <?php echo $sort_by === 'priority' ? 'selected' : ''; ?>>Sort by Priority</option>
                        </select>
                    </div>
                </div>
            </header>

            <section class="announcements-grid" id="announcementsGrid">
                <!-- Initial announcements for non-JS fallback -->
                <noscript>
                    <?php if (!empty($announcements)): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <div class="announcement-card <?php echo $announcement['category']; ?>">
                                <div class="announcement-header">
                                    <div class="announcement-meta">
                                        <span class="announcement-category <?php echo $announcement['category']; ?>">
                                            <?php 
                                            $category_icons = [
                                                'general' => '📢',
                                                'academic' => '🎓',
                                                'co-curricular' => '🏆',
                                                'event' => '📅',
                                                'urgent' => '🚨'
                                            ];
                                            echo ($category_icons[$announcement['category']] ?? '📢') . ' ' . ucfirst(str_replace('-', ' ', $announcement['category'])); 
                                            ?>
                                        </span>
                                        <?php if ($announcement['priority'] === 'urgent'): ?>
                                        <span class="announcement-priority urgent">🚨 URGENT</span>
                                        <?php elseif ($announcement['priority'] === 'high'): ?>
                                            <span class="announcement-priority high">⚠️ HIGH</span>
                                        <?php elseif ($announcement['priority'] === 'medium'): ?>
                                            <span class="announcement-priority medium">ℹ️ Medium</span>
                                        <?php elseif ($announcement['priority'] === 'low'): ?>
                                            <span class="announcement-priority low">⬇️ Low</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="announcement-time"><?php echo $announcement['time_ago']; ?></div>
                                </div>
                                
                                <div class="announcement-content">
                                    <h3 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                    <p class="announcement-description">
                                        <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                                    </p>
                                </div>
                                
                                <?php if (!empty($announcement['image'])): ?>
                                    <div class="announcement-image">
                                        <img src="../uploads/announcements/<?php echo htmlspecialchars($announcement['image']); ?>" 
                                             alt="Announcement Image" 
                                             loading="lazy">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="announcement-footer">
                                    <div class="announcement-author">
                                        <span class="author-icon">👤</span>
                                        <span class="author-name">
                                            <?php echo htmlspecialchars($announcement['created_by_name'] ?? 'PCC Admin'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-announcements">
                            <div class="no-announcements-icon">📭</div>
                            <h3>No Announcements Found</h3>
                            <p>There are currently no announcements available. Check back later!</p>
                        </div>
                    <?php endif; ?>
                </noscript>
            </section>

            <div class="load-more-container">
                <button class="load-more-btn" id="loadMoreBtn">Load More Announcements</button>
            </div>

            <div class="footer-info">
                © PCC Connect • v1.0
            </div>
        </main>
    </div>

    <script src="../assets/js/user-js/announcement.js"></script>
</body>
</html> 