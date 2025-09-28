# 📚 Project Features Documentation

This document outlines the core features and functionalities of the system.

---

## 🚀 Main Features

### 1. Dashboard (Latest Announcement)
- Always updates in **real-time** when the admin posts new announcements/events.  
- Displays only the **4 most recent announcements**.  
- Simple and clean design for better user experience.  

---

### 2. Group Chat
- Chatbox connected directly to the **database**.  
- Displays **user name** based on login session.  
- Supported features:  
  - 📷 Upload & download images  
  - 📞 Voice call & video call  
  - 📍 Share location  
  - 👥 Create and join group chats  
  - 📅 Make meetings  

---

### 3. Go Fitness
- **Real-time tracking** for distance & time.  
- Buttons to control activities: **Start, Pause, Stop**.  
- Auto-save all activities to the database.  
- Shows **latest 5 activity history**, with a *View All* button for full history.  
- Linked with **Enroll Program Page** → when user enrolls, the activity is synced here.  

---

### 4. Student Statistic & Merit Ranking
- Displays student statistics connected with another database.  
- **Merit ranking system (PTS)** to compare user performance.  
- Updates in **real-time** → if user B overtakes user A, ranking changes instantly.  
- Loads **user profile** within the ranking list.  

---

### 5. Announcement Page
- Displays all announcements posted by the admin.  
- Users can view full details of each announcement.  

---

### 6. Certificate Page
- Shows **demo certificates**.  
- Users can **view and download** certificates.  
- When a program is completed → certificate is automatically displayed here.  

---

  ### 7. Profile Page
  - Users can upload their own **profile image**.  
  - If no image uploaded → automatically displays the **first letter of the user's name**.  
  - Button Logout problem.

---

## ➕ Add-On Features

### 1. Enroll Program Page
- Users can enroll into available programs.  
- When marked as completed:  
  - Progress syncs with **Go Fitness**.  
  - Certificate is automatically generated in **Certificate Page**.  

---

## 🗂️ System Flow (Simplified)

**Admin** → Manage Announcement & Program  
⬇️  
**User** → Enroll Program → Go Fitness Tracking → Certificate  
⬇️  
**All Users** → Group Chat + Merit Ranking + Profile Management  

