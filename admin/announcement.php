<?php
// admin/announcements.php
session_start();
include('../config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $title = $connect->real_escape_string($_POST['title']);
                $content = $connect->real_escape_string($_POST['content']);
                $category = $connect->real_escape_string($_POST['category']);
                $priority = $connect->real_escape_string($_POST['priority']);
                
                // Generate unique announcement code
                $code = 'ANN' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                
                $sql = "INSERT INTO announcements (announcement_code, title, content, category, priority, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("sssssi", $code, $title, $content, $category, $priority, $admin_id);
                
                if ($stmt->execute()) {
                    $success_message = "Announcement created successfully!";
                } else {
                    $error_message = "Error creating announcement: " . $connect->error;
                }
                break;
                
            case 'delete':
                $announcement_id = (int)$_POST['announcement_id'];
                $sql = "DELETE FROM announcements WHERE announcement_id = ?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("i", $announcement_id);
                
                if ($stmt->execute()) {
                    $success_message = "Announcement deleted successfully!";
                } else {
                    $error_message = "Error deleting announcement: " . $connect->error;
                }
                break;
                
            case 'toggle_status':
                $announcement_id = (int)$_POST['announcement_id'];
                $sql = "UPDATE announcements SET is_active = NOT is_active WHERE announcement_id = ?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("i", $announcement_id);
                
                if ($stmt->execute()) {
                    $success_message = "Announcement status updated!";
                } else {
                    $error_message = "Error updating status: " . $connect->error;
                }
                break;
        }
    }
}

// Get all announcements
$sql = "SELECT a.*, ad.admin_name 
        FROM announcements a 
        JOIN admins ad ON a.created_by = ad.admin_id 
        ORDER BY a.created_at DESC";
$announcements_result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Announcements</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            padding: 2rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .header h1 {
            color: #4f46e5;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .form-section {
            background: #f8fafc;
            padding: 2rem;
            border-radius: 0.75rem;
            margin-bottom: 3rem;
        }
        
        .form-section h2 {
            color: #1e293b;
            margin-bottom: 1.5rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        input, select, textarea {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.3s ease;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
        }
        
        .announcements-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .announcements-table th {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .announcements-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .announcements-table tr:hover {
            background: #f9fafb;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .category-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .category-academic { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
        .category-facility { background: rgba(16, 185, 129, 0.1); color: #059669; }
        .category-event { background: rgba(139, 92, 246, 0.1); color: #7c3aed; }
        .category-technical { background: rgba(245, 158, 11, 0.1); color: #d97706; }
        
        .priority-urgent {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 1rem;
            }
            
            .announcements-table {
                font-size: 0.875rem;
            }
            
            .announcements-table th,
            .announcements-table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📢 Manage Announcements</h1>
            <p>Create, edit, and manage system announcements</p>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Create Announcement Form -->
        <div class="form-section">
            <h2>Create New Announcement</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="academic">Academic</option>
                            <option value="facility">Facility</option>
                            <option value="event">Event</option>
                            <option value="technical">Technical</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" required>
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                
                <div class="form-group full-width">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Announcement</button>
            </form>
        </div>

        <!-- Announcements List -->
        <div class="form-section">
            <h2>All Announcements</h2>
            <table class="announcements-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($announcement = $announcements_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $announcement['announcement_code']; ?></td>
                            <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                            <td>
                                <span class="category-badge category-<?php echo $announcement['category']; ?>">
                                    <?php echo ucfirst($announcement['category']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($announcement['priority'] === 'urgent'): ?>
                                    <span class="priority-urgent">URGENT</span>
                                <?php else: ?>
                                    Normal
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $announcement['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $announcement['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></td>
                            <td class="actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="announcement_id" value="<?php echo $announcement['announcement_id']; ?>">
                                    <button type="submit" class="btn btn-warning">
                                        <?php echo $announcement['is_active'] ? 'Disable' : 'Enable'; ?>
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="announcement_id" value="<?php echo $announcement['announcement_id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>