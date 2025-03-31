<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Status - Waste Worker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .report-card {
            border-left: 4px solid;
            transition: all 0.2s;
        }
        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
        }
        .timeline {
            border-left: 2px solid #dee2e6;
            margin-left: 15px;
        }
        .timeline-item {
            position: relative;
            padding-left: 25px;
            margin-bottom: 25px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .completed-report { border-color: #28a745; }
        .delayed-report { border-color: #ffc107; }
        .missed-report { border-color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row g-4">
            <!-- Report Form -->
            <div class="col-lg-6">
                <div class="card report-card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            <i class="bi bi-clipboard-check me-2"></i>Report Collection Status
                        </h4>
                        
                        <form id="reportForm">
                            <div class="mb-3">
                                <label class="form-label">Select Task</label>
                                <select class="form-select" id="taskSelect" required>
                                    <option value="">Choose Task...</option>
                                    <option value="1">Area 1 - Residential Zone</option>
                                    <option value="2">Area 2 - Market Street</option>
                                    <option value="3">Area 3 - Commercial Complex</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="statusSelect" required>
                                    <option value="">Select Status...</option>
                                    <option value="completed">Completed</option>
                                    <option value="delayed">Delayed</option>
                                    <option value="missed">Missed</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Comments</label>
                                <textarea class="form-control" rows="3" 
                                    placeholder="Add additional details..." id="comments"></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-send-check me-2"></i>Submit Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-clock-history me-2"></i>Recent Reports
                        </h5>
                        
                        <!-- Stats Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0" id="totalReports">0</h3>
                                        <small class="text-muted">Total Reports</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0" id="completedCount">0</h3>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="mb-0" id="pendingCount">0</h3>
                                        <small class="text-muted">Pending Issues</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Report Timeline -->
                        <div class="timeline" id="reportTimeline">
                            <!-- Reports will be added here dynamically -->
                            <div class="text-muted">No reports submitted yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fake Data Storage
        let reports = [];
        
        // Status Colors
        const statusColors = {
            completed: '#28a745',
            delayed: '#ffc107',
            missed: '#dc3545'
        };

        // Form Submission
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const task = document.getElementById('taskSelect').value;
            const status = document.getElementById('statusSelect').value;
            const comments = document.getElementById('comments').value;
            
            if(!task || !status) {
                alert('Please select task and status');
                return;
            }

            const newReport = {
                id: Date.now(),
                task: document.getElementById('taskSelect').options[taskSelect.selectedIndex].text,
                status: status,
                comments: comments,
                timestamp: new Date().toLocaleString()
            };

            reports.unshift(newReport);
            updateReportsDisplay();
            updateStatistics();
            this.reset();
            
            // Show success alert
            const alert = document.createElement('div');
            alert.className = `alert alert-success alert-dismissible fade show mt-3`;
            alert.innerHTML = `
                Report submitted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').prepend(alert);
        });

        // Update Reports Display
        function updateReportsDisplay() {
            const timeline = document.getElementById('reportTimeline');
            timeline.innerHTML = '';
            
            reports.slice(0, 5).forEach(report => {
                const item = document.createElement('div');
                item.className = 'timeline-item';
                item.innerHTML = `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6>${report.task}</h6>
                                <span class="status-indicator" 
                                      style="background: ${statusColors[report.status]}"></span>
                            </div>
                            <small class="text-muted">${report.timestamp}</small>
                            ${report.comments ? `<p class="mt-2 small">${report.comments}</p>` : ''}
                        </div>
                    </div>
                `;
                timeline.appendChild(item);
            });
        }

        // Update Statistics
        function updateStatistics() {
            document.getElementById('totalReports').textContent = reports.length;
            document.getElementById('completedCount').textContent = 
                reports.filter(r => r.status === 'completed').length;
            document.getElementById('pendingCount').textContent = 
                reports.filter(r => r.status !== 'completed').length;
        }

        // Initial fake data
        reports = [
            {
                id: 1,
                task: 'Area 2 - Market Street',
                status: 'completed',
                comments: 'Regular collection',
                timestamp: '2024-01-15 09:30 AM'
            },
            {
                id: 2,
                task: 'Area 1 - Residential Zone',
                status: 'delayed',
                comments: 'Vehicle breakdown',
                timestamp: '2024-01-14 02:15 PM'
            }
        ];
        updateReportsDisplay();
        updateStatistics();
    </script>
</body>
</html>