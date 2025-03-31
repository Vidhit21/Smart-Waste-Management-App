<?php
session_start();
include('navbar.php');
include('db_connection.php');

// Optionally, restrict access based on user role

// Query for Collection Tasks Tracking
$collectionTasksSql = "SELECT ct.task_id, ct.scheduled_date, ct.scheduled_time, ct.status, ct.remarks, 
                        u.name AS worker_name,
                        CONCAT(addr.street, ', ', addr.division, ', ', addr.pincode) AS address 
                        FROM CollectionTasks ct
                        JOIN Workers w ON ct.worker_id = w.worker_id
                        JOIN Users u ON w.user_id = u.user_id
                        LEFT JOIN Address addr ON ct.address_id = addr.address_id
                        ORDER BY ct.scheduled_date DESC";
$collectionTasksResult = $conn->query($collectionTasksSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Collection</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Track Collection</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Worker</th>
                <th>Location</th>
                <th>Scheduled Date</th>
                <th>Scheduled Time</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($collectionTasksResult && $collectionTasksResult->num_rows > 0): ?>
                <?php while ($row = $collectionTasksResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['task_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['worker_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo $row['scheduled_date']; ?></td>
                        <td><?php echo $row['scheduled_time']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No collection tasks found.</td>
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
