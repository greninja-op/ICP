<?php

/**
 * University Portal - Entry Point
 * 
 * This file serves as the front controller for all HTTP requests.
 * It initializes the application and routes requests to appropriate controllers.
 */

// Define application paths
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('SRC_PATH', ROOT_PATH . '/src');
define('VIEWS_PATH', ROOT_PATH . '/views');

// Bootstrap the application
require_once ROOT_PATH . '/src/bootstrap.php';

// Import required classes
use App\Utils\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

// Create router instance
$router = new Router();

// ============================================================================
// PUBLIC ROUTES (No authentication required)
// ============================================================================

$router->get('/', function() {
    header('Location: /login');
    exit;
});

// Authentication routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->post('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword');

// ============================================================================
// STUDENT ROUTES (Require student role)
// ============================================================================

$router->get('/dashboard', 'DashboardController@index', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/subjects', 'StudentController@subjects', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/results', 'StudentController@results', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/payments', 'StudentController@payments', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/notices', 'NoticeController@index', [AuthMiddleware::class]);
$router->get('/analysis', 'StudentController@analysis', [AuthMiddleware::class, RoleMiddleware::class]);

// Student API endpoints
$router->get('/api/student/dashboard', 'StudentController@dashboardData', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/api/student/subjects', 'StudentController@subjectsData', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/api/student/results', 'StudentController@resultsData', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/api/student/payments', 'StudentController@paymentsData', [AuthMiddleware::class, RoleMiddleware::class]);
$router->post('/api/student/payment', 'StudentController@processPayment', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/api/student/notices', 'NoticeController@studentNotices', [AuthMiddleware::class]);
$router->get('/api/student/analysis', 'StudentController@analysisData', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/api/student/notifications', 'StudentController@notifications', [AuthMiddleware::class, RoleMiddleware::class]);

// ============================================================================
// TEACHER ROUTES (Require teacher role)
// ============================================================================

$router->get('/teacher/dashboard', 'TeacherController@dashboard', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/teacher/attendance', 'TeacherController@attendance', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/teacher/marks', 'TeacherController@marks', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/teacher/students', 'TeacherController@students', [AuthMiddleware::class, RoleMiddleware::class]);

// Teacher API endpoints
$router->get('/api/teacher/courses', 'TeacherController@courses', [AuthMiddleware::class, RoleMiddleware::class]);
$router->post('/api/teacher/attendance', 'TeacherController@markAttendance', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/api/teacher/attendance', 'TeacherController@viewAttendance', [AuthMiddleware::class, RoleMiddleware::class]);
$router->post('/api/teacher/marks', 'TeacherController@saveMarks', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/api/teacher/students', 'TeacherController@studentsList', [AuthMiddleware::class, RoleMiddleware::class]);

// ============================================================================
// ADMIN ROUTES (Require admin role)
// ============================================================================

$router->get('/admin/dashboard', 'AdminController@dashboard', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/admin/students', 'AdminController@students', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/admin/teachers', 'AdminController@teachers', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/admin/fees', 'AdminController@feeManagement', [AuthMiddleware::class, RoleMiddleware::class]);
$router->get('/admin/courses', 'AdminController@courses', [AuthMiddleware::class, RoleMiddleware::class]);

// Admin API endpoints - Students
$router->get('/api/admin/students', 'AdminController@studentsList', [AuthMiddleware::class, RoleMiddleware::class]);
$router->post('/api/admin/students', 'AdminController@addStudent', [AuthMiddleware::class, RoleMiddleware::class]);
$router->put('/api/admin/students/{id}', 'AdminController@updateStudent', [AuthMiddleware::class, RoleMiddleware::class]);
$router->delete('/api/admin/students/{id}', 'AdminController@deleteStudent', [AuthMiddleware::class, RoleMiddleware::class]);

// Admin API endpoints - Teachers
$router->get('/api/admin/teachers', 'AdminController@teachersList', [AuthMiddleware::class, RoleMiddleware::class]);
$router->post('/api/admin/teachers', 'AdminController@addTeacher', [AuthMiddleware::class, RoleMiddleware::class]);
$router->put('/api/admin/teachers/{id}', 'AdminController@updateTeacher', [AuthMiddleware::class, RoleMiddleware::class]);
$router->delete('/api/admin/teachers/{id}', 'AdminController@deleteTeacher', [AuthMiddleware::class, RoleMiddleware::class]);

// Admin API endpoints - Notices
$router->get('/api/admin/notices', 'AdminController@noticesList', [AuthMiddleware::class, RoleMiddleware::class]);
$router->post('/api/admin/notices', 'AdminController@createNotice', [AuthMiddleware::class, RoleMiddleware::class]);
$router->put('/api/admin/notices/{id}', 'AdminController@updateNotice', [AuthMiddleware::class, RoleMiddleware::class]);
$router->delete('/api/admin/notices/{id}', 'AdminController@deleteNotice', [AuthMiddleware::class, RoleMiddleware::class]);

// Admin API endpoints - Reports
$router->get('/api/admin/reports', 'AdminController@generateReports', [AuthMiddleware::class, RoleMiddleware::class]);

// ============================================================================
// ERROR PAGES
// ============================================================================

$router->get('/403', function() {
    http_response_code(403);
    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"en\">\n";
    echo "<head>\n";
    echo "    <meta charset=\"UTF-8\">\n";
    echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
    echo "    <title>403 - Forbidden</title>\n";
    echo "    <style>\n";
    echo "        * { margin: 0; padding: 0; box-sizing: border-box; }\n";
    echo "        body { \n";
    echo "            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;\n";
    echo "            background: linear-gradient(135deg, #e0f2ff 0%, #ffffff 100%);\n";
    echo "            min-height: 100vh;\n";
    echo "            display: flex;\n";
    echo "            align-items: center;\n";
    echo "            justify-content: center;\n";
    echo "            padding: 2rem;\n";
    echo "        }\n";
    echo "        .error-container {\n";
    echo "            background: rgba(255, 255, 255, 0.7);\n";
    echo "            backdrop-filter: blur(10px);\n";
    echo "            border-radius: 1.5rem;\n";
    echo "            padding: 3rem;\n";
    echo "            text-align: center;\n";
    echo "            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);\n";
    echo "            max-width: 500px;\n";
    echo "        }\n";
    echo "        h1 { \n";
    echo "            color: #dc3545;\n";
    echo "            font-size: 4rem;\n";
    echo "            font-weight: 900;\n";
    echo "            margin-bottom: 1rem;\n";
    echo "        }\n";
    echo "        h2 {\n";
    echo "            color: #333;\n";
    echo "            font-size: 1.5rem;\n";
    echo "            font-weight: 700;\n";
    echo "            margin-bottom: 1rem;\n";
    echo "        }\n";
    echo "        p { \n";
    echo "            color: #666;\n";
    echo "            font-size: 1rem;\n";
    echo "            margin-bottom: 2rem;\n";
    echo "            line-height: 1.6;\n";
    echo "        }\n";
    echo "        a { \n";
    echo "            display: inline-block;\n";
    echo "            background: #137fec;\n";
    echo "            color: white;\n";
    echo "            padding: 0.75rem 2rem;\n";
    echo "            border-radius: 0.5rem;\n";
    echo "            text-decoration: none;\n";
    echo "            font-weight: 500;\n";
    echo "            transition: all 0.3s ease;\n";
    echo "        }\n";
    echo "        a:hover {\n";
    echo "            background: #0d6efd;\n";
    echo "            transform: translateY(-2px);\n";
    echo "            box-shadow: 0 4px 12px rgba(19, 127, 236, 0.3);\n";
    echo "        }\n";
    echo "    </style>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "    <div class=\"error-container\">\n";
    echo "        <h1>403</h1>\n";
    echo "        <h2>Access Forbidden</h2>\n";
    echo "        <p>You don't have permission to access this resource.</p>\n";
    echo "        <a href=\"/login\">Go to Login</a>\n";
    echo "    </div>\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
});

// ============================================================================
// DISPATCH REQUEST
// ============================================================================

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($requestMethod, $requestUri);
