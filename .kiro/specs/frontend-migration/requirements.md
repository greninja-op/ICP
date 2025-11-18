# Requirements Document

## Introduction

This document outlines the requirements for migrating the University Portal from a dual-architecture system (Legacy HTML/CSS/JS + React SPA) to a unified single-project architecture using primarily HTML/CSS/PHP (70%) with minimal React components (30%). The migration will preserve the exact UI/UX design of the React version while consolidating all features into one cohesive system.

## Glossary

- **System**: The University Portal web application
- **User**: Any person accessing the portal (Student, Teacher, or Admin)
- **Student**: A user with student role accessing academic services
- **Teacher**: A user with staff role managing courses and students
- **Admin**: A user with administrative privileges managing the entire system
- **Session**: An authenticated user's active connection to the system
- **Module**: A distinct functional area of the portal (Dashboard, Subjects, Results, etc.)
- **Component**: A reusable UI element or functionality unit
- **Migration**: The process of converting React components to HTML/CSS/PHP
- **Glassmorphism**: A design style using backdrop-filter blur effects with transparency
- **SGPA**: Semester Grade Point Average
- **SCPA**: Semester Credit Point Average
- **Mock Data**: Temporary hardcoded data used during development
- **PHP Backend**: Server-side PHP scripts handling business logic and data
- **Minimal React**: Limited use of React for complex interactive components only

## Requirements

### Requirement 1: Project Architecture and Structure

**User Story:** As a developer, I want a unified project structure with clear separation of concerns, so that the codebase is maintainable and scalable.

#### Acceptance Criteria

1. THE System SHALL organize all files in a single root directory with clear folder structure for PHP, HTML, CSS, JavaScript, and assets
2. THE System SHALL use PHP for server-side routing, authentication, and business logic
3. THE System SHALL use HTML/CSS for 70% of the UI implementation with semantic markup
4. THE System SHALL use minimal React (30% or less) only for highly interactive components like data tables, charts, and complex forms
5. THE System SHALL maintain consistent naming conventions across all files (kebab-case for files, camelCase for JavaScript, snake_case for PHP)
6. THE System SHALL separate concerns with dedicated folders: `/includes` for PHP partials, `/assets` for static files, `/api` for backend endpoints, `/components` for React components

### Requirement 2: Visual Design and UI Consistency

**User Story:** As a user, I want the exact same visual design and user experience as the React version, so that the interface feels modern and polished.

#### Acceptance Criteria

1. THE System SHALL replicate the exact glassmorphism design system from the React version with backdrop-filter blur effects
2. THE System SHALL use the same color palette: primary (#137fec), background gradients (light: #e0f2ff to #ffffff, dark: #1e3a8a to #101922)
3. THE System SHALL implement the same typography using Inter font family with weights 400, 500, 700, and 900
4. THE System SHALL maintain identical spacing, border-radius values (0.25rem, 0.5rem, 0.75rem, 1.5rem, full), and shadow effects
5. THE System SHALL preserve all animations and transitions from the React version using CSS transitions and animations
6. THE System SHALL implement responsive design with the same breakpoints and mobile-first approach
7. THE System SHALL support dark mode with the same color scheme and toggle mechanism

### Requirement 3: Authentication and Session Management

**User Story:** As a user, I want to securely log in with my credentials and role, so that I can access my personalized portal features.

#### Acceptance Criteria

1. WHEN a user submits login credentials with a selected role, THE System SHALL validate the username, password, and role against the database
2. WHEN authentication succeeds, THE System SHALL create a secure PHP session with user data (id, name, email, role, department, semester)
3. WHEN authentication fails, THE System SHALL display an appropriate error message without revealing whether username or password was incorrect
4. THE System SHALL implement role-based access control preventing unauthorized access to admin and teacher routes
5. WHEN a user logs out, THE System SHALL destroy the PHP session and redirect to the login page
6. THE System SHALL implement session timeout after 30 minutes of inactivity
7. THE System SHALL remember the user's role selection using a cookie for convenience

### Requirement 4: Student Dashboard Module

**User Story:** As a student, I want to view my academic overview and important notifications on my dashboard, so that I stay informed about my progress.

#### Acceptance Criteria

1. WHEN a student accesses the dashboard, THE System SHALL display a welcome card with the student's name and personalized greeting
2. THE System SHALL display academic progress with a circular progress indicator showing current GPA
3. THE System SHALL list upcoming assignments with due dates and "View Details" links
4. THE System SHALL show college announcements in a dedicated section
5. THE System SHALL display notifications in a sidebar with color-coded icons (blue for events, red for urgent, green for informational)
6. THE System SHALL include a fixed bottom navigation bar with icons for Dashboard, Subjects, Notice, Results, and Payments
7. THE System SHALL load all dashboard data dynamically from PHP backend without page refresh for notifications

### Requirement 5: Subjects Module

**User Story:** As a student, I want to view my enrolled subjects with instructor details, so that I can access course information easily.

#### Acceptance Criteria

1. WHEN a student accesses the subjects page, THE System SHALL display all subjects for their current semester in a grid layout
2. THE System SHALL show each subject card with subject code, subject name, instructor name, instructor department, and a gradient header
3. WHEN a user clicks "View Details" on a subject card, THE System SHALL expand the card to show additional topics without page reload
4. THE System SHALL display instructor avatars using UI Avatars API with instructor name and custom background colors
5. THE System SHALL organize subjects by department and semester from the database
6. THE System SHALL indicate lab subjects with a "Lab" badge
7. THE System SHALL maintain the collapsible card functionality using vanilla JavaScript

### Requirement 6: Results Module

**User Story:** As a student, I want to view my examination results with grades and SGPA, so that I can track my academic performance.

#### Acceptance Criteria

1. WHEN a student accesses the results page, THE System SHALL display results organized by semester in collapsible sections
2. THE System SHALL show SGPA and total marks prominently for each semester
3. WHEN a user expands a semester section, THE System SHALL display a table with subject names, internal marks, theory marks, total marks, and grades
4. THE System SHALL calculate grades automatically based on total marks (A+ ≥90, A ≥80, B+ ≥70, B ≥60, C ≥50, D ≥40, F <40)
5. THE System SHALL highlight lab subjects with a distinct background color and show only total marks (no internal/theory split)
6. THE System SHALL display earlier semesters in a summary view with SGPA and total marks
7. THE System SHALL implement collapsible sections using vanilla JavaScript with smooth transitions

### Requirement 7: Payments Module

**User Story:** As a student, I want to view pending fees and make payments online, so that I can manage my financial obligations conveniently.

#### Acceptance Criteria

1. WHEN a student accesses the payments page, THE System SHALL display a payment summary showing total paid and pending amounts
2. THE System SHALL list all pending payments with fee type, amount, due date, and "Pay Now" button
3. THE System SHALL show a semester fees overview with payment status (Paid, Pending, Upcoming) for all semesters
4. WHEN a user clicks "Pay Now", THE System SHALL open a modal with payment details and method selection (QR Code, Card, UPI)
5. THE System SHALL display payment history with transaction IDs, dates, amounts, and "Download Receipt" buttons
6. WHEN a user downloads a receipt, THE System SHALL generate a PDF receipt using PHP with university header, student details, and payment information
7. THE System SHALL update payment status in the database after successful payment processing

### Requirement 8: Notice Board Module

**User Story:** As a user, I want to view important announcements and notices, so that I stay updated with university information.

#### Acceptance Criteria

1. WHEN a user accesses the notice page, THE System SHALL display all active notices in reverse chronological order
2. THE System SHALL show each notice with title, date, category badge, and full content
3. THE System SHALL filter notices by category (Academic, Events, Urgent, General) using tabs or dropdown
4. THE System SHALL highlight urgent notices with a red badge and icon
5. THE System SHALL allow admins to create, edit, and delete notices through an admin interface
6. THE System SHALL support rich text formatting in notice content (bold, italic, lists, links)
7. THE System SHALL implement pagination showing 10 notices per page

### Requirement 9: Analysis Module

**User Story:** As a student, I want to view visual analytics of my academic performance, so that I can identify trends and areas for improvement.

#### Acceptance Criteria

1. WHEN a student accesses the analysis page, THE System SHALL display performance charts using a minimal React charting library (Chart.js or Recharts)
2. THE System SHALL show a line chart of SGPA progression across all semesters
3. THE System SHALL display a bar chart comparing marks across subjects for the current semester
4. THE System SHALL show a pie chart of grade distribution (A+, A, B+, B, C, D, F)
5. THE System SHALL calculate and display overall CGPA (Cumulative Grade Point Average)
6. THE System SHALL show attendance percentage if attendance data is available
7. THE System SHALL allow filtering charts by semester using a dropdown selector

### Requirement 10: Admin Dashboard Module

**User Story:** As an admin, I want to manage students, teachers, courses, and fees from a centralized dashboard, so that I can efficiently administer the portal.

#### Acceptance Criteria

1. WHEN an admin accesses the admin dashboard, THE System SHALL display statistics cards showing total students, teachers, courses, and pending payments
2. THE System SHALL show recent activities feed with timestamps
3. THE System SHALL provide quick action buttons for adding students, teachers, and notices
4. THE System SHALL display charts showing enrollment trends and payment status distribution
5. THE System SHALL include navigation links to all admin modules (Students, Teachers, Notices, Fee Management, Courses)
6. THE System SHALL restrict access to admin routes using PHP session validation
7. THE System SHALL log all admin actions with timestamps and admin user ID

### Requirement 11: Admin Student Management

**User Story:** As an admin, I want to add, edit, view, and delete student records, so that I can maintain accurate student data.

#### Acceptance Criteria

1. WHEN an admin accesses student management, THE System SHALL display a searchable and sortable table of all students using a minimal React data table component
2. THE System SHALL show student ID, name, email, department, semester, and action buttons (Edit, Delete) for each student
3. WHEN an admin clicks "Add Student", THE System SHALL open a modal form with fields for all student details
4. WHEN an admin submits the add student form, THE System SHALL validate all fields and insert the record into the database
5. WHEN an admin clicks "Edit" on a student, THE System SHALL populate the form with existing data and allow updates
6. WHEN an admin clicks "Delete" on a student, THE System SHALL show a confirmation dialog before removing the record
7. THE System SHALL support bulk operations (import from CSV, export to CSV)
8. THE System SHALL implement pagination showing 20 students per page

### Requirement 12: Admin Teacher Management

**User Story:** As an admin, I want to manage teacher records and assign courses, so that I can organize the teaching staff effectively.

#### Acceptance Criteria

1. WHEN an admin accesses teacher management, THE System SHALL display a table of all teachers with ID, name, email, department, and assigned courses
2. THE System SHALL allow adding new teachers with fields for name, email, phone, department, qualification, and joining date
3. THE System SHALL allow editing teacher details and course assignments
4. THE System SHALL allow deleting teacher records with confirmation
5. THE System SHALL show the number of courses assigned to each teacher
6. THE System SHALL support searching teachers by name, department, or email
7. THE System SHALL validate email format and phone number format before saving

### Requirement 13: Admin Fee Management

**User Story:** As an admin, I want to manage fee structures and track payments, so that I can ensure proper financial administration.

#### Acceptance Criteria

1. WHEN an admin accesses fee management, THE System SHALL display fee structures for all departments and semesters
2. THE System SHALL allow creating new fee types (Semester Fee, Exam Fee, Lab Fee, Other)
3. THE System SHALL allow setting fee amounts, due dates, and fine amounts for late payment
4. THE System SHALL show a payment tracking dashboard with total collected, pending, and overdue amounts
5. THE System SHALL allow viewing payment history for individual students
6. THE System SHALL allow generating payment reports filtered by date range, department, or semester
7. THE System SHALL send automated email reminders for pending payments 3 days before due date

### Requirement 14: Teacher Dashboard Module

**User Story:** As a teacher, I want to view my assigned courses and student statistics, so that I can manage my teaching responsibilities.

#### Acceptance Criteria

1. WHEN a teacher accesses the teacher dashboard, THE System SHALL display all assigned courses with student counts
2. THE System SHALL show upcoming classes and pending tasks (attendance, marks entry)
3. THE System SHALL display recent student submissions and queries
4. THE System SHALL provide quick links to attendance, marks entry, and student list
5. THE System SHALL show teacher profile information with edit capability
6. THE System SHALL display announcements relevant to teachers
7. THE System SHALL show a calendar view of classes and exams

### Requirement 15: Teacher Attendance Module

**User Story:** As a teacher, I want to mark student attendance for my classes, so that I can track student participation.

#### Acceptance Criteria

1. WHEN a teacher accesses the attendance module, THE System SHALL display a list of assigned courses
2. WHEN a teacher selects a course, THE System SHALL show all enrolled students with checkboxes for marking attendance
3. THE System SHALL allow marking attendance for a specific date with Present/Absent/Leave options
4. THE System SHALL save attendance records to the database with timestamp
5. THE System SHALL show attendance statistics (percentage) for each student
6. THE System SHALL allow viewing and editing past attendance records
7. THE System SHALL generate attendance reports for download as PDF or CSV

### Requirement 16: Teacher Marks Entry Module

**User Story:** As a teacher, I want to enter and update student marks, so that students can view their results.

#### Acceptance Criteria

1. WHEN a teacher accesses marks entry, THE System SHALL display assigned courses with exam types (Internal, Theory, Lab)
2. WHEN a teacher selects a course and exam type, THE System SHALL show a table of students with input fields for marks
3. THE System SHALL validate marks are within allowed range (0-20 for internal, 0-80 for theory, 0-100 for lab)
4. THE System SHALL calculate total marks and grades automatically
5. THE System SHALL save marks to the database with teacher ID and timestamp
6. THE System SHALL allow bulk import of marks from CSV file
7. THE System SHALL show a summary of marks entry status (completed/pending) for each course

### Requirement 17: Navigation and Routing

**User Story:** As a user, I want intuitive navigation between different modules, so that I can access features quickly.

#### Acceptance Criteria

1. THE System SHALL implement PHP-based routing for all pages without exposing .php extensions in URLs
2. THE System SHALL display a fixed bottom navigation bar for students with active state highlighting
3. THE System SHALL display a sidebar navigation for admin and teacher dashboards
4. THE System SHALL implement breadcrumb navigation showing current location
5. THE System SHALL redirect unauthorized users to login page when accessing protected routes
6. THE System SHALL maintain navigation state across page loads
7. THE System SHALL support browser back/forward buttons correctly

### Requirement 18: Responsive Design and Mobile Support

**User Story:** As a user, I want the portal to work seamlessly on mobile devices, so that I can access it from anywhere.

#### Acceptance Criteria

1. THE System SHALL implement mobile-first responsive design with breakpoints at 640px, 768px, 1024px, and 1280px
2. THE System SHALL adapt navigation to a hamburger menu on mobile devices
3. THE System SHALL ensure all tables are horizontally scrollable on small screens
4. THE System SHALL optimize touch targets to minimum 44x44 pixels for mobile
5. THE System SHALL use responsive images with appropriate sizes for different screen widths
6. THE System SHALL test and ensure functionality on iOS Safari, Android Chrome, and mobile Firefox
7. THE System SHALL maintain readability with appropriate font sizes (minimum 16px for body text on mobile)

### Requirement 19: Performance and Optimization

**User Story:** As a user, I want fast page loads and smooth interactions, so that I have a pleasant experience using the portal.

#### Acceptance Criteria

1. THE System SHALL load initial page content within 2 seconds on a standard broadband connection
2. THE System SHALL implement lazy loading for images and heavy components
3. THE System SHALL minify and concatenate CSS and JavaScript files for production
4. THE System SHALL use browser caching for static assets with appropriate cache headers
5. THE System SHALL optimize database queries to execute within 100ms
6. THE System SHALL implement pagination for large data sets (>50 records)
7. THE System SHALL use CSS animations instead of JavaScript where possible for better performance

### Requirement 20: Data Management and Database

**User Story:** As a developer, I want a well-structured database schema, so that data is organized efficiently and queries are optimized.

#### Acceptance Criteria

1. THE System SHALL use MySQL database with properly normalized tables (3NF)
2. THE System SHALL implement foreign key constraints to maintain referential integrity
3. THE System SHALL use prepared statements for all database queries to prevent SQL injection
4. THE System SHALL create indexes on frequently queried columns (user_id, student_id, semester, department)
5. THE System SHALL implement database connection pooling for better performance
6. THE System SHALL backup database daily with retention of 30 days
7. THE System SHALL log all database errors with timestamps and query details

### Requirement 21: Security and Data Protection

**User Story:** As a user, I want my personal data to be secure and protected, so that I can trust the portal with my information.

#### Acceptance Criteria

1. THE System SHALL hash all passwords using bcrypt with a cost factor of 12 before storing in database
2. THE System SHALL implement CSRF protection for all form submissions
3. THE System SHALL sanitize all user inputs to prevent XSS attacks
4. THE System SHALL use HTTPS for all communications in production
5. THE System SHALL implement rate limiting on login attempts (5 attempts per 15 minutes)
6. THE System SHALL log all authentication attempts with IP addresses
7. THE System SHALL comply with data protection regulations by allowing users to request data deletion

### Requirement 22: Error Handling and Logging

**User Story:** As a developer, I want comprehensive error handling and logging, so that I can quickly diagnose and fix issues.

#### Acceptance Criteria

1. THE System SHALL display user-friendly error messages without exposing technical details
2. THE System SHALL log all errors to a file with timestamp, error message, stack trace, and user context
3. THE System SHALL implement custom error pages for 404, 403, and 500 errors
4. THE System SHALL send email notifications to admins for critical errors
5. THE System SHALL validate all form inputs on both client and server side
6. THE System SHALL handle database connection failures gracefully with retry logic
7. THE System SHALL implement error boundaries for React components to prevent full page crashes

### Requirement 23: File Upload and Management

**User Story:** As a user, I want to upload profile pictures and documents, so that I can personalize my profile and submit assignments.

#### Acceptance Criteria

1. WHEN a user uploads a profile picture, THE System SHALL validate file type (JPEG, PNG, WebP) and size (max 2MB)
2. THE System SHALL resize and optimize uploaded images to 300x300 pixels for profiles
3. THE System SHALL store uploaded files in a secure directory outside the web root
4. THE System SHALL generate unique filenames to prevent conflicts and security issues
5. THE System SHALL scan uploaded files for malware using ClamAV or similar
6. THE System SHALL allow users to delete their uploaded files
7. THE System SHALL implement file upload progress indicator using vanilla JavaScript

### Requirement 24: Email Notifications

**User Story:** As a user, I want to receive email notifications for important events, so that I stay informed even when not logged in.

#### Acceptance Criteria

1. WHEN a new notice is posted, THE System SHALL send email notifications to all relevant users
2. WHEN a payment is due in 3 days, THE System SHALL send a reminder email to the student
3. WHEN marks are published, THE System SHALL send an email notification to affected students
4. THE System SHALL use a professional email template with university branding
5. THE System SHALL implement email queue to prevent blocking during bulk sends
6. THE System SHALL allow users to configure email notification preferences
7. THE System SHALL use PHPMailer or similar library for reliable email delivery

### Requirement 25: Search and Filtering

**User Story:** As a user, I want to search and filter data easily, so that I can find information quickly.

#### Acceptance Criteria

1. THE System SHALL implement global search functionality accessible from all pages
2. THE System SHALL support searching students by name, ID, email, or department
3. THE System SHALL support filtering results by multiple criteria (department, semester, status)
4. THE System SHALL highlight search terms in results
5. THE System SHALL implement autocomplete suggestions for search inputs
6. THE System SHALL show search results count and allow sorting by relevance
7. THE System SHALL maintain search filters when navigating between pages

### Requirement 26: Accessibility Compliance

**User Story:** As a user with disabilities, I want the portal to be accessible, so that I can use all features independently.

#### Acceptance Criteria

1. THE System SHALL meet WCAG 2.1 Level AA accessibility standards
2. THE System SHALL provide proper ARIA labels for all interactive elements
3. THE System SHALL ensure all functionality is keyboard accessible with visible focus indicators
4. THE System SHALL maintain color contrast ratio of at least 4.5:1 for normal text
5. THE System SHALL provide alt text for all images
6. THE System SHALL support screen readers with proper semantic HTML
7. THE System SHALL allow text resizing up to 200% without breaking layout

### Requirement 27: Internationalization Support

**User Story:** As a user, I want to use the portal in my preferred language, so that I can understand content better.

#### Acceptance Criteria

1. THE System SHALL support multiple languages (English, Hindi) with easy language switching
2. THE System SHALL store all UI text in language files for easy translation
3. THE System SHALL detect browser language preference and set default language accordingly
4. THE System SHALL format dates, numbers, and currency according to selected locale
5. THE System SHALL allow admins to add new language translations through admin panel
6. THE System SHALL maintain language preference across sessions using cookies
7. THE System SHALL support right-to-left (RTL) languages in the future

### Requirement 28: Reporting and Analytics

**User Story:** As an admin, I want to generate various reports, so that I can analyze portal usage and academic performance.

#### Acceptance Criteria

1. THE System SHALL allow generating student performance reports filtered by department, semester, or date range
2. THE System SHALL generate payment collection reports with total collected, pending, and overdue amounts
3. THE System SHALL generate attendance reports showing student-wise and course-wise statistics
4. THE System SHALL export all reports in PDF and CSV formats
5. THE System SHALL show visual charts for key metrics on admin dashboard
6. THE System SHALL allow scheduling automated report generation and email delivery
7. THE System SHALL track portal usage statistics (page views, active users, peak hours)

### Requirement 29: Backup and Recovery

**User Story:** As an admin, I want automated backups and recovery options, so that data is protected against loss.

#### Acceptance Criteria

1. THE System SHALL perform automated daily database backups at 2 AM
2. THE System SHALL store backups in a secure off-site location
3. THE System SHALL retain daily backups for 30 days and monthly backups for 1 year
4. THE System SHALL provide a restore functionality accessible only to super admins
5. THE System SHALL test backup integrity weekly by attempting restoration to a test environment
6. THE System SHALL backup uploaded files separately from database
7. THE System SHALL send email notifications if backup fails

### Requirement 30: Development and Deployment

**User Story:** As a developer, I want clear development and deployment processes, so that I can work efficiently and deploy safely.

#### Acceptance Criteria

1. THE System SHALL use Git for version control with clear branching strategy (main, develop, feature branches)
2. THE System SHALL include a comprehensive README with setup instructions
3. THE System SHALL use environment variables for configuration (database credentials, API keys)
4. THE System SHALL include database migration scripts for schema changes
5. THE System SHALL use Composer for PHP dependency management
6. THE System SHALL implement automated testing for critical functionality
7. THE System SHALL use a staging environment for testing before production deployment
