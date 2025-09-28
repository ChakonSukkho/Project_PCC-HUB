// Replace with your actual Firebase project credentials
const firebaseConfig = {
  apiKey: "YOUR_KEY",
  authDomain: "YOUR_PROJECT.firebaseapp.com",
  databaseURL: "https://YOUR_PROJECT.firebaseio.com",
  projectId: "pcc-hub",
  storageBucket: "pcc-hub.appspot.com",
  messagingSenderId: "YOUR_SENDER_ID",
  appId: "YOUR_APP_ID"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Get database reference
const db = firebase.database();

// Test connection (optional - remove in production)
db.ref('.info/connected').on('value', function(snapshot) {
  if (snapshot.val() === true) {
    console.log('Firebase connected successfully');
  } else {
    console.log('Firebase disconnected');
  }
});

// Export for use in other files (if using modules)
// export { db, firebase };