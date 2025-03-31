<?php
session_start();
include('navbar.php');
include('db_connection.php');

// Optionally, you can restrict access here by checking for a specific user role.

// Query for Assigned Tasks
$assignedTasksSql = "SELECT ta.assignment_id, ta.assigned_date, ta.details, 
                      u.name AS worker_name, 
                      CONCAT(addr.street, ', ', addr.division, ', ', addr.pincode) AS address 
                      FROM TaskAssignments ta
                      JOIN Workers w ON ta.worker_id = w.worker_id
                      JOIN Users u ON w.user_id = u.user_id
                      LEFT JOIN Address addr ON ta.address_id = addr.address_id
                      ORDER BY ta.assigned_date DESC";
$assignedTasksResult = $conn->query($assignedTasksSql);

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

// Query for Optimized Routes
$routesSql = "SELECT r.route_id, r.optimized_at, r.location_ids, u.name AS worker_name
              FROM WasteCollectionRoute r
              JOIN Workers w ON r.worker_id = w.worker_id
              JOIN Users u ON w.user_id = u.user_id
              ORDER BY r.optimized_at DESC";
$routesResult = $conn->query($routesSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Operations Overview</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
  <h1 class="mb-4">Operations Overview</h1>
  
  <!-- Section: Assigned Tasks -->
  <section>
    <div class="d-flex justify-content-between align-items-center">
      <h2>Assigned Tasks</h2>
      <a href="assign-tasks.php" class="btn btn-link">View Details</a>
    </div>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Assignment ID</th>
          <th>Worker</th>
          <th>Location</th>
          <th>Details</th>
          <th>Assigned Date</th>
        </tr>
      </thead>
      <tbody>
      <?php
      if ($assignedTasksResult && $assignedTasksResult->num_rows > 0) {
          while ($row = $assignedTasksResult->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['assignment_id'] . "</td>";
              echo "<td>" . htmlspecialchars($row['worker_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['address']) . "</td>";
              echo "<td>" . htmlspecialchars($row['details']) . "</td>";
              echo "<td>" . $row['assigned_date'] . "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='5'>No task assignments found.</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </section>
  
  <!-- managem area_management.php -->
   <section class="mt-5">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Manage Areas</h2>
      <a href="area_management.php" class="btn btn-link">View Details</a>
    </div>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Area ID</th>
          <th>Area Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
      if ($areasResult && $areasResult->num_rows > 0) {
          while ($row = $areasResult->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['area_id'] . "</td>";
              echo "<td>" . htmlspecialchars($row['area_name']) . "</td>";
              echo "<td><a href='edit_area.php?id=" . $row['area_id'] . "' class='btn btn-primary'>Edit</a></td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='3'>No areas found.</td></tr>";
      }
      ?>
      </tbody>
    </table>
   </section>

  <!-- manage schedule -->
  
  <section class="mt-5">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Manage Waste Collection Schedule</h2>
      <a href="manage-schedule.php" class="btn btn-link">View Details</a>
    </div>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Schedule ID</th>
          <th>Location</th>
          <th>Collection Day</th>
          <th>Time Slot</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
      <?php
      if ($scheduleResult && $scheduleResult->num_rows > 0) {
          while ($row = $scheduleResult->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['schedule_id'] . "</td>";
              echo "<td>" . htmlspecialchars($row['address']) . "</td>";
              echo "<td>" . htmlspecialchars($row['collection_day']) . "</td>";
              echo "<td>" . htmlspecialchars($row['time_slot']) . "</td>";
              echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='5'>No schedules found.</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </section>


  <!-- Section: Collection Tasks Tracking -->
  <section class="mt-5">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Collection Tasks Tracking</h2>
      <a href="track-collection.php" class="btn btn-link">View Details</a>
    </div>
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
      <?php
      if ($collectionTasksResult && $collectionTasksResult->num_rows > 0) {
          while ($row = $collectionTasksResult->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['task_id'] . "</td>";
              echo "<td>" . htmlspecialchars($row['worker_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['address']) . "</td>";
              echo "<td>" . $row['scheduled_date'] . "</td>";
              echo "<td>" . $row['scheduled_time'] . "</td>";
              echo "<td>" . $row['status'] . "</td>";
              echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='7'>No collection tasks found.</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </section>
  
  <!-- Section: Optimized Routes -->
  <section class="mt-5">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Optimized Routes</h2>
      <a href="optimize-routes.php" class="btn btn-link">View Details</a>
    </div>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Route ID</th>
          <th>Worker</th>
          <th>Route Details</th>
          <th>Optimized At</th>
        </tr>
      </thead>
      <tbody>
      <?php
      if ($routesResult && $routesResult->num_rows > 0) {
          while ($row = $routesResult->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['route_id'] . "</td>";
              echo "<td>" . htmlspecialchars($row['worker_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['location_ids']) . "</td>";
              echo "<td>" . $row['optimized_at'] . "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='4'>No optimized routes found.</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </section>
  
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
