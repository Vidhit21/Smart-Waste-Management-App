<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Verify that the logged in user is an Authority.
// It assumes that an entry in the Authorities table exists for authority users.
$query = "SELECT authority_id FROM Authorities WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>You do not have permission to assign tasks.</div>";
    exit;
}

$row = $result->fetch_assoc();
$authority_id = $row['authority_id'];

// Initialize messages
$success_message = "";
$error_message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $worker_id = isset($_POST['worker_id']) ? intval($_POST['worker_id']) : 0;
    $address_id = isset($_POST['address_id']) ? intval($_POST['address_id']) : 0;
    $details = isset($_POST['details']) ? trim($_POST['details']) : "";

    if ($worker_id && $address_id && !empty($details)) {
        // Insert the new task into TaskAssignments
        $sql = "INSERT INTO TaskAssignments (authority_id, worker_id, address_id, details) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $error_message = "Prepare failed: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("iiis", $authority_id, $worker_id, $address_id, $details);
            if ($stmt->execute()) {
                $success_message = "Task assigned successfully.";
            } else {
                $error_message = "Error: " . htmlspecialchars($stmt->error);
            }
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}

// Fetch list of workers (joining Workers with Users to display names)
$workers = [];
$workerQuery = "SELECT w.worker_id, u.name FROM Workers w JOIN Users u ON w.user_id = u.user_id";
$workerResult = $conn->query($workerQuery);
if ($workerResult && $workerResult->num_rows > 0) {
    while ($row = $workerResult->fetch_assoc()) {
        $workers[] = $row;
    }
}

// Fetch list of addresses to assign as task locations
$addresses = [];
$addressQuery = "SELECT address_id, CONCAT(street, ', ', division, ', ', pincode) AS address FROM Address";
$addressResult = $conn->query($addressQuery);
if ($addressResult && $addressResult->num_rows > 0) {
    while ($row = $addressResult->fetch_assoc()) {
        $addresses[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Task</title>
    <!-- Include any CSS libraries like Bootstrap if needed -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php 
include('navbar.php'); ?>
<div class="container mt-4">
    <h2>Assign Task to Worker</h2>
    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>
    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>
    <form method="post" action="assign-tasks.php">
        <div class="form-group">
            <label for="worker_id">Select Worker</label>
            <select name="worker_id" id="worker_id" class="form-control" required>
                <option value="">--Select Worker--</option>
                <?php foreach ($workers as $worker) { ?>
                    <option value="<?php echo $worker['worker_id']; ?>">
                        <?php echo htmlspecialchars($worker['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="address_id">Select Address</label>
            <select name="address_id" id="address_id" class="form-control" required>
                <option value="">--Select Address--</option>
                <?php foreach ($addresses as $address) { ?>
                    <option value="<?php echo $address['address_id']; ?>">
                        <?php echo htmlspecialchars($address['address']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="details">Task Details</label>
            <textarea name="details" id="details" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Assign Task</button>
    </form>
</div>
<!-- Optionally include JS libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
