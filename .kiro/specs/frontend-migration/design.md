# Design Document

## Overview

This design document outlines the technical architecture and implementation strategy for migrating the University Portal from a dual-architecture system (Legacy HTML/CSS/JS + React SPA) to a unified single-project architecture. The new system will use primarily HTML/CSS/PHP (70%) with minimal React components (30%) while maintaining the exact UI/UX design of the React version.

### Design Goals

1. **Unified Codebase**: Single project structure eliminating code duplication
2. **Performance**: Fast page loads with server-side rendering and minimal JavaScript
3. **Maintainability**: Clear separation of concerns with modular architecture
4. **Scalability**: Database-driven system supporting growth
5. **Security**: Robust authentication and data protection
6. **Accessibility**: WCAG 2.1 Level AA compliance
7. **Modern UX**: Glassmorphism design with smooth interactions

### Technology Stack

**Backend:**
- PHP 8.1+ (Server-side logic, routing, authentication)
- MySQL 8.0+ (Database)
- Composer (Dependency management)
- PHPMailer (Email notifications)

**Frontend:**
- HTML5 (Semantic markup - 70%)
- CSS3 (Styling with custom properties - 70%)
- Vanilla JavaScript (Interactions - 20%)
- React 19 (Complex components only - 10%)
- Tailwind CSS (Utility classes)

**Build Tools:**
- Vite (For React components bundling)
- PostCSS (CSS processing)
- Autoprefixer (Browser compatibility)

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Client Browser                        │
│  ┌────────────┐  ┌────────────┐  ┌──────────────────────┐  │
│  │   HTML/CSS │  │ JavaScript │  │  React Components    │  │
│  │   (70%)    │  │  (20%)     │  │     (10%)            │  │
│  └────────────┘  └────────────┘  └──────────────────────┘  │
└───────────────────────────┬─────────────────────────────────┘
                            │ HTTP/HTTPS
┌───────────────────────────┴─────────────────────────────────┐
│                      Web Server (Apache/Nginx)               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              PHP Application Layer                    │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────────────┐   │   │
│  │  │  Router  │  │   Auth   │  │   Controllers    │   │   │
│  │  └──────────┘  └──────────┘  └──────────────────┘   │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────────────┐   │   │
│  │  │  Models  │  │  Views   │  │    Services      │   │   │
│  │  └──────────┘  └──────────┘  └──────────────────┘   │   │
│  └──────────────────────────────────────────────────────┘   │
└───────────────────────────┬─────────────────────────────────┘
                            │ PDO/MySQLi
┌───────────────────────────┴─────────────────────────────────┐
│                      MySQL Database                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  Users   │  │ Students │  │ Teachers │  │ Subjects │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │ Results  │  │ Payments │  │ Notices  │  │   Logs   │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Directory Structure

```
university-portal/
├── config/
│   ├── database.php          # Database configuration
│   ├── app.php               # Application settings
│   └── mail.php              # Email configuration
├── public/                   # Web root
│   ├── index.php             # Entry point
│   ├── .htaccess             # URL rewriting
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css      # Main stylesheet
│   │   │   ├── components.css
│   │   │   └── utilities.css
│   │   ├── js/
│   │   │   ├── app.js        # Main JavaScript
│   │   │   ├── components/   # Vanilla JS components
│   │   │   └── utils/
│   │   ├── images/
│   │   └── fonts/
│   └── uploads/              # User uploaded files
├── src/
│   ├── Controllers/          # Request handlers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── StudentController.php
│   │   ├── TeacherController.php
│   │   └── AdminController.php
│   ├── Models/               # Data models
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   ├── Subject.php
│   │   ├── Result.php
│   │   ├── Payment.php
│   │   └── Notice.php
│   ├── Services/             # Business logic
│   │   ├── AuthService.php
│   │   ├── GradeCalculator.php
│   │   ├── PaymentProcessor.php
│   │   ├── EmailService.php
│   │   └── ReportGenerator.php
│   ├── Middleware/           # Request middleware
│   │   ├── AuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── CsrfMiddleware.php
│   └── Utils/                # Helper functions
│       ├── Validator.php
│       ├── Sanitizer.php
│       └── FileUploader.php
├── views/                    # HTML templates
│   ├── layouts/
│   │   ├── main.php          # Main layout
│   │   ├── admin.php         # Admin layout
│   │   └── teacher.php       # Teacher layout
│   ├── components/           # Reusable components
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── navigation.php
│   │   └── modals.php
│   ├── student/
│   │   ├── dashboard.php
│   │   ├── subjects.php
│   │   ├── results.php
│   │   ├── payments.php
│   │   ├── notices.php
│   │   └── analysis.php
│   ├── teacher/
│   │   ├── dashboard.php
│   │   ├── attendance.php
│   │   ├── marks.php
│   │   └── students.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── students.php
│   │   ├── teachers.php
│   │   ├── fees.php
│   │   └── courses.php
│   └── auth/
│       ├── login.php
│       └── forgot-password.php
├── react-components/         # React components (10%)
│   ├── src/
│   │   ├── DataTable.jsx     # Sortable/filterable table
│   │   ├── Charts.jsx        # Performance charts
│   │   └── RichTextEditor.jsx
│   ├── package.json
│   └── vite.config.js
├── database/
│   ├── migrations/           # Database migrations
│   └── seeds/                # Sample data
├── tests/                    # Test files
│   ├── Unit/
│   └── Integration/
├── vendor/                   # Composer dependencies
├── composer.json
├── .env.example
└── README.md
```


## Components and Interfaces

### Component Breakdown: HTML/CSS vs React

**Pure HTML/CSS/PHP Components (70%):**

1. **Authentication Pages**
   - Login form with role selector
   - Forgot password form
   - Implementation: Pure HTML forms with CSS animations

2. **Static Layouts**
   - Header, footer, navigation
   - Breadcrumbs
   - Implementation: PHP includes with CSS

3. **Dashboard Cards**
   - Welcome cards
   - Statistics cards
   - Notification cards
   - Implementation: HTML with CSS Grid/Flexbox

4. **Subject Cards**
   - Subject information display
   - Collapsible details
   - Implementation: HTML with vanilla JS for collapse

5. **Results Display**
   - Semester results tables
   - Grade badges
   - Implementation: HTML tables with CSS styling

6. **Payment Cards**
   - Payment summary
   - Payment history
   - Implementation: HTML with CSS

7. **Notice Board**
   - Notice list
   - Category filters
   - Implementation: HTML with vanilla JS filtering

8. **Forms (Simple)**
   - Profile edit forms
   - Password change forms
   - Implementation: HTML forms with PHP validation

**React Components (30%):**

1. **DataTable Component** (Complex)
   - Student/Teacher management tables
   - Features: Sorting, filtering, pagination, inline editing
   - Reason: Complex state management and interactions

2. **Charts Component** (Complex)
   - Performance analytics charts
   - SGPA progression line chart
   - Grade distribution pie chart
   - Reason: Data visualization requires charting library

3. **RichTextEditor Component** (Complex)
   - Notice content editor
   - Reason: Rich text editing requires specialized library

4. **FileUploader Component** (Complex)
   - Drag-and-drop file upload
   - Progress indicators
   - Image cropping
   - Reason: Complex file handling and preview

### PHP MVC Architecture

**Router (public/index.php)**
```php
// URL routing without .php extensions
$routes = [
    '/' => 'DashboardController@index',
    '/login' => 'AuthController@login',
    '/logout' => 'AuthController@logout',
    '/dashboard' => 'DashboardController@index',
    '/subjects' => 'StudentController@subjects',
    '/results' => 'StudentController@results',
    '/payments' => 'StudentController@payments',
    '/notices' => 'NoticeController@index',
    '/admin/students' => 'AdminController@students',
    // ... more routes
];
```

**Controller Pattern**
```php
class DashboardController extends BaseController {
    public function index() {
        $this->requireAuth();
        $user = $this->getUser();
        
        if ($user['role'] === 'student') {
            $data = [
                'gpa' => $this->gradeCalculator->calculateGPA($user['id']),
                'assignments' => $this->getUpcomingAssignments($user['id']),
                'notifications' => $this->getNotifications($user['id'])
            ];
            return $this->view('student/dashboard', $data);
        }
        // ... handle other roles
    }
}
```

**Model Pattern**
```php
class Student extends Model {
    protected $table = 'students';
    
    public function getSubjects($semester) {
        return $this->db->query(
            "SELECT s.* FROM subjects s 
             JOIN student_subjects ss ON s.id = ss.subject_id 
             WHERE ss.student_id = ? AND s.semester = ?",
            [$this->id, $semester]
        );
    }
    
    public function getResults($semester) {
        return $this->db->query(
            "SELECT * FROM results 
             WHERE student_id = ? AND semester = ?",
            [$this->id, $semester]
        );
    }
}
```

### API Endpoints

**Authentication Endpoints**
```
POST   /api/auth/login          # User login
POST   /api/auth/logout         # User logout
POST   /api/auth/refresh        # Refresh session
POST   /api/auth/forgot-password # Password reset request
```

**Student Endpoints**
```
GET    /api/student/dashboard   # Dashboard data
GET    /api/student/subjects    # Enrolled subjects
GET    /api/student/results     # Exam results
GET    /api/student/payments    # Payment history
POST   /api/student/payment     # Process payment
GET    /api/student/notices     # View notices
```

**Admin Endpoints**
```
GET    /api/admin/students      # List all students
POST   /api/admin/students      # Add student
PUT    /api/admin/students/:id  # Update student
DELETE /api/admin/students/:id  # Delete student
GET    /api/admin/teachers      # List all teachers
POST   /api/admin/teachers      # Add teacher
GET    /api/admin/reports       # Generate reports
```

**Teacher Endpoints**
```
GET    /api/teacher/courses     # Assigned courses
POST   /api/teacher/attendance  # Mark attendance
POST   /api/teacher/marks       # Enter marks
GET    /api/teacher/students    # View students
```

### Response Format
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful",
  "errors": []
}
```


## Data Models

### Database Schema

**users table**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);
```

**students table**
```sql
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    admission_year INT NOT NULL,
    date_of_birth DATE,
    address TEXT,
    guardian_name VARCHAR(100),
    guardian_phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_department (department),
    INDEX idx_semester (semester)
);
```

**teachers table**
```sql
CREATE TABLE teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    teacher_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(50) NOT NULL,
    qualification VARCHAR(100),
    specialization VARCHAR(100),
    joining_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_department (department)
);
```

**subjects table**
```sql
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    credits INT NOT NULL,
    is_lab BOOLEAN DEFAULT FALSE,
    teacher_id INT,
    description TEXT,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL,
    INDEX idx_subject_code (subject_code),
    INDEX idx_department_semester (department, semester)
);
```

**student_subjects table** (Enrollment)
```sql
CREATE TABLE student_subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, subject_id, academic_year),
    INDEX idx_student (student_id),
    INDEX idx_subject (subject_id)
);
```

**results table**
```sql
CREATE TABLE results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    semester INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    internal_marks INT,
    theory_marks INT,
    lab_marks INT,
    total_marks INT NOT NULL,
    grade VARCHAR(2) NOT NULL,
    grade_point INT NOT NULL,
    credit_points INT NOT NULL,
    result_status ENUM('pass', 'fail') NOT NULL,
    published_date TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_result (student_id, subject_id, semester, academic_year),
    INDEX idx_student_semester (student_id, semester)
);
```

**payments table**
```sql
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    fee_type ENUM('semester', 'exam', 'lab', 'other') NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    fine_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    due_date DATE NOT NULL,
    payment_date TIMESTAMP NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_status ENUM('pending', 'paid', 'overdue', 'cancelled') NOT NULL DEFAULT 'pending',
    receipt_url VARCHAR(255),
    semester INT,
    academic_year VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_student_status (student_id, payment_status),
    INDEX idx_due_date (due_date)
);
```

**notices table**
```sql
CREATE TABLE notices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('academic', 'events', 'urgent', 'general') NOT NULL,
    target_audience ENUM('all', 'students', 'teachers') NOT NULL DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    priority INT DEFAULT 0,
    published_by INT NOT NULL,
    published_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date TIMESTAMP NULL,
    FOREIGN KEY (published_by) REFERENCES users(id),
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_published_date (published_date)
);
```

**attendance table**
```sql
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'leave') NOT NULL,
    marked_by INT NOT NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES teachers(id),
    UNIQUE KEY unique_attendance (student_id, subject_id, attendance_date),
    INDEX idx_student_subject (student_id, subject_id),
    INDEX idx_date (attendance_date)
);
```

**sessions table**
```sql
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    data TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_last_activity (last_activity)
);
```

**activity_logs table**
```sql
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);
```

### Entity Relationships

```
users (1) ──── (1) students
users (1) ──── (1) teachers
users (1) ──── (many) sessions
users (1) ──── (many) activity_logs
users (1) ──── (many) notices [published_by]

students (many) ──── (many) subjects [through student_subjects]
students (1) ──── (many) results
students (1) ──── (many) payments
students (1) ──── (many) attendance

teachers (1) ──── (many) subjects [assigned]
teachers (1) ──── (many) attendance [marked_by]

subjects (1) ──── (many) results
subjects (1) ──── (many) attendance
```


## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Naming Convention Consistency
*For any* file in the project, the naming convention should match the specified pattern (kebab-case for files, camelCase for JavaScript, snake_case for PHP variables)
**Validates: Requirements 1.5**

### Property 2: Color Palette Consistency
*For any* CSS file, all color values should match the specified palette (primary: #137fec, backgrounds: #e0f2ff, #ffffff, #1e3a8a, #101922)
**Validates: Requirements 2.2**

### Property 3: Typography Consistency
*For any* page, the font-family should be Inter with specified weights (400, 500, 700, 900)
**Validates: Requirements 2.3**

### Property 4: Spacing and Border-Radius Consistency
*For any* CSS declaration, border-radius values should be from the specified set (0.25rem, 0.5rem, 0.75rem, 1.5rem, full)
**Validates: Requirements 2.4**

### Property 5: Responsive Breakpoint Consistency
*For any* media query, breakpoints should match specifications (640px, 768px, 1024px, 1280px)
**Validates: Requirements 2.6**

### Property 6: Dark Mode Support
*For any* page, toggling dark mode should apply the correct color scheme without breaking layout
**Validates: Requirements 2.7**

### Property 7: Authentication Validation
*For any* login attempt with valid credentials and matching role, authentication should succeed and create a session
**Validates: Requirements 3.1**

### Property 8: Session Creation on Success
*For any* successful authentication, a PHP session should be created with user data (id, name, email, role, department, semester)
**Validates: Requirements 3.2**

### Property 9: Error Message on Failed Authentication
*For any* failed authentication attempt, an appropriate error message should be displayed without revealing whether username or password was incorrect
**Validates: Requirements 3.3**

### Property 10: Role-Based Access Control
*For any* user attempting to access a route, access should be granted only if the user's role is authorized for that route
**Validates: Requirements 3.4**

### Property 11: Session Destruction on Logout
*For any* logout action, the PHP session should be destroyed and the user redirected to login page
**Validates: Requirements 3.5**

### Property 12: Session Timeout
*For any* session inactive for more than 30 minutes, the session should be invalidated
**Validates: Requirements 3.6**

### Property 13: Role Selection Cookie
*For any* role selection during login, a cookie should be set to remember the selection
**Validates: Requirements 3.7**

### Property 14: Assignment Display
*For any* student with upcoming assignments, all assignments should be displayed on the dashboard
**Validates: Requirements 4.3**

### Property 15: Notification Display
*For any* notification for a student, it should appear in the sidebar with appropriate color-coded icon
**Validates: Requirements 4.5**

### Property 16: Dynamic Data Loading
*For any* dashboard data request, data should be loaded from PHP backend without full page refresh
**Validates: Requirements 4.7**

### Property 17: Subject Display by Semester
*For any* student, only subjects for their current semester should be displayed on the subjects page
**Validates: Requirements 5.1**

### Property 18: Subject Card Information Completeness
*For any* subject card, it should display subject code, name, instructor name, instructor department, and gradient header
**Validates: Requirements 5.2**

### Property 19: Card Expansion Without Reload
*For any* subject card, clicking "View Details" should expand the card without page reload
**Validates: Requirements 5.3**

### Property 20: Instructor Avatar Generation
*For any* instructor, an avatar should be generated using UI Avatars API with their name
**Validates: Requirements 5.4**

### Property 21: Subject Organization
*For any* subject query, results should be filtered by department and semester correctly
**Validates: Requirements 5.5**

### Property 22: Lab Subject Badge
*For any* lab subject, a "Lab" badge should be displayed on the card
**Validates: Requirements 5.6**

### Property 23: Collapsible Card Functionality
*For any* collapsible card, the expand/collapse functionality should work correctly
**Validates: Requirements 5.7**

### Property 24: Results Organization by Semester
*For any* student, results should be organized and displayed by semester
**Validates: Requirements 6.1**

### Property 25: SGPA and Total Marks Display
*For any* semester with results, SGPA and total marks should be calculated and displayed correctly
**Validates: Requirements 6.2**

### Property 26: Marks Table Display on Expansion
*For any* semester section, expanding it should display a table with subject names, internal marks, theory marks, total marks, and grades
**Validates: Requirements 6.3**

### Property 27: Automatic Grade Calculation
*For any* total marks value, the correct grade should be assigned (A+ ≥90, A ≥80, B+ ≥70, B ≥60, C ≥50, D ≥40, F <40)
**Validates: Requirements 6.4**

### Property 28: Lab Subject Highlighting
*For any* lab subject in results, it should have a distinct background color
**Validates: Requirements 6.5**

### Property 29: Earlier Semester Summary
*For any* past semester, a summary view with SGPA and total marks should be displayed
**Validates: Requirements 6.6**

### Property 30: Pending Payments Display
*For any* student with pending payments, all pending payments should be listed with fee type, amount, due date, and "Pay Now" button
**Validates: Requirements 7.2**

### Property 31: Semester Fees Overview
*For any* semester, the payment status (Paid, Pending, Upcoming) should be displayed correctly
**Validates: Requirements 7.3**

### Property 32: Payment Modal Opening
*For any* payment, clicking "Pay Now" should open a modal with payment details and method selection
**Validates: Requirements 7.4**

### Property 33: Payment History Display
*For any* completed payment, it should appear in the payment history section
**Validates: Requirements 7.5**

### Property 34: PDF Receipt Generation
*For any* payment, clicking "Download Receipt" should generate a PDF receipt with university header, student details, and payment information
**Validates: Requirements 7.6**

### Property 35: Payment Status Update
*For any* successful payment, the payment status should be updated in the database
**Validates: Requirements 7.7**

### Property 36: Active Notices Display
*For any* user, all active notices should be displayed in reverse chronological order
**Validates: Requirements 8.1**

### Property 37: Notice Information Completeness
*For any* notice, it should display title, date, category badge, and full content
**Validates: Requirements 8.2**

### Property 38: Notice Category Filtering
*For any* category filter selection, only notices matching that category should be displayed
**Validates: Requirements 8.3**

### Property 39: Urgent Notice Highlighting
*For any* urgent notice, it should be highlighted with a red badge and icon
**Validates: Requirements 8.4**

### Property 40: Admin Notice Management
*For any* admin action on notices (create, edit, delete), the operation should complete successfully
**Validates: Requirements 8.5**

### Property 41: Rich Text Rendering
*For any* notice with rich text content, it should render correctly with formatting preserved
**Validates: Requirements 8.6**

### Property 42: Notice Pagination
*For any* page of notices, exactly 10 notices (or fewer on the last page) should be displayed
**Validates: Requirements 8.7**

### Property 43: SGPA Progression Chart
*For any* student with multiple semesters, a line chart showing SGPA progression should be displayed
**Validates: Requirements 9.2**

### Property 44: Subject Marks Comparison
*For any* semester, a bar chart comparing marks across subjects should be displayed
**Validates: Requirements 9.3**

### Property 45: Grade Distribution Chart
*For any* set of grades, a pie chart showing distribution should be displayed
**Validates: Requirements 9.4**

### Property 46: CGPA Calculation
*For any* student, the overall CGPA should be calculated correctly from all semesters
**Validates: Requirements 9.5**

### Property 47: Attendance Percentage Display
*For any* student with attendance data, the attendance percentage should be calculated and displayed
**Validates: Requirements 9.6**

### Property 48: Chart Filtering by Semester
*For any* semester selection in the filter, all charts should update to show data for that semester
**Validates: Requirements 9.7**

### Property 49: Recent Activities Feed
*For any* recent activity, it should appear in the admin dashboard activities feed with timestamp
**Validates: Requirements 10.2**

### Property 50: Enrollment Trends Chart
*For any* enrollment data, trends should be displayed in a chart on the admin dashboard
**Validates: Requirements 10.4**

### Property 51: Admin Route Access Control
*For any* non-admin user attempting to access admin routes, access should be denied
**Validates: Requirements 10.6**

### Property 52: Admin Action Logging
*For any* admin action, a log entry should be created with timestamp and admin user ID
**Validates: Requirements 10.7**

### Property 53: Student Information Display
*For any* student in the management table, all required fields (ID, name, email, department, semester) should be displayed
**Validates: Requirements 11.2**

### Property 54: Add Student Modal Opening
*For any* "Add Student" button click, a modal form should open with all required fields
**Validates: Requirements 11.3**

### Property 55: Student Record Insertion
*For any* valid student data submitted through the add form, a record should be inserted into the database
**Validates: Requirements 11.4**

### Property 56: Edit Form Pre-population
*For any* "Edit" button click on a student, the form should be populated with existing student data
**Validates: Requirements 11.5**

### Property 57: Delete Confirmation Dialog
*For any* "Delete" button click on a student, a confirmation dialog should be displayed before deletion
**Validates: Requirements 11.6**

### Property 58: Bulk Operations Support
*For any* bulk operation (import/export CSV), the operation should complete successfully for all valid records
**Validates: Requirements 11.7**

### Property 59: Student Pagination
*For any* page in student management, exactly 20 students (or fewer on the last page) should be displayed
**Validates: Requirements 11.8**

### Property 60: PHP Routing Without Extensions
*For any* URL accessed, the .php extension should not be visible in the browser address bar
**Validates: Requirements 17.1**

### Property 61: Active Navigation State
*For any* page, the corresponding navigation item should be highlighted as active
**Validates: Requirements 17.2**

### Property 62: Sidebar Navigation for Admin/Teacher
*For any* admin or teacher page, a sidebar navigation should be displayed
**Validates: Requirements 17.3**

### Property 63: Breadcrumb Navigation
*For any* page, breadcrumbs should display the correct navigation path
**Validates: Requirements 17.4**

### Property 64: Unauthorized Access Redirect
*For any* unauthorized access attempt to a protected route, the user should be redirected to the login page
**Validates: Requirements 17.5**

### Property 65: Navigation State Persistence
*For any* page navigation, the navigation state should persist across page loads
**Validates: Requirements 17.6**

### Property 66: Browser Navigation Support
*For any* browser back/forward button click, the navigation should work correctly
**Validates: Requirements 17.7**

### Property 67: Page Load Performance
*For any* page load, the initial content should be visible within 2 seconds on a standard broadband connection
**Validates: Requirements 19.1**

### Property 68: Password Hashing with Bcrypt
*For any* password stored in the database, it should be hashed using bcrypt with cost factor 12
**Validates: Requirements 21.1**

### Property 69: File Upload Validation
*For any* file upload attempt, the system should validate file type (JPEG, PNG, WebP) and size (max 2MB)
**Validates: Requirements 23.1**


## Error Handling

### Error Handling Strategy

**Client-Side Validation:**
- Form validation using HTML5 attributes and JavaScript
- Real-time feedback for invalid inputs
- User-friendly error messages

**Server-Side Validation:**
- All inputs validated on the server
- Prepared statements to prevent SQL injection
- Input sanitization to prevent XSS attacks

**Error Response Format:**
```php
[
    'success' => false,
    'message' => 'User-friendly error message',
    'errors' => [
        'field_name' => 'Specific field error'
    ],
    'code' => 'ERROR_CODE'
]
```

**Error Logging:**
```php
// Log format
[timestamp] [level] [user_id] [ip] [message] [context]

// Example
[2025-01-15 10:30:45] ERROR [user:123] [192.168.1.1] Database connection failed [context: {...}]
```

**Custom Error Pages:**
- 404: Page Not Found
- 403: Forbidden Access
- 500: Internal Server Error
- 503: Service Unavailable

### Exception Handling

```php
try {
    // Database operation
    $result = $db->query($sql, $params);
} catch (PDOException $e) {
    // Log error
    $logger->error('Database error', [
        'message' => $e->getMessage(),
        'query' => $sql,
        'user_id' => $_SESSION['user_id'] ?? null
    ]);
    
    // Return user-friendly error
    return [
        'success' => false,
        'message' => 'An error occurred. Please try again later.'
    ];
}
```

## Testing Strategy

### Dual Testing Approach

The system will implement both unit testing and property-based testing to ensure comprehensive coverage:

**Unit Tests:**
- Test specific examples and edge cases
- Verify integration points between components
- Test error conditions and boundary values
- Focus on critical business logic

**Property-Based Tests:**
- Verify universal properties across all inputs
- Test with randomly generated data
- Ensure correctness properties hold for all valid inputs
- Run minimum 100 iterations per property

### Testing Framework

**PHP Testing:**
- **PHPUnit** for unit tests
- **Pest** for property-based testing (using Pest's dataset features)
- **Mockery** for mocking dependencies

**JavaScript Testing:**
- **Jest** for unit tests
- **fast-check** for property-based testing
- **Testing Library** for React component tests

**Integration Testing:**
- **Selenium** or **Cypress** for end-to-end tests
- Test complete user workflows
- Verify cross-browser compatibility

### Property-Based Test Examples

**Property Test 1: Grade Calculation**
```php
// Feature: frontend-migration, Property 27: Automatic Grade Calculation
test('grade calculation property', function () {
    // Generate random marks between 0 and 100
    $marks = range(0, 100);
    
    foreach ($marks as $mark) {
        $grade = GradeCalculator::calculateGrade($mark);
        
        // Verify grade matches specification
        if ($mark >= 90) expect($grade)->toBe('A+');
        elseif ($mark >= 80) expect($grade)->toBe('A');
        elseif ($mark >= 70) expect($grade)->toBe('B+');
        elseif ($mark >= 60) expect($grade)->toBe('B');
        elseif ($mark >= 50) expect($grade)->toBe('C');
        elseif ($mark >= 40) expect($grade)->toBe('D');
        else expect($grade)->toBe('F');
    }
})->repeat(100);
```

**Property Test 2: Session Creation**
```php
// Feature: frontend-migration, Property 8: Session Creation on Success
test('session creation on successful authentication', function () {
    // Generate random valid user credentials
    $users = generateRandomUsers(100);
    
    foreach ($users as $user) {
        $result = AuthService::login($user['username'], $user['password'], $user['role']);
        
        // Verify session is created
        expect($result['success'])->toBeTrue();
        expect($_SESSION['user_id'])->toBe($user['id']);
        expect($_SESSION['user_name'])->toBe($user['full_name']);
        expect($_SESSION['user_role'])->toBe($user['role']);
        expect($_SESSION['user_email'])->toBe($user['email']);
    }
});
```

**Property Test 3: Role-Based Access Control**
```php
// Feature: frontend-migration, Property 10: Role-Based Access Control
test('role-based access control property', function () {
    $routes = [
        '/admin/students' => ['admin'],
        '/admin/teachers' => ['admin'],
        '/teacher/attendance' => ['teacher'],
        '/student/dashboard' => ['student'],
    ];
    
    $roles = ['student', 'teacher', 'admin'];
    
    foreach ($routes as $route => $allowedRoles) {
        foreach ($roles as $role) {
            $user = createUserWithRole($role);
            $canAccess = Router::canAccess($route, $user);
            
            if (in_array($role, $allowedRoles)) {
                expect($canAccess)->toBeTrue();
            } else {
                expect($canAccess)->toBeFalse();
            }
        }
    }
})->repeat(100);
```

**Property Test 4: SGPA Calculation**
```php
// Feature: frontend-migration, Property 25: SGPA and Total Marks Display
test('SGPA calculation property', function () {
    // Generate random subject results
    for ($i = 0; $i < 100; $i++) {
        $subjects = generateRandomSubjects(5, 8); // 5-8 subjects
        
        $totalCP = 0;
        $totalCredits = 0;
        
        foreach ($subjects as $subject) {
            $totalCP += $subject['credit_points'];
            $totalCredits += $subject['credits'];
        }
        
        $expectedSGPA = $totalCredits > 0 ? $totalCP / $totalCredits : 0;
        $calculatedSGPA = GradeCalculator::calculateSGPA($subjects);
        
        expect($calculatedSGPA)->toBeCloseTo($expectedSGPA, 2);
    }
});
```

**Property Test 5: Password Hashing**
```php
// Feature: frontend-migration, Property 68: Password Hashing with Bcrypt
test('password hashing property', function () {
    $passwords = generateRandomPasswords(100);
    
    foreach ($passwords as $password) {
        $hash = AuthService::hashPassword($password);
        
        // Verify bcrypt is used (starts with $2y$)
        expect($hash)->toStartWith('$2y$');
        
        // Verify cost factor is 12
        expect(substr($hash, 4, 2))->toBe('12');
        
        // Verify password can be verified
        expect(AuthService::verifyPassword($password, $hash))->toBeTrue();
        
        // Verify wrong password fails
        expect(AuthService::verifyPassword($password . 'wrong', $hash))->toBeFalse();
    }
});
```

### Unit Test Examples

**Unit Test 1: Login Form Validation**
```php
test('login form validates required fields', function () {
    $result = AuthController::login('', '', 'student');
    expect($result['success'])->toBeFalse();
    expect($result['errors'])->toHaveKey('username');
    expect($result['errors'])->toHaveKey('password');
});
```

**Unit Test 2: Payment Receipt Generation**
```php
test('payment receipt generates PDF with correct data', function () {
    $payment = [
        'id' => 1,
        'description' => 'Semester 5 Fee',
        'amount' => 17000,
        'student_name' => 'John Doe',
        'student_id' => 'S2024001'
    ];
    
    $pdf = PaymentService::generateReceipt($payment);
    
    expect($pdf)->toBeInstanceOf(TCPDF::class);
    expect($pdf->getPage())->toContain('Semester 5 Fee');
    expect($pdf->getPage())->toContain('₹17,000');
});
```

### Test Coverage Goals

- **Unit Test Coverage**: Minimum 80% code coverage
- **Property Test Coverage**: All correctness properties tested
- **Integration Test Coverage**: All critical user workflows
- **Performance Tests**: Page load times, database query performance
- **Security Tests**: SQL injection, XSS, CSRF protection

### Continuous Integration

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install
      - name: Run unit tests
        run: vendor/bin/phpunit
      - name: Run property tests
        run: vendor/bin/pest
      - name: Check code coverage
        run: vendor/bin/phpunit --coverage-text
```

