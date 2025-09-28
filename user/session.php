<?php
if(!isset($_SESSION['user_id']))
{
    header('Location: '.$link.'/user/login.php');
    exit();
}

// Verify user exists and is active
$sql_login = "SELECT * FROM users WHERE user_id = '".$_SESSION['user_id']."' AND is_active = 1";
$result_login = $connect->query($sql_login);

if($result_login && $result_login->num_rows > 0)
{
    $rows_login = $result_login->fetch_array();
    // User is valid, continue
}
else
{
    // User doesn't exist or is inactive, destroy session and redirect
    session_destroy();
    header('Location: '.$link.'/user/login.php');
    exit();
}
?>