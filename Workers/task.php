<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List - Waste Worker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/task.css">
    <link rel="stylesheet" href="css/nav.css">
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>
    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Today's Tasks <span class="badge bg-primary">15 Jan 2024</span></h3>
            <div class="stats">
                <span class="badge bg-success me-2">Completed: 8</span>
                <span class="badge bg-warning me-2">Pending: 5</span>
                <span class="badge bg-danger">Delayed: 2</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-4 btn-group">
            <button class="btn btn-outline-secondary active" data-filter="all">All</button>
            <button class="btn btn-outline-primary" data-filter="pending">Pending</button>
            <button class="btn btn-outline-success" data-filter="completed">Completed</button>
            <button class="btn btn-outline-danger" data-filter="delayed">Delayed</button>
        </div>

        <!-- Task Grid -->
        <div class="row g-4" id="taskContainer">
            <!-- Tasks injected via JS -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/task.js"></script>
</body>
</html>