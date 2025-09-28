<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id    = $_POST['admin_id'];
    $admin_name  = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_phone = $_POST['admin_phone'];

    $sql = "UPDATE admins 
            SET admin_name = ?, 
                admin_email = ?, 
                admin_phone = ?, 
                updated_at = NOW() 
            WHERE admin_id = ?";
    
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sssi", $admin_name, $admin_email, $admin_phone, $admin_id);

    if ($stmt->execute()) {
        header("Location: profile.php?success=1");
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
}
?>
