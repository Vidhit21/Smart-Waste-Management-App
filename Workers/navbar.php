<?php
// Start session (if not already started) and set the active page.
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Determine the current page (without extension)
$activePage = basename($_SERVER['PHP_SELF'], ".php");

// Assume the username is stored in the session (e.g., after login).
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <style>
    .navbar {
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      background: #28a745 !important;
      /* Green color */
    }

    .nav-link {
      font-weight: 500;
      transition: all 0.3s ease;
      padding: 0.5rem 1rem !important;
      border-radius: 0.5rem;
    }

    .nav-link:hover,
    .nav-link.active {
      background: rgba(255, 255, 255, 0.15);
    }

    .dropdown-menu {
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-success py-3">
    <div class="container">
      <!-- Brand/Logo -->
      <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="logo.png" alt="Logo" width="40" class="me-2" />
        Waste Worker Portal
      </a>

      <!-- Mobile Toggle Button -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
        aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navigation Links -->
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>" aria-current="page" href="dashboard.php">
              <i class="  bi bi-speedometer2 me-1"></i>
              Dashboard
            </a> 
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($activePage == 'task') ? 'active' : ''; ?>" href="task.php">
              <i class="bi bi-list-task me-1"></i>
              Tasks
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($activePage == 'routes') ? 'active' : ''; ?>" href="routes.php">
              <i class="bi bi-geo-alt me-1"></i>
              Routes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($activePage == 'report') ? 'active' : ''; ?>" href="report.php">
              <i class="bi bi-clipboard-check me-1"></i>
              Report
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($activePage == 'user') ? 'active' : ''; ?>" href="profile.php">
              <i class="bi bi-clipboard-check me-1"></i>
              <?php echo htmlspecialchars($username); ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($activePage == 'logout') ? 'active' : ''; ?>" href="logout.php">
              <i class="bi bi-clipboard-check me-1"></i>
              Logout
            </a>
          </li>
          <!-- User Profile Dropdown -->
          <!-- <li class="nav-item dropdown ms-lg-3">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i>
              
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="profile.php">
                  <i class="bi bi-person me-2"></i>Profile
                </a>
              </li>
              <li>
                <hr class="dropdown-divider" />
              </li>
              <li>
                <a class="dropdown-item text-danger" href="logout.php">
                  <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
              </li>
            </ul>
          </li> -->
        </ul>
      </div>
    </div>
  </nav>


  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>