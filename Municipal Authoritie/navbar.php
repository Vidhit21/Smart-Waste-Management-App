<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
$activePage = basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Working Navbar Example</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Custom CSS -->
  <style>
    :root {
      --primary-color: #2A5C82;
      --secondary-color: #4CAF50;
      --accent-color: #FFC107;
      --light-bg: #F8F9FA;
    }
    .navbar-custom {
      background: white;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 0.8rem 1rem;
    }
    .nav-link-custom {
      color: var(--primary-color) !important;
      font-weight: 500;
      padding: 0.5rem 1.2rem;
      margin: 0 0.3rem;
      border-radius: 8px;
      transition: all 0.2s ease;
    }
    .nav-link-custom:hover {
      background: rgba(42, 92, 130, 0.08);
      transform: translateY(-2px);
    }
    .active-link {
      background: var(--primary-color) !important;
      color: white !important;
    }
    .badge-notification {
      position: absolute;
      top: 5px;
      right: -5px;
      background: var(--secondary-color);
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">
        <img src="city-logo.png" alt="City Logo" style="height: 40px" class="me-2">
        Jamnagar Waste Management
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#municipalNav" aria-controls="municipalNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="municipalNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link nav-link-custom <?php echo ($activePage == 'dashboard') ? 'active-link' : ''; ?>" href="dashboard.php">
              <i class="bi bi-speedometer2 me-2"></i>
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom <?php echo ($activePage == 'operations') ? 'active-link' : ''; ?>" href="operations.php">
              <i class="bi bi-speedometer2 me-2"></i>
              Operations
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom <?php echo ($activePage == 'analytics') ? 'active-link' : ''; ?>" href="analytics.php">
              <i class="bi bi-bar-chart-line me-2"></i>
              Analytics
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom <?php echo ($activePage == 'system-settings') ? 'active-link' : ''; ?>" href="system-settings.php">
              <i class="bi bi-sliders me-2"></i>
              Settings
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom <?php echo ($activePage == 'profile') ? 'active-link' : ''; ?>" href="profile.php">
              <i class="bi bi-person-fill me-2"></i>
              Profile
            </a>
          </li>
          <!-- logout -->
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="logout.php">
              <i class="bi bi-box-arrow-right me-2"></i>
              Logout
            </a>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Optional spacing for fixed navbar -->
  <div style="margin-top: 80px;"></div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
