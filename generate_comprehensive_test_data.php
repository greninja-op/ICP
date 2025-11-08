<?php
/**
 * Comprehensive Test Data Generator
 * Generates 80 students (5 per department per year) and 20 teachers (5 per department)
 * 
 * Usage: php generate_comprehensive_test_data.php
 */

// Database configuration
$host = 'localhost';
$dbname = 'studentportal';
$username = 'root';
$password = '';

// Departments
$departments = ['BCA', 'BBA', 'B.Com', 'BSc Physics'];

// Indian first names
$firstNames = [
    'Aarav', 'Vivaan', 'Aditya', 'Vihaan', 'Arjun', 'Sai', 'Arnav', 'Ayaan', 'Krishna', 'Ishaan',
    'Shaurya', 'Atharv', 'Advik', 'Pranav', 'Reyansh', 'Aadhya', 'Ananya', 'Pari', 'Anika', 'Ira',
    'Diya', 'Saanvi', 'Myra', 'Sara', 'Anvi', 'Riya', 'Navya', 'Kiara', 'Aarohi', 'Tara',
    'Kabir', 'Dhruv', 'Rohan', 'Yash', 'Karan', 'Lakshmi', 'Priya', 'Meera', 'Kavya', 'Nisha',
    'Raj', 'Amit', 'Suresh', 'Vikram', 'Rahul', 'Pooja', 'Sneha', 'Anjali', 'Divya', 'Shreya'
];

// Indian last names
$lastNames = [
    'Sharma', 'Patel', 'Kumar', 'Singh', 'Reddy', 'Gupta', 'Verma', 'Joshi', 'Mehta', 'Desai',
    'Nair', 'Iyer', 'Rao', 'Pillai', 'Menon', 'Bhat', 'Agarwal', 'Banerjee', 'Chatterjee', 'Das',
    'Malhotra', 'Kapoor', 'Chopra', 'Khanna', 'Sethi', 'Bose', 'Ghosh', 'Mukherjee', 'Sen', 'Roy'
];

// Cities
$cities = [
    'Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad',
    'Jaipur', 'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Bhopal', 'Visakhapatnam', 'Patna'
];

// Blood groups
$bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n\n";
    echo "========================================\n";
    echo "GENERATING COMPREHENSIVE TEST DATA\n";
    echo "========================================\n\n";
    
    $hashedPassword = password_hash('123', PASSWORD_DEFAULT);
    
    // ============================================
    // GENERATE TEACHERS (5 per department = 20 total)
    // ============================================
    
    echo "GENERATING TEACHERS (5 per department)...\n";
    echo "----------------------------------------\n";
    
    $teacherStmt = $pdo->prepare("
        INSERT INTO users (username, password, full_name, email, role, department, phone, created_at)
        VALUES (:username, :password, :full_name, :email, 'staff', :department, :phone, NOW())
    ");
    
    $teacherCount = 0;
    foreach ($departments as $dept) {
        echo "\n$dept Department:\n";
        for ($i = 1; $i <= 5; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = "$firstName $lastName";
            $username = strtolower($firstName) . '.' . strtolower($lastName);
            $email = $username . '@university.edu';
            $phone = '+91 ' . rand(90000, 99999) . ' ' . rand(10000, 99999);
            
            try {
                $teacherStmt->execute([
                    'username' => $username,
                    'password' => $hashedPassword,
                    'full_name' => $fullName,
                    'email' => $email,
                    'department' => $dept,
                    'phone' => $phone
                ]);
                $teacherCount++;
                echo "  ✓ $fullName ($username)\n";
            } catch (PDOException $e) {
                // If duplicate, try with a number
                $username = $username . rand(1, 99);
                $email = $username . '@university.edu';
                try {
                    $teacherStmt->execute([
                        'username' => $username,
                        'password' => $hashedPassword,
                        'full_name' => $fullName,
                        'email' => $email,
                        'department' => $dept,
                        'phone' => $phone
                    ]);
                    $teacherCount++;
                    echo "  ✓ $fullName ($username)\n";
                } catch (PDOException $e2) {
                    echo "  ✗ Error adding $fullName\n";
                }
            }
        }
    }
    
    echo "\n✓ Total Teachers Added: $teacherCount\n\n";
    
    // ============================================
    // GENERATE STUDENTS (5 per department per year = 80 total)
    // ============================================
    
    echo "GENERATING STUDENTS (5 per department per year)...\n";
    echo "---------------------------------------------------\n";
    
    $studentStmt = $pdo->prepare("
        INSERT INTO users (
            username, password, full_name, email, role, department, year, semester, section, 
            roll_no, phone, date_of_birth, blood_group, address, guardian_name, guardian_phone, created_at
        )
        VALUES (
            :username, :password, :full_name, :email, 'student', :department, :year, :semester, :section,
            :roll_no, :phone, :date_of_birth, :blood_group, :address, :guardian_name, :guardian_phone, NOW()
        )
    ");
    
    $studentCount = 0;
    $sections = ['A', 'B', 'C'];
    
    foreach ($departments as $dept) {
        echo "\n$dept Department:\n";
        
        for ($year = 1; $year <= 4; $year++) {
            echo "  Year $year:\n";
            
            for ($i = 1; $i <= 5; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $fullName = "$firstName $lastName";
                $username = strtolower($firstName) . '.' . strtolower($lastName);
                $email = $username . '@university.edu';
                
                // Generate roll number
                $deptCode = strtoupper(str_replace(' ', '', $dept));
                $yearCode = 2025 - $year;
                $rollNo = $deptCode . $yearCode . str_pad($i, 3, '0', STR_PAD_LEFT);
                
                // Calculate semester
                $semester = ($year * 2) - 1;
                
                // Random section
                $section = $sections[array_rand($sections)];
                
                // Random phone
                $phone = '+91 ' . rand(90000, 99999) . ' ' . rand(10000, 99999);
                
                // Random DOB (18-22 years old)
                $birthYear = 2007 - $year;
                $birthMonth = rand(1, 12);
                $birthDay = rand(1, 28);
                $dob = sprintf('%d-%02d-%02d', $birthYear, $birthMonth, $birthDay);
                
                // Random blood group
                $bloodGroup = $bloodGroups[array_rand($bloodGroups)];
                
                // Random address
                $city = $cities[array_rand($cities)];
                $address = rand(1, 999) . ' ' . $lastNames[array_rand($lastNames)] . ' Street, ' . $city;
                
                // Guardian info
                $guardianFirstName = $firstNames[array_rand($firstNames)];
                $guardianName = $guardianFirstName . ' ' . $lastName;
                $guardianPhone = '+91 ' . rand(90000, 99999) . ' ' . rand(10000, 99999);
                
                try {
                    $studentStmt->execute([
                        'username' => $username,
                        'password' => $hashedPassword,
                        'full_name' => $fullName,
                        'email' => $email,
                        'department' => $dept,
                        'year' => $year,
                        'semester' => $semester,
                        'section' => $section,
                        'roll_no' => $rollNo,
                        'phone' => $phone,
                        'date_of_birth' => $dob,
                        'blood_group' => $bloodGroup,
                        'address' => $address,
                        'guardian_name' => $guardianName,
                        'guardian_phone' => $guardianPhone
                    ]);
                    $studentCount++;
                    echo "    ✓ $fullName ($rollNo) - Section $section\n";
                } catch (PDOException $e) {
                    // If duplicate, try with a number
                    $username = $username . rand(1, 99);
                    $email = $username . '@university.edu';
                    try {
                        $studentStmt->execute([
                            'username' => $username,
                            'password' => $hashedPassword,
                            'full_name' => $fullName,
                            'email' => $email,
                            'department' => $dept,
                            'year' => $year,
                            'semester' => $semester,
                            'section' => $section,
                            'roll_no' => $rollNo,
                            'phone' => $phone,
                            'date_of_birth' => $dob,
                            'blood_group' => $bloodGroup,
                            'address' => $address,
                            'guardian_name' => $guardianName,
                            'guardian_phone' => $guardianPhone
                        ]);
                        $studentCount++;
                        echo "    ✓ $fullName ($rollNo) - Section $section\n";
                    } catch (PDOException $e2) {
                        echo "    ✗ Error adding $fullName\n";
                    }
                }
            }
        }
    }
    
    echo "\n✓ Total Students Added: $studentCount\n\n";
    
    echo "========================================\n";
    echo "DATA GENERATION COMPLETE!\n";
    echo "========================================\n\n";
    
    echo "SUMMARY:\n";
    echo "--------\n";
    echo "Teachers: $teacherCount (5 per department)\n";
    echo "Students: $studentCount (5 per department per year)\n";
    echo "\nDepartments: " . implode(', ', $departments) . "\n";
    echo "\nAll passwords: 123\n";
    echo "\nYou can now login with any username and password '123'\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease update the database credentials at the top of this file.\n";
}
?>
