<?php
// Database connection
require_once 'db_connection.php';

// --- Registration Code ---
if (isset($_POST['register'])) {
    // Retrieve form values for user details
    $username = trim($_POST['username']);
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $department = trim($_POST['department']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Municipal authorities do not need an address.
        // Set address_id to NULL.
        $address_id = NULL;
        $user_type = 'Authority';

        // Insert user details into Users table.
        $stmt = $conn->prepare("INSERT INTO Users (username, name, email, phone, password_hash, user_type, address_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // 'address_id' is bound as an integer; if NULL is passed, it will be stored as NULL.
        $stmt->bind_param("ssssssi", $username, $name, $email, $phone, $password_hash, $user_type, $address_id);
        if (!$stmt->execute()) {
            $error = "Error inserting user: " . $stmt->error;
        } else {
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Insert authority-specific details into Authorities table.
            $stmt = $conn->prepare("INSERT INTO Authorities (user_id, department) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $department);
            if (!$stmt->execute()) {
                $error = "Error inserting authority details: " . $stmt->error;
            } else {
                $success = "Registration successful. You can now login.";
            }
            $stmt->close();
        }
    }
}

// --- Login Code ---
if (isset($_POST['login'])) {
    $login_username = trim($_POST['login_username']);
    $login_password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT user_id, password_hash, user_type FROM Users WHERE username = ?");
    $stmt->bind_param("s", $login_username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($login_password, $row['password_hash'])) {
            // Ensure the user is a municipal authority
            if ($row['user_type'] === 'Authority') {
                session_start();
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $login_username;
                $_SESSION['user_type'] = $row['user_type'];
                header("Location: dashboard.php"); // Redirect to dashboard page
                exit();
            } else {
                $login_error = "You are not authorized as a municipal authority.";
            }
        } else {
            $login_error = "Invalid username or password.";
        }
    } else {
        $login_error = "Invalid username or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Municipal Authority Login & Registration</title>
    <link rel="stylesheet" href="css/index.css">
    <!-- You can include Bootstrap or custom styles as needed -->
</head>
<body>
    <!-- Municipal Authority Login Form -->
    <h2>Municipal Authority Login</h2>
    <?php if(isset($login_error)) { echo "<p style='color:red;'>$login_error</p>"; } ?>
    <form method="post" action="">
        <label for="login_username">Username:</label>
        <input type="text" name="login_username" required>
        <br>
        <label for="login_password">Password:</label>
        <input type="password" name="login_password" required>
        <br>
        <input type="submit" name="login" value="Login">
    </form>

    <hr>

    <!-- Municipal Authority Registration Form -->
    <h2>Municipal Authority Registration</h2>
    <?php 
        if(isset($error)) { 
            echo "<p style='color:red;'>$error</p>"; 
        }
        if(isset($success)) { 
            echo "<p style='color:green;'>$success</p>"; 
        } 
    ?>
    <form method="post" action="">
        <h3>User Details</h3>
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <br>
        <label for="name">Full Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <br>
        <label for="phone">Phone:</label>
        <input type="text" name="phone">
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <label for="department">Department:</label>
        <input type="text" name="department" required>
        <br>
        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>
