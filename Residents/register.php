<?php 
session_start();
include("db_connect.php"); // Include your database connection file

$error = "";
$success = "";

// Fetch Areas from the Areas table
$areas = [];
$sqlAreas = "SELECT area_id, area_name FROM Areas ORDER BY area_name ASC";
$resultAreas = $conn->query($sqlAreas);
if ($resultAreas && $resultAreas->num_rows > 0) {
    while ($row = $resultAreas->fetch_assoc()){
        $areas[] = $row;
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and trim user details
    $username         = trim($_POST['username']);
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $phone            = trim($_POST['phone']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Retrieve and trim address details (required)
    // Use "home_location" as the house/building number.
    $house_number     = trim($_POST['home_location']);
    // The area is fetched from the table; its id is sent.
    $area             = trim($_POST['area']);
    // The street dropdown returns a street_id from the Streets table.
    $street_id        = intval($_POST['street']);
    $pincode          = trim($_POST['pincode']);
    $latitude         = trim($_POST['latitude']);
    $longitude        = trim($_POST['longitude']);

    // Validate required fields (user & address)
    if (empty($username) || empty($name) || empty($email) || empty($password) || empty($confirm_password)
        || empty($house_number) || empty($area) || empty($street_id) || empty($pincode)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email already exists
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
        // Prepare latitude and longitude; if empty, set to null
        $latitude_val = (!empty($latitude)) ? $latitude : NULL;
        $longitude_val = (!empty($longitude)) ? $longitude : NULL;

        // Insert address into Address table using the new design.
        // The Address table now stores: house_number, street_id, pincode, latitude, longitude.
        $stmt = $conn->prepare("INSERT INTO Address (house_number, street_id, pincode, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
        // Bind parameters: house_number (string), street_id (integer), pincode (string), latitude and longitude as doubles.
        $stmt->bind_param("sisdd", $house_number, $street_id, $pincode, $latitude_val, $longitude_val);
        if ($stmt->execute()) {
            $address_id = $stmt->insert_id;
        } else {
            $error = "Failed to insert address: " . $stmt->error;
        }
        $stmt->close();
    }

    if (empty($error)) {
        // Hash the password for security
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $user_type = "Resident";

        // Insert user into Users table with the new address_id
        $stmt = $conn->prepare("INSERT INTO Users (username, name, email, phone, password_hash, user_type, address_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $username, $name, $email, $phone, $password_hash, $user_type, $address_id);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Insert resident-specific details into Residents table
            $stmt = $conn->prepare("INSERT INTO Residents (user_id) VALUES (?)");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                header("Location: login.php?success=Registration successful! Please log in.");
                exit;
            } else {
                $error = "Error inserting resident details: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error inserting user: " . $stmt->error;
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
    html, body {
      height: 100%;
      overflow: hidden;
      margin: 0;
      padding: 0;
    }
    body {
      background: linear-gradient(135deg, #74ebd5, #acb6e5);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .registration-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 500px;
      padding: 20px;
      max-height: 90vh;
      overflow-y: auto;
    }
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
          <?php echo htmlspecialchars($error); ?>
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
        <h5 class="mb-3">Address Details *</h5>
        <div class="mb-3">
          <label for="home_location" class="form-label">House/Building No. *</label>
          <input type="text" id="home_location" name="home_location" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="area" class="form-label">Area *</label>
          <select id="area" name="area" class="form-control" required>
            <option value="">-- Select Area --</option>
            <?php foreach($areas as $a): ?>
              <option value="<?php echo $a['area_id']; ?>"><?php echo htmlspecialchars($a['area_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="street" class="form-label">Street *</label>
          <select id="street" name="street" class="form-control" required>
            <option value="">-- Select Street --</option>
            <!-- This dropdown will be dynamically populated based on the selected area -->
          </select>
        </div>
        <div class="mb-3">
          <label for="pincode" class="form-label">Pincode *</label>
          <input type="text" id="pincode" name="pincode" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="latitude" class="form-label">Latitude (Optional)</label>
          <input type="text" id="latitude" name="latitude" class="form-control">
        </div>
        <div class="mb-3">
          <label for="longitude" class="form-label">Longitude (Optional)</label>
          <input type="text" id="longitude" name="longitude" class="form-control">
        </div>
        <button type="submit" class="btn-custom">Register</button>
      </form>
    </div>
  </div>
  
  <!-- jQuery and Bootstrap Bundle with Popper -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // When the Area dropdown changes, fetch the Streets for that Area.
    $(document).ready(function(){
      $("#area").change(function(){
        var areaId = $(this).val();
        if(areaId != ""){
          $.ajax({
            url: "fetch_streets.php",
            type: "GET",
            data: { area_id: areaId },
            success: function(data){
              $("#street").html(data);
            },
            error: function(){
              $("#street").html('<option value="">-- Select Street --</option>');
            }
          });
        } else {
          $("#street").html('<option value="">-- Select Street --</option>');
        }
      });
    });
  </script>
</body>
</html>
