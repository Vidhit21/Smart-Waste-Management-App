<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Query for the worker in the Users table.
        $stmt = $conn->prepare("SELECT user_id, password_hash FROM Users WHERE username = ? AND user_type = 'Worker'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                // Successful login; set session and redirect.
                $_SESSION['user_id'] = $user['user_id'];
                header("Location: dashboard.php"); // Adjust dashboard page as needed.
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Worker Login</h2>
    <?php if (isset($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>
    <form method="post" action="worker_login.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" class="form-control" required>
         </div>
         <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" required>
         </div>
         <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="index.php">Register here</a>.</p>
</div>
</body>
</html>
