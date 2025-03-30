<?php
session_start();
include("db_connect.php");

$errors = array();

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } else {
        // Only select users with user_type 'Resident'
        $stmt = $conn->prepare("SELECT user_id, username, password_hash FROM Users WHERE email=? AND user_type='Resident' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $username, $password_hash);
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                // Set session variables and redirect to resident dashboard
                $_SESSION['user_id']   = $user_id;
                $_SESSION['username']  = $username;
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resident Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Full-page gradient background */
    body {
      background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    /* Card styling for the login form */
    .login-card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      max-width: 400px;
      width: 100%;
      background: #fff;
    }
    /* Header styling */
    .login-header {
      background: #007bff;
      color: #fff;
      padding: 20px;
      text-align: center;
    }
    /* Form body padding */
    .login-body {
      padding: 30px;
    }
    /* Rounded input fields */
    .form-control {
      border-radius: 10px;
    }
    /* Rounded login button with extra padding */
    .btn-login {
      border-radius: 10px;
      padding: 10px 20px;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <h2>Resident Login</h2>
    </div>
    <div class="login-body">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <p><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
        </div>
        <div class="d-grid">
          <button type="submit" name="login" class="btn btn-primary btn-login">Login</button>
        </div>
        <!-- write code for create an account option -->
        <div class="mt-3 text-center">
          <p>Don't have an account? <a href="register.php">Create one</a></p>
      </form>
    </div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
