<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Waste Worker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #28a745, #218838);
            border-radius: 15px 15px 0 0;
        }
        .timeline {
            border-left: 3px solid #28a745;
            margin-left: 1rem;
        }
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 2rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 5px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #28a745;
            border: 3px solid white;
        }
        .performance-card {
            transition: transform 0.2s;
        }
        .performance-card:hover {
            transform: translateY(-5px);
        }
        .edit-btn {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>
  

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row g-4">
            <!-- Profile Column -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="profile-header text-center text-white p-4">
                        <img src="avatar.png" alt="Profile" 
                             class="rounded-circle mb-3" width="100">
                        <h3 id="userName">John Doe</h3>
                        <p class="mb-0">Senior Waste Collector</p>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark me-1">
                                <i class="bi bi-star-fill text-warning"></i> 4.8
                            </span>
                            <span class="badge bg-light text-dark">
                                ID: WC-2456
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body position-relative">
                        <button class="btn btn-sm btn-outline-success edit-btn" 
                                onclick="toggleEdit()">
                            <i class="bi bi-pencil"></i> Edit
                        </button>

                        <form id="profileForm">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" 
                                       id="userEmail" value="john@wastemgmt.com" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" 
                                       id="userPhone" value="+91 9876543210" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vehicle Number</label>
                                <input type="text" class="form-control" 
                                       value="GJ-10-AB-1234" disabled>
                            </div>
                            <div class="d-none" id="saveSection">
                                <button type="submit" class="btn btn-success">
                                    Save Changes
                                </button>
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="toggleEdit()">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Work History Column -->
            <div class="col-lg-8">
                <!-- Performance Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card performance-card bg-primary text-white">
                            <div class="card-body">
                                <h5>Total Collections</h5>
                                <h1>1,245</h1>
                                <small>Since 2020</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card performance-card bg-success text-white">
                            <div class="card-body">
                                <h5>Current Rating</h5>
                                <h1>4.8</h1>
                                <small>Based on 128 reviews</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Timeline -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Recent Work History</h5>
                        <div class="timeline" id="workTimeline">
                            <!-- Timeline items will be populated by JS -->
                        </div>
                    </div>
                </div>

                <!-- Performance Chart -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Performance</h5>
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Profile Edit Toggle
        function toggleEdit() {
            const inputs = document.querySelectorAll('#profileForm input');
            const saveSection = document.getElementById('saveSection');
            
            inputs.forEach(input => {
                input.disabled = !input.disabled;
            });
            
            saveSection.classList.toggle('d-none');
        }

        // Form Submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            toggleEdit();
            alert('Profile updated successfully!');
        });

        // Work History Data
        const workHistory = [
            {
                date: '2024-01-15',
                task: 'Residential Zone A',
                status: 'completed',
                details: 'Collected 85kg waste, 15 minutes ahead of schedule'
            },
            {
                date: '2024-01-14',
                task: 'Commercial Complex',
                status: 'delayed',
                details: 'Vehicle maintenance delay, 45 minutes late'
            },
            {
                date: '2024-01-13',
                task: 'Market Area',
                status: 'completed',
                details: 'Special collection for festival waste'
            }
        ];

        // Populate Timeline
        const timeline = document.getElementById('workTimeline');
        workHistory.forEach(entry => {
            const statusColor = {
                completed: 'success',
                delayed: 'warning',
                missed: 'danger'
            }[entry.status];

            const item = document.createElement('div');
            item.className = 'timeline-item';
            item.innerHTML = `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6>${entry.task}</h6>
                            <span class="badge bg-${statusColor}">
                                ${entry.status}
                            </span>
                        </div>
                        <small class="text-muted">${entry.date}</small>
                        <p class="mt-2 mb-0">${entry.details}</p>
                    </div>
                </div>
            `;
            timeline.appendChild(item);
        });

        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Tasks Completed',
                    data: [65, 59, 80, 81, 56, 55],
                    backgroundColor: '#28a745',
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>