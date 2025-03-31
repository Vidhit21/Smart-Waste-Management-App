<?php
session_start();
include("db_connection.php");

// Optional: Ensure that only municipal authorities can access this page.
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Authority') {
    header("Location: login.php");
    exit;
}

// --------------------
// Process POST requests
// --------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Add Area
    if (isset($_POST['add_area'])) {
        $area_name = trim($_POST['area_name']);
        if (empty($area_name)) {
            $error = "Area name is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Areas (area_name) VALUES (?)");
            $stmt->bind_param("s", $area_name);
            if ($stmt->execute()) {
                $success = "Area added successfully.";
            } else {
                $error = "Error adding area: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Edit Area
    if (isset($_POST['edit_area'])) {
        $area_id = intval($_POST['area_id']);
        $area_name = trim($_POST['area_name']);
        if (empty($area_name)) {
            $error = "Area name cannot be empty.";
        } else {
            $stmt = $conn->prepare("UPDATE Areas SET area_name = ? WHERE area_id = ?");
            $stmt->bind_param("si", $area_name, $area_id);
            if ($stmt->execute()) {
                $success = "Area updated successfully.";
            } else {
                $error = "Error updating area: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Add Street
    if (isset($_POST['add_street'])) {
        $area_id = intval($_POST['area_id']);
        $street_name = trim($_POST['street_name']);
        $sequence_number = intval($_POST['sequence_number']);
        if (empty($street_name)) {
            $error = "Street name is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Streets (area_id, street_name, sequence_number) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $area_id, $street_name, $sequence_number);
            if ($stmt->execute()) {
                $success = "Street added successfully.";
            } else {
                $error = "Error adding street: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Edit Street
    if (isset($_POST['edit_street'])) {
        $street_id = intval($_POST['street_id']);
        $area_id = intval($_POST['area_id']);
        $street_name = trim($_POST['street_name']);
        $sequence_number = intval($_POST['sequence_number']);
        if (empty($street_name)) {
            $error = "Street name cannot be empty.";
        } else {
            $stmt = $conn->prepare("UPDATE Streets SET area_id = ?, street_name = ?, sequence_number = ? WHERE street_id = ?");
            $stmt->bind_param("isii", $area_id, $street_name, $sequence_number, $street_id);
            if ($stmt->execute()) {
                $success = "Street updated successfully.";
            } else {
                $error = "Error updating street: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// ----------------------
// Process GET requests for deletion
// ----------------------
if (isset($_GET['delete_area'])) {
    $area_id = intval($_GET['delete_area']);
    $stmt = $conn->prepare("DELETE FROM Areas WHERE area_id = ?");
    $stmt->bind_param("i", $area_id);
    if ($stmt->execute()) {
        $success = "Area deleted successfully.";
    } else {
        $error = "Error deleting area: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_GET['delete_street'])) {
    $street_id = intval($_GET['delete_street']);
    $stmt = $conn->prepare("DELETE FROM Streets WHERE street_id = ?");
    $stmt->bind_param("i", $street_id);
    if ($stmt->execute()) {
        $success = "Street deleted successfully.";
    } else {
        $error = "Error deleting street: " . $stmt->error;
    }
    $stmt->close();
}

// ----------------------
// Fetch Areas and Streets
// ----------------------
$areas = [];
$result = $conn->query("SELECT area_id, area_name FROM Areas ORDER BY area_name ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $areas[] = $row;
    }
}

$streets = [];
$query = "SELECT s.street_id, s.area_id, s.street_name, s.sequence_number, a.area_name 
          FROM Streets s 
          JOIN Areas a ON s.area_id = a.area_id 
          ORDER BY a.area_name ASC, s.sequence_number ASC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $streets[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Area and Street Management</title>
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include("navbar.php"); // Include your navigation bar here ?> 
    <div class="container mt-4">
        <h1>Area Management</h1>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>"; } ?>
        <?php if (isset($success)) { echo "<div class='alert alert-success'>" . htmlspecialchars($success) . "</div>"; } ?>

        <!-- Form for adding a new area -->
        <div class="card mb-4">
            <div class="card-header">Add New Area</div>
            <div class="card-body">
                <form method="post" action="area_management.php">
                    <div class="form-group">
                        <label for="area_name">Area Name:</label>
                        <input type="text" name="area_name" id="area_name" class="form-control" required>
                    </div>
                    <button type="submit" name="add_area" class="btn btn-primary mt-2">Add Area</button>
                </form>
            </div>
        </div>

        <!-- List existing areas -->
        <h2>Existing Areas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Area Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($areas) > 0): ?>
                    <?php foreach ($areas as $area): ?>
                        <tr>
                            <td><?php echo $area['area_id']; ?></td>
                            <td><?php echo htmlspecialchars($area['area_name']); ?></td>
                            <td>
                                <a href="area_management.php?edit_area=<?php echo $area['area_id']; ?>"
                                    class="btn btn-sm btn-info">Edit</a>
                                <a href="area_management.php?delete_area=<?php echo $area['area_id']; ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this area?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No areas found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
        // If an edit action is requested for area, display the edit form.
        if (isset($_GET['edit_area'])) {
            $edit_area_id = intval($_GET['edit_area']);
            $edit_area_name = "";
            foreach ($areas as $area) {
                if ($area['area_id'] == $edit_area_id) {
                    $edit_area_name = $area['area_name'];
                    break;
                }
            }
            ?>
            <div class="card mt-4">
                <div class="card-header">Edit Area</div>
                <div class="card-body">
                    <form method="post" action="area_management.php">
                        <input type="hidden" name="area_id" value="<?php echo $edit_area_id; ?>">
                        <div class="form-group">
                            <label for="area_name_edit">Area Name:</label>
                            <input type="text" name="area_name" id="area_name_edit" class="form-control"
                                value="<?php echo htmlspecialchars($edit_area_name); ?>" required>
                        </div>
                        <button type="submit" name="edit_area" class="btn btn-success mt-2">Update Area</button>
                    </form>
                </div>
            </div>
        <?php } ?>

        <hr>

        <h1>Manage Streets</h1>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>"; } ?>
        <?php if (isset($success)) { echo "<div class='alert alert-success'>" . htmlspecialchars($success) . "</div>"; } ?>

        <!-- Form for adding a new street -->
        <div class="card mb-4">
            <div class="card-header">Add New Street</div>
            <div class="card-body">
                <form method="post" action="area_management.php">
                    <div class="form-group">
                        <label for="area_id">Area:</label>
                        <select name="area_id" id="area_id" class="form-control" required>
                            <option value="">-- Select Area --</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?php echo $area['area_id']; ?>">
                                    <?php echo htmlspecialchars($area['area_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="street_name">Street Name:</label>
                        <input type="text" name="street_name" id="street_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="sequence_number">Sequence Number:</label>
                        <input type="number" name="sequence_number" id="sequence_number" class="form-control" required>
                    </div>
                    <button type="submit" name="add_street" class="btn btn-primary mt-2">Add Street</button>
                </form>
            </div>
        </div>

        <!-- List existing streets -->
        <h2>Existing Streets</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Street ID</th>
                    <th>Area</th>
                    <th>Street Name</th>
                    <th>Sequence Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($streets) > 0): ?>
                    <?php foreach ($streets as $s): ?>
                        <tr>
                            <td><?php echo $s['street_id']; ?></td>
                            <td><?php echo htmlspecialchars($s['area_name']); ?></td>
                            <td><?php echo htmlspecialchars($s['street_name']); ?></td>
                            <td><?php echo $s['sequence_number']; ?></td>
                            <td>
                                <a href="area_management.php?edit_street=<?php echo $s['street_id']; ?>"
                                    class="btn btn-sm btn-info">Edit</a>
                                <a href="area_management.php?delete_street=<?php echo $s['street_id']; ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this street?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No streets found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
        // If an edit action is requested for street, display the edit form.
        if (isset($_GET['edit_street'])) {
            $edit_street_id = intval($_GET['edit_street']);
            $edit_street = null;
            foreach ($streets as $s) {
                if ($s['street_id'] == $edit_street_id) {
                    $edit_street = $s;
                    break;
                }
            }
            if ($edit_street):
            ?>
            <div class="card mt-4">
                <div class="card-header">Edit Street</div>
                <div class="card-body">
                    <form method="post" action="area_management.php">
                        <input type="hidden" name="street_id" value="<?php echo $edit_street_id; ?>">
                        <div class="form-group">
                            <label for="area_id_edit">Area:</label>
                            <select name="area_id" id="area_id_edit" class="form-control" required>
                                <option value="">-- Select Area --</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?php echo $area['area_id']; ?>" <?php if ($area['area_id'] == $edit_street['area_id']) echo "selected"; ?>>
                                        <?php echo htmlspecialchars($area['area_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="street_name_edit">Street Name:</label>
                            <input type="text" name="street_name" id="street_name_edit" class="form-control"
                                value="<?php echo htmlspecialchars($edit_street['street_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="sequence_number_edit">Sequence Number:</label>
                            <input type="number" name="sequence_number" id="sequence_number_edit" class="form-control"
                                value="<?php echo $edit_street['sequence_number']; ?>" required>
                        </div>
                        <button type="submit" name="edit_street" class="btn btn-success mt-2">Update Street</button>
                    </form>
                </div>
            </div>
            <?php
            endif;
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
