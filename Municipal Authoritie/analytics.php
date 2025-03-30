<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Analytics - Jamnagar Waste Management</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Existing CSS -->
    <link rel="stylesheet" href="css/nav.css" />
    <link rel="stylesheet" href="css/analytics.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
  </head>
  <body>
    <!-- Navbar Section -->
    <?php include 'navbar.php'; ?>

    <div class="main-content">
      <div class="container-fluid">
        <!-- Page Header -->
        <div class="analytics-header">
          <div class="header-content">
            <div class="header-title-group">
              <h1 class="page-title">
                <i class="bi bi-graph-up header-icon"></i>
                Waste Analytics Dashboard
              </h1>
              <div class="header-meta">
                <span class="update-info">
                  <i class="bi bi-clock-history me-1"></i>
                  Last updated:
                  <time datetime="2023-12-15T14:30" class="live-update"
                    >1 hour ago</time
                  >
                </span>
                <span class="data-range">
                  <i class="bi bi-calendar-range me-1"></i>
                  Showing data for:
                  <span class="date-range">Dec 1 - Dec 15, 2023</span>
                </span>
              </div>
            </div>

            <div class="header-controls">
              <div class="btn-group time-period-selector">
                <button
                  type="button"
                  class="btn btn-period active"
                  data-period="day"
                >
                  Daily
                </button>
                <button type="button" class="btn btn-period" data-period="week">
                  Weekly
                </button>
                <button
                  type="button"
                  class="btn btn-period"
                  data-period="month"
                >
                  Monthly
                </button>
              </div>

              <div class="header-actions">
                <button class="btn btn-refresh">
                  <i class="bi bi-arrow-clockwise"></i>
                  <span class="d-none d-md-inline">Refresh Data</span>
                </button>
                <button class="btn btn-export">
                  <i class="bi bi-download"></i>
                  <span class="d-none d-md-inline">Export Report</span>
                </button>
              </div>
            </div>
          </div>

          <div class="data-freshness-indicator">
            <span class="freshness-dot"></span>
            <span class="freshness-text">Live Data Feed Connected</span>
          </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="row g-4 mb-4">
          <!-- Waste Collected Card -->
          <div class="col-12 col-md-6 col-xl-3">
            <article
              class="stats-card"
              role="region"
              aria-labelledby="waste-metric"
            >
              <div class="card-header">
                <div class="icon-container bg-primary" aria-hidden="true">
                  <i class="bi bi-trash3"></i>
                </div>
                <div class="trend-indicator positive" role="status">
                  <i class="bi bi-arrow-up-short" aria-hidden="true"></i>
                  <span class="visually-hidden">Increase of </span>8%
                </div>
              </div>
              <div class="card-content">
                <h3 id="waste-metric" class="metric-value">
                  245<span class="metric-unit">T</span>
                </h3>
                <p class="metric-label">Monthly Waste Collected</p>
                <div
                  class="progress"
                  role="progressbar"
                  aria-valuenow="65"
                  aria-valuemin="0"
                  aria-valuemax="100"
                >
                  <div class="progress-bar" style="width: 65%">
                    <span class="visually-hidden">65% of monthly target</span>
                  </div>
                </div>
                <div class="metric-meta">
                  <span class="text-muted">Target: 375T</span>
                </div>
              </div>
            </article>
          </div>

          <!-- Recycling Rate Card -->
          <div class="col-12 col-md-6 col-xl-3">
            <article
              class="stats-card"
              role="region"
              aria-labelledby="recycling-metric"
            >
              <div class="card-header">
                <div class="icon-container bg-success" aria-hidden="true">
                  <i class="bi bi-recycle"></i>
                </div>
                <div class="trend-indicator positive" role="status">
                  <i class="bi bi-arrow-up-short" aria-hidden="true"></i>
                  <span class="visually-hidden">Increase of </span>12%
                </div>
              </div>
              <div class="card-content">
                <h3 id="recycling-metric" class="metric-value">
                  68<span class="metric-unit">%</span>
                </h3>
                <p class="metric-label">Recycling Rate</p>
                <div
                  class="progress"
                  role="progressbar"
                  aria-valuenow="68"
                  aria-valuemin="0"
                  aria-valuemax="100"
                >
                  <div class="progress-bar bg-success" style="width: 68%">
                    <span class="visually-hidden">68% recycling rate</span>
                  </div>
                </div>
                <div class="metric-meta">
                  <span class="text-muted">+5% vs last month</span>
                </div>
              </div>
            </article>
          </div>

          <!-- Priority Zones Card -->
          <div class="col-12 col-md-6 col-xl-3">
            <article
              class="stats-card"
              role="region"
              aria-labelledby="priority-metric"
            >
              <div class="card-header">
                <div class="icon-container bg-warning" aria-hidden="true">
                  <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="trend-indicator negative" role="status">
                  <i class="bi bi-arrow-down-short" aria-hidden="true"></i>
                  <span class="visually-hidden">Decrease of </span>5%
                </div>
              </div>
              <div class="card-content">
                <h3 id="priority-metric" class="metric-value">23</h3>
                <p class="metric-label">High Priority Zones</p>
                <div
                  class="progress"
                  role="progressbar"
                  aria-valuenow="40"
                  aria-valuemin="0"
                  aria-valuemax="100"
                >
                  <div class="progress-bar bg-warning" style="width: 40%">
                    <span class="visually-hidden">40% resolution progress</span>
                  </div>
                </div>
                <div class="metric-meta">
                  <span class="text-muted">5 unresolved critical</span>
                </div>
              </div>
            </article>
          </div>

          <!-- Route Efficiency Card -->
          <div class="col-12 col-md-6 col-xl-3">
            <article
              class="stats-card"
              role="region"
              aria-labelledby="route-metric"
            >
              <div class="card-header">
                <div class="icon-container bg-info" aria-hidden="true">
                  <i class="bi bi-speedometer2"></i>
                </div>
                <div class="trend-indicator positive" role="status">
                  <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
                  <span class="visually-hidden">Positive trend</span>
                </div>
              </div>
              <div class="card-content">
                <h3 id="route-metric" class="metric-value">
                  84<span class="metric-unit">%</span>
                </h3>
                <p class="metric-label">Route Efficiency</p>
                <div class="performance-status">
                  <i class="bi bi-star-fill" aria-hidden="true"></i>
                  <small>Optimal Performance</small>
                </div>
                <div class="metric-meta">
                  <span class="text-muted">Target: 80%</span>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- Charts Section -->
        <!-- Analytics Page Content -->
        <div class="container-fluid">
          <!-- Header with Chart Selector -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-primary">Waste Analytics</h3>
            <select
              class="form-select form-select-sm w-auto"
              id="chartSelector"
            >
              <option value="summary">Summary Overview</option>
              <option value="trend">Waste Trend Analysis</option>
              <option value="composition">Waste Composition</option>
              <option value="zones">Zone Comparison</option>
              <option value="recycling">Recycling Progress</option>
            </select>
          </div>

          <div class="data-panel row g-3"></div>
          <!-- Chart Containers -->
          <div class="chart-container" id="trendChartContainer">
            <canvas id="trendChart"></canvas>
          </div>

          <div class="chart-container d-none" id="compositionChartContainer">
            <canvas id="compositionChart"></canvas>
          </div>

          <div class="chart-container d-none" id="zoneChartContainer">
            <canvas id="zoneChart"></canvas>
          </div>

          <div class="chart-container d-none" id="recyclingChartContainer">
            <canvas id="recyclingChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/analytics.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
