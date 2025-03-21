let currentCheckpoint = 1;
const totalCheckpoints = 5;

function updateProgress(action) {
  if (action === "next" && currentCheckpoint < totalCheckpoints) {
    currentCheckpoint++;
  } else if (action === "reset") {
    currentCheckpoint = 1;
  }

  // Update progress bar
  const progress = (currentCheckpoint / totalCheckpoints) * 100;
  document.getElementById("routeProgress").style.width = `${progress}%`;
  document.getElementById("routeProgress").textContent = `${Math.round(
    progress
  )}%`;

  // Update statistics
  document.getElementById(
    "passedCheckpoints"
  ).textContent = `${currentCheckpoint}/${totalCheckpoints}`;
  document.getElementById("completedDistance").textContent = `${(
    15 *
    (currentCheckpoint / totalCheckpoints)
  ).toFixed(1)} km`;
  document.getElementById("timeLeft").textContent = `${Math.round(
    5 - 5 * (currentCheckpoint / totalCheckpoints)
  )}h ${Math.round(
    0.6 * (5 - 5 * (currentCheckpoint / totalCheckpoints)) * 60
  )}m`;

  // Update checkpoint statuses
  const checkpoints = document.querySelectorAll(".checkpoint-item");
  checkpoints.forEach((item, index) => {
    const status = item.querySelector(".checkpoint-status");
    if (index < currentCheckpoint) {
      status.innerHTML = '<i class="bi bi-check"></i>';
      status.className = "checkpoint-status bg-success text-white";
    } else if (index === currentCheckpoint) {
      status.innerHTML = '<i class="bi bi-geo"></i>';
      status.className = "checkpoint-status bg-primary text-white";
    } else {
      status.innerHTML = "";
      status.className = "checkpoint-status bg-secondary text-white";
    }
  });
}
