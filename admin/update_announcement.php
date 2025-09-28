<?php
include('../config.php');
session_start();

// Check if admin logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Update logic
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $connect->prepare("UPDATE announcements SET title = ?, content = ?, updated_at = NOW() WHERE announcement_id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);

    if ($stmt->execute()) {
        echo "Announcement updated successfully.";
    } else {
        echo "Error updating announcement: " . $connect->error;
    }
}

// Fetch announcement for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $connect->prepare("SELECT * FROM announcements WHERE announcement_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $announcement = $result->fetch_assoc();
}
?>

<!-- HTML Form -->
<form method="POST">
  <input type="hidden" name="id" value="<?php echo $announcement['announcement_id']; ?>">
  <input type="text" name="title" value="<?php echo $announcement['title']; ?>" required>
  <textarea name="content" required><?php echo $announcement['content']; ?></textarea>
  <button type="submit" name="update">Update</button>
</form>
