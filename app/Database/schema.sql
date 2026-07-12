-- Enable Foreign Key Support in SQLite
PRAGMA foreign_keys = ON;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT CHECK(role IN ('admin', 'coordinator', 'officer', 'student', 'supervisor')) NOT NULL,
    status TEXT CHECK(status IN ('active', 'inactive')) DEFAULT 'active',
    profile_pic TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments Table
CREATE TABLE IF NOT EXISTS departments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    code TEXT UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    matric_no TEXT UNIQUE NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    department_id INTEGER NOT NULL,
    course_of_study TEXT NOT NULL,
    year_of_study INTEGER NOT NULL,
    phone TEXT,
    passport_path TEXT,
    cv_path TEXT,
    placement_letter_path TEXT,
    siwes_status TEXT CHECK(siwes_status IN ('unallocated', 'pending_approval', 'allocated', 'completed')) DEFAULT 'unallocated',
    pref_company_1_id INTEGER,
    pref_company_2_id INTEGER,
    pref_company_3_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(department_id) REFERENCES departments(id) ON DELETE RESTRICT,
    FOREIGN KEY(pref_company_1_id) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY(pref_company_2_id) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY(pref_company_3_id) REFERENCES companies(id) ON DELETE SET NULL
);

-- Companies Table
CREATE TABLE IF NOT EXISTS companies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    phone TEXT,
    address TEXT NOT NULL,
    latitude REAL NOT NULL,
    longitude REAL NOT NULL,
    industry_category TEXT NOT NULL,
    available_slots INTEGER DEFAULT 0,
    contact_person TEXT NOT NULL,
    status TEXT CHECK(status IN ('pending', 'verified', 'rejected')) DEFAULT 'pending',
    rating INTEGER CHECK(rating BETWEEN 1 AND 5) DEFAULT 5,
    specializations TEXT, -- Comma-separated specializations
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Supervisors Table
CREATE TABLE IF NOT EXISTS supervisors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    company_id INTEGER, -- Null if academic/institution supervisor
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    phone TEXT,
    type TEXT CHECK(type IN ('company', 'institution')) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(company_id) REFERENCES companies(id) ON DELETE SET NULL
);

-- Allocations Table
CREATE TABLE IF NOT EXISTS allocations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER UNIQUE NOT NULL,
    company_id INTEGER NOT NULL,
    supervisor_id INTEGER, -- Company Supervisor
    institution_supervisor_id INTEGER, -- Academic Supervisor (linked to users/supervisors table)
    status TEXT CHECK(status IN ('active', 'locked', 'completed')) DEFAULT 'active',
    allocated_by TEXT CHECK(allocated_by IN ('auto', 'manual')) DEFAULT 'auto',
    allocation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY(company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY(supervisor_id) REFERENCES supervisors(id) ON DELETE SET NULL,
    FOREIGN KEY(institution_supervisor_id) REFERENCES supervisors(id) ON DELETE SET NULL
);

-- Logbooks Table (Daily logs)
CREATE TABLE IF NOT EXISTS logbooks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    date TEXT NOT NULL, -- Format YYYY-MM-DD
    day_of_week TEXT CHECK(day_of_week IN ('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')) NOT NULL,
    activity_description TEXT NOT NULL,
    image_path TEXT,
    document_path TEXT,
    supervisor_status TEXT CHECK(supervisor_status IN ('pending', 'approved', 'queried')) DEFAULT 'pending',
    supervisor_comments TEXT,
    coordinator_comments TEXT,
    week_number INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, date),
    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Weekly Reports Table
CREATE TABLE IF NOT EXISTS weekly_reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    week_number INTEGER NOT NULL,
    summary TEXT NOT NULL,
    supervisor_status TEXT CHECK(supervisor_status IN ('pending', 'approved', 'queried')) DEFAULT 'pending',
    supervisor_comments TEXT,
    coordinator_comments TEXT,
    supervisor_grade INTEGER CHECK(supervisor_grade BETWEEN 0 AND 10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, week_number),
    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    date TEXT NOT NULL, -- Format YYYY-MM-DD
    status TEXT CHECK(status IN ('present', 'absent', 'excused')) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, date),
    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Evaluations Table
CREATE TABLE IF NOT EXISTS evaluations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER UNIQUE NOT NULL,
    company_id INTEGER NOT NULL,
    supervisor_score INTEGER CHECK(supervisor_score BETWEEN 0 AND 100) DEFAULT 0,
    supervisor_remarks TEXT,
    coordinator_score INTEGER CHECK(coordinator_score BETWEEN 0 AND 100) DEFAULT 0,
    coordinator_remarks TEXT,
    final_grade TEXT CHECK(final_grade IN ('A', 'B', 'C', 'D', 'F', 'Pending')) DEFAULT 'Pending',
    recommendations TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY(company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    is_read INTEGER CHECK(is_read IN (0, 1)) DEFAULT 0,
    type TEXT CHECK(type IN ('info', 'success', 'warning', 'danger')) DEFAULT 'info',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages Table (Inbox)
CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    subject TEXT,
    message TEXT NOT NULL,
    is_read INTEGER CHECK(is_read IN (0, 1)) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT UNIQUE NOT NULL,
    value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Audit / Activity Logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action TEXT NOT NULL,
    ip_address TEXT,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Indices for optimization
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_students_matric ON students(matric_no);
CREATE INDEX IF NOT EXISTS idx_students_user ON students(user_id);
CREATE INDEX IF NOT EXISTS idx_logbooks_student ON logbooks(student_id);
CREATE INDEX IF NOT EXISTS idx_logbooks_date ON logbooks(date);
CREATE INDEX IF NOT EXISTS idx_allocations_student ON allocations(student_id);
CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_messages_receiver ON messages(receiver_id);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    subject TEXT,
    message TEXT NOT NULL,
    status TEXT CHECK(status IN ('unread', 'read')) DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Specialized SIWES Fields Table
CREATE TABLE IF NOT EXISTS specialized_fields (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    badge TEXT,
    image_name TEXT,
    company_name TEXT,
    company_initials TEXT,
    rating REAL DEFAULT 5.0,
    slots_count INTEGER DEFAULT 0,
    status TEXT CHECK(status IN ('Active', 'Inactive')) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
