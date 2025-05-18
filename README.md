# citizen_engagement_system
this is my project in hackthon program

# 🛠️ Citizen Complaint Management System

## 📌 Project Summary

A web application for **citizens to submit complaints** related to public services and for **administrators to manage and route** these complaints to relevant government agencies. The system ensures proper tracking, transparency, and follow-up.

---

## 👥 Roles & Access

### 🧍 Citizens
- Submit complaints using a clean form.
- View their own complaints and statuses.

**Example Citizen**  
Name: Tuyishimire Angelo  
Email: tuyishimireangelo@gmail.com

---

### 👨💼 Admin Staff
- Login securely using email and password.
- View all user complaints.
- Assign each complaint to the correct agency.
- Change complaint status (e.g., from "Pending" to "Resolved").
- Delete or edit complaints when necessary.

**Example Admins**  
- Sammy (Email: sammy@gmail.com)  
- Marry (Email: marry@gmail.com)

---

## 📁 Database Tables

### 1. `users`

| Column     | Type       | Description               |
|------------|------------|---------------------------|
| id         | INT        | Unique user ID            |
| name       | VARCHAR    | Full name                 |
| email      | VARCHAR    | Login email               |
| password   | VARCHAR    | Hashed password (bcrypt)  |
| role       | VARCHAR    | 'staff' or null (citizen) |
| created_at | DATETIME   | Registration timestamp    |

---

### 2. `complaints`

| Column     | Type     | Description                             |
|------------|----------|-----------------------------------------|
| id         | INT      | Complaint ID                            |
| user_id    | INT      | FK to users.id                          |
| title      | TEXT     | Complaint subject/title                 |
| description| TEXT     | Detailed description                    |
| category   | VARCHAR  | Complaint category                      |
| status     | VARCHAR  | 'Pending', 'In Progress', or 'Resolved' |
| created_at | DATETIME | Time of submission                      |

---

### 3. `agencies`

| Column     | Type     | Description                           |
|------------|----------|---------------------------------------|
| agency_id  | INT      | Unique agency ID                      |
| name       | VARCHAR  | Full name of the agency               |
| category   | VARCHAR  | Field of responsibility               |

**Example Agencies:**
- Rwanda National Police (Public Safety)
- Rwanda Biomedical Center (Health Services)
- Rwanda Housing Authority (Urban Planning)
- Rwanda Energy Group (Utilities and Energy)
- Water and Sanitation Corporation (Water and Sanitation)

---

### 4. `complaint_agencies`

| Column       | Type     | Description                      |
|--------------|----------|----------------------------------|
| complaint_id | INT      | FK to complaint                  |
| agency_id    | INT      | FK to assigned agency            |
| assigned_at  | DATETIME | Timestamp of the assignment      |

---

## 🧭 How It Works

### Step-by-Step Flow:

1. **User submits a complaint**
   - Fills form: title, description, category.
   - Complaint is saved in `complaints` with status `"Pending"`.

2. **Admin logs in**
   - Views all complaints (joined with user details).
   - Clicks **"Route"** to assign complaint to a relevant agency.

3. **Assignment stored**
   - Links `complaint_id` with `agency_id` in `complaint_agencies`.
   - Prevents duplicate assignments.

4. **Admin updates status**
   - From `"Pending"` → `"In Progress"` or `"Resolved"`.

5. **Complaint appears with agency**
   - Joined data shows complaint, user, and agency.

---

## ✅ Real Data Example

### Complaint:  
> **Noise Disturbance in Nyamirambo at Night**  
> “I live near the main road in Nyamirambo, and every night it gets very loud due to bars and traffic…”

- **Submitted by**: Tuyishimire Angelo (User ID: 40)  
- **Category**: Public Safety  
- **Status**: Resolved  
- **Agency Assigned**: Rwanda National Police (Agency ID: 7)  
- **Assignment Time**: 2025-05-18 14:43:06

---

## ⚙️ Technologies Used

- **PHP** (Core logic, routing, login, admin panel)
- **MySQL** (Database)
- **HTML/CSS** (Frontend layout and design)
- **JavaScript** (Confirmation modals for delete & routing)
- **Session Management** (Admin login security)

---

## 🔐 Security

- Passwords hashed with `bcrypt`.
- Admin pages protected with session checks.
- Routing and deletion operations use `POST` and confirmations.

---

## 🧪 How to Run

1. Import the SQL schema into MySQL (`citizen_feedback`).
2. Update database credentials in `db_config.php`.
3. Start Apache and MySQL via XAMPP or similar.
4. Access:
   - Citizen Portal: `http://localhost/submit_complaint.php`
   - Admin Panel: `http://localhost/admin_login.php`

---

## 👨💻 Developer Info

**Name**: Sammy  
**Email**: sammy@gmail.com  
**Role**: Admin/Developer

---
### 📥 Database Setup
1. Open phpMyAdmin and create a database named `citizen_feedback`.
2. Import `citizen_feedback.sql` file.
3. Update your `db_config.php` file with correct DB credentials.

