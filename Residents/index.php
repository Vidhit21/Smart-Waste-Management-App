<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch basic user details from Users table
$stmt = $conn->prepare("SELECT name, email, phone, address_id, created_at FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $address_id, $created_at);
$stmt->fetch();
$stmt->close();

// Fetch recycling points from Residents table
$stmt = $conn->prepare("SELECT recycling_points FROM Residents WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($recycling_points);
$stmt->fetch();
$stmt->close();

// Determine current day name (e.g., "Monday")
$current_day = date('l');

// Fetch today's waste collection schedule based on resident's address
$stmt = $conn->prepare("SELECT time_slot, notes FROM WasteCollectionSchedules WHERE address_id = ? AND collection_day = ?");
$stmt->bind_param("is", $address_id, $current_day);
$stmt->execute();
$stmt->store_result();
$hasSchedule = false;
$time_slot = "";
$notes = "";
if ($stmt->num_rows > 0) {
    $stmt->bind_result($time_slot, $notes);
    $stmt->fetch();
    $hasSchedule = true;
}
$stmt->close();

// Fetch latest 5 notifications for the resident
$stmt = $conn->prepare("SELECT message, created_at FROM Notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();

$conn->close();

// Format the join year
$member_since = date("Y", strtotime($created_at));
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resident Home | Jamnagar Smart Waste</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { 
      padding-top: 70px; 
      background-color: #f0f2f5; 
    }
    /* Welcome header styling with gradient background */
    .welcome-card {
      background: linear-gradient(90deg, #4caf50, #81c784);
      color: #fff;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    .welcome-card h1 {
      font-size: 1.8rem;
      margin-bottom: 5px;
    }
    .welcome-card p {
      margin: 0;
      font-size: 0.9rem;
    }
    /* Dashboard card enhancements */
    .dashboard-card {
      border-radius: 10px;
      margin-bottom: 20px;
      transition: transform 0.2s;
      cursor: pointer;
    }
    .dashboard-card:hover {
      transform: translateY(-5px);
    }
    .card-icon {
      font-size: 2.5rem;
      color: #4caf50;
      margin-right: 10px;
    }
    .card-title {
      font-weight: bold;
    }
    .notification-item {
      border-bottom: 1px solid #e0e0e0;
      padding: 10px 0;
    }
    .notification-item:last-child {
      border-bottom: none;
    }
    /* Quick links list */
    .quick-links a {
      flex: 1;
      text-align: center;
      padding: 10px;
      color: #4caf50;
      text-decoration: none;
      border: 1px solid #4caf50;
      border-radius: 5px;
      margin: 5px;
      transition: background-color 0.2s, color 0.2s;
    }
    .quick-links a:hover {
      background-color: #4caf50;
      color: #fff;
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <?php include 'nav.php'; ?>

  <div class="container">
    <!-- Welcome Section -->
    <div class="welcome-card d-flex justify-content-between align-items-center">
      <div>
        <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
        <p>Resident since <?php echo htmlspecialchars($member_since); ?></p>
      </div>
      <div class="text-center">
        <div class="fs-3">
          <i class="bi bi-recycle"></i>
        </div>
        <p class="mb-0">Recycling Points</p>
        <p class="h4 mb-0"><?php echo htmlspecialchars($recycling_points); ?></p>
      </div>
    </div>

    <!-- Dashboard Section -->
    <div class="row">
      <!-- Today's Collection Schedule -->
      <div class="col-md-6">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-calendar-check card-icon"></i>
              <h5 class="card-title mb-0">Today's Collection (<?php echo $current_day; ?>)</h5>
            </div>
            <?php if ($hasSchedule): ?>
              <p class="card-text">Time: <?php echo date("h:i A", strtotime($time_slot)); ?></p>
              <?php if (!empty($notes)): ?>
                <p class="card-text">Notes: <?php echo htmlspecialchars($notes); ?></p>
              <?php endif; ?>
            <?php else: ?>
              <p class="card-text">No collection scheduled for today.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Recent Notifications -->
      <div class="col-md-6">
        <div class="card dashboard-card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2">
              <i class="bi bi-bell card-icon"></i>
              <h5 class="card-title mb-0">Recent Notifications</h5>
            </div>
            <?php if (count($notifications) > 0): ?>
              <div class="list-group">
                <?php foreach ($notifications as $notif): ?>
                  <div class="list-group-item notification-item">
                    <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                    <small class="text-muted"><?php echo date("M d, Y h:i A", strtotime($notif['created_at'])); ?></small>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="card-text">No notifications at this time.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Links Section -->
    <div class="row">
      <div class="col-12">
        <div class="card dashboard-card">
          <div class="card-body">
            <h5 class="card-title">Quick Links</h5>
            <div class="d-flex quick-links flex-wrap">
              <a href="schedule.php"><i class="bi bi-calendar3"></i> Waste Schedule</a>
              <a href="waste_tips.php"><i class="bi bi-lightbulb"></i> Waste Tips</a>
              <a href="support.php"><i class="bi bi-headset"></i> Support</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
