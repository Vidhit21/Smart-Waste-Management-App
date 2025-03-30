<?php
session_start();
require 'db_connect.php'; // This should set up your $conn variable

// Assume the resident's address_id is stored in session
$residentAddressId = isset($_SESSION['address_id']) ? $_SESSION['address_id'] : null;

// Fetch schedule for resident's address from WasteCollectionSchedules table
// Expected columns: collection_day (e.g., 'Monday'), time_slot (TIME), notes (TEXT)
$schedules = [];
if ($residentAddressId) {
    $stmt = $conn->prepare("SELECT collection_day, time_slot, notes FROM WasteCollectionSchedules WHERE address_id = ?");
    $stmt->bind_param("i", $residentAddressId);
    $stmt->execute();
    $result = $stmt->get_result();
    // There may be multiple entries per day so we group them by day name.
    while ($row = $result->fetch_assoc()) {
        $day = $row['collection_day'];
        if (!isset($schedules[$day])) {
            $schedules[$day] = [];
        }
        $schedules[$day][] = $row;
    }
    $stmt->close();
}
$conn->close();

// Today's date info
$today = date('Y-m-d');
$todayDayName = date('l'); // e.g., "Monday"
$todaySchedule = isset($schedules[$todayDayName]) ? $schedules[$todayDayName] : [];

// Prepare schedule data in JSON format for JavaScript usage.
$scheduleDataJSON = json_encode($schedules);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Waste Schedule | Jamnagar Smart Waste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/schedule.css" />
    <style>
      /* Minimal custom styles for calendar */
      .calendar-day { cursor: pointer; }
      .today { background-color: #d1e7dd; border-radius: 5px; }
      .badge-schedule { font-size: 0.8rem; }
      body{
        margin-top: 75px;
      }
    </style>
  </head>
  <body>
    <!-- Navbar -->
    <?php include 'nav.php'; ?>

    <main class="container my-4">
      <div class="row g-4">
        <!-- Main Calendar -->
        <div class="col-lg-8">
          <div class="card schedule-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><?php echo date('F Y'); ?></h3>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(-1)">
                    <i class="bi bi-chevron-left"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(1)">
                    <i class="bi bi-chevron-right"></i>
                  </button>
                </div>
              </div>

              <div class="row row-cols-7 g-2 mb-3 fw-bold text-center">
                <div class="col">Sun</div>
                <div class="col">Mon</div>
                <div class="col">Tue</div>
                <div class="col">Wed</div>
                <div class="col">Thu</div>
                <div class="col">Fri</div>
                <div class="col">Sat</div>
              </div>

              <div class="row row-cols-7 g-2" id="calendar-grid">
                <!-- Calendar days will be dynamically generated -->
              </div>
            </div>
          </div>
        </div>

        <!-- Schedule Sidebar -->
        <div class="col-lg-4">
          <div class="card schedule-card">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">Today's Schedule - <?php echo date('l, F j, Y'); ?></h5>
            </div>
            <div class="card-body" id="schedule-list">
              <?php if (!empty($todaySchedule)): ?>
                <?php foreach ($todaySchedule as $sch): ?>
                  <div class="mb-3">
                    <strong>Pickup Time:</strong> <?php echo date('h:i A', strtotime($sch['time_slot'])); ?><br>
                    <?php if (!empty($sch['notes'])): ?>
                      <small><?php echo htmlspecialchars($sch['notes']); ?></small>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="text-muted">No schedule for today.</p>
              <?php endif; ?>
            </div>
          </div>

          <div class="card schedule-card mt-4">
            <div class="card-body">
              <h5 class="mb-3">Schedule Legend</h5>
              <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="status-badge bg-success rounded-circle" style="width: 10px; height: 10px;"></span>
                  Regular Collection
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="status-badge bg-warning rounded-circle" style="width: 10px; height: 10px;"></span>
                  Bulk Pickup
                </div>
                <!-- Holiday section removed for residents -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Get schedule data from PHP (an object keyed by day-of-week)
      const scheduleData = <?php echo $scheduleDataJSON; ?>;
      
      let currentYear = new Date().getFullYear();
      let currentMonth = new Date().getMonth(); // Month is 0-indexed

      function generateCalendar(year, month) {
        const calendarGrid = document.getElementById("calendar-grid");
        calendarGrid.innerHTML = "";
        // First day of the month
        const firstDay = new Date(year, month, 1);
        const startingDay = firstDay.getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Blank cells before the first day
        for (let i = 0; i < startingDay; i++) {
          const blankCell = document.createElement("div");
          blankCell.className = "col calendar-day p-2";
          calendarGrid.appendChild(blankCell);
        }

        // Generate each day cell
        for (let day = 1; day <= daysInMonth; day++) {
          const date = new Date(year, month, day);
          const dateStr = date.toISOString().split("T")[0];
          const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
          const dayCell = document.createElement("div");
          dayCell.className = "col calendar-day p-2 text-center";
          // Highlight today
          if (dateStr === "<?php echo $today; ?>") {
            dayCell.classList.add("today");
          }
          // Check if there is any schedule for this day of the week
          let badge = "";
          if (scheduleData[dayName] && scheduleData[dayName].length > 0) {
            badge = `<div class="mt-1"><span class="badge bg-success rounded-pill badge-schedule">${scheduleData[dayName].length}</span></div>`;
          }
          dayCell.innerHTML = `<div class="position-relative p-2">
                                  ${day}
                                  ${badge}
                                </div>`;
          dayCell.addEventListener("click", () => showDaySchedule(dayName, dateStr));
          calendarGrid.appendChild(dayCell);
        }
      }

      function showDaySchedule(dayName, dateStr) {
        const scheduleList = document.getElementById("schedule-list");
        let scheduleHTML = `<h6>${dayName}, ${dateStr}</h6>`;
        if (scheduleData[dayName] && scheduleData[dayName].length > 0) {
          scheduleData[dayName].forEach(item => {
            scheduleHTML += `<div class="mb-3">
                               <strong>Pickup Time:</strong> ${formatTime(item.time_slot)}<br>
                               ${item.notes ? `<small>${item.notes}</small>` : ""}
                             </div>`;
          });
        } else {
          scheduleHTML += `<p class="text-muted">No collections scheduled.</p>`;
        }
        scheduleList.innerHTML = scheduleHTML;
      }

      function formatTime(timeStr) {
        const [hour, minute] = timeStr.split(":");
        let hourNum = parseInt(hour);
        const ampm = hourNum >= 12 ? "PM" : "AM";
        hourNum = hourNum % 12 || 12;
        return `${hourNum}:${minute} ${ampm}`;
      }

      function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth < 0) {
          currentMonth = 11;
          currentYear--;
        } else if (currentMonth > 11) {
          currentMonth = 0;
          currentYear++;
        }
        generateCalendar(currentYear, currentMonth);
      }

      function refreshSchedule() {
        // For now, simply regenerate the calendar (you could add AJAX refresh here)
        generateCalendar(currentYear, currentMonth);
      }

      document.addEventListener("DOMContentLoaded", () => {
        generateCalendar(currentYear, currentMonth);
        // Show today's schedule by default
        const todayName = new Date().toLocaleDateString('en-US', { weekday: 'long' });
        showDaySchedule(todayName, "<?php echo $today; ?>");
      });
    </script>
  </body>
</html>
