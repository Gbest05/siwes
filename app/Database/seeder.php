<?php

class Seeder {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function seed() {
        echo "Starting seeding...\n";

        // 1. Seed Departments
        $departments = [
            ['name' => 'Computer Science', 'code' => 'CSC', 'status' => 'active', 'show_on_signup' => 1]
        ];

        foreach ($departments as $dept) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO departments (name, code, status, show_on_signup) VALUES (:name, :code, :status, :show_on_signup)");
            $stmt->bindValue(':name', $dept['name']);
            $stmt->bindValue(':code', $dept['code']);
            $stmt->bindValue(':status', $dept['status'] ?? 'active');
            $stmt->bindValue(':show_on_signup', $dept['show_on_signup'] ?? 1);
            $stmt->execute();
        }
        echo "Departments seeded.\n";

        // 1b. Seed Academic Levels
        $levels = [
            ['name' => '100 Level', 'code' => '100', 'year_value' => 1, 'status' => 'active', 'show_on_signup' => 0],
            ['name' => '200 Level', 'code' => '200', 'year_value' => 2, 'status' => 'active', 'show_on_signup' => 0],
            ['name' => '300 Level', 'code' => '300', 'year_value' => 3, 'status' => 'active', 'show_on_signup' => 1],
            ['name' => '400 Level', 'code' => '400', 'year_value' => 4, 'status' => 'active', 'show_on_signup' => 1],
            ['name' => '500 Level', 'code' => '500', 'year_value' => 5, 'status' => 'active', 'show_on_signup' => 1],
        ];

        foreach ($levels as $lvl) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO academic_levels (name, code, year_value, status, show_on_signup) VALUES (:name, :code, :year_value, :status, :show_on_signup)");
            $stmt->bindValue(':name', $lvl['name']);
            $stmt->bindValue(':code', $lvl['code']);
            $stmt->bindValue(':year_value', $lvl['year_value']);
            $stmt->bindValue(':status', $lvl['status']);
            $stmt->bindValue(':show_on_signup', $lvl['show_on_signup']);
            $stmt->execute();
        }
        echo "Academic levels seeded.\n";

        // Get department IDs for references
        $deptIds = [];
        $res = $this->db->query("SELECT id, code FROM departments");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $deptIds[$row['code']] = $row['id'];
        }

        // 2. Seed Users
        // Default passwords will be hashed
        $users = [
            // Admin
            ['username' => 'adminsiwes', 'email' => 'admin@university.edu.ng', 'password' => 'admin123', 'role' => 'admin', 'status' => 'active'],
            // Coordinator
            ['username' => 'coordinator', 'email' => 'siwes_coord@university.edu.ng', 'password' => 'coord123', 'role' => 'coordinator', 'status' => 'active'],
            // Department Officer
            ['username' => 'officer_cs', 'email' => 'officer_cs@university.edu.ng', 'password' => 'officer123', 'role' => 'officer', 'status' => 'active'],
            // Students
            ['username' => 'student1', 'email' => 'samuel.alao@student.edu.ng', 'password' => 'student123', 'role' => 'student', 'status' => 'active'],
            ['username' => 'student2', 'email' => 'chidi.okafor@student.edu.ng', 'password' => 'student123', 'role' => 'student', 'status' => 'active'],
            ['username' => 'student3', 'email' => 'fatima.bello@student.edu.ng', 'password' => 'student123', 'role' => 'student', 'status' => 'active'],
            ['username' => 'student4', 'email' => 'aminu.kano@student.edu.ng', 'password' => 'student123', 'role' => 'student', 'status' => 'active'],
            ['username' => 'student5', 'email' => 'bola.tinubu@student.edu.ng', 'password' => 'student123', 'role' => 'student', 'status' => 'active'],
            ['username' => 'student6', 'email' => 'emeka.nwachukwu@student.edu.ng', 'password' => 'student123', 'role' => 'student', 'status' => 'active'],
            // Company Supervisors
            ['username' => 'glo_super', 'email' => 'glo_supervisor@glo.com', 'password' => 'super123', 'role' => 'supervisor', 'status' => 'active'],
            ['username' => 'andela_super', 'email' => 'andela_supervisor@andela.com', 'password' => 'super123', 'role' => 'supervisor', 'status' => 'active'],
            ['username' => 'flutter_super', 'email' => 'flutter_supervisor@flutterwave.com', 'password' => 'super123', 'role' => 'supervisor', 'status' => 'active'],
            ['username' => 'chevron_super', 'email' => 'chevron_supervisor@chevron.com', 'password' => 'super123', 'role' => 'supervisor', 'status' => 'active'],
            ['username' => 'nihort_super', 'email' => 'nihort_supervisor@nihort.gov.ng', 'password' => 'super123', 'role' => 'supervisor', 'status' => 'active'],
            // Academic supervisor
            ['username' => 'acad_super1', 'email' => 'acad_super1@university.edu.ng', 'password' => 'super123', 'role' => 'supervisor', 'status' => 'active'],
        ];

        foreach ($users as $u) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO users (username, email, password_hash, role, status) VALUES (:username, :email, :password_hash, :role, :status)");
            $stmt->bindValue(':username', $u['username']);
            $stmt->bindValue(':email', $u['email']);
            $stmt->bindValue(':password_hash', password_hash($u['password'], PASSWORD_BCRYPT));
            $stmt->bindValue(':role', $u['role']);
            $stmt->bindValue(':status', $u['status']);
            $stmt->execute();
        }
        echo "Users seeded.\n";

        // Get user IDs for references
        $userIds = [];
        $res = $this->db->query("SELECT id, username FROM users");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $userIds[$row['username']] = $row['id'];
        }

        // 3. Seed Companies
        $companies = [
            [
                'name' => 'Globacom Nigeria Limited',
                'email' => 'careers@glo.com',
                'phone' => '+2348055555555',
                'address' => 'Mike Adenuga Towers, 1 Mike Adenuga Close, Victoria Island, Lagos',
                'latitude' => 6.4292,
                'longitude' => 3.4354,
                'industry_category' => 'Telecommunications & Software',
                'available_slots' => 5,
                'contact_person' => 'Mr. Tunde Adebayo',
                'status' => 'verified',
                'rating' => 4,
                'specializations' => 'Software Development, Network Engineering, Cybersecurity'
            ],
            [
                'name' => 'Andela Africa',
                'email' => 'internships@andela.com',
                'phone' => '+23414545565',
                'address' => '235 Ikorodu Road, Ilupeju, Lagos',
                'latitude' => 6.5367,
                'longitude' => 3.3668,
                'industry_category' => 'Software Engineering',
                'available_slots' => 6,
                'contact_person' => 'Ms. Sarah Jenkins',
                'status' => 'verified',
                'rating' => 5,
                'specializations' => 'Software Development, UI/UX Design, Data Science'
            ],
            [
                'name' => 'Flutterwave Inc.',
                'email' => 'talent@flutterwavego.com',
                'phone' => '+2349030009581',
                'address' => '8 Providence St, Lekki Phase 1, Lagos',
                'latitude' => 6.4354,
                'longitude' => 3.4411,
                'industry_category' => 'Fintech & Cloud Computing',
                'available_slots' => 8,
                'contact_person' => 'Mr. Gbenga Agboola',
                'status' => 'verified',
                'rating' => 5,
                'specializations' => 'Software Development, DevOps, Technical Support'
            ],
            [
                'name' => 'Chevron Nigeria Limited',
                'email' => 'chevroninterns@chevron.com',
                'phone' => '+23412760000',
                'address' => 'Lekki Peninsula, Lagos',
                'latitude' => 6.4285,
                'longitude' => 3.4215,
                'industry_category' => 'Energy & Engineering',
                'available_slots' => 3,
                'contact_person' => 'Engr. Obi Nwosu',
                'status' => 'verified',
                'rating' => 5,
                'specializations' => 'Mechanical Engineering, Electrical Engineering, Instrumentation'
            ],
            [
                'name' => 'National Institute of Horticultural Research (NIHORT)',
                'email' => 'info@nihort.gov.ng',
                'phone' => '+2348033830200',
                'address' => 'Idi-Ishin, Jericho, Ibadan, Oyo State',
                'latitude' => 7.3912,
                'longitude' => 3.8424,
                'industry_category' => 'Agriculture & BioScience',
                'available_slots' => 4,
                'contact_person' => 'Dr. Mrs. Alice Banjo',
                'status' => 'verified',
                'rating' => 3,
                'specializations' => 'Biochemistry, Plant Pathology, Soil Science'
            ],
            [
                'name' => 'Pending Tech Solutions',
                'email' => 'info@pendingtech.com',
                'phone' => '+2347012345678',
                'address' => '12 Herbert Macaulay Way, Yaba, Lagos',
                'latitude' => 6.5095,
                'longitude' => 3.3711,
                'industry_category' => 'Information Technology',
                'available_slots' => 2,
                'contact_person' => 'James Cole',
                'status' => 'pending',
                'rating' => 3,
                'specializations' => 'Web Design, Hardware Repair'
            ]
        ];

        foreach ($companies as $c) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO companies (name, email, phone, address, latitude, longitude, industry_category, available_slots, contact_person, status, rating, specializations) VALUES (:name, :email, :phone, :address, :latitude, :longitude, :industry_category, :available_slots, :contact_person, :status, :rating, :specializations)");
            $stmt->bindValue(':name', $c['name']);
            $stmt->bindValue(':email', $c['email']);
            $stmt->bindValue(':phone', $c['phone']);
            $stmt->bindValue(':address', $c['address']);
            $stmt->bindValue(':latitude', $c['latitude']);
            $stmt->bindValue(':longitude', $c['longitude']);
            $stmt->bindValue(':industry_category', $c['industry_category']);
            $stmt->bindValue(':available_slots', $c['available_slots']);
            $stmt->bindValue(':contact_person', $c['contact_person']);
            $stmt->bindValue(':status', $c['status']);
            $stmt->bindValue(':rating', $c['rating']);
            $stmt->bindValue(':specializations', $c['specializations']);
            $stmt->execute();
        }
        echo "Companies seeded.\n";

        // Get company IDs
        $companyIds = [];
        $res = $this->db->query("SELECT id, name FROM companies");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $companyIds[$row['name']] = $row['id'];
        }

        // 4. Seed Supervisors
        $supervisors = [
            ['username' => 'glo_super', 'company_name' => 'Globacom Nigeria Limited', 'first_name' => 'Tunde', 'last_name' => 'Adebayo', 'phone' => '+2348051234567', 'type' => 'company'],
            ['username' => 'andela_super', 'company_name' => 'Andela Africa', 'first_name' => 'Sarah', 'last_name' => 'Jenkins', 'phone' => '+2348039876543', 'type' => 'company'],
            ['username' => 'flutter_super', 'company_name' => 'Flutterwave Inc.', 'first_name' => 'Gbenga', 'last_name' => 'Agboola', 'phone' => '+2349033332222', 'type' => 'company'],
            ['username' => 'chevron_super', 'company_name' => 'Chevron Nigeria Limited', 'first_name' => 'Obi', 'last_name' => 'Nwosu', 'phone' => '+2348123456789', 'type' => 'company'],
            ['username' => 'nihort_super', 'company_name' => 'National Institute of Horticultural Research (NIHORT)', 'first_name' => 'Alice', 'last_name' => 'Banjo', 'phone' => '+2347065432109', 'type' => 'company'],
            ['username' => 'acad_super1', 'company_name' => null, 'first_name' => 'Dr. Charles', 'last_name' => 'Koffi', 'phone' => '+2348011223344', 'type' => 'institution'],
        ];

        foreach ($supervisors as $s) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO supervisors (user_id, company_id, first_name, last_name, phone, type) VALUES (:user_id, :company_id, :first_name, :last_name, :phone, :type)");
            $stmt->bindValue(':user_id', $userIds[$s['username']]);
            $stmt->bindValue(':company_id', $s['company_name'] ? $companyIds[$s['company_name']] : null);
            $stmt->bindValue(':first_name', $s['first_name']);
            $stmt->bindValue(':last_name', $s['last_name']);
            $stmt->bindValue(':phone', $s['phone']);
            $stmt->bindValue(':type', $s['type']);
            $stmt->execute();
        }
        echo "Supervisors seeded.\n";

        // Get supervisor IDs
        $supervisorIds = [];
        $res = $this->db->query("SELECT s.id, u.username FROM supervisors s JOIN users u ON s.user_id = u.id");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $supervisorIds[$row['username']] = $row['id'];
        }

        // 5. Seed Students
        $students = [
            [
                'username' => 'student1',
                'matric_no' => 'CSC/2021/045',
                'first_name' => 'Samuel',
                'last_name' => 'Alao',
                'department_code' => 'CSC',
                'course' => 'Computer Science',
                'year' => 4,
                'phone' => '+2347087654321',
                'status' => 'allocated', // Will link to allocation
                'pref1' => 'Flutterwave Inc.',
                'pref2' => 'Andela Africa',
                'pref3' => 'Globacom Nigeria Limited'
            ],
            [
                'username' => 'student2',
                'matric_no' => 'CSC/2021/012',
                'first_name' => 'Chidi',
                'last_name' => 'Okafor',
                'department_code' => 'CSC',
                'course' => 'Computer Science',
                'year' => 4,
                'phone' => '+2348023456789',
                'status' => 'allocated',
                'pref1' => 'Chevron Nigeria Limited',
                'pref2' => 'Globacom Nigeria Limited',
                'pref3' => 'Andela Africa'
            ],
            [
                'username' => 'student3',
                'matric_no' => 'CSC/2021/089',
                'first_name' => 'Fatima',
                'last_name' => 'Bello',
                'department_code' => 'CSC',
                'course' => 'Computer Science',
                'year' => 4,
                'phone' => '+2349054321098',
                'status' => 'allocated',
                'pref1' => 'National Institute of Horticultural Research (NIHORT)',
                'pref2' => 'Andela Africa',
                'pref3' => 'Flutterwave Inc.'
            ],
            [
                'username' => 'student4',
                'matric_no' => 'CSC/2021/092',
                'first_name' => 'Aminu',
                'last_name' => 'Kano',
                'department_code' => 'CSC',
                'course' => 'Computer Science',
                'year' => 4,
                'phone' => '+2348122334455',
                'status' => 'unallocated', // Eligible for Auto-Allocation
                'pref1' => 'Flutterwave Inc.',
                'pref2' => 'Globacom Nigeria Limited',
                'pref3' => 'Andela Africa'
            ],
            [
                'username' => 'student5',
                'matric_no' => 'CSC/2021/056',
                'first_name' => 'Bola',
                'last_name' => 'Tinubu',
                'department_code' => 'CSC',
                'course' => 'Computer Science',
                'year' => 4,
                'phone' => '+2347033445566',
                'status' => 'unallocated',
                'pref1' => 'Chevron Nigeria Limited',
                'pref2' => 'Globacom Nigeria Limited',
                'pref3' => 'National Institute of Horticultural Research (NIHORT)'
            ],
            [
                'username' => 'student6',
                'matric_no' => 'CSC/2021/021',
                'first_name' => 'Emeka',
                'last_name' => 'Nwachukwu',
                'department_code' => 'CSC',
                'course' => 'Computer Science',
                'year' => 4,
                'phone' => '+2348088990011',
                'status' => 'unallocated',
                'pref1' => 'Andela Africa',
                'pref2' => 'Flutterwave Inc.',
                'pref3' => 'Globacom Nigeria Limited'
            ]
        ];

        foreach ($students as $s) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO students (user_id, matric_no, first_name, last_name, department_id, course_of_study, year_of_study, phone, siwes_status, pref_company_1_id, pref_company_2_id, pref_company_3_id) VALUES (:user_id, :matric_no, :first_name, :last_name, :department_id, :course_of_study, :year_of_study, :phone, :siwes_status, :p1, :p2, :p3)");
            $stmt->bindValue(':user_id', $userIds[$s['username']]);
            $stmt->bindValue(':matric_no', $s['matric_no']);
            $stmt->bindValue(':first_name', $s['first_name']);
            $stmt->bindValue(':last_name', $s['last_name']);
            $stmt->bindValue(':department_id', $deptIds[$s['department_code']]);
            $stmt->bindValue(':course_of_study', $s['course']);
            $stmt->bindValue(':year_of_study', $s['year']);
            $stmt->bindValue(':phone', $s['phone']);
            $stmt->bindValue(':siwes_status', $s['status']);
            $stmt->bindValue(':p1', $companyIds[$s['pref1']]);
            $stmt->bindValue(':p2', $companyIds[$s['pref2']]);
            $stmt->bindValue(':p3', $companyIds[$s['pref3']]);
            $stmt->execute();
        }
        echo "Students seeded.\n";

        // Get student IDs
        $studentIds = [];
        $res = $this->db->query("SELECT s.id, u.username FROM students s JOIN users u ON s.user_id = u.id");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $studentIds[$row['username']] = $row['id'];
        }

        // 6. Seed Allocations for student1, student2, student3
        $allocations = [
            [
                'student' => 'student1',
                'company' => 'Flutterwave Inc.',
                'supervisor' => 'flutter_super',
                'acad_super' => 'acad_super1',
                'status' => 'locked', // locked so auto allocation doesn't move them
                'by' => 'manual'
            ],
            [
                'student' => 'student2',
                'company' => 'Chevron Nigeria Limited',
                'supervisor' => 'chevron_super',
                'acad_super' => 'acad_super1',
                'status' => 'active',
                'by' => 'auto'
            ],
            [
                'student' => 'student3',
                'company' => 'National Institute of Horticultural Research (NIHORT)',
                'supervisor' => 'nihort_super',
                'acad_super' => 'acad_super1',
                'status' => 'active',
                'by' => 'auto'
            ]
        ];

        foreach ($allocations as $a) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO allocations (student_id, company_id, supervisor_id, institution_supervisor_id, status, allocated_by) VALUES (:student_id, :company_id, :supervisor_id, :institution_supervisor_id, :status, :allocated_by)");
            $stmt->bindValue(':student_id', $studentIds[$a['student']]);
            $stmt->bindValue(':company_id', $companyIds[$a['company']]);
            $stmt->bindValue(':supervisor_id', $supervisorIds[$a['supervisor']]);
            $stmt->bindValue(':institution_supervisor_id', $supervisorIds[$a['acad_super']]);
            $stmt->bindValue(':status', $a['status']);
            $stmt->bindValue(':allocated_by', $a['by']);
            $stmt->execute();

            // Decrement available slots in target company
            $this->db->exec("UPDATE companies SET available_slots = available_slots - 1 WHERE id = " . $companyIds[$a['company']]);
        }
        echo "Allocations seeded.\n";

        // 7. Seed Logbooks for student1
        // Create 5 mock activities for student1 (Week 1)
        $activities = [
            ['date' => '2026-07-06', 'day' => 'Monday', 'desc' => 'Orientation and introduction to Flutterwave payment gateway architecture. Set up local development environment with PHP and SQLite.'],
            ['date' => '2026-07-07', 'day' => 'Tuesday', 'desc' => 'Studied API integration patterns and resolved local test merchant configuration. Integrated standard checkout v3 demo.'],
            ['date' => '2026-07-08', 'day' => 'Wednesday', 'desc' => 'Collaborated with team to refactor API payload validation logic. Set up unit testing using phpunit.'],
            ['date' => '2026-07-09', 'day' => 'Thursday', 'desc' => 'Attended design sprint session for dashboard UX redesign. Reviewed tailwind guidelines and Figma wireframes.'],
            ['date' => '2026-07-10', 'day' => 'Friday', 'desc' => 'Built a helper utility in PHP to parse webhook events and update internal transactions table. Demoed to supervisor.']
        ];

        foreach ($activities as $act) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO logbooks (student_id, date, day_of_week, activity_description, supervisor_status, supervisor_comments, coordinator_comments, week_number) VALUES (:student_id, :date, :day_of_week, :activity_description, :supervisor_status, :supervisor_comments, :coordinator_comments, :week_number)");
            $stmt->bindValue(':student_id', $studentIds['student1']);
            $stmt->bindValue(':date', $act['date']);
            $stmt->bindValue(':day_of_week', $act['day']);
            $stmt->bindValue(':activity_description', $act['desc']);
            $stmt->bindValue(':supervisor_status', 'approved');
            $stmt->bindValue(':supervisor_comments', 'Good progress. Code compiles and runs fine.');
            $stmt->bindValue(':coordinator_comments', 'Keep up the good work.');
            $stmt->bindValue(':week_number', 1);
            $stmt->execute();
        }

        // Add a pending log for today
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO logbooks (student_id, date, day_of_week, activity_description, supervisor_status, week_number) VALUES (:student_id, :date, :day_of_week, :activity_description, :supervisor_status, :week_number)");
        $stmt->bindValue(':student_id', $studentIds['student1']);
        $stmt->bindValue(':date', '2026-07-11');
        $stmt->bindValue(':day_of_week', 'Saturday');
        $stmt->bindValue(':activity_description', 'Began studying Redis caching implementation for high-traffic payment processing terminals.');
        $stmt->bindValue(':supervisor_status', 'pending');
        $stmt->bindValue(':week_number', 1);
        $stmt->execute();

        echo "Logbook entries seeded for student1.\n";

        // 8. Seed Weekly Report for student1 (Week 1)
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO weekly_reports (student_id, week_number, summary, supervisor_status, supervisor_comments, coordinator_comments, supervisor_grade) VALUES (:student_id, :week_number, :summary, :supervisor_status, :supervisor_comments, :coordinator_comments, :supervisor_grade)");
        $stmt->bindValue(':student_id', $studentIds['student1']);
        $stmt->bindValue(':week_number', 1);
        $stmt->bindValue(':summary', 'Completed local setup, payment gate UI prototype, and integrated SQLite log testing framework. Learnt modern webhook handling paradigms.');
        $stmt->bindValue(':supervisor_status', 'approved');
        $stmt->bindValue(':supervisor_comments', 'Excellent weekly summary. Highly proactive.');
        $stmt->bindValue(':coordinator_comments', 'Well done.');
        $stmt->bindValue(':supervisor_grade', 9);
        $stmt->execute();

        // 9. Seed Attendance for student1
        $attendanceDates = ['2026-07-06', '2026-07-07', '2026-07-08', '2026-07-09', '2026-07-10'];
        foreach ($attendanceDates as $d) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO attendance (student_id, date, status) VALUES (:student_id, :date, :status)");
            $stmt->bindValue(':student_id', $studentIds['student1']);
            $stmt->bindValue(':date', $d);
            $stmt->bindValue(':status', 'present');
            $stmt->execute();
        }
        echo "Attendance records seeded for student1.\n";

        // 10. Seed System Settings
        $settings = [
            ['key' => 'system_name', 'value' => 'SIWES Placement Portal'],
            ['key' => 'institution_name', 'value' => 'State University of Technology'],
            ['key' => 'current_session', 'value' => '2025/2026'],
            ['key' => 'allocation_locked', 'value' => '0'],
            ['key' => 'backup_frequency', 'value' => 'weekly']
        ];

        foreach ($settings as $set) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)");
            $stmt->bindValue(':key', $set['key']);
            $stmt->bindValue(':value', $set['value']);
            $stmt->execute();
        }
        echo "Settings seeded.\n";

        // 11. Seed Notifications
        $notifications = [
            ['user_id' => $userIds['adminsiwes'], 'title' => 'System Initialized', 'message' => 'The SIWES system database and basic seed configurations have been set up successfully.', 'type' => 'success'],
            ['user_id' => $userIds['student1'], 'title' => 'SIWES Allocation Approved', 'message' => 'Congratulations! You have been allocated to Flutterwave Inc. for your SIWES placement.', 'type' => 'success'],
            ['user_id' => $userIds['student4'], 'title' => 'Profile Incomplete', 'message' => 'Please upload your passport photograph and complete your preference options to proceed.', 'type' => 'warning'],
            ['user_id' => $userIds['glo_super'], 'title' => 'New Student Assigned', 'message' => 'A new student has been assigned to Globacom. Please review their details.', 'type' => 'info']
        ];

        foreach ($notifications as $n) {
            $stmt = $this->db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:user_id, :title, :message, :type)");
            $stmt->bindValue(':user_id', $n['user_id']);
            $stmt->bindValue(':title', $n['title']);
            $stmt->bindValue(':message', $n['message']);
            $stmt->bindValue(':type', $n['type']);
            $stmt->execute();
        }
        echo "Notifications seeded.\n";

        // 12. Seed Messages
        $messages = [
            [
                'sender' => 'coordinator',
                'receiver' => 'student1',
                'subject' => 'Placement Details Clarification',
                'message' => 'Hello Samuel, please ensure you download your placement letter and submit it to Flutterwave HR within 48 hours.'
            ],
            [
                'sender' => 'student1',
                'receiver' => 'coordinator',
                'subject' => 'Re: Placement Details Clarification',
                'message' => 'Thank you, Sir. I have already printed the letter and handed it to my company supervisor, Mr. Gbenga Agboola.'
            ]
        ];

        foreach ($messages as $m) {
            $stmt = $this->db->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES (:sender_id, :receiver_id, :subject, :message)");
            $stmt->bindValue(':sender_id', $userIds[$m['sender']]);
            $stmt->bindValue(':receiver_id', $userIds[$m['receiver']]);
            $stmt->bindValue(':subject', $m['subject']);
            $stmt->bindValue(':message', $m['message']);
            $stmt->execute();
        }
        echo "Messages seeded.\n";

        // 13. Seed Specialized Fields
        $specialized_fields = [
            [
                'title' => 'UI/UX & Product Design',
                'description' => 'Learn the basics of interface design and create stunning layouts for mobile/web apps attachment.',
                'badge' => 'Bestseller',
                'image_name' => 'ui_ux.png',
                'company_name' => 'Flutterwave',
                'company_initials' => 'FL',
                'rating' => 4.8,
                'slots_count' => 8,
                'status' => 'Active'
            ],
            [
                'title' => 'Software Engineering',
                'description' => 'Complete guide to modern software engineering, web application setups, and MVC models.',
                'badge' => 'Hot',
                'image_name' => 'software.png',
                'company_name' => 'Andela',
                'company_initials' => 'AN',
                'rating' => 4.9,
                'slots_count' => 6,
                'status' => 'Active'
            ],
            [
                'title' => 'Fintech & Cloud Systems',
                'description' => 'Master transaction APIs integration, secure webhooks processing, and server load balancing.',
                'badge' => 'New',
                'image_name' => 'fintech.png',
                'company_name' => 'Globacom',
                'company_initials' => 'GL',
                'rating' => 4.5,
                'slots_count' => 5,
                'status' => 'Active'
            ],
            [
                'title' => 'Bioscience & Chemistry',
                'description' => 'Learn plant pathology testing, laboratory analytics, and soil sample chemical extraction.',
                'badge' => 'Trending',
                'image_name' => 'biochem.png',
                'company_name' => 'NIHORT',
                'company_initials' => 'NI',
                'rating' => 4.7,
                'slots_count' => 4,
                'status' => 'Active'
            ]
        ];

        foreach ($specialized_fields as $sf) {
            $stmt = $this->db->prepare("INSERT OR IGNORE INTO specialized_fields (title, description, badge, image_name, company_name, company_initials, rating, slots_count, status) VALUES (:title, :description, :badge, :image_name, :company_name, :company_initials, :rating, :slots_count, :status)");
            $stmt->bindValue(':title', $sf['title']);
            $stmt->bindValue(':description', $sf['description']);
            $stmt->bindValue(':badge', $sf['badge']);
            $stmt->bindValue(':image_name', $sf['image_name']);
            $stmt->bindValue(':company_name', $sf['company_name']);
            $stmt->bindValue(':company_initials', $sf['company_initials']);
            $stmt->bindValue(':rating', $sf['rating']);
            $stmt->bindValue(':slots_count', $sf['slots_count']);
            $stmt->bindValue(':status', $sf['status']);
            $stmt->execute();
        }
        echo "Specialized fields seeded.\n";

        echo "Seeding completed successfully.\n";
    }
}
