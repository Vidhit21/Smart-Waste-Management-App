<?php
session_start();
session_destroy();
header("Location: worker_login.php"); // Redirect to the login page after logout
exit();
?>
