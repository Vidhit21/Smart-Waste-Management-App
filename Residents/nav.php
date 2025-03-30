<?php 

$is_logged_in = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-trash"></i> Jamnagar Waste Mgmt
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Other navbar items -->
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="index.php">
            <i class="bi bi-house"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'Schedule.php') ? 'active' : ''; ?>" href="Schedule.php">
            <i class="bi bi-calendar-event"></i> Schedule
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'report.php') ? 'active' : ''; ?>" href="report.php">
            <i class="bi bi-exclamation-triangle"></i> Report
          </a>
        </li>
        <!-- Profile Dropdown -->
        <?php if ($is_logged_in): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo ($currentPage == 'pro_set.php' || $currentPage == 'logout.php') ? 'active' : ''; ?>" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person"></i> Profile
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="pro_set.php">Profile Settings</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'login.php') ? 'active' : ''; ?>" href="login.php">
              <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
