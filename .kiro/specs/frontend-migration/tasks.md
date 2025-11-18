# Implementation Plan

- [x] 1. Project Setup and Infrastructure





  - Create root directory structure with folders: config/, public/, src/, views/, react-components/, database/, tests/
  - Initialize Composer for PHP dependency management
  - Set up Git repository with .gitignore for vendor/, node_modules/, .env
  - Create .env.example with database credentials, app settings, and mail configuration
  - Install core dependencies: PHPMailer, TCPDF, PHPUnit, Pest
  - Set up Apache/Nginx with URL rewriting rules to hide .php extensions
  - _Requirements: 1.1, 1.6, 30.1, 30.2, 30.3_
-

- [x] 2. Database Schema Implementation




  - [x] 2.1 Create database migration system


    - Write migration runner script in PHP
    - Create migrations folder structure
    - _Requirements: 20.1, 30.4_
  
  - [x] 2.2 Implement core tables migration


    - Create users table with indexes on username, email, role
    - Create students table with foreign key to users
    - Create teachers table with foreign key to users
    - Create sessions table for session management
    - Create activity_logs table for audit trail
    - _Requirements: 20.1, 20.2, 20.3_
  
  - [x] 2.3 Implement academic tables migration


    - Create subjects table with indexes on subject_code, department, semester
    - Create student_subjects enrollment table
    - Create results table with composite unique key
    - Create attendance table
    - _Requirements: 20.1, 20.2_
  
  - [x] 2.4 Implement administrative tables migration


    - Create payments table with indexes on student_id, payment_status, due_date
    - Create notices table with indexes on category, is_active, published_date
    - _Requirements: 20.1, 20.2_
  
  - [ ]* 2.5 Write property test for database schema
    - **Property 20: Database Integrity**
    - **Validates: Requirements 20.2**
    - Test that all foreign key constraints are properly enforced
    - Test that indexes exist on frequently queried columns
  
  - [x] 2.6 Create database seeder with sample data


    - Seed admin user (username: admin, password: hashed)
    - Seed 50 sample students across departments (BCA, BBA, B.Com, BSc Physics)
    - Seed 10 sample teachers
    - Seed 30 subjects across departments and semesters
    - Seed sample results, payments, and notices
    - _Requirements: 20.1_

-

- [x] 3. Core PHP Backend - Authentication and Session Management



  - [x] 3.1 Implement database connection class


    - Create Database.php with PDO connection pooling
    - Implement prepared statement wrapper methods
    - Add error logging for database errors
    - _Requirements: 20.3, 20.5_
  
  - [x] 3.2 Implement base Model class


    - Create Model.php with CRUD operations
    - Implement query builder methods
    - Add validation helpers
    - _Requirements: 20.3_
  
  - [x] 3.3 Implement User model


    - Create User.php extending Model
    - Add methods: findByUsername(), findByEmail(), create(), update()
    - _Requirements: 20.3_
  
  - [x] 3.4 Implement AuthService


    - Create AuthService.php with login(), logout(), hashPassword(), verifyPassword()
    - Implement bcrypt password hashing with cost factor 12
    - Add session creation with user data
    - Implement session timeout (30 minutes)
    - _Requirements: 3.1, 3.2, 3.5, 3.6, 21.1_
  
  - [ ]* 3.5 Write property test for authentication
    - **Property 7: Authentication Validation**
    - **Validates: Requirements 3.1**
    - Test that valid credentials with matching role succeed
  
  - [ ]* 3.6 Write property test for session creation
    - **Property 8: Session Creation on Success**
    - **Validates: Requirements 3.2**
    - Test that successful auth creates session with correct data
  
  - [ ]* 3.7 Write property test for password hashing
    - **Property 68: Password Hashing with Bcrypt**
    - **Validates: Requirements 21.1**
    - Test that all passwords are hashed with bcrypt cost 12
  
  - [x] 3.8 Implement AuthController


    - Create AuthController.php with login(), logout(), forgotPassword()
    - Add CSRF token generation and validation
    - Implement rate limiting (5 attempts per 15 minutes)
    - _Requirements: 3.1, 3.3, 21.2, 21.5_
  
  - [ ]* 3.9 Write property test for role-based access control
    - **Property 10: Role-Based Access Control**
    - **Validates: Requirements 3.4**
    - Test that users can only access routes for their role
  
  - [x] 3.10 Implement middleware system


    - Create AuthMiddleware.php to check authentication
    - Create RoleMiddleware.php to check role permissions
    - Create CsrfMiddleware.php for CSRF protection
    - _Requirements: 3.4, 21.2_

- [x] 4. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.
- [x] 5. Routing and Request Handling




- [ ] 5. Routing and Request Handling

  - [x] 5.1 Implement Router class


    - Create Router.php with route registration and matching
    - Implement URL rewriting without .php extensions
    - Add middleware support
    - _Requirements: 17.1, 17.5_
  
  - [ ]* 5.2 Write property test for routing
    - **Property 60: PHP Routing Without Extensions**
    - **Validates: Requirements 17.1**
    - Test that no .php extensions appear in URLs
  
  - [x] 5.3 Define all application routes


    - Register student routes: /dashboard, /subjects, /results, /payments, /notices, /analysis
    - Register teacher routes: /teacher/dashboard, /teacher/attendance, /teacher/marks, /teacher/students
    - Register admin routes: /admin/dashboard, /admin/students, /admin/teachers, /admin/fees, /admin/courses
    - Register auth routes: /login, /logout, /forgot-password
    - _Requirements: 17.1_
  
  - [x] 5.4 Implement BaseController


    - Create BaseController.php with view(), json(), redirect() methods
    - Add getUser(), requireAuth(), requireRole() helpers
    - _Requirements: 3.4_

- [x] 6. Design System and CSS Framework



  - [x] 6.1 Create CSS custom properties


    - Define color palette in :root (primary, backgrounds, text colors)
    - Define spacing scale (0.25rem, 0.5rem, 0.75rem, 1rem, 1.5rem, 2rem, 3rem, 4rem)
    - Define border-radius values (0.25rem, 0.5rem, 0.75rem, 1.5rem, full)
    - Define typography scale and font weights
    - _Requirements: 2.2, 2.3, 2.4_
  
  - [ ]* 6.2 Write property test for color palette
    - **Property 2: Color Palette Consistency**
    - **Validates: Requirements 2.2**
    - Test that all CSS colors match specifications
  
  - [ ]* 6.3 Write property test for typography
    - **Property 3: Typography Consistency**
    - **Validates: Requirements 2.3**
    - Test that font-family is Inter with correct weights
  
  - [x] 6.4 Implement glassmorphism components


    - Create .glass-card class with backdrop-filter blur
    - Create .glass-nav class for navigation
    - Create .glass-modal class for modals
    - _Requirements: 2.1_
  
  - [x] 6.5 Implement responsive breakpoints


    - Define media queries at 640px, 768px, 1024px, 1280px
    - Create responsive utility classes
    - _Requirements: 2.6, 18.1_
  
  - [ ]* 6.6 Write property test for breakpoints
    - **Property 5: Responsive Breakpoint Consistency**
    - **Validates: Requirements 2.6**
    - Test that media queries match specifications
  
  - [x] 6.7 Implement dark mode styles


    - Create dark mode color scheme
    - Add .dark class toggle functionality
    - Store preference in localStorage
    - _Requirements: 2.7_
  
  - [ ]* 6.8 Write property test for dark mode
    - **Property 6: Dark Mode Support**
    - **Validates: Requirements 2.7**
    - Test that dark mode applies correct colors without breaking layout

- [-] 7. Layout Components (HTML/CSS/PHP)


  - [x] 7.1 Create main layout template


    - Create views/layouts/main.php with header, main, footer structure
    - Include meta tags, CSS links, and JavaScript
    - Add navigation placeholder
    - _Requirements: 1.2, 1.3_
  
  - [-] 7.2 Create header component

    - Create views/components/header.php with logo and user profile
    - Add responsive hamburger menu for mobile
    - _Requirements: 18.2_
  
  - [ ] 7.3 Create bottom navigation component
    - Create views/components/bottom-nav.php with fixed positioning
    - Add icons for Dashboard, Subjects, Notice, Results, Payments
    - Implement active state highlighting
    - _Requirements: 4.6, 17.2_
  
  - [ ]* 7.4 Write property test for active navigation
    - **Property 61: Active Navigation State**
    - **Validates: Requirements 17.2**
    - Test that correct nav item is highlighted on each page
  
  - [ ] 7.5 Create sidebar navigation for admin/teacher
    - Create views/components/sidebar.php with collapsible menu
    - Add icons and labels for all admin/teacher routes
    - _Requirements: 17.3_
  
  - [ ] 7.6 Create breadcrumb component
    - Create views/components/breadcrumb.php
    - Implement dynamic breadcrumb generation based on current route
    - _Requirements: 17.4_
  
  - [ ] 7.7 Create modal component
    - Create views/components/modal.php with glassmorphism styling
    - Add open/close functionality with vanilla JavaScript
    - _Requirements: 2.1_

- [ ] 8. Authentication Pages (HTML/CSS/PHP)

  - [ ] 8.1 Create login page
    - Create views/auth/login.php with username, password, role selector
    - Implement role selector with sliding highlight animation
    - Add "Remember Me" checkbox and "Forgot Password" link
    - _Requirements: 3.1, 3.7_
  
  - [ ] 8.2 Add login form validation
    - Implement client-side validation with HTML5 and JavaScript
    - Add server-side validation in AuthController
    - Display error messages without revealing security details
    - _Requirements: 3.3, 22.5_
  
  - [ ] 8.3 Create forgot password page
    - Create views/auth/forgot-password.php with email input
    - Implement password reset token generation
    - _Requirements: 3.1_
  
  - [ ] 8.4 Implement password reset email
    - Create email template for password reset
    - Send email with reset link using PHPMailer
    - _Requirements: 24.1_


- [ ] 9. Student Dashboard Module (HTML/CSS/PHP)
  - [ ] 9.1 Create Student model
    - Create src/Models/Student.php extending Model
    - Add methods: getSubjects(), getResults(), getPayments(), getNotifications()
    - _Requirements: 20.3_
  
  - [ ] 9.2 Create DashboardController
    - Create src/Controllers/DashboardController.php
    - Implement index() method to fetch dashboard data
    - _Requirements: 4.1_
  
  - [ ] 9.3 Create dashboard view
    - Create views/student/dashboard.php
    - Add welcome card with student name and avatar
    - Add academic progress card with circular GPA indicator
    - Add upcoming assignments card
    - Add college announcements section
    - Add notifications sidebar
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [ ]* 9.4 Write property test for assignment display
    - **Property 14: Assignment Display**
    - **Validates: Requirements 4.3**
    - Test that all assignments are displayed for student
  
  - [ ] 9.5 Implement circular progress indicator
    - Create CSS for circular progress with SVG
    - Add JavaScript to animate progress on page load
    - _Requirements: 4.2_
  
  - [ ] 9.6 Implement dynamic notification loading
    - Create API endpoint /api/student/notifications
    - Use fetch API to load notifications without page refresh
    - _Requirements: 4.7_
  
  - [ ]* 9.7 Write property test for dynamic loading
    - **Property 16: Dynamic Data Loading**
    - **Validates: Requirements 4.7**
    - Test that data loads from backend without full page refresh

- [ ] 10. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.



- [ ] 11. Subjects Module (HTML/CSS/PHP)
  - [ ] 11.1 Create Subject model
    - Create src/Models/Subject.php extending Model
    - Add methods: getByDepartmentAndSemester(), getEnrolledSubjects(), getTeacher()
    - _Requirements: 20.3_
  
  - [ ] 11.2 Create StudentController
    - Create src/Controllers/StudentController.php
    - Implement subjects() method to fetch student's subjects
    - _Requirements: 5.1_
  
  - [ ] 11.3 Create subjects view
    - Create views/student/subjects.php
    - Display subjects in grid layout with glassmorphism cards
    - Show subject code, name, instructor with avatar
    - Add gradient headers with different colors per card
    - _Requirements: 5.1, 5.2_
  
  - [ ]* 11.4 Write property test for subject display
    - **Property 17: Subject Display by Semester**
    - **Validates: Requirements 5.1**
    - Test that only current semester subjects are shown
  
  - [ ]* 11.5 Write property test for subject card information
    - **Property 18: Subject Card Information Completeness**
    - **Validates: Requirements 5.2**
    - Test that all required fields are displayed
  
  - [ ] 11.6 Implement collapsible subject cards
    - Add "View Details" button to each card
    - Implement expand/collapse with vanilla JavaScript
    - Show topics list when expanded
    - _Requirements: 5.3, 5.7_
  
  - [ ]* 11.7 Write property test for card expansion
    - **Property 19: Card Expansion Without Reload**
    - **Validates: Requirements 5.3**
    - Test that cards expand without page reload
  
  - [ ] 11.8 Implement instructor avatars
    - Use UI Avatars API with instructor name
    - Add custom background colors per instructor
    - _Requirements: 5.4_
  
  - [ ] 11.9 Add lab subject badges
    - Check is_lab flag from database
    - Display "Lab" badge for lab subjects
    - _Requirements: 5.6_
  
  - [ ]* 11.10 Write property test for lab badges
    - **Property 22: Lab Subject Badge**
    - **Validates: Requirements 5.6**
    - Test that lab subjects show badge

- [ ] 12. Results Module (HTML/CSS/PHP)

  - [ ] 12.1 Create Result model
    - Create src/Models/Result.php extending Model
    - Add methods: getBySemester(), calculateSGPA(), calculateCGPA()
    - _Requirements: 20.3_
  
  - [ ] 12.2 Create GradeCalculator service
    - Create src/Services/GradeCalculator.php
    - Implement calculateGrade($marks) method
    - Implement calculateGP($grade) method
    - Implement calculateCP($credits, $gp) method
    - Implement calculateSGPA($results) method
    - Implement calculateCGPA($allResults) method
    - _Requirements: 6.4, 9.5_
  
  - [ ]* 12.3 Write property test for grade calculation
    - **Property 27: Automatic Grade Calculation**
    - **Validates: Requirements 6.4**
    - Test that correct grade is assigned for any marks value
  
  - [ ]* 12.4 Write property test for SGPA calculation
    - **Property 25: SGPA and Total Marks Display**
    - **Validates: Requirements 6.2**
    - Test that SGPA is calculated correctly
  
  - [ ] 12.5 Implement results() method in StudentController
    - Fetch all results grouped by semester
    - Calculate SGPA for each semester
    - Calculate overall CGPA
    - _Requirements: 6.1, 6.2_
  
  - [ ] 12.6 Create results view
    - Create views/student/results.php
    - Display results in collapsible semester sections
    - Show SGPA and total marks prominently
    - _Requirements: 6.1, 6.2_
  
  - [ ] 12.7 Create results table
    - Display table with subject, internal, theory, total, grade columns
    - Highlight lab subjects with distinct background
    - Show grade badges with color coding
    - _Requirements: 6.3, 6.5_
  
  - [ ]* 12.8 Write property test for results organization
    - **Property 24: Results Organization by Semester**
    - **Validates: Requirements 6.1**
    - Test that results are organized by semester
  
  - [ ] 12.9 Implement collapsible semester sections
    - Add expand/collapse functionality with vanilla JavaScript
    - Smooth transitions with CSS
    - _Requirements: 6.7_
  
  - [ ] 12.10 Create earlier semesters summary
    - Display past semesters in summary cards
    - Show SGPA and total marks for each
    - _Requirements: 6.6_

- [ ] 13. Payments Module (HTML/CSS/PHP)

  - [ ] 13.1 Create Payment model
    - Create src/Models/Payment.php extending Model
    - Add methods: getPending(), getPaid(), getOverdue(), create(), updateStatus()
    - _Requirements: 20.3_
  
  - [ ] 13.2 Create PaymentProcessor service
    - Create src/Services/PaymentProcessor.php
    - Implement processPayment($paymentId, $method) method
    - Add payment gateway integration (mock for now)
    - _Requirements: 7.7_
  
  - [ ] 13.3 Implement payments() method in StudentController
    - Fetch payment summary (total paid, pending)
    - Fetch all pending payments
    - Fetch payment history
    - _Requirements: 7.1, 7.2, 7.5_
  
  - [ ] 13.4 Create payments view
    - Create views/student/payments.php
    - Display payment summary card
    - List pending payments with "Pay Now" buttons
    - Show semester fees overview
    - Display payment history
    - _Requirements: 7.1, 7.2, 7.3, 7.5_
  
  - [ ]* 13.5 Write property test for pending payments
    - **Property 30: Pending Payments Display**
    - **Validates: Requirements 7.2**
    - Test that all pending payments are listed
  
  - [ ] 13.6 Create payment modal
    - Add modal with payment details
    - Implement payment method selection (QR, Card, UPI)
    - Add QR code display section
    - Add card payment form
    - Add UPI ID input
    - _Requirements: 7.4_
  
  - [ ]* 13.7 Write property test for payment modal
    - **Property 32: Payment Modal Opening**
    - **Validates: Requirements 7.4**
    - Test that modal opens with correct details
  
  - [ ] 13.7 Implement payment processing
    - Create API endpoint /api/student/payment
    - Process payment and update status
    - Generate transaction ID
    - _Requirements: 7.7_
  
  - [ ]* 13.8 Write property test for payment status update
    - **Property 35: Payment Status Update**
    - **Validates: Requirements 7.7**
    - Test that status updates after successful payment
  
  - [ ] 13.9 Implement receipt generation
    - Create src/Services/ReportGenerator.php
    - Use TCPDF to generate PDF receipts
    - Include university header, student details, payment info
    - _Requirements: 7.6_
  
  - [ ]* 13.10 Write property test for receipt generation
    - **Property 34: PDF Receipt Generation**
    - **Validates: Requirements 7.6**
    - Test that PDF is generated with correct data

- [ ] 14. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.


- [ ] 15. Notice Board Module (HTML/CSS/PHP)
  - [ ] 15.1 Create Notice model
    - Create src/Models/Notice.php extending Model
    - Add methods: getActive(), getByCategory(), create(), update(), delete()
    - _Requirements: 20.3_
  
  - [ ] 15.2 Create NoticeController
    - Create src/Controllers/NoticeController.php
    - Implement index() method to fetch notices
    - Implement filter by category
    - Implement pagination (10 per page)
    - _Requirements: 8.1, 8.3, 8.7_
  
  - [ ] 15.3 Create notices view
    - Create views/student/notices.php
    - Display notices in reverse chronological order
    - Show title, date, category badge, content
    - Add category filter tabs
    - Implement pagination controls
    - _Requirements: 8.1, 8.2, 8.3, 8.7_
  
  - [ ]* 15.4 Write property test for notice display
    - **Property 36: Active Notices Display**
    - **Validates: Requirements 8.1**
    - Test that all active notices are displayed
  
  - [ ]* 15.5 Write property test for category filtering
    - **Property 38: Notice Category Filtering**
    - **Validates: Requirements 8.3**
    - Test that filtering shows only matching notices
  
  - [ ] 15.6 Implement urgent notice highlighting
    - Check priority/category for urgent notices
    - Apply red badge and icon styling
    - _Requirements: 8.4_
  
  - [ ]* 15.7 Write property test for pagination
    - **Property 42: Notice Pagination**
    - **Validates: Requirements 8.7**
    - Test that exactly 10 notices per page are shown
  
  - [ ] 15.8 Implement rich text rendering
    - Support HTML formatting in notice content
    - Sanitize content to prevent XSS
    - _Requirements: 8.6, 21.3_

- [ ] 16. Analysis Module (React Component)

  - [ ] 16.1 Set up React build environment
    - Initialize npm in react-components/
    - Install React, Recharts, Vite
    - Configure Vite for component bundling
    - _Requirements: 1.4, 9.1_
  
  - [ ] 16.2 Create Charts React component
    - Create react-components/src/Charts.jsx
    - Implement SGPA progression line chart
    - Implement subject marks bar chart
    - Implement grade distribution pie chart
    - Add semester filter dropdown
    - _Requirements: 9.2, 9.3, 9.4, 9.7_
  
  - [ ]* 16.3 Write property test for SGPA chart
    - **Property 43: SGPA Progression Chart**
    - **Validates: Requirements 9.2**
    - Test that chart displays for students with multiple semesters
  
  - [ ]* 16.4 Write property test for CGPA calculation
    - **Property 46: CGPA Calculation**
    - **Validates: Requirements 9.5**
    - Test that CGPA is calculated correctly
  
  - [ ] 16.5 Create analysis view
    - Create views/student/analysis.php
    - Include React component mount point
    - Load bundled React component
    - Pass data as props
    - _Requirements: 9.1_
  
  - [ ] 16.6 Create API endpoint for analysis data
    - Create /api/student/analysis endpoint
    - Return SGPA progression, subject marks, grade distribution
    - Calculate CGPA and attendance percentage
    - _Requirements: 9.5, 9.6_
  
  - [ ]* 16.7 Write property test for chart filtering
    - **Property 48: Chart Filtering by Semester**
    - **Validates: Requirements 9.7**
    - Test that charts update when semester is selected

- [ ] 17. Admin Dashboard Module (HTML/CSS/PHP)

  - [ ] 17.1 Create AdminController
    - Create src/Controllers/AdminController.php
    - Implement dashboard() method with statistics
    - Add role check middleware
    - _Requirements: 10.6_
  
  - [ ]* 17.2 Write property test for admin access control
    - **Property 51: Admin Route Access Control**
    - **Validates: Requirements 10.6**
    - Test that non-admin users cannot access admin routes
  
  - [ ] 17.3 Create admin dashboard view
    - Create views/admin/dashboard.php with admin layout
    - Display statistics cards (total students, teachers, courses, pending payments)
    - Show recent activities feed
    - Add quick action buttons
    - Display enrollment trends chart
    - _Requirements: 10.1, 10.2, 10.3, 10.4_
  
  - [ ] 17.4 Implement activity logging
    - Log all admin actions to activity_logs table
    - Include user_id, action, entity_type, entity_id, timestamp
    - _Requirements: 10.7_
  
  - [ ]* 17.5 Write property test for admin logging
    - **Property 52: Admin Action Logging**
    - **Validates: Requirements 10.7**
    - Test that all admin actions are logged

- [ ] 18. Admin Student Management (React DataTable)

  - [ ] 18.1 Create DataTable React component
    - Create react-components/src/DataTable.jsx
    - Implement sortable columns
    - Implement search/filter functionality
    - Implement pagination
    - Add inline editing capability
    - Add action buttons (Edit, Delete)
    - _Requirements: 11.1, 11.8_
  
  - [ ] 18.2 Create admin students view
    - Create views/admin/students.php
    - Include DataTable component mount point
    - Add "Add Student" button
    - _Requirements: 11.1_
  
  - [ ] 18.3 Create add/edit student modal
    - Create modal form with all student fields
    - Implement client-side validation
    - _Requirements: 11.3, 11.5_
  
  - [ ]* 18.4 Write property test for student form
    - **Property 55: Student Record Insertion**
    - **Validates: Requirements 11.4**
    - Test that valid data is inserted correctly
  
  - [ ] 18.5 Create API endpoints for student management
    - GET /api/admin/students - List all students
    - POST /api/admin/students - Add student
    - PUT /api/admin/students/:id - Update student
    - DELETE /api/admin/students/:id - Delete student
    - _Requirements: 11.4, 11.5, 11.6_
  
  - [ ]* 18.6 Write property test for delete confirmation
    - **Property 57: Delete Confirmation Dialog**
    - **Validates: Requirements 11.6**
    - Test that confirmation is required before deletion
  
  - [ ] 18.7 Implement bulk operations
    - Add CSV import functionality
    - Add CSV export functionality
    - Validate bulk data before import
    - _Requirements: 11.7_
  
  - [ ]* 18.8 Write property test for pagination
    - **Property 59: Student Pagination**
    - **Validates: Requirements 11.8**
    - Test that exactly 20 students per page are shown

- [ ] 19. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.

- [ ] 20. Admin Teacher Management
  - [ ] 20.1 Implement teachers() method in AdminController
    - Fetch all teachers with assigned courses count
    - _Requirements: 12.1_
  
  - [ ] 20.2 Create admin teachers view
    - Create views/admin/teachers.php
    - Use DataTable component for teacher list
    - Add "Add Teacher" button
    - _Requirements: 12.1_
  
  - [ ] 20.3 Create add/edit teacher modal
    - Create modal form with teacher fields
    - Include course assignment multi-select
    - _Requirements: 12.2_
  
  - [ ] 20.4 Create API endpoints for teacher management
    - GET /api/admin/teachers - List all teachers
    - POST /api/admin/teachers - Add teacher
    - PUT /api/admin/teachers/:id - Update teacher
    - DELETE /api/admin/teachers/:id - Delete teacher
    - _Requirements: 12.2, 12.3, 12.4_

- [ ] 21. Admin Fee Management

  - [ ] 21.1 Implement feeManagement() method in AdminController
    - Fetch fee structures by department and semester
    - Calculate payment statistics
    - _Requirements: 13.1, 13.4_
  
  - [ ] 21.2 Create admin fee management view
    - Create views/admin/fees.php
    - Display fee structures table
    - Show payment tracking dashboard
    - Add "Create Fee Type" button
    - _Requirements: 13.1, 13.4_
  
  - [ ] 21.3 Create fee type modal
    - Create modal form for fee type creation
    - Include fields: type, amount, due date, fine amount
    - _Requirements: 13.2, 13.3_
  
  - [ ] 21.4 Implement payment reports
    - Add report generation with filters
    - Export reports as PDF and CSV
    - _Requirements: 13.6_
  
  - [ ] 21.5 Implement payment reminders
    - Create email reminder system
    - Send reminders 3 days before due date
    - _Requirements: 13.7_

- [ ] 22. Admin Notice Management (React RichTextEditor)

  - [ ] 22.1 Create RichTextEditor React component
    - Create react-components/src/RichTextEditor.jsx
    - Use TinyMCE or similar library
    - Support bold, italic, lists, links
    - _Requirements: 8.6_
  
  - [ ] 22.2 Create admin notices view
    - Create views/admin/notices.php
    - List all notices with edit/delete buttons
    - Add "Create Notice" button
    - _Requirements: 8.5_
  
  - [ ] 22.3 Create notice modal
    - Create modal with RichTextEditor component
    - Include fields: title, category, target audience, expiry date
    - _Requirements: 8.5_
  
  - [ ] 22.4 Create API endpoints for notice management
    - GET /api/admin/notices - List all notices
    - POST /api/admin/notices - Create notice
    - PUT /api/admin/notices/:id - Update notice
    - DELETE /api/admin/notices/:id - Delete notice
    - _Requirements: 8.5_
  
  - [ ]* 22.5 Write property test for notice management
    - **Property 40: Admin Notice Management**
    - **Validates: Requirements 8.5**
    - Test that all CRUD operations work correctly

- [ ] 23. Teacher Dashboard Module

  - [ ] 23.1 Create TeacherController
    - Create src/Controllers/TeacherController.php
    - Implement dashboard() method
    - Fetch assigned courses and student counts
    - _Requirements: 14.1_
  
  - [ ] 23.2 Create teacher dashboard view
    - Create views/teacher/dashboard.php with teacher layout
    - Display assigned courses cards
    - Show upcoming classes
    - Display pending tasks
    - _Requirements: 14.1, 14.2, 14.3_

- [ ] 24. Teacher Attendance Module
  - [ ] 24.1 Create Attendance model
    - Create src/Models/Attendance.php extending Model
    - Add methods: mark(), getByDate(), getStatistics()
    - _Requirements: 20.3_
  
  - [ ] 24.2 Implement attendance() method in TeacherController
    - Fetch assigned courses
    - Fetch enrolled students for selected course
    - _Requirements: 15.1, 15.2_
  
  - [ ] 24.3 Create teacher attendance view
    - Create views/teacher/attendance.php
    - Display course selector
    - Show student list with attendance checkboxes
    - Add date picker
    - Display attendance statistics
    - _Requirements: 15.2, 15.3, 15.5_
  
  - [ ] 24.4 Create API endpoint for attendance
    - POST /api/teacher/attendance - Mark attendance
    - GET /api/teacher/attendance - View past attendance
    - _Requirements: 15.4, 15.6_
  
  - [ ] 24.5 Implement attendance reports
    - Generate attendance reports as PDF/CSV
    - Show percentage for each student
    - _Requirements: 15.7_

- [ ] 25. Teacher Marks Entry Module

  - [ ] 25.1 Implement marks() method in TeacherController
    - Fetch assigned courses
    - Fetch students for selected course
    - _Requirements: 16.1, 16.2_
  
  - [ ] 25.2 Create teacher marks view
    - Create views/teacher/marks.php
    - Display course and exam type selector
    - Show student table with marks input fields
    - Auto-calculate total and grade
    - _Requirements: 16.2, 16.4_
  
  - [ ] 25.3 Create API endpoint for marks entry
    - POST /api/teacher/marks - Save marks
    - Validate marks are within range
    - _Requirements: 16.3, 16.5_
  
  - [ ] 25.4 Implement bulk marks import
    - Add CSV upload for marks
    - Validate and import marks
    - _Requirements: 16.6_
  
  - [ ] 25.5 Show marks entry status
    - Display completion status for each course
    - Highlight pending entries
    - _Requirements: 16.7_

- [ ] 26. Checkpoint - Ensure all tests pass


  - Ensure all tests pass, ask the user if questions arise.

- [ ] 27. File Upload System

  - [ ] 27.1 Create FileUploader utility
    - Create src/Utils/FileUploader.php
    - Implement validate() method for type and size
    - Implement resize() method for images
    - Implement generateUniqueFilename() method
    - _Requirements: 23.1, 23.2, 23.4_
  
  - [ ]* 27.2 Write property test for file validation
    - **Property 69: File Upload Validation**
    - **Validates: Requirements 23.1**
    - Test that validation works for all file types
  
  - [ ] 27.3 Create FileUploader React component
    - Create react-components/src/FileUploader.jsx
    - Implement drag-and-drop functionality
    - Add progress indicator
    - Add image cropping for profile pictures
    - _Requirements: 23.1, 23.7_
  
  - [ ] 27.4 Create upload API endpoint
    - POST /api/upload/image - Upload profile picture
    - Validate, resize, and save file
    - Return file URL
    - _Requirements: 23.1, 23.2, 23.3_
  
  - [ ] 27.5 Implement malware scanning
    - Integrate ClamAV or similar
    - Scan all uploaded files
    - _Requirements: 23.5_

- [ ] 28. Email Notification System
  - [ ] 28.1 Create EmailService
    - Create src/Services/EmailService.php
    - Use PHPMailer for email sending
    - Create email templates
    - _Requirements: 24.4, 24.7_
  
  - [ ] 28.2 Implement email queue
    - Create email_queue table
    - Implement queue worker
    - _Requirements: 24.5_
  
  - [ ] 28.3 Implement notification triggers
    - Send email on new notice
    - Send email on payment due
    - Send email on marks published
    - _Requirements: 24.1, 24.2, 24.3_
  
  - [ ] 28.4 Implement notification preferences
    - Allow users to configure email preferences
    - Store preferences in database
    - _Requirements: 24.6_

- [ ] 29. Search and Filtering System
  - [ ] 29.1 Implement global search
    - Create search bar component
    - Search across students, teachers, subjects, notices
    - _Requirements: 25.1, 25.2_
  
  - [ ] 29.2 Implement autocomplete
    - Add autocomplete suggestions
    - Use AJAX for real-time suggestions
    - _Requirements: 25.5_
  
  - [ ] 29.3 Implement advanced filtering
    - Add multi-criteria filters
    - Maintain filters across navigation
    - _Requirements: 25.3, 25.7_
  
  - [ ] 29.4 Implement search highlighting
    - Highlight search terms in results
    - Show results count
    - _Requirements: 25.4, 25.6_

- [ ] 30. Performance Optimization
  - [ ] 30.1 Implement lazy loading
    - Lazy load images
    - Lazy load React components
    - _Requirements: 19.2_
  
  - [ ] 30.2 Minify and concatenate assets
    - Minify CSS and JavaScript
    - Concatenate files for production
    - _Requirements: 19.3_
  
  - [ ] 30.3 Implement caching
    - Set cache headers for static assets
    - Implement browser caching
    - _Requirements: 19.4_
  
  - [ ] 30.4 Optimize database queries
    - Add indexes to frequently queried columns
    - Optimize slow queries
    - _Requirements: 19.5, 20.4_
  
  - [ ]* 30.5 Write property test for page load performance
    - **Property 67: Page Load Performance**
    - **Validates: Requirements 19.1**
    - Test that pages load within 2 seconds

- [ ] 31. Security Hardening
  - [ ] 31.1 Implement CSRF protection
    - Generate CSRF tokens for all forms
    - Validate tokens on submission
    - _Requirements: 21.2_
  
  - [ ] 31.2 Implement input sanitization
    - Create Sanitizer utility
    - Sanitize all user inputs
    - _Requirements: 21.3_
  
  - [ ] 31.3 Implement rate limiting
    - Limit login attempts (5 per 15 minutes)
    - Store attempts in session
    - _Requirements: 21.5_
  
  - [ ] 31.4 Implement security logging
    - Log all authentication attempts to file
    - Log failed access attempts
    - _Requirements: 21.6_

- [ ] 32. Accessibility Implementation
  - [ ] 32.1 Add ARIA labels
    - Add aria-label to all interactive elements
    - Add aria-describedby for form fields
    - _Requirements: 26.2_
  
  - [ ] 32.2 Implement keyboard navigation
    - Ensure all functionality is keyboard accessible
    - Add visible focus indicators
    - _Requirements: 26.3_
  
  - [ ] 32.3 Ensure color contrast
    - Verify all text meets 4.5:1 contrast ratio
    - Adjust colors if needed
    - _Requirements: 26.4_
  
  - [ ] 32.4 Add alt text to images
    - Add descriptive alt text to all images
    - _Requirements: 26.5_
  
  - [ ] 32.5 Test with screen readers
    - Test with NVDA/JAWS
    - Fix any issues found
    - _Requirements: 26.6_

- [ ] 33. Internationalization
  - [ ] 33.1 Create language files
    - Create language files for English and Hindi
    - Store all UI text in language files
    - _Requirements: 27.2_
  
  - [ ] 33.2 Implement language switcher
    - Add language selector to header
    - Store preference in cookie
    - _Requirements: 27.1, 27.6_
  
  - [ ] 33.3 Implement locale formatting
    - Format dates, numbers, currency by locale
    - _Requirements: 27.4_

- [ ] 34. Reporting and Analytics
  - [ ] 34.1 Implement student performance reports
    - Generate reports with filters
    - Export as PDF and CSV
    - _Requirements: 28.1_
  
  - [ ] 34.2 Implement payment reports
    - Generate collection reports
    - Show statistics
    - _Requirements: 28.2_
  
  - [ ] 34.3 Implement attendance reports
    - Generate student-wise and course-wise reports
    - _Requirements: 28.3_
  
  - [ ] 34.4 Implement usage analytics
    - Track page views, active users, peak hours
    - Display on admin dashboard
    - _Requirements: 28.7_

- [ ] 35. Error Pages and User Experience Polish
  - [ ] 35.1 Create custom error pages
    - Create 404 error page with navigation back
    - Create 403 forbidden page
    - Create 500 server error page
    - _Requirements: 22.3_
  
  - [ ] 35.2 Add loading indicators
    - Add spinners for AJAX requests
    - Add skeleton screens for data loading
    - _Requirements: 19.1_
  
  - [ ] 35.3 Add success/error toast notifications
    - Create toast notification component
    - Show feedback for all user actions
    - _Requirements: 22.1_
  
  - [ ] 35.4 Polish animations and transitions
    - Smooth page transitions
    - Card hover effects
    - Button interactions
    - _Requirements: 2.5_

- [ ] 36. Final Integration and Testing
  - [ ] 36.1 Create simple setup script
    - Create setup.php to initialize database
    - Auto-create tables and seed sample data
    - _Requirements: 30.2_
  
  - [ ] 36.2 Create .env configuration
    - Set up .env file with database credentials
    - Configure app settings (timezone, locale, etc.)
    - _Requirements: 30.3_
  
  - [ ] 36.3 Test all user workflows
    - Test student login and all modules
    - Test teacher login and all modules
    - Test admin login and all modules
    - Test on Chrome, Firefox, Safari
    - Test on mobile devices
    - _Requirements: 30.6_
  
  - [ ] 36.4 Fix any bugs found during testing
    - Address any issues discovered
    - Ensure smooth user experience
    - _Requirements: 22.1_

- [ ] 37. Final Checkpoint - Project Complete
  - Ensure all features are working correctly
  - Verify the project runs smoothly on localhost
  - Confirm all modules are accessible and functional
  - Test with sample data to ensure everything works as expected
