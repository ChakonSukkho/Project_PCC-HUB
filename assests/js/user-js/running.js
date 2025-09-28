// ============================
// js/firebase-config.js
// ============================
const firebaseConfig = {
  apiKey: "AIzaSyEXAMPLE-123456",
  authDomain: "pcc-hub.firebaseapp.com",
  databaseURL: "https://pcc-hub-default-rtdb.firebaseio.com",
  projectId: "pcc-hub",
  storageBucket: "pcc-hub.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:abcdefg12345"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Realtime Database reference
const db = firebase.database();


// ============================
// js/running-tracker.js
// ============================
class RunningTracker {
    constructor() {
        this.map = null;
        this.watchId = null;
        this.polyline = null;
        this.userMarker = null;
        this.isRunning = false;
        this.startTime = null;
        this.runTimer = null;
        this.path = [];
        this.totalDistance = 0;
        this.lastPosition = null;

        this.init();
    }

    async init() {
        try {
            await this.initializeMap();
            this.setupEventListeners();
        } catch (err) {
            console.error("Init error:", err);
        }
    }

    async initializeMap() {
        try {
            const pos = await this.getCurrentPosition();
            this.createMap(pos.coords.latitude, pos.coords.longitude);
        } catch {
            this.createMap(5.4164, 100.3327); // Penang default
        }
    }

    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error("Geolocation not supported"));
                return;
            }
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            });
        });
    }

    createMap(lat, lng) {
        this.map = L.map("map").setView([lat, lng], 15);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap"
        }).addTo(this.map);

        this.polyline = L.polyline([], { color: "blue" }).addTo(this.map);
        this.userMarker = L.circleMarker([lat, lng], {
            color: "#fff",
            fillColor: "#10b981",
            fillOpacity: 1,
            radius: 8,
            weight: 3
        }).addTo(this.map);
    }

    setupEventListeners() {
        const startBtn = document.getElementById("startBtn");
        const stopBtn = document.getElementById("stopBtn");

        startBtn.addEventListener("click", () => this.startRun());
        stopBtn.addEventListener("click", () => this.stopRun());
    }

    async startRun() {
        if (this.isRunning) return;

        this.isRunning = true;
        this.startTime = Date.now();
        this.totalDistance = 0;
        this.path = [];
        this.polyline.setLatLngs([]);

        document.getElementById("startBtn").disabled = true;
        document.getElementById("stopBtn").disabled = false;

        this.runTimer = setInterval(() => this.updateRunDisplay(), 1000);

        this.watchId = navigator.geolocation.watchPosition(
            (pos) => this.handleLocationUpdate(pos),
            (err) => console.error("GPS error:", err),
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    }

    async stopRun() {
        if (!this.isRunning) return;

        this.isRunning = false;
        clearInterval(this.runTimer);
        navigator.geolocation.clearWatch(this.watchId);

        document.getElementById("startBtn").disabled = false;
        document.getElementById("stopBtn").disabled = true;

        const duration = Math.floor((Date.now() - this.startTime) / 1000);
        const distance = this.totalDistance / 1000;

        const runData = {
            userId: "tempUser", // replace with real user ID if needed
            distance: distance.toFixed(2),
            duration,
            path: this.path
        };

        // Save to Firebase live
        db.ref("runs/" + runData.userId).push(runData);

        // Save to PHP/MySQL
        fetch("save_run.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(runData)
        })
        .then((res) => res.json())
        .then((data) => {
            console.log("Saved to PHP:", data);
            alert("Run saved!");
        });
    }

    handleLocationUpdate(position) {
        const { latitude, longitude } = position.coords;
        const currentPos = [latitude, longitude];

        if (this.userMarker) this.userMarker.setLatLng(currentPos);
        if (this.map) this.map.setView(currentPos, this.map.getZoom());

        if (this.lastPosition) {
            const d = this.getDistance(
                this.lastPosition[0],
                this.lastPosition[1],
                latitude,
                longitude
            );
            if (d > 5) {
                this.totalDistance += d;
                this.path.push(currentPos);
                this.polyline.addLatLng(currentPos);
            }
        } else {
            this.path.push(currentPos);
        }

        this.lastPosition = currentPos;
    }

    updateRunDisplay() {
        const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
        const distanceKm = this.totalDistance / 1000;

        document.getElementById("currentTime").textContent = this.formatTime(elapsed);
        document.getElementById("currentDistance").textContent = distanceKm.toFixed(2);
        document.getElementById("currentPace").textContent = this.formatPace(elapsed, distanceKm);

        // Live update Firebase
        db.ref("runs/tempUser/live").set({
            distance: distanceKm,
            duration: elapsed,
            path: this.path
        });
    }

    formatTime(seconds) {
        const min = Math.floor(seconds / 60);
        const sec = seconds % 60;
        return `${min}:${sec.toString().padStart(2, "0")}`;
    }

    formatPace(seconds, distanceKm) {
        if (distanceKm === 0) return "0:00";
        const pace = seconds / distanceKm;
        const min = Math.floor(pace / 60);
        const sec = Math.floor(pace % 60);
        return `${min}:${sec.toString().padStart(2, "0")}`;
    }

    getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a =
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
}

function saveFinalRun(userId, runData) {
    fetch("save_run.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            user_id: userId,
            run: runData
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log("✅ Run saved to MySQL:", data);
    })
    .catch(err => console.error("❌ Error:", err));
}



// Init when DOM loaded
document.addEventListener("DOMContentLoaded", () => {
    window.runningTracker = new RunningTracker();
});
