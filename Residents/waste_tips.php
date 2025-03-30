<?php
session_start();
// If you plan to load tips from the database, include db_connect.php here.
// require 'db_connect.php';

// Static arrays with categorized waste management tips
$general_tips = [
    [
        'title' => 'Plan Your Purchases',
        'description' => 'Before shopping, make a list and stick to it. This helps reduce impulse buys and minimizes waste.'
    ],
    [
        'title' => 'Reuse Items',
        'description' => 'Before discarding, think if an item can be repurposed or donated.'
    ]
];

$recycling_tips = [
    [
        'title' => 'Sort Your Waste',
        'description' => 'Separate your waste into recyclables, compostables, and landfill items. Follow your local recycling guidelines.'
    ],
    [
        'title' => 'Clean Recyclables',
        'description' => 'Rinse containers before recycling to avoid contamination of recyclable materials.'
    ]
];

$composting_tips = [
    [
        'title' => 'Start a Compost Bin',
        'description' => 'Collect organic waste like food scraps and yard debris to create nutrient-rich compost for your garden.'
    ],
    [
        'title' => 'Maintain the Right Balance',
        'description' => 'Ensure your compost pile has a mix of green (nitrogen-rich) and brown (carbon-rich) materials.'
    ]
];

$education_tips = [
    [
        'title' => 'Stay Informed',
        'description' => 'Keep up with local waste management policies and programs to participate effectively in recycling and composting.'
    ],
    [
        'title' => 'Share Knowledge',
        'description' => 'Educate your family, friends, and community about the importance of reducing waste and recycling correctly.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Waste Management Tips | Jamnagar Smart Waste</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { padding-top: 70px; }
    .tip-card { margin-bottom: 20px; }
    .section-title { margin-top: 40px; margin-bottom: 20px; }
    .intro-text { margin-bottom: 40px; }
  </style>
</head>
<body>
  <?php include 'nav.php'; ?>

  <div class="container">
    <header class="mb-5">
      <h1 class="mt-4 mb-3">Waste Management Tips</h1>
      <p class="lead intro-text">
        Effective waste management is vital for the health of our environment and communities.
        Whether you're looking to reduce waste at home or improve your recycling habits,
        these tips are designed to help you make smarter choices every day.
      </p>
    </header>

    <!-- General Tips Section -->
    <section>
      <h2 class="section-title">General Tips</h2>
      <div class="row">
        <?php foreach ($general_tips as $tip): ?>
          <div class="col-md-6">
            <div class="card tip-card">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($tip['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($tip['description']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Recycling Tips Section -->
    <section>
      <h2 class="section-title">Recycling Tips</h2>
      <div class="row">
        <?php foreach ($recycling_tips as $tip): ?>
          <div class="col-md-6">
            <div class="card tip-card">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($tip['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($tip['description']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Composting Tips Section -->
    <section>
      <h2 class="section-title">Composting Tips</h2>
      <div class="row">
        <?php foreach ($composting_tips as $tip): ?>
          <div class="col-md-6">
            <div class="card tip-card">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($tip['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($tip['description']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Educational Tips Section -->
    <section>
      <h2 class="section-title">Educational Tips</h2>
      <div class="row">
        <?php foreach ($education_tips as $tip): ?>
          <div class="col-md-6">
            <div class="card tip-card">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($tip['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($tip['description']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Call-to-Action Section -->
    <section class="mt-5">
      <div class="p-4 bg-light rounded">
        <h3>Get Involved</h3>
        <p>
          Join local initiatives and community groups to learn more about how you can
          contribute to a cleaner, greener Jamnagar. Together, we can make a significant impact.
        </p>
        <a href="community.php" class="btn btn-success">
          Learn More <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </section>
  </div>

  <?php include 'footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
