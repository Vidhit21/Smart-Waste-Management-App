<?php
session_start();
include('navbar.php');
include('db_connection.php');

// Process deletion if requested via GET parameter
if (isset($_GET['delete_schedule'])) {
    $delete_id = intval($_GET['delete_schedule']);
    $stmt = $conn->prepare("DELETE FROM WasteCollectionSchedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $delete_message = "Schedule deleted successfully.";
    } else {
        $delete_error = "Error deleting schedule: " . $stmt->error;
    }
    $stmt->close();
}

// Process form submission to add a new schedule
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_schedule'])) {
    $address_id = intval($_POST['address_id']);
    $collection_day = $_POST['collection_day']; // Expected: Monday, Tuesday, etc.
    $time_slot = $_POST['time_slot'];           // Format: HH:MM (24-hour)
    $notes = trim($_POST['notes']);

    // Basic validation
    if ($address_id == 0 || empty($collection_day) || empty($time_slot)) {
        $error_message = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO WasteCollectionSchedules (address_id, collection_day, time_slot, notes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $address_id, $collection_day, $time_slot, $notes);
        if ($stmt->execute()) {
            $success_message = "Schedule added successfully.";
        } else {
            $error_message = "Error adding schedule: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch addresses for the dropdown
$addresses = [];
$addressSql = "SELECT address_id, written_address AS address FROM Address";
$addressResult = $conn->query($addressSql);
if ($addressResult && $addressResult->num_rows > 0) {
    while ($row = $addressResult->fetch_assoc()) {
        $addresses[] = $row;
    }
}

// Fetch all schedules to display
$scheduleSql = "SELECT s.schedule_id, s.collection_day, s.time_slot, s.notes, 
                       a.written_address AS address
                FROM WasteCollectionSchedules s
                LEFT JOIN Address a ON s.address_id = a.address_id
                ORDER BY FIELD(s.collection_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), s.time_slot";
$scheduleResult = $conn->query($scheduleSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Waste Collection Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Manage Waste Collection Schedule</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <?php if (isset($delete_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($delete_message); ?></div>
    <?php endif; ?>
    <?php if (isset($delete_error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($delete_error); ?></div>
    <?php endif; ?>

    <!-- Form to add new schedule -->
    <div class="card mb-4">
        <div class="card-header">
            Add New Schedule
        </div>
        <div class="card-body">
            <form method="post" action="manage-schedule.php">
                <div class="form-group">
                    <label for="address_id">Location (Address)</label>
                    <select name="address_id" id="address_id" class="form-control" required>
                        <option value="">-- Select Address --</option>
                        <?php foreach ($addresses as $address): ?>
                            <option value="<?php echo $address['address_id']; ?>">
                                <?php echo htmlspecialchars($address['address']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="collection_day">Collection Day</label>
                    <select name="collection_day" id="collection_day" class="form-control" required>
                        <option value="">-- Select Day --</option>
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ($days as $day):
                        ?>
                            <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="time_slot">Time Slot</label>
                    <input type="time" name="time_slot" id="time_slot" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" name="add_schedule" class="btn btn-primary mt-3">Add Schedule</button>
            </form>
        </div>
    </div>

    <!-- Table of existing schedules -->
    <h2>Existing Schedules</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Schedule ID</th>
                <th>Location</th>
                <th>Collection Day</th>
                <th>Time Slot</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($scheduleResult && $scheduleResult->num_rows > 0): ?>
                <?php while ($row = $scheduleResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['schedule_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo $row['collection_day']; ?></td>
                        <td><?php echo $row['time_slot']; ?></td>
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        <td>
                            <!-- Edit link (optional: you can create edit-schedule.php for editing) -->
                            <a href="edit-schedule.php?schedule_id=<?php echo $row['schedule_id']; ?>" class="btn btn-sm btn-info">Edit</a>
                            <!-- Delete link -->
                            <a href="manage-schedule.php?delete_schedule=<?php echo $row['schedule_id']; ?>" onclick="return confirm('Are you sure you want to delete this schedule?');" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No schedules found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
