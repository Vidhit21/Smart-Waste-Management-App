<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Assign Tasks - Jamnagar Waste Management</title>

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

    <!-- Existing CSS -->
    <link rel="stylesheet" href="css/nav.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <link rel="stylesheet" href="css/assign-tasks.css" />
  </head>
  <body>
    <!-- Navbar Section -->
    <?php include 'navbar.php'; ?>

    <div class="main-content">
      <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="fw-bold text-primary">Task Management</h3>
          <button class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Create New Task
          </button>
        </div>

        <!-- Task Assignment Interface -->
        <div class="row g-4">
          <!-- Task Form -->
          <div class="col-12 col-lg-4">
            <div class="card-base task-form-card">
              <h5 class="mb-4">
                <i class="bi bi-pencil-square me-2"></i>Create Task
              </h5>

              <form>
                <div class="mb-3">
                  <label class="form-label">Task Title</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter task title"
                  />
                </div>

                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea
                    class="form-control"
                    rows="3"
                    placeholder="Add task details"
                  ></textarea>
                </div>

                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="form-label">Assigned Worker</label>
                    <select class="form-select">
                      <option>Select Worker</option>
                      <option>Ramesh Patel (Zone 1)</option>
                      <option>Suresh Sharma (Zone 2)</option>
                      <option>Arjun Mehta (Zone 3)</option>
                    </select>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Zone</label>
                    <select class="form-select">
                      <option>Select Zone</option>
                      <option>Zone 1 - Central</option>
                      <option>Zone 2 - North</option>
                      <option>Zone 3 - South</option>
                    </select>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Priority</label>
                    <select class="form-select">
                      <option>Normal</option>
                      <option>High</option>
                      <option>Urgent</option>
                    </select>
                  </div>

                  <div class="col-12 col-md-6">
                    <label class="form-label">Due Date</label>
                    <input type="date" class="form-control" />
                  </div>
                </div>

                <div class="mt-4 d-grid">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-check me-2"></i>Assign Task
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Active Tasks List -->
          <div class="col-12 col-lg-8">
            <article
              class="card-base tasks-list-card"
              aria-labelledby="active-tasks-heading"
            >
              <header class="task-list-header">
                <h2 id="active-tasks-heading" class="h5 mb-0">
                  <i class="bi bi-list-task me-2" aria-hidden="true"></i>
                  Active Tasks
                </h2>
                <p class="text-muted mb-0" aria-live="polite">
                  Showing <span class="visually-hidden">tasks</span>
                  <span class="badge bg-primary">8</span> of 23
                </p>
              </header>

              <!-- Task List -->
              <ul class="task-list list-unstyled" role="list">
                <!-- Task Item -->
                <li
                  class="task-item"
                  role="listitem"
                  data-priority="urgent"
                  data-status="not-started"
                >
                  <div class="task-priority-indicator" aria-hidden="true"></div>

                  <div class="task-content">
                    <div class="task-header">
                      <h3 class="h6 task-title">
                        <span class="visually-hidden">Priority:</span>
                        Emergency Collection - Market Area
                      </h3>
                      <div class="task-meta">
                        <span class="badge bg-danger">
                          <i
                            class="bi bi-exclamation-circle me-1"
                            aria-hidden="true"
                          ></i>
                          Urgent
                        </span>
                        <time datetime="2023-12-15T15:00" class="text-muted">
                          <i class="bi bi-clock me-1" aria-hidden="true"></i>
                          Due: Today 3 PM
                        </time>
                        <span class="task-id text-muted">#TASK-458</span>
                      </div>
                    </div>

                    <p class="task-description">
                      Collect overflowing waste from main market area.
                      <span class="text-muted">Last updated: 2 hours ago</span>
                    </p>

                    <footer class="task-footer">
                      <div class="worker-info">
                        <img
                          src="worker-avatar.png"
                          class="worker-avatar"
                          alt="Ramesh Patel profile picture"
                          loading="lazy"
                          width="40"
                          height="40"
                        />
                        <div>
                          <span class="worker-name">Ramesh Patel</span>
                          <span class="worker-zone d-block text-muted"
                            >Zone 5</span
                          >
                        </div>
                      </div>

                      <div class="task-progress">
                        <div class="progress-status">
                          <span class="progress-text">Not Started</span>
                          <span class="progress-percent">0%</span>
                        </div>
                        <div class="progress-track">
                          <div class="progress-fill" style="width: 0%"></div>
                        </div>
                      </div>
                    </footer>
                  </div>
                </li>

                <!-- More Task Items -->
                <li
                  class="task-item"
                  role="listitem"
                  data-priority="high"
                  data-status="in-progress"
                >
                  <!-- Similar structure with appropriate data attributes -->
                </li>
              </ul>

              <!-- Empty State -->
              <div class="task-list-empty text-center py-5 d-none">
                <i class="bi bi-check2-circle display-5 text-muted mb-3"></i>
                <p class="text-muted">No active tasks found. Great job!</p>
              </div>
            </article>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
