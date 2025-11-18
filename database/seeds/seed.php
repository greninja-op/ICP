<?php
/**
 * Database Seeder
 * 
 * This script seeds the database with sample data for development and testing.
 * Usage: php database/seeds/seed.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Load database configuration
$dbConfig = require __DIR__ . '/../../config/database.php';
$config = $dbConfig['connections'][$dbConfig['default']];

// Create PDO connection
try {
    $dsn = sprintf(
        "%s:host=%s;port=%s;dbname=%s;charset=%s",
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Starting database seeding...\n\n";

// Helper function to hash passwords
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// 1. Seed admin user
echo "Seeding admin user...\n";
$stmt = $pdo->prepare("
    INSERT INTO users (username, email, password_hash, role, full_name, phone, is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    'admin',
    'admin@university.edu',
    hashPassword('admin123'),
    'admin',
    'System Administrator',
    '+91-9876543210',
    1
]);
$adminUserId = $pdo->lastInsertId();
echo "✓ Admin user created (username: admin, password: admin123)\n\n";

// 2. Seed teachers
echo "Seeding 10 teachers...\n";
$departments = ['BCA', 'BBA', 'B.Com', 'BSc Physics'];
$teachers = [
    ['Dr. Rajesh Kumar', 'rajesh.kumar', 'BCA', 'PhD Computer Science', 'Artificial Intelligence'],
    ['Prof. Priya Sharma', 'priya.sharma', 'BCA', 'MTech', 'Database Systems'],
    ['Dr. Amit Patel', 'amit.patel', 'BBA', 'PhD Management', 'Marketing'],
    ['Prof. Sneha Gupta', 'sneha.gupta', 'BBA', 'MBA', 'Finance'],
    ['Dr. Vikram Singh', 'vikram.singh', 'B.Com', 'PhD Commerce', 'Accounting'],
    ['Prof. Anjali Verma', 'anjali.verma', 'B.Com', 'MCom', 'Business Law'],
    ['Dr. Suresh Reddy', 'suresh.reddy', 'BSc Physics', 'PhD Physics', 'Quantum Mechanics'],
    ['Prof. Kavita Nair', 'kavita.nair', 'BSc Physics', 'MSc Physics', 'Thermodynamics'],
    ['Dr. Rahul Mehta', 'rahul.mehta', 'BCA', 'PhD', 'Software Engineering'],
    ['Prof. Deepa Joshi', 'deepa.joshi', 'BBA', 'MBA', 'Human Resources']
];

$teacherIds = [];
foreach ($teachers as $index => $teacher) {
    // Create user
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password_hash, role, full_name, phone, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $teacher[1],
        $teacher[1] . '@university.edu',
        hashPassword('teacher123'),
        'teacher',
        $teacher[0],
        '+91-98765432' . str_pad($index + 10, 2, '0', STR_PAD_LEFT),
        1
    ]);
    $userId = $pdo->lastInsertId();
    
    // Create teacher record
    $stmt = $pdo->prepare("
        INSERT INTO teachers (user_id, teacher_id, department, qualification, specialization, joining_date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        'T' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
        $teacher[2],
        $teacher[3],
        $teacher[4],
        date('Y-m-d', strtotime('-' . rand(1, 10) . ' years'))
    ]);
    $teacherIds[$teacher[2]][] = $pdo->lastInsertId();
}
echo "✓ 10 teachers created\n\n";

// 3. Seed students
echo "Seeding 50 students...\n";
$firstNames = ['Aarav', 'Vivaan', 'Aditya', 'Vihaan', 'Arjun', 'Sai', 'Arnav', 'Ayaan', 'Krishna', 'Ishaan',
               'Ananya', 'Diya', 'Aadhya', 'Saanvi', 'Kiara', 'Anika', 'Navya', 'Angel', 'Pari', 'Sara'];
$lastNames = ['Sharma', 'Verma', 'Patel', 'Kumar', 'Singh', 'Gupta', 'Reddy', 'Nair', 'Mehta', 'Joshi'];

$studentIds = [];
$studentCount = 0;
foreach ($departments as $dept) {
    $studentsPerDept = ($dept === 'BCA') ? 15 : 12; // More BCA students
    
    for ($i = 0; $i < $studentsPerDept; $i++) {
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $fullName = $firstName . ' ' . $lastName;
        $username = strtolower($firstName . '.' . $lastName . rand(1, 99));
        
        // Create user
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role, full_name, phone, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $username,
            $username . '@student.university.edu',
            hashPassword('student123'),
            'student',
            $fullName,
            '+91-' . rand(7000000000, 9999999999),
            1
        ]);
        $userId = $pdo->lastInsertId();
        
        // Create student record
        $semester = rand(1, 6);
        $admissionYear = date('Y') - (int)($semester / 2);
        
        $stmt = $pdo->prepare("
            INSERT INTO students (user_id, student_id, department, semester, admission_year, date_of_birth, guardian_name, guardian_phone)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            'S' . date('Y') . str_pad(++$studentCount, 3, '0', STR_PAD_LEFT),
            $dept,
            $semester,
            $admissionYear,
            date('Y-m-d', strtotime('-' . rand(18, 25) . ' years')),
            'Guardian of ' . $fullName,
            '+91-' . rand(7000000000, 9999999999)
        ]);
        $studentIds[$dept][] = ['id' => $pdo->lastInsertId(), 'semester' => $semester];
    }
}
echo "✓ 50 students created across departments\n\n";

// 4. Seed subjects
echo "Seeding 30 subjects...\n";
$subjectsByDept = [
    'BCA' => [
        ['BCA101', 'Programming in C', 1, 4, false],
        ['BCA102', 'Digital Electronics', 1, 3, false],
        ['BCA103', 'Mathematics-I', 1, 4, false],
        ['BCA104', 'C Programming Lab', 1, 2, true],
        ['BCA201', 'Data Structures', 2, 4, false],
        ['BCA202', 'Database Management Systems', 2, 4, false],
        ['BCA203', 'Web Technologies', 2, 3, false],
        ['BCA204', 'DBMS Lab', 2, 2, true],
    ],
    'BBA' => [
        ['BBA101', 'Principles of Management', 1, 4, false],
        ['BBA102', 'Business Economics', 1, 4, false],
        ['BBA103', 'Financial Accounting', 1, 3, false],
        ['BBA201', 'Marketing Management', 2, 4, false],
        ['BBA202', 'Human Resource Management', 2, 4, false],
        ['BBA203', 'Business Statistics', 2, 3, false],
    ],
    'B.Com' => [
        ['BCOM101', 'Financial Accounting', 1, 4, false],
        ['BCOM102', 'Business Organization', 1, 3, false],
        ['BCOM103', 'Business Economics', 1, 4, false],
        ['BCOM201', 'Corporate Accounting', 2, 4, false],
        ['BCOM202', 'Business Law', 2, 3, false],
        ['BCOM203', 'Cost Accounting', 2, 4, false],
    ],
    'BSc Physics' => [
        ['PHY101', 'Mechanics', 1, 4, false],
        ['PHY102', 'Waves and Optics', 1, 4, false],
        ['PHY103', 'Physics Lab-I', 1, 2, true],
        ['PHY201', 'Thermodynamics', 2, 4, false],
        ['PHY202', 'Electromagnetism', 2, 4, false],
        ['PHY203', 'Physics Lab-II', 2, 2, true],
    ]
];

$subjectIdMap = [];
foreach ($subjectsByDept as $dept => $subjects) {
    foreach ($subjects as $subject) {
        $teacherId = $teacherIds[$dept][array_rand($teacherIds[$dept])];
        
        $stmt = $pdo->prepare("
            INSERT INTO subjects (subject_code, subject_name, department, semester, credits, is_lab, teacher_id, description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $subject[0],
            $subject[1],
            $dept,
            $subject[2],
            $subject[3],
            $subject[4],
            $teacherId,
            'Description for ' . $subject[1]
        ]);
        $subjectIdMap[$dept][$subject[2]][] = $pdo->lastInsertId();
    }
}
echo "✓ 30 subjects created\n\n";

// 5. Enroll students in subjects
echo "Enrolling students in subjects...\n";
$academicYear = date('Y') . '-' . (date('Y') + 1);
foreach ($studentIds as $dept => $students) {
    foreach ($students as $student) {
        if (isset($subjectIdMap[$dept][$student['semester']])) {
            foreach ($subjectIdMap[$dept][$student['semester']] as $subjectId) {
                $stmt = $pdo->prepare("
                    INSERT INTO student_subjects (student_id, subject_id, academic_year)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$student['id'], $subjectId, $academicYear]);
            }
        }
    }
}
echo "✓ Students enrolled in subjects\n\n";

// 6. Seed results for students in earlier semesters
echo "Seeding sample results...\n";
$grades = ['A+' => 10, 'A' => 9, 'B+' => 8, 'B' => 7, 'C' => 6, 'D' => 5, 'F' => 0];
$resultCount = 0;

foreach ($studentIds as $dept => $students) {
    foreach ($students as $student) {
        // Only add results for students in semester 2 or higher
        if ($student['semester'] >= 2) {
            $previousSemester = $student['semester'] - 1;
            if (isset($subjectIdMap[$dept][$previousSemester])) {
                foreach ($subjectIdMap[$dept][$previousSemester] as $subjectId) {
                    // Get subject details
                    $stmt = $pdo->prepare("SELECT credits, is_lab FROM subjects WHERE id = ?");
                    $stmt->execute([$subjectId]);
                    $subjectInfo = $stmt->fetch();
                    
                    // Generate random marks
                    $isLab = $subjectInfo['is_lab'];
                    if ($isLab) {
                        $labMarks = rand(60, 100);
                        $totalMarks = $labMarks;
                        $internalMarks = null;
                        $theoryMarks = null;
                    } else {
                        $internalMarks = rand(12, 20);
                        $theoryMarks = rand(40, 80);
                        $totalMarks = $internalMarks + $theoryMarks;
                        $labMarks = null;
                    }
                    
                    // Calculate grade
                    $grade = 'F';
                    $gradePoint = 0;
                    if ($totalMarks >= 90) { $grade = 'A+'; $gradePoint = 10; }
                    elseif ($totalMarks >= 80) { $grade = 'A'; $gradePoint = 9; }
                    elseif ($totalMarks >= 70) { $grade = 'B+'; $gradePoint = 8; }
                    elseif ($totalMarks >= 60) { $grade = 'B'; $gradePoint = 7; }
                    elseif ($totalMarks >= 50) { $grade = 'C'; $gradePoint = 6; }
                    elseif ($totalMarks >= 40) { $grade = 'D'; $gradePoint = 5; }
                    
                    $creditPoints = $gradePoint * $subjectInfo['credits'];
                    $resultStatus = $totalMarks >= 40 ? 'pass' : 'fail';
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO results (student_id, subject_id, semester, academic_year, internal_marks, theory_marks, lab_marks, total_marks, grade, grade_point, credit_points, result_status, published_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $student['id'],
                        $subjectId,
                        $previousSemester,
                        $academicYear,
                        $internalMarks,
                        $theoryMarks,
                        $labMarks,
                        $totalMarks,
                        $grade,
                        $gradePoint,
                        $creditPoints,
                        $resultStatus,
                        date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
                    ]);
                    $resultCount++;
                }
            }
        }
    }
}
echo "✓ $resultCount results created\n\n";

// 7. Seed payments
echo "Seeding sample payments...\n";
$feeTypes = ['semester', 'exam', 'lab'];
$paymentCount = 0;

foreach ($studentIds as $dept => $students) {
    foreach ($students as $student) {
        // Create 2-3 payments per student
        $numPayments = rand(2, 3);
        for ($i = 0; $i < $numPayments; $i++) {
            $feeType = $feeTypes[array_rand($feeTypes)];
            $amount = ($feeType === 'semester') ? 17000 : (($feeType === 'exam') ? 1500 : 2000);
            $isPaid = rand(0, 100) > 30; // 70% paid
            
            $stmt = $pdo->prepare("
                INSERT INTO payments (student_id, fee_type, description, amount, fine_amount, total_amount, due_date, payment_date, payment_method, transaction_id, payment_status, semester, academic_year)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $student['id'],
                $feeType,
                ucfirst($feeType) . ' Fee - Semester ' . $student['semester'],
                $amount,
                0,
                $amount,
                date('Y-m-d', strtotime('+' . rand(10, 60) . ' days')),
                $isPaid ? date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')) : null,
                $isPaid ? ['card', 'upi', 'qr'][array_rand(['card', 'upi', 'qr'])] : null,
                $isPaid ? 'TXN' . strtoupper(bin2hex(random_bytes(8))) : null,
                $isPaid ? 'paid' : 'pending',
                $student['semester'],
                $academicYear
            ]);
            $paymentCount++;
        }
    }
}
echo "✓ $paymentCount payments created\n\n";

// 8. Seed notices
echo "Seeding sample notices...\n";
$notices = [
    ['Academic Calendar 2025', 'The academic calendar for the year 2025 has been released. Please check the important dates for examinations and holidays.', 'academic', 'all', 5],
    ['Sports Day Event', 'Annual Sports Day will be held on March 15, 2025. All students are encouraged to participate.', 'events', 'students', 3],
    ['Library Closure Notice', 'The library will remain closed on January 26, 2025 due to Republic Day.', 'general', 'all', 2],
    ['Exam Fee Payment Deadline', 'Last date to pay examination fees is February 10, 2025. Late fees will be applicable after this date.', 'urgent', 'students', 10],
    ['Guest Lecture on AI', 'A guest lecture on Artificial Intelligence will be conducted by Dr. Smith on February 5, 2025.', 'events', 'all', 4],
    ['Semester Results Published', 'Results for Semester 1 examinations have been published. Students can check their results on the portal.', 'academic', 'students', 8],
    ['Faculty Meeting', 'All faculty members are requested to attend the meeting on January 30, 2025 at 10:00 AM.', 'general', 'teachers', 3],
    ['Scholarship Applications Open', 'Applications for merit scholarships are now open. Deadline: February 20, 2025.', 'academic', 'students', 6],
];

foreach ($notices as $notice) {
    $stmt = $pdo->prepare("
        INSERT INTO notices (title, content, category, target_audience, is_active, priority, published_by, published_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $notice[0],
        $notice[1],
        $notice[2],
        $notice[3],
        1,
        $notice[4],
        $adminUserId,
        date('Y-m-d H:i:s', strtotime('-' . rand(1, 15) . ' days'))
    ]);
}
echo "✓ " . count($notices) . " notices created\n\n";

echo "✅ Database seeding completed successfully!\n\n";
echo "Summary:\n";
echo "- 1 admin user (username: admin, password: admin123)\n";
echo "- 10 teachers (username: firstname.lastname, password: teacher123)\n";
echo "- 50 students (username: firstname.lastname##, password: student123)\n";
echo "- 30 subjects across 4 departments\n";
echo "- $resultCount results\n";
echo "- $paymentCount payments\n";
echo "- " . count($notices) . " notices\n\n";
echo "You can now log in with any of the seeded accounts!\n";
