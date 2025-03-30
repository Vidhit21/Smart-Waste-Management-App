<?php
session_start();
require 'db_connect.php'; // This should create a $conn variable

// Ensure the resident is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the resident_id from the Residents table using user_id
$stmt = $conn->prepare("SELECT resident_id FROM Residents WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($resident_id);
    $stmt->fetch();
} else {
    die("Access denied. You are not registered as a resident.");
}
$stmt->close();

$report_error = $report_success = "";
$bulk_error = $bulk_success = "";

// Process Issue Report Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_report'])) {
    $issue_type  = trim($_POST['issue_type']);
    $description = trim($_POST['description']);
    // If "Other" is selected, require additional details
    if ($issue_type === "Other") {
        $other_issue = trim($_POST['other_issue']);
        if (empty($other_issue)) {
            $report_error = "Please provide details for 'Other' issue type.";
        } else {
            // Append the additional information to the description
            $description = "Other - " . $other_issue . "\n" . $description;
        }
    }
    if (empty($issue_type) || empty($description)) {
        $report_error = "Please fill in all required fields for reporting an issue.";
    } else {
        $stmt = $conn->prepare("INSERT INTO IssueReports (resident_id, issue_type, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $resident_id, $issue_type, $description);
        if ($stmt->execute()) {
            $report_success = "Report submitted successfully.";
        } else {
            $report_error = "Error submitting report. Please try again.";
        }
        $stmt->close();
    }
}

// Process Bulk Pickup Request Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_bulk_request'])) {
    $request_details = trim($_POST['request_details']);
    if (empty($request_details)) {
        $bulk_error = "Please provide details for the bulk pickup request.";
    } else {
        $stmt = $conn->prepare("INSERT INTO BulkPickupRequests (resident_id, request_details) VALUES (?, ?)");
        $stmt->bind_param("is", $resident_id, $request_details);
        if ($stmt->execute()) {
            $bulk_success = "Bulk pickup request submitted successfully.";
        } else {
            $bulk_error = "Error submitting bulk request. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Report Issue & Bulk Request | Jamnagar Smart Waste</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      body { padding-top: 70px; }
      .form-section { margin-bottom: 40px; }
    </style>
  </head>
  <body>
    <!-- Include Navbar -->
    <?php include 'nav.php'; ?>

    <div class="container">
      <!-- Issue Report Section -->
      <div class="form-section">
        <h1 class="mt-4 mb-4">Report an Issue</h1>
        <?php if (!empty($report_error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($report_error); ?></div>
        <?php endif; ?>
        <?php if (!empty($report_success)): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($report_success); ?></div>
        <?php endif; ?>
        <form method="POST" action="report.php">
          <div class="mb-3">
            <label for="issue_type" class="form-label">Issue Type</label>
            <select class="form-select" id="issue_type" name="issue_type" required>
              <option value="">Select an Issue Type</option>
              <option value="Missed Pickup">Missed Pickup</option>
              <option value="Improper Waste Disposal">Improper Waste Disposal</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <!-- Extra details for "Other" will be shown if selected -->
          <div class="mb-3" id="otherIssueDiv" style="display:none;">
            <label for="other_issue" class="form-label">Please describe your issue</label>
            <textarea class="form-control" id="other_issue" name="other_issue" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Additional Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" placeholder="Provide any additional details"></textarea>
          </div>
          <button type="submit" name="submit_report" class="btn btn-primary">Submit Report</button>
        </form>
      </div>

      <!-- Bulk Pickup Request Section -->
      <div class="form-section">
        <h1 class="mt-4 mb-4">Bulk Pickup Request</h1>
        <?php if (!empty($bulk_error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($bulk_error); ?></div>
        <?php endif; ?>
        <?php if (!empty($bulk_success)): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($bulk_success); ?></div>
        <?php endif; ?>
        <form method="POST" action="report.php">
          <div class="mb-3">
            <label for="request_details" class="form-label">Request Details</label>
            <textarea class="form-control" id="request_details" name="request_details" rows="5" placeholder="Describe your bulk pickup request"></textarea>
          </div>
          <button type="submit" name="submit_bulk_request" class="btn btn-primary">Submit Bulk Request</button>
        </form>
      </div>

      <!-- Display Previous Issue Reports -->
      <div class="form-section">
        <h2>Your Previous Issue Reports</h2>
        <?php
        // Re-open connection to fetch previous reports
        require 'db_connect.php';
        $reports = [];
        $stmt = $conn->prepare("SELECT report_id, issue_type, description, status, reported_at 
                                FROM IssueReports 
                                WHERE resident_id = ? 
                                ORDER BY reported_at DESC");
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        $stmt->close();
        ?>
        <?php if (count($reports) > 0): ?>
          <table class="table table-bordered mt-3">
            <thead class="table-light">
              <tr>
                <th>Report ID</th>
                <th>Issue Type</th>
                <th>Description</th>
                <th>Status</th>
                <th>Reported At</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reports as $rep): ?>
                <tr>
                  <td><?php echo htmlspecialchars($rep['report_id']); ?></td>
                  <td><?php echo htmlspecialchars($rep['issue_type']); ?></td>
                  <td><?php echo htmlspecialchars($rep['description']); ?></td>
                  <td><?php echo htmlspecialchars($rep['status']); ?></td>
                  <td><?php echo htmlspecialchars($rep['reported_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="text-muted">You have not submitted any issue reports yet.</p>
        <?php endif; ?>
      </div>

      <!-- Display Previous Bulk Pickup Requests -->
      <div class="form-section">
        <h2>Your Previous Bulk Pickup Requests</h2>
        <?php
        // Re-open connection to fetch bulk requests
        require 'db_connect.php';
        $bulkRequests = [];
        $stmt = $conn->prepare("SELECT request_id, request_details, status, request_date 
                                FROM BulkPickupRequests 
                                WHERE resident_id = ? 
                                ORDER BY request_date DESC");
        $stmt->bind_param("i", $resident_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $bulkRequests[] = $row;
        }
        $stmt->close();
        $conn->close();
        ?>
        <?php if (count($bulkRequests) > 0): ?>
          <table class="table table-bordered mt-3">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Request Details</th>
                <th>Status</th>
                <th>Requested At</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bulkRequests as $bulk): ?>
                <tr>
                  <td><?php echo htmlspecialchars($bulk['request_id']); ?></td>
                  <td><?php echo htmlspecialchars($bulk['request_details']); ?></td>
                  <td><?php echo htmlspecialchars($bulk['status']); ?></td>
                  <td><?php echo htmlspecialchars($bulk['request_date']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="text-muted">You have not submitted any bulk pickup requests yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Toggle "Other" text area based on issue type selection
      document.getElementById('issue_type').addEventListener('change', function () {
        if (this.value === 'Other') {
          document.getElementById('otherIssueDiv').style.display = 'block';
        } else {
          document.getElementById('otherIssueDiv').style.display = 'none';
        }
      });
    </script>
  </body>
</html>
