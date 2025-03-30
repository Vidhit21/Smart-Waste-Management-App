<?php
session_start();
include("db_connect.php"); // Include the database connection file

$error = "";
$success = "";

// Process form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and trim form data
    $username         = trim($_POST['username']);
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $phone            = trim($_POST['phone']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $division         = trim($_POST['division']);
    $street           = trim($_POST['street']);
    $pincode          = trim($_POST['pincode']);
    $latitude         = trim($_POST['latitude']);
    $longitude        = trim($_POST['longitude']);

    // Validate required fields
    if (empty($username) || empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check for existing username or email
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Username or email already exists.";
        }
        $stmt->close();
    }

    if (empty($error)) {
        // Insert address if any address field is provided (optional)
        if (!empty($division) || !empty($street) || !empty($pincode) || !empty($latitude) || !empty($longitude)) {
            $stmt = $conn->prepare("INSERT INTO Address (division, street, pincode, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $division, $street, $pincode, $latitude, $longitude);
            if ($stmt->execute()) {
                $address_id = $stmt->insert_id;
            } else {
                $error = "Failed to insert address.";
            }
            $stmt->close();
        } else {
            $address_id = NULL;
        }
    }

    if (empty($error)) {
        // Hash the password for security
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        // Since this page is only for Residents, user_type is fixed as 'Resident'
        $user_type = "Resident";

        // Insert the new user into the Users table
        $stmt = $conn->prepare("INSERT INTO Users (username, name, email, phone, password_hash, user_type, address_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $username, $name, $email, $phone, $password_hash, $user_type, $address_id);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Insert additional resident-specific details into Residents table
            $stmt = $conn->prepare("INSERT INTO Residents (user_id) VALUES (?)");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                //redirect to login.php
                header("Location: login.php?success=Registration successful! Please log in.");
            } else {
                $error = "Error inserting resident details.";
            }
            $stmt->close();
        } else {
            $error = "Error inserting user.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WasteWise - Resident Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Ensure no scrolling by forcing the viewport height */
    html, body {
      height: 100%;
      overflow: hidden;
      margin: 0;
      padding: 0;
    }
    /* Vibrant gradient background */
    body {
      background: linear-gradient(135deg, #74ebd5, #acb6e5);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    /* Registration card with glassmorphism effect */
    .registration-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 500px;
      padding: 20px;
      max-height: 90vh; /* Ensure card doesn't exceed viewport */
      overflow-y: auto; /* Allow internal scroll if needed */
    }
    /* Themed header with eco-friendly color */
    .registration-header {
      background: #56ab2f;
      color: #fff;
      padding: 15px;
      text-align: center;
      border-radius: 10px 10px 0 0;
    }
    .registration-header h1 {
      margin: 0;
      font-size: 1.8rem;
    }
    /* Body styling inside the card */
    .registration-body {
      padding: 15px;
    }
    .registration-body h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
      font-size: 1.5rem;
    }
    .form-label {
      font-weight: 500;
      margin-top: 10px;
    }
    .form-control {
      border-radius: 5px;
    }
    .btn-custom {
      background-color: #56ab2f;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 10px;
      width: 100%;
      margin-top: 15px;
    }
    .btn-custom:hover {
      background-color: #489724;
    }
    hr {
      border-top: 1px solid #ddd;
    }
  </style>
</head>
<body>
  <div class="registration-card">
    <div class="registration-header">
      <h1>WasteWise</h1>
    </div>
    <div class="registration-body">
      <h2>Resident Registration</h2>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>
      <form method="post" action="register.php">
        <div class="mb-3">
          <label for="username" class="form-label">Username *</label>
          <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="name" class="form-label">Full Name *</label>
          <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email *</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone</label>
          <input type="text" id="phone" name="phone" class="form-control">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password *</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password *</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <hr>
        <h5 class="mb-3">Address Details (Optional)</h5>
        <div class="mb-3">
          <label for="division" class="form-label">Division</label>
          <input type="text" id="division" name="division" class="form-control">
        </div>
        <div class="mb-3">
          <label for="street" class="form-label">Street</label>
          <input type="text" id="street" name="street" class="form-control">
        </div>
        <div class="mb-3">
          <label for="pincode" class="form-label">Pincode</label>
          <input type="text" id="pincode" name="pincode" class="form-control">
        </div>
        <div class="mb-3">
          <label for="latitude" class="form-label">Latitude</label>
          <input type="text" id="latitude" name="latitude" class="form-control">
        </div>
        <div class="mb-3">
          <label for="longitude" class="form-label">Longitude</label>
          <input type="text" id="longitude" name="longitude" class="form-control">
        </div>
        <button type="submit" class="btn-custom">Register</button>
      </form>
    </div>
  </div>
  
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

