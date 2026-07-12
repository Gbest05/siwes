# Design and Implementation of SIWES Allocation System Using Nginx and SQLite

A modern, responsive, university-grade portal for managing the Students Industrial Work Experience Scheme (SIWES). Built using an MVC PHP backend, an SQLite database, Tailwind CSS (via CDN), Alpine.js, and optimized for Nginx server architectures.

---

## Features

1. **Intelligent Allocation Engine**: Matches students to corporate attachments based on course specialization, student choices, coordinates distance, and company star reviews.
2. **Interactive Daily Logbooks**: Enables students to log work activities, attach drawing layouts, and monitor supervisor signing.
3. **Multi-Role Workspaces**:
   - **Administrators**: Audit system activities logs, run database backup restoration points.
   - **Coordinators**: Manage verification pipelines, execute auto-allocation, and print reports.
   - **Department Officers**: Access department-specific attachment statistics and enrolment lists.
   - **Students**: Download placement introductory letters, submit logs, and check performance scores.
   - **Company Supervisors**: Sign daily/weekly logs, leave feedback remarks, and submit evaluations sheets.
4. **Light and Dark Modes**: Dynamic localStorage based theme toggle.
5. **Simulated File Exports**: Export all data tables directly to CSV/Excel sheets locally and print layout views offline.

---

## Default Login Credentials

Use the following default accounts to immediately access the systems:

| Role | Username | Password | Purpose |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | Backups management, global stats auditing. |
| **SIWES Coordinator** | `coordinator` | `coord123` | Running allocation algorithm, company verifications. |
| **Department Officer** | `officer_cs` | `officer123` | Department student list auditing. |
| **Student** | `student1` | `student123` | Logbook records, introductory letter printing. |
| **Company Supervisor** | `flutter_super` | `super123` | Signing logs and submitting final evaluations (Flutterwave). |
| **Academic Supervisor** | `acad_super1` | `super123` | Leaving academic supervisor comments. |

---

## Installation & Deployment Guide

### Prerequisites
- **PHP 8.0+** installed.
- **SQLite3 extension** enabled in PHP configuration.
- **Nginx Web Server** or Apache server setup.

### Step 1: Clone and Configure
1. Extract or copy the codebase to your XAMPP web directory (e.g. `C:\xampp\htdocs\siwes`).
2. Open your `php.ini` file (found via `php --ini` or inside `C:\xampp\php\php.ini`) and ensure the SQLite extension is uncommented:
   ```ini
   extension=sqlite3
   extension=pdo_sqlite
   ```

### Step 2: Initialize Database & Seed
Run the initialization script from the terminal to create the database file (`app/Database/siwes.db`) and seed the default testing accounts:
```bash
php -d extension=sqlite3 app/Database/db_init.php
```

### Step 3: Configure Web Server (Nginx or Apache)

#### Option A: Nginx (Recommended)
Copy the configuration block in `nginx.conf` into your Nginx virtual host configuration folder, restart the Nginx service, and access via `http://localhost/` or `http://siwes-portal.local/`.

#### Option B: PHP Built-in Server (Quick preview)
Navigate to the project root directory and start PHP's built-in development server:
```bash
php -d extension=sqlite3 -S localhost:8000 -t public
```
Open your browser and navigate to `http://localhost:8000/`.

---

## Intelligent Matching Scoring Logic

The automatic allocation score is computed as follows:

$$Score = (SpecMatch \times 40) + PreferenceScore + DistanceScore + RatingScore$$

1. **`SpecMatch`** ($0$ or $40$ pts): $40$ points if the company specialization keywords match the student's field of study, else $0$ points.
2. **`PreferenceScore`** ($0$ to $30$ pts): $30$ points if the company is the student's 1st choice, $20$ for 2nd choice, $10$ for 3rd choice, $0$ otherwise.
3. **`DistanceScore`** ($0$ to $20$ pts): Euclidean distance between university base location coordinates and company coordinates, scaled down to penalize high distances:
   $$DistanceScore = \max(0, 20 - (\text{Distance} \times 15))$$
4. **`RatingScore`** ($0$ to $10$ pts): Company feedback rating ($1$ to $5$ stars) multiplied by $2$.

The matcher sorts the student-company scoring matrix in descending order and assigns placements using a greedy pass, respecting company slots capacities. Locked manual placements are skipped.
