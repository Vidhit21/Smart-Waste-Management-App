<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Worker Dashboard - Smart Waste Management</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/nav.css">
  </head>
  <body class="bg-light">
    <?php include('navbar.php'); ?>

    <!-- Dashboard Content -->
    <div class="container mt-4">
      <h3 class="mb-4">Today's Overview</h3>

      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card metric-card text-white bg-primary">
            <div class="card-body">
              <h5 class="card-title">Total Tasks</h5>
              <h2 class="mb-0">15</h2>
              <small>Updated: 2h ago</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card metric-card text-white bg-success">
            <div class="card-body">
              <h5 class="card-title">Completed</h5>
              <h2 class="mb-0">9</h2>
              <small>Ahead of schedule</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card metric-card text-white bg-warning">
            <div class="card-body">
              <h5 class="card-title">Pending</h5>
              <h2 class="mb-0">4</h2>
              <small>To be collected</small>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card metric-card text-white bg-info">
            <div class="card-body">
              <h5 class="card-title">Efficiency</h5>
              <h2 class="mb-0">82%</h2>
              <small>Current rating</small>
            </div>
          </div>
        </div>
      </div>
      <!-- Route Progress -->
      <div class="route-progress">
        <div class="progress-step active">
          <div class="step-icon"><i class="bi bi-geo-alt"></i></div>
          <span>Collection Start</span>
        </div>
        <div class="progress-step">
          <div class="step-icon">1</div>
          <span>Checkpoint 1</span>
          <div class="progress-connector"></div>
        </div>
        <div class="progress-step">
          <div class="step-icon">2</div>
          <span>Checkpoint 2</span>
          <div class="progress-connector"></div>
        </div>
        <div class="progress-step">
          <div class="step-icon"><i class="bi bi-flag"></i></div>
          <span>Completion</span>
        </div>
      </div>

      <!-- Progress Controls -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Route Progress</h5>
              <div class="progress mb-3" style="height: 25px">
                <div
                  class="progress-bar bg-success"
                  role="progressbar"
                  style="width: 30%"
                  id="routeProgress"
                >
                  30%
                </div>
              </div>
              <button
                class="btn btn-sm btn-outline-success"
                onclick="updateProgress('next')"
              >
                <i class="bi bi-arrow-right"></i> Next Checkpoint
              </button>
              <button
                class="btn btn-sm btn-outline-danger"
                onclick="updateProgress('reset')"
              >
                <i class="bi bi-arrow-clockwise"></i> Reset Route
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Route Statistics</h5>
              <div class="row">
                <div class="col-6">
                  <div class="mb-3">
                    <small class="text-muted">Total Distance</small>
                    <h4 id="totalDistance">15 km</h4>
                  </div>
                  <div class="mb-3">
                    <small class="text-muted">Completed</small>
                    <h4 id="completedDistance">4.5 km</h4>
                  </div>
                </div>
                <div class="col-6">
                  <div class="mb-3">
                    <small class="text-muted">Checkpoints Passed</small>
                    <h4 id="passedCheckpoints">1/5</h4>
                  </div>
                  <div class="mb-3">
                    <small class="text-muted">Estimated Time Left</small>
                    <h4 id="timeLeft">2h 45m</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Checkpoint List -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Checkpoints</h5>
          <ul class="checkpoint-list" id="checkpointList">
            <li class="checkpoint-item">
              <div class="checkpoint-status bg-success text-white">
                <i class="bi bi-check"></i>
              </div>
              <div>
                <h6>Checkpoint 1 - Residential Area</h6>
                <small class="text-muted">Completed: 9:15 AM</small>
              </div>
            </li>
            <li class="checkpoint-item">
              <div class="checkpoint-status bg-primary text-white">
                <i class="bi bi-geo"></i>
              </div>
              <div>
                <h6>Checkpoint 2 - Market Zone (Current)</h6>
                <small class="text-muted">Estimated arrival: 10:30 AM</small>
              </div>
            </li>
            <!-- Add more checkpoints -->
          </ul>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
  </body>
</html>
