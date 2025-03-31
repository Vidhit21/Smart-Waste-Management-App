<?php
require_once 'db_connection.php';

// Set today's and yesterday's date
$today = date("Y-m-d");
$yesterday = date("Y-m-d", strtotime("-1 day"));

// ===== Waste Collected Today =====
$query = "SELECT SUM(weight_kg) AS total_waste FROM WasteData WHERE waste_date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalWasteTodayKg = $row['total_waste'] ? $row['total_waste'] : 0;
$totalWasteTodayT = round($totalWasteTodayKg / 1000, 1); // convert kg to T
$stmt->close();

// Waste Collected Yesterday
$query = "SELECT SUM(weight_kg) AS total_waste FROM WasteData WHERE waste_date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $yesterday);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalWasteYesterdayKg = $row['total_waste'] ? $row['total_waste'] : 0;
$totalWasteYesterdayT = round($totalWasteYesterdayKg / 1000, 1);
$stmt->close();

// Calculate trend percentage change (avoid division by zero)
if ($totalWasteYesterdayT > 0) {
  $wasteTrendPercentage = round((($totalWasteTodayT - $totalWasteYesterdayT) / $totalWasteYesterdayT) * 100);
} else {
  $wasteTrendPercentage = 0;
}

// Calculate progress bar width based on a target (e.g., 20T)
$targetWasteT = 20;
$progressWidth = min(100, ($totalWasteTodayT / $targetWasteT) * 100);

// ===== Pending Tasks =====
// Using CollectionTasks where status is 'Assigned'
$query = "SELECT COUNT(*) AS total_pending FROM CollectionTasks WHERE status = 'Assigned'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalPendingTasks = $row['total_pending'] ? $row['total_pending'] : 0;
$stmt->close();

// Simulate a breakdown: assume 35% are high priority, remainder regular
$highPriority = round($totalPendingTasks * 0.35);
$regularPriority = $totalPendingTasks - $highPriority;

// ===== Reports Resolved =====
$query = "SELECT COUNT(*) AS resolved FROM IssueReports WHERE status = 'Resolved'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$reportsResolved = $row['resolved'] ? $row['resolved'] : 0;
$stmt->close();

// Total reports for resolution rate calculation
$query = "SELECT COUNT(*) AS total_reports FROM IssueReports";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalReports = $row['total_reports'] ? $row['total_reports'] : 0;
$stmt->close();

$resolutionRate = ($totalReports > 0) ? round(($reportsResolved / $totalReports) * 100) : 0;

// ===== Collection Efficiency =====
// Use CollectionTasks scheduled for today: efficiency = (completed tasks / total tasks) * 100
$query = "SELECT COUNT(*) AS total_tasks FROM CollectionTasks WHERE scheduled_date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalTasksToday = $row['total_tasks'] ? $row['total_tasks'] : 0;
$stmt->close();

$query = "SELECT COUNT(*) AS completed_tasks FROM CollectionTasks WHERE scheduled_date = ? AND status = 'Completed'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$completedTasksToday = $row['completed_tasks'] ? $row['completed_tasks'] : 0;
$stmt->close();

$collectionEfficiency = ($totalTasksToday > 0) ? round(($completedTasksToday / $totalTasksToday) * 100) : 0;

// Last Updated time (current time)
$lastUpdated = date("g:i A");

// ---------------------------
// CHART DATA: Weekly Trends
// ---------------------------
$today = date("Y-m-d");
$startDate = date("Y-m-d", strtotime("-6 days"));

// Prepare an array for all 7 days (even if no data exists)
$chartLabels = [];
$chartData = [];
for ($d = 0; $d < 7; $d++) {
  $date = date("Y-m-d", strtotime("-" . (6 - $d) . " days"));
  $dayName = date("D", strtotime($date)); // e.g., Mon, Tue, etc.
  $chartLabels[$date] = $dayName;
  $chartData[$date] = 0;
}

// Query WasteData for the last 7 days
$query = "SELECT waste_date, SUM(weight_kg) AS total_waste 
          FROM WasteData 
          WHERE waste_date BETWEEN ? AND ? 
          GROUP BY waste_date 
          ORDER BY waste_date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $startDate, $today);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $date = $row['waste_date'];
  // Convert kg to Tons (assuming 1000 kg = 1T)
  $chartData[$date] = round($row['total_waste'] / 1000, 1);
}
$stmt->close();

// Re-index arrays for use with Chart.js
$chartLabelsFinal = array_values($chartLabels);
$chartDataFinal = array_values($chartData);

// ---------------------------
// Dynamic Metrics Summary
// ---------------------------
$currentVolume = end($chartDataFinal); // latest day's value (assumed today)
$avgDaily = count($chartDataFinal) ? round(array_sum($chartDataFinal) / count($chartDataFinal), 1) : 0;
$peakVolume = max($chartDataFinal);
$peakDayIndex = array_search($peakVolume, $chartDataFinal);
$peakDay = $chartLabelsFinal[$peakDayIndex];
$trendChange = ($avgDaily > 0) ? round((($currentVolume - $avgDaily) / $avgDaily) * 100) : 0;

// Simulate projected trend as 10% higher than actual values
$projectedDataFinal = array_map(function ($val) {
  return round($val * 1.1, 1);
}, $chartDataFinal);

// ---------------------------
// ACTIVITY FEED DATA
// ---------------------------
// 1. Collection Completed Activity (from CollectionTasks)
$collectionActivity = null;
$query = "SELECT ct.scheduled_date, ct.scheduled_time, ct.remarks, 
                 w.worker_id, u.name AS worker_name, 
                 CONCAT(ar.area_name, ' -> ', s.street_name, ' -> ', a.house_number) AS address_info
          FROM CollectionTasks ct
          JOIN Workers w ON ct.worker_id = w.worker_id
          JOIN Users u ON w.user_id = u.user_id
          JOIN Address a ON ct.address_id = a.address_id
          JOIN Streets s ON a.street_id = s.street_id
          JOIN Areas ar ON s.area_id = ar.area_id
          WHERE ct.status = 'Completed'
          ORDER BY ct.scheduled_date DESC 
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  $collectionActivity = $result->fetch_assoc();
}
$stmt->close();

// 2. Report Resolved Activity (from IssueReports)
$reportResolvedActivity = null;
$query = "SELECT ir.report_id, ir.reported_at, ir.description, r.resident_id, u.name AS resident_name
          FROM IssueReports ir
          JOIN Residents r ON ir.resident_id = r.resident_id
          JOIN Users u ON r.user_id = u.user_id
          WHERE ir.status = 'Resolved'
          ORDER BY ir.reported_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  $reportResolvedActivity = $result->fetch_assoc();
}
$stmt->close();

// 3. New Alert Activity (from IssueReports with status 'Pending')
$newAlertActivity = null;
$query = "SELECT ir.report_id, ir.reported_at, ir.description, r.resident_id, u.name AS resident_name
          FROM IssueReports ir
          JOIN Residents r ON ir.resident_id = r.resident_id
          JOIN Users u ON r.user_id = u.user_id
          WHERE ir.status = 'Pending'
          ORDER BY ir.reported_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  $newAlertActivity = $result->fetch_assoc();
}
$stmt->close();

// ---------- QUICK ACTIONS ----------
// 1. Pending Assign Requests: Count tasks with status 'Assigned'
$query = "SELECT COUNT(*) AS total_pending FROM CollectionTasks WHERE status = 'Assigned'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalPendingTasks = $row['total_pending'] ? $row['total_pending'] : 0;
$stmt->close();

// 2. Last Report: Get most recent report from EnvironmentalReports
$query = "SELECT report_date FROM EnvironmentalReports ORDER BY report_date DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  $lastReportRow = $result->fetch_assoc();
  $lastReportTime = strtotime($lastReportRow['report_date']);
  $hoursDiff = round((time() - $lastReportTime) / 3600);
  $lastReportDisplay = ($hoursDiff > 0) ? $hoursDiff . "h ago" : "Just now";
} else {
  $lastReportDisplay = "No reports yet";
}
$stmt->close();

// 3. Potential Savings: Simulate a savings percentage (can be dynamically computed)
$potentialSavings = 17;

// 4. Urgent Alerts: Count IssueReports with status 'Pending'
$query = "SELECT COUNT(*) AS urgent_alerts FROM IssueReports WHERE status = 'Pending'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$urgentAlertsCount = $row['urgent_alerts'] ? $row['urgent_alerts'] : 0;
$stmt->close();

// ---------- LIVE TRACKING MAP ----------
// Count active vehicles: distinct worker_ids from CollectionTasks with status 'Assigned'
$query = "SELECT COUNT(DISTINCT worker_id) AS active_vehicles FROM CollectionTasks WHERE status = 'Assigned'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$activeVehicles = $row['active_vehicles'] ? $row['active_vehicles'] : 0;
$stmt->close();

// Count completed tasks: tasks with status 'Completed'
$query = "SELECT COUNT(*) AS completed_tasks FROM CollectionTasks WHERE status = 'Completed'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$completedTasks = $row['completed_tasks'] ? $row['completed_tasks'] : 0;
$stmt->close();

// For the map overlay, fetch one active task to display a vehicle's info
$query = "SELECT ct.task_id, ct.scheduled_date, ct.scheduled_time, w.worker_id, u.name AS worker_name 
          FROM CollectionTasks ct
          JOIN Workers w ON ct.worker_id = w.worker_id
          JOIN Users u ON w.user_id = u.user_id
          WHERE ct.status = 'Assigned'
          ORDER BY ct.scheduled_date ASC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$activeTask = $result->fetch_assoc();
$stmt->close();

$vehicleNumber = isset($activeTask['worker_id']) ? $activeTask['worker_id'] : 'N/A';
// Since tonnage is not stored in CollectionTasks, we simulate a value:
$vehicleTonnage = "2.4T";

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Municipal Dashboard - Jamnagar Waste Management</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/nav.css" />
  <link rel="stylesheet" href="css/dashboard.css" />
</head>

<body>
  <!-- Navbar Section -->
  <?php include 'navbar.php'; ?>

  <div class="main-content">
    <div class="container-fluid">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Municipal Dashboard</h3>
        <div class="text-muted">Last Updated: <?php echo $lastUpdated; ?></div>
      </div>

      <!-- Stats Cards Section -->
      <div class="row g-4">
        <!-- Waste Collected Card -->
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stats-card">
            <div class="card-header">
              <div class="icon-container bg-primary">
                <i class="bi bi-trash3"></i>
              </div>
              <div class="trend-indicator positive">
                <i class="bi bi-arrow-up-short"></i> <?php echo $wasteTrendPercentage; ?>%
              </div>
            </div>
            <div class="card-content">
              <h3><?php echo $totalWasteTodayT; ?><span>T</span></h3>
              <p>Waste Collected Today</p>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo $progressWidth; ?>%"></div>
              </div>
              <div class="comparison-text">
                <small>vs <?php echo $totalWasteYesterdayT; ?>T yesterday</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Tasks Card -->
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stats-card">
            <div class="card-header">
              <div class="icon-container bg-warning">
                <i class="bi bi-exclamation-octagon"></i>
              </div>
              <div class="badge urgent">URGENT</div>
            </div>
            <div class="card-content">
              <h3><?php echo $totalPendingTasks; ?></h3>
              <p>Pending Tasks</p>
              <div class="task-breakdown">
                <div class="task-type">
                  <span class="dot bg-primary"></span>
                  <small><?php echo $highPriority; ?> High Priority</small>
                </div>
                <div class="task-type">
                  <span class="dot bg-secondary"></span>
                  <small><?php echo $regularPriority; ?> Regular</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Reports Resolved Card -->
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stats-card">
            <div class="card-header">
              <div class="icon-container bg-success">
                <i class="bi bi-check2-circle"></i>
              </div>
              <div class="trend-indicator positive">
                <i class="bi bi-graph-up-arrow"></i>
              </div>
            </div>
            <div class="card-content">
              <h3><?php echo $reportsResolved; ?></h3>
              <p>Reports Resolved</p>
              <div class="resolution-rate">
                <div class="rate-progress">
                  <div class="fill" style="width: <?php echo $resolutionRate; ?>%"></div>
                </div>
                <small><?php echo $resolutionRate; ?>% Resolution Rate</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Collection Efficiency Card -->
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stats-card">
            <div class="card-header">
              <div class="icon-container bg-info">
                <i class="bi bi-speedometer2"></i>
              </div>
              <div class="efficiency-meter">
                <div class="gauge" style="--value:<?php echo $collectionEfficiency; ?>; --gauge-color: #17a2b8;">
                  <div class="percentage"><?php echo $collectionEfficiency; ?>%</div>
                </div>
              </div>
            </div>
            <div class="card-content">
              <h3><?php echo $collectionEfficiency; ?><span>%</span></h3>
              <p>Collection Efficiency</p>
              <div class="performance-status">
                <i class="bi bi-star-fill text-warning"></i>
                <small>Excellent Performance</small>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Charts Section -->
      <div class="row g-4 mt-4">
        <div class="col-12 col-lg-8">
          <div class="advanced-chart-container">
            <!-- Chart Header -->
            <div class="chart-header">
              <div class="chart-title-group">
                <h4 class="chart-title">Waste Collection Analytics</h4>
                <p class="chart-subtitle">Weekly Collection Trends &amp; Projections</p>
              </div>
              <div class="chart-controls">
                <div class="btn-group chart-filter">
                  <button type="button" class="btn btn-sm btn-time active" data-period="week">7D</button>
                  <button type="button" class="btn btn-sm btn-time" data-period="month">30D</button>
                  <button type="button" class="btn btn-sm btn-time" data-period="quarter">90D</button>
                </div>
              </div>
            </div>

            <!-- Chart Metrics Summary -->
            <div class="chart-metrics">
              <div class="metric-item">
                <span class="metric-label">Current Volume</span>
                <span class="metric-value"><?php echo $currentVolume; ?>T
                  <span
                    class="metric-change positive"><?php echo ($trendChange >= 0 ? '↑' : '↓') . abs($trendChange); ?>%</span>
                </span>
              </div>
              <div class="metric-item">
                <span class="metric-label">Avg. Daily</span>
                <span class="metric-value"><?php echo $avgDaily; ?>T</span>
              </div>
              <div class="metric-item">
                <span class="metric-label">Peak Day</span>
                <span class="metric-value"><?php echo $peakDay; ?> <span
                    class="metric-highlight"><?php echo $peakVolume; ?>T</span></span>
              </div>
            </div>

            <!-- Chart Canvas -->
            <div class="chart-wrapper">
              <canvas id="collectionChart"></canvas>
            </div>

            <!-- Chart Footer -->
            <div class="chart-footer">
              <div class="chart-legend">
                <div class="legend-item">
                  <span class="legend-color primary"></span>
                  Actual Collection
                </div>
                <div class="legend-item">
                  <span class="legend-color projected"></span>
                  Projected Trend
                </div>
              </div>
              <div class="chart-source">
                <i class="bi bi-info-circle"></i> Data updated 15 minutes ago
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="col-12 col-lg-4">
          <div class="activity-feed">
            <!-- Header with Filter -->
            <div class="feed-header d-flex justify-content-between align-items-center mb-4">
              <h4 class="feed-title">Activity Feed</h4>
              <div class="feed-filter">
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-filter active">All</button>
                  <button type="button" class="btn btn-sm btn-filter">Alerts</button>
                  <button type="button" class="btn btn-sm btn-filter">Tasks</button>
                </div>
              </div>
            </div>

            <!-- Activity Timeline -->
            <div class="activity-timeline">
              <?php if ($collectionActivity): ?>
                <!-- Collection Completed Activity -->
                <div class="activity-item collection-completed">
                  <div class="activity-icon">
                    <i class="bi bi-truck"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-header">
                      <span class="activity-time">
                        <?php
                        // Format the scheduled date/time (example: "15 mins ago")
                        echo date("i \m\i\n\s \a\g\o", strtotime($collectionActivity['scheduled_date']));
                        ?>
                      </span>
                      <span class="activity-badge completed">Completed</span>
                    </div>
                    <h6 class="activity-title">Collection Update at <?php echo $collectionActivity['address_info']; ?></h6>
                    <p class="activity-details">
                      Vehicle #<?php echo $collectionActivity['worker_id']; ?> collected <?php echo $currentVolume; ?>T
                      waste
                      <span class="activity-meta">
                        <i class="bi bi-clock-history"></i> 18 mins early
                      </span>
                    </p>
                    <div class="activity-progress">
                      <div class="progress-text">Route Completion</div>
                      <div class="progress-bar" style="width: 92%"></div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($reportResolvedActivity): ?>
                <!-- Report Resolved Activity -->
                <div class="activity-item report-resolved">
                  <div class="activity-icon">
                    <i class="bi bi-check2-circle"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-header">
                      <span
                        class="activity-time"><?php echo date("g:i A", strtotime($reportResolvedActivity['reported_at'])); ?></span>
                      <span class="activity-badge resolved">Resolved</span>
                    </div>
                    <h6 class="activity-title">Report #<?php echo $reportResolvedActivity['report_id'] ?? 'N/A'; ?> Closed
                    </h6>
                    <p class="activity-details">
                      <?php echo $reportResolvedActivity['description']; ?>
                      <span class="activity-meta">
                        <i class="bi bi-person-check"></i> Verified by Inspector
                      </span>
                    </p>
                    <div class="activity-stats">
                      <div class="stat-item">
                        <i class="bi bi-calendar-check"></i> 3 days resolution time
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($newAlertActivity): ?>
                <!-- New Alert Activity -->
                <div class="activity-item new-alert">
                  <div class="activity-icon">
                    <i class="bi bi-exclamation-octagon"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-header">
                      <span
                        class="activity-time"><?php echo date("g:i A", strtotime($newAlertActivity['reported_at'])); ?></span>
                      <span class="activity-badge urgent">Urgent</span>
                    </div>
                    <h6 class="activity-title">New Complaint Received</h6>
                    <p class="activity-details">
                      <?php echo $newAlertActivity['description']; ?>
                      <span class="activity-meta">
                        <i class="bi bi-geo-alt"></i> Near City Market
                      </span>
                    </p>
                    <div class="activity-act  ions">
                      <button class="btn btn-action">
                        <i class="bi bi-eye"></i> View Details
                      </button>
                      <button class="btn btn-action">
                        <i class="bi bi-check2"></i> Acknowledge
                      </button>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <!-- Timeline Connector -->
            <div class="timeline-connector"></div>

            <!-- View All Link -->
            <div class="text-end mt-3">
              <a href="#" class="view-all-link">
                View All Activities <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions & Live Tracking Section -->
      <div class="row g-4 mt-4">
        <!-- Enhanced Quick Actions -->
        <div class="col-12 col-lg-4">
          <div class="quick-actions-panel">
            <div class="panel-header d-flex justify-content-between align-items-center mb-4">
              <h4 class="panel-title">Quick Actions</h4>
              <i class="bi bi-lightning-charge fs-5 text-primary"></i>
            </div>

            <div class="action-grid">
              <!-- Assign Task Card -->
              <div class="action-card assign-task">
                <div class="card-icon">
                  <i class="bi bi-person-add"></i>
                </div>
                <div class="card-content">
                  <h5>Assign Task</h5>
                  <p>Create new collection assignments</p>
                  <div class="action-progress">
                    <span><?php echo $totalPendingTasks; ?> pending requests</span>
                    <!-- For demo, the progress width is scaled (e.g., 20% per request up to 100%) -->
                    <div class="progress-bar" style="width: <?php echo min(100, $totalPendingTasks * 20); ?>%"></div>
                  </div>
                </div>
                <button class="action-button">Assign Now</button>
              </div>

              <!-- Generate Report Card -->
              <div class="action-card generate-report">
                <div class="card-icon">
                  <i class="bi bi-file-bar-graph"></i>
                </div>
                <div class="card-content">
                  <h5>Generate Report</h5>
                  <p>Daily performance analytics</p>
                  <div class="action-meta">
                    <span class="badge completed">Last report: <?php echo $lastReportDisplay; ?></span>
                  </div>
                </div>
                <button class="action-button">Create Report</button>
              </div>

              <!-- Optimize Routes Card -->
              <div class="action-card optimize-routes">
                <div class="card-icon">
                  <i class="bi bi-signpost-split"></i>
                </div>
                <div class="card-content">
                  <h5>Optimize Routes</h5>
                  <p>AI-powered route optimization</p>
                  <div class="action-meta">
                    <span class="badge warning">Potential savings: <?php echo $potentialSavings; ?>%</span>
                  </div>
                </div>
                <button class="action-button">Optimize Now</button>
              </div>

              <!-- View Alerts Card -->
              <div class="action-card view-alerts">
                <div class="card-icon">
                  <i class="bi bi-bell"></i>
                </div>
                <div class="card-content">
                  <h5>System Alerts</h5>
                  <p>Active notifications &amp; warnings</p>
                  <div class="action-meta">
                    <span class="badge danger"><?php echo $urgentAlertsCount; ?> urgent alerts</span>
                  </div>
                </div>
                <button class="action-button">View Alerts</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Live Tracking Map -->
        <div class="col-12 col-lg-8">
          <div class="live-tracking-container">
            <div class="map-header d-flex justify-content-between align-items-center p-3">
              <h5 class="map-title mb-0">Live Collection Tracking</h5>
              <div class="map-stats">
                <span class="stat-item">
                  <i class="bi bi-truck"></i> <?php echo $activeVehicles; ?> Active
                </span>
                <span class="stat-item">
                  <i class="bi bi-check-circle"></i> <?php echo $completedTasks; ?> Completed
                </span>
              </div>
            </div>

            <div class="map-wrapper">
              <!-- Interactive Map Placeholder -->
              <div class="advanced-map-placeholder">
                <!-- Map Overlay Elements -->
                <div class="map-vehicle" role="img" aria-label="Collection vehicle #<?php echo $vehicleNumber; ?>"
                  style="top: 30%; left: 25%">
                  <i class="bi bi-truck"></i>
                  <div class="vehicle-info">#<?php echo $vehicleNumber; ?> • <?php echo $vehicleTonnage; ?></div>
                </div>
                <div class="map-route" style="top: 35%; left: 20%"></div>
                <div class="map-zone" style="top: 40%; left: 30%">Zone 5</div>

                <!-- Map Legend -->
                <div class="map-legend">
                  <div class="legend-item">
                    <i class="bi bi-truck"></i> Collection Vehicle
                  </div>
                  <div class="legend-item">
                    <i class="bi bi-geo-alt"></i> Active Zone
                  </div>
                </div>

                <!-- Zoom Controls -->
                <div class="map-controls">
                  <button class="control-btn" aria-label="Zoom in">
                    <i class="bi bi-plus"></i>
                  </button>
                  <button class="control-btn">
                    <i class="bi bi-dash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('collectionChart').getContext('2d');
    const collectionChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($chartLabelsFinal); ?>,
        datasets: [
          {
            label: 'Actual Collection',
            data: <?php echo json_encode($chartDataFinal); ?>,
            borderColor: '#2A5C82',
            backgroundColor: 'rgba(42, 92, 130, 0.1)',
            fill: true,
            tension: 0.4
          },
          {
            label: 'Projected Trend',
            data: <?php echo json_encode($projectedDataFinal); ?>,
            borderColor: '#FFC107',
            backgroundColor: 'rgba(255, 193, 7, 0.1)',
            borderDash: [5, 5],
            fill: false,
            tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return value + 'T';
              }
            }
          }
        }
      }
    });
  </script>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>