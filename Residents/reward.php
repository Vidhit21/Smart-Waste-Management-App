<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recycling Rewards | Jamnagar Smart Waste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
            body {
        padding-top: 75px;
    }
    
    .navbar {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    footer a:hover {
        color: var(--secondary-green) !important;
    }
        .recycling-card {
            border-radius: 15px;
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .recycling-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.1);
        }

        .progress-ring {
            width: 120px;
            height: 120px;
        }

        .reward-card {
            cursor: pointer;
            border: 2px solid transparent;
        }

        .reward-card:hover {
            border-color: #2E7D32;
        }

        .recycling-badge {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .eco-animation {
            animation: ecoPulse 2s infinite;
        }

        @keyframes ecoPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
  <!-- Navbar -->
  <?php include 'nav.php'; ?>
<div class="container py-4">
    <!-- Points Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">♻️ Recycling Rewards</h2>
            <p class="text-muted">Earn points for proper waste segregation</p>
        </div>
        <div class="text-center bg-success text-white p-3 rounded-3">
            <div class="h2 mb-0">1,250</div>
            <small>ECO Points</small>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Recycling Log -->
            <div class="card recycling-card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Log Recycling Activity</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select class="form-select">
                                <option>Select Waste Type</option>
                                <option>Plastic</option>
                                <option>Paper</option>
                                <option>Glass</option>
                                <option>Metal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" placeholder="Weight (kg)">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success w-100">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress & Achievements -->
            <div class="card recycling-card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Your Eco-Progress</h5>
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <svg class="progress-ring">
                                <circle class="text-success" stroke-width="8" 
                                        fill="transparent" r="52" cx="60" cy="60"
                                        stroke-dasharray="326.56" stroke-dashoffset="65"></circle>
                            </svg>
                            <div class="mt-2">Monthly Goal: 80%</div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="card p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="recycling-badge bg-success me-3">
                                                <i class="bi bi-recycle text-white"></i>
                                            </div>
                                            <div>
                                                <div class="h6 mb-0">35 kg</div>
                                                <small>Recycled This Month</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Add more stats -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card recycling-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Recent Activities</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <div>
                                <i class="bi bi-trash me-2"></i>
                                Plastic Recycling
                            </div>
                            <div>
                                <span class="text-success">+50 pts</span>
                                <small class="text-muted ms-2">2kg</small>
                            </div>
                        </div>
                        <!-- Add more transactions -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Rewards Catalog -->
            <div class="card recycling-card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Redeem Points</h5>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="card reward-card p-3">
                                <div class="d-flex align-items-center">
                                    <div class="eco-animation me-3">
                                        <i class="bi bi-gift fs-3 text-success"></i>
                                    </div>
                                    <div>
                                        <div class="h6 mb-0">Municipal Tax Discount</div>
                                        <small>500 pts = ₹100 off</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Add more rewards -->
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="card recycling-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Community Leaderboard</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <span class="badge bg-success me-2">1</span>
                            Ramesh Patel - 2,450 pts
                        </div>
                        <!-- Add more leaderboard entries -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reward Modal -->
<div class="modal fade" id="rewardModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Redeem Reward</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Your reward request has been processed!</p>
                <p>🔄 500 points deducted from your account</p>
                <div class="alert alert-success">
                    Voucher Code: <strong>JMC-RW-54983</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <h5>Jamnagar Municipal Corporation</h5>
                <p class="text-muted">Smart Waste Management Initiative</p>
                <div class="d-flex gap-2">
                    <a href="#" class="text-white"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Waste Segregation Guide</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Recycling Centers</a></li>
                    <li><a href="#" class="text-white text-decoration-none">City Regulations</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-telephone"></i> +91 79 2323 0000</li>
                    <li><i class="bi bi-envelope"></i> waste@jamnagarmc.gov.in</li>
                    <li><i class="bi bi-geo-alt"></i> Municipal Corporation, Jamnagar</li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-4 pt-4 border-top border-secondary">
            <small class="text-muted">&copy; 2023 Jamnagar Municipal Corporation. All rights reserved.</small>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Circular Progress Animation
    const circle = document.querySelector('.progress-ring circle');
    const radius = circle.r.baseVal.value;
    const circumference = radius * 2 * Math.PI;

    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    circle.style.strokeDashoffset = circumference;

    function setProgress(percent) {
        const offset = circumference - (percent / 100) * circumference;
        circle.style.strokeDashoffset = offset;
    }

    setProgress(80); // Set initial progress

    // Reward Redemption
    function redeemReward(points) {
        if(confirm(`Redeem ${points} points for this reward?`)) {
            new bootstrap.Modal('#rewardModal').show();
        }
    }

    // Add Recycling Log
    document.querySelector('[data-action="add-log"]').addEventListener('click', () => {
        // Add form validation and API call here
        showToast('Recycling activity logged! +50 points', 'success');
    });

    // Toast Notification
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type}`;
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast).show();
    }
</script>
</body>
</html>