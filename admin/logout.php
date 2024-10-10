<?php
session_start();
$_SESSION = []; // Clear the session
session_destroy(); // Destroy the session
header("Location: login.php"); // Redirect to login page
exit();
?>
