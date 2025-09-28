<?php
session_start();
session_unset();
session_destroy();

// Try different paths based on your structure
header('Location: homepage.php');
header('Location: ../homepage.php');
header('Location: /your-project/homepage.php');
exit();
?>