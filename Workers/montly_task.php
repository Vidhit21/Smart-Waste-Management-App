<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Tasks - Waste Worker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .calendar-day {
            border: 1px solid #dee2e6;
            min-height: 120px;
            transition: all 0.2s;
        }
        .task-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin: 2px;
        }
        .completed-dot { background: #28a745; }
        .pending-dot { background: #ffc107; }
        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #28a745;
        }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>January 2024 Performance</h3>
            <div class="month-selector">
                <button class="btn btn-outline-secondary"><</button>
                <span class="mx-3">January 2024</span>
                <button class="btn btn-outline-secondary">></button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stats-card border-left-primary">
                    <div class="card-body">
                        <h5 class="text-muted">Total Tasks</h5>
                        <h2>142</h2>
                        <span class="text-success">+12% from last month</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card border-left-success">
                    <div class="card-body">
                        <h5 class="text-muted">Completed</h5>
                        <h2>128</h2>
                        <span class="text-success">90% success rate</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card border-left-info">
                    <div class="card-body">
                        <h5 class="text-muted">Avg. Time</h5>
                        <h2>3.2h</h2>
                        <span class="text-success">-24m from last month</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card border-left-warning">
                    <div class="card-body">
                        <h5 class="text-muted">Rating</h5>
                        <h2>4.8 <i class="bi bi-star-fill text-warning"></i></h2>
                        <span class="text-muted">Based on 34 reviews</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="row g-1 mb-4" id="calendar">
            <!-- Calendar days will be injected here -->
        </div>

        <!-- Completion Timeline -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Completions</h5>
                <div class="timeline">
                    <div class="timeline-item">
                        <h6>Residential Zone A</h6>
                        <small class="text-muted">15 Jan - 09:00 AM</small>
                        <p>Completed 12 minutes ahead of schedule</p>
                    </div>
                    <div class="timeline-item">
                        <h6>Commercial Complex</h6>
                        <small class="text-muted">14 Jan - 02:30 PM</small>
                        <p>Received 4.9 star rating</p>
                    </div>
                    <!-- Add more timeline items -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fake monthly data
        const monthlyTasks = {
            '2024-01-05': { completed: 8, pending: 2 },
            '2024-01-12': { completed: 10, pending: 1 },
            '2024-01-18': { completed: 9, pending: 0 },
            '2024-01-25': { completed: 7, pending: 3 }
        };

        // Generate calendar
        function generateCalendar() {
            const calendar = document.getElementById('calendar');
            const daysInMonth = 31;
            const startDay = 1; // Monday
            
            // Add empty days
            for(let i = 0; i < startDay; i++) {
                calendar.innerHTML += `<div class="col calendar-day"></div>`;
            }

            // Add actual days
            for(let day = 1; day <= daysInMonth; day++) {
                const date = `2024-01-${day.toString().padStart(2, '0')}`;
                const tasks = monthlyTasks[date] || { completed: 0, pending: 0 };
                
                const dots = [];
                for(let i = 0; i < tasks.completed; i++) {
                    dots.push('<span class="task-dot completed-dot"></span>');
                }
                for(let i = 0; i < tasks.pending; i++) {
                    dots.push('<span class="task-dot pending-dot"></span>');
                }

                calendar.innerHTML += `
                    <div class="col calendar-day p-2">
                        <div class="d-flex justify-content-between">
                            <strong>${day}</strong>
                            <div>${dots.join('')}</div>
                        </div>
                    </div>
                `;
            }
        }

        // Initial generation
        generateCalendar();
    </script>
</body>
</html>