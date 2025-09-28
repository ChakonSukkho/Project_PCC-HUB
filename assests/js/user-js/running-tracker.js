function loadRunHistory() {
  fetch("get_runs.php")
    .then(res => res.json())
    .then(runs => {
      const container = document.getElementById("runHistory");
      container.innerHTML = "";
      if (runs.length === 0) {
        container.innerHTML = "<p>No runs yet.</p>";
        return;
      }
      runs.forEach(r => {
        const div = document.createElement("div");
        div.className = "run-card";
        div.innerHTML = `
          <p><b>Date:</b> ${r.session_date}</p>
          <p><b>Distance:</b> ${r.total_distance} km</p>
          <p><b>Duration:</b> ${r.total_duration} sec</p>
        `;
        container.appendChild(div);
      });
    });
}
loadRunHistory();


let isPaused = false;

// Pause button
const pauseBtn = document.getElementById("pauseBtn");

pauseBtn.addEventListener("click", () => {
  if (!isRunning) return;

  if (!isPaused) {
    // Pause
    isPaused = true;
    pauseBtn.textContent = "▶️ Resume";
    clearInterval(timerInterval);
    navigator.geolocation.clearWatch(watchId);
  } else {
    // Resume
    isPaused = false;
    pauseBtn.textContent = "⏸️ Pause";

    startTime = Date.now() - elapsedTime; // keep old duration
    timerInterval = setInterval(updateTimer, 1000);

    watchId = navigator.geolocation.watchPosition(pos => {
      const lat = pos.coords.latitude;
      const lng = pos.coords.longitude;

      path.push([lat, lng]);
      polyline.addLatLng([lat, lng]);

      if (path.length > 1) {
        const last = path[path.length - 2];
        currentDistance += getDistanceFromLatLonInKm(last[0], last[1], lat, lng);
      }

      distanceDisplay.textContent = currentDistance.toFixed(2);
      updatePace();
    });
  }
});

// Track elapsed time properly
let elapsedTime = 0;

function updateTimer() {
  elapsedTime = Math.floor((Date.now() - startTime) / 1000);
  const min = Math.floor(elapsedTime / 60);
  const sec = elapsedTime % 60;
  timeDisplay.textContent = `${min}:${sec.toString().padStart(2, "0")}`;
}

// Modify Start
startBtn.addEventListener("click", () => {
  if (isRunning) return;
  isRunning = true;
  isPaused = false;
  elapsedTime = 0;
  startTime = Date.now();
  currentDistance = 0;
  path = [];
  polyline.setLatLngs([]);

  startBtn.disabled = true;
  stopBtn.disabled = false;
  pauseBtn.disabled = false;
  pauseBtn.textContent = "⏸️ Pause";

  timerInterval = setInterval(updateTimer, 1000);

//   watchId = navigator.geolocation.watchPosition(...); // same as before
});

// Modify Stop
stopBtn.addEventListener("click", () => {
  if (!isRunning) return;
  isRunning = false;
  isPaused = false;

  clearInterval(timerInterval);
  navigator.geolocation.clearWatch(watchId);

  startBtn.disabled = false;
  stopBtn.disabled = true;
  pauseBtn.disabled = true;

  saveRun(); // call save function
});

// Auto-save if user closes tab
window.addEventListener("beforeunload", (e) => {
  if (isRunning) {
    saveRun(true); // mark as autosave
  }
});

// Extract save logic
function saveRun(auto = false) {
  const runData = {
    userId: 1, // TODO: replace with session user_id
    distance: currentDistance.toFixed(2),
    duration: elapsedTime,
    path
  };

  fetch("save_run.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(runData)
  });
  
  if (!auto) {
    alert("Run saved!");
    loadRunHistory();
  }
}

// test save to Firebase
db.ref("test").set({
  message: "Hello Firebase from PCC Hub!"
});


// Example: update live running data in Firebase
function saveLiveRun(userId, runData) {
    db.ref("live_runs/" + userId).set(runData);
}

// Example usage while running
let runData = {
    startTime: Date.now(),
    distance: 1.23, // km
    pace: "05:32",
    coords: [
        { lat: 5.4164, lng: 100.3327 },
        { lat: 5.4170, lng: 100.3330 }
    ]
};

saveLiveRun("user123", runData);
