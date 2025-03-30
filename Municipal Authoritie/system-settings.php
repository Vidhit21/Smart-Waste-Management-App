<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>System Settings - Jamnagar Waste Management</title>

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
  </head>
  <body>
    <!-- Navbar Section -->
    <?php include 'navbar.php'; ?>
    
    <div class="main-content">
      <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="fw-bold text-primary">System Settings</h3>
          <div class="text-muted">Last Updated: 15 mins ago</div>
        </div>

        <!-- Settings Sections -->
        <div class="row g-4">
          <!-- User Management -->
          <div class="col-12 col-lg-6">
            <div class="card-base">
              <h5 class="mb-4">
                <i class="bi bi-people me-2"></i>User Management
              </h5>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Role</th>
                      <th>Last Login</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Admin User</td>
                      <td><span class="badge bg-primary">Super Admin</span></td>
                      <td>2 hours ago</td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary me-2">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <!-- More users -->
                  </tbody>
                </table>
              </div>
              <button class="btn btn-primary mt-3">
                <i class="bi bi-plus-lg me-2"></i>Add New User
              </button>
            </div>
          </div>

          <!-- System Configuration -->
          <div class="col-12 col-lg-6">
            <div class="card-base">
              <h5 class="mb-4">
                <i class="bi bi-sliders me-2"></i>System Configuration
              </h5>
              <form>
                <div class="mb-3">
                  <label class="form-label">Data Retention Period</label>
                  <select class="form-select">
                    <option>30 Days</option>
                    <option>60 Days</option>
                    <option>90 Days</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Auto Backup Frequency</label>
                  <div class="input-group">
                    <input type="number" class="form-control" value="7" />
                    <span class="input-group-text">Days</span>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Default Language</label>
                  <select class="form-select">
                    <option>English</option>
                    <option>Hindi</option>
                    <option>Gujarati</option>
                  </select>
                </div>

                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save me-2"></i>Save Changes
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
