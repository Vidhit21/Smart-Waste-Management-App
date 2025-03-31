<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and trim form inputs.
    $username         = trim($_POST['username']);
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $phone            = trim($_POST['phone']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Address details (optional)
    $division  = trim($_POST['division']);
    $street    = trim($_POST['street']);
    $pincode   = trim($_POST['pincode']);
    $latitude  = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    
    // Worker-specific details
    $vehicle_info = trim($_POST['vehicle_info']);

    // Basic validations.
    if (empty($username) || empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if username or email already exists.
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error_message = "Username or Email already exists.";
        } else {
            // Insert an address record if address details are provided.
            $address_id = null;
            if (!empty($division) && !empty($street) && !empty($pincode)) {
                $stmt_addr = $conn->prepare("INSERT INTO Address (division, street, pincode, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
                $stmt_addr->bind_param("sssdd", $division, $street, $pincode, $latitude, $longitude);
                if ($stmt_addr->execute()) {
                    $address_id = $stmt_addr->insert_id;
                }
                $stmt_addr->close();
            }
            
            // Insert into Users table.
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $user_type     = "Worker";
            $stmt_user = $conn->prepare("INSERT INTO Users (username, name, email, phone, password_hash, user_type, address_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_user->bind_param("ssssssi", $username, $name, $email, $phone, $password_hash, $user_type, $address_id);
            if ($stmt_user->execute()) {
                $user_id = $stmt_user->insert_id;
                // Insert into Workers table.
                $stmt_worker = $conn->prepare("INSERT INTO Workers (user_id, vehicle_info) VALUES (?, ?)");
                $stmt_worker->bind_param("is", $user_id, $vehicle_info);
                if ($stmt_worker->execute()) {
                    $success_message = "Registration successful! You can now log in.";
                } else {
                    $error_message = "Error in worker creation: " . $stmt_worker->error;
                }
                $stmt_worker->close();
            } else {
                $error_message = "Error in user creation: " . $stmt_user->error;
            }
            $stmt_user->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Worker Registration</h2>
    <?php if (isset($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>
    <?php if (isset($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>
    <form method="post" action="index.php">
        <!-- User Details -->
        <div class="form-group">
            <label for="username">Username *</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
         <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" class="form-control" required>
         </div>
         <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" class="form-control">
         </div>
         <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" name="password" class="form-control" required>
         </div>
         <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <input type="password" name="confirm_password" class="form-control" required>
         </div>

         <!-- Optional Address Details -->
         <h4>Address Details (Optional)</h4>
         <div class="form-group">
            <label for="division">Division</label>
            <input type="text" name="division" class="form-control">
         </div>
         <div class="form-group">
            <label for="street">Street</label>
            <input type="text" name="street" class="form-control">
         </div>
         <div class="form-group">
            <label for="pincode">Pincode</label>
            <input type="text" name="pincode" class="form-control">
         </div>
         <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="number" step="any" name="latitude" class="form-control">
         </div>
         <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="number" step="any" name="longitude" class="form-control">
         </div>

         <!-- Worker Specific -->
         <div class="form-group">
            <label for="vehicle_info">Vehicle Info</label>
            <input type="text" name="vehicle_info" class="form-control">
         </div>
         <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p class="mt-3">Already have an account? <a href="worker_login.php">Login here</a>.</p>
</div>
</body>
</html>
