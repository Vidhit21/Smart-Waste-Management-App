<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$update_success = "";
$update_error = "";

// Process form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted data
    $new_name    = trim($_POST['name']);
    $new_email   = trim($_POST['email']);
    $new_phone   = trim($_POST['phone']);
    $new_division = trim($_POST['division']);
    $new_street   = trim($_POST['street']);
    $new_pincode  = trim($_POST['pincode']);

    // Update Users table
    $stmt = $conn->prepare("UPDATE Users SET name = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $new_name, $new_email, $new_phone, $user_id);
    if ($stmt->execute()) {
        $update_success = "Profile updated successfully.";
    } else {
        $update_error = "Error updating profile.";
    }
    $stmt->close();

    // Update Address table – update individual fields for the address.
    $stmt = $conn->prepare("UPDATE Address SET division = ?, street = ?, pincode = ? WHERE address_id = (SELECT address_id FROM Users WHERE user_id = ?)");
    $stmt->bind_param("sssi", $new_division, $new_street, $new_pincode, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch user details along with address info
$stmt = $conn->prepare("SELECT u.name, u.email, u.phone, a.division, a.street, a.pincode, u.created_at 
                        FROM Users u 
                        LEFT JOIN Address a ON u.address_id = a.address_id 
                        WHERE u.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $division, $street, $pincode, $created_at);
$stmt->fetch();
$stmt->close();
$conn->close();

$member_since = date("Y", strtotime($created_at));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile Settings | Jamnagar Smart Waste</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { padding-top: 50px; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include 'nav.php'; ?>

  <div class="container py-4">
    <h1 class="mt-4 mb-4">Profile Settings</h1>

    <?php if (!empty($update_success)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($update_success); ?></div>
    <?php elseif (!empty($update_error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($update_error); ?></div>
    <?php endif; ?>

    <form method="POST" action="pro_set.php">
      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
      </div>
      <h5 class="mt-4">Address Details</h5>
      <div class="mb-3">
        <label for="division" class="form-label">Division</label>
        <input type="text" name="division" class="form-control" value="<?php echo htmlspecialchars($division); ?>">
      </div>
      <div class="mb-3">
        <label for="street" class="form-label">Street</label>
        <input type="text" name="street" class="form-control" value="<?php echo htmlspecialchars($street); ?>">
      </div>
      <div class="mb-3">
        <label for="pincode" class="form-label">Pincode</label>
        <input type="text" name="pincode" class="form-control" value="<?php echo htmlspecialchars($pincode); ?>">
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
