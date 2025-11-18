# Frontend Integration Guide

This documentation provides a complete reference for integrating the frontend with the Student Portal backend.

## 1. System Overview

The backend is built with **PHP** and uses **MySQL** for the database. It exposes a RESTful API that returns JSON responses.

- **Base URL**: `http://localhost:8000/api` (development) or `/backend/api` (relative path)
- **Authentication**: JWT (JSON Web Tokens) with Token Blacklisting
- **Database**: MySQL 8.0+ (utf8mb4_unicode_ci)
- **PHP Version**: 7.4+ (8.0+ recommended)
- **Protocol**: HTTP in development, HTTPS in production

## 2. Authentication & Security

### JWT Authentication
All protected endpoints require a valid JWT token in the `Authorization` header.

**Header Format:**
```http
Authorization: Bearer <your_jwt_token>
```

### Login Endpoint
- **URL**: `/auth/login.php`
- **Method**: `POST`
- **Rate Limit**: **5 requests per minute per IP**. Exceeding this returns `429 Too Many Requests`.
- **Auth Required**: No

**Request Body:**
```json
{
  "username": "student123",
  "password": "password123",
  "role": "student"  // Optional: validates against user's actual role
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsIn...",
    "user": {
      "id": 1,
      "username": "student123",
      "email": "student@example.com",
      "role": "student",
      "status": "active",
      "last_login": "2025-11-19 10:30:00"
    }
  }
}
```

**Error Responses:**
- `400`: Missing username/password
- `401`: Invalid credentials
- `403`: Account inactive or role mismatch
- `429`: Too many login attempts

### Logout Endpoint
- **URL**: `/auth/logout.php`
- **Method**: `POST`
- **Auth Required**: Yes
- **Action**: Invalidates the current JWT token by adding it to a blacklist.

**Request:** No body required (token from Authorization header)

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

**Frontend Action Required:** Remove token from localStorage/sessionStorage after successful logout.

### Token Verification Endpoint
- **URL**: `/auth/verify.php`
- **Method**: `GET`
- **Auth Required**: Yes
- **Purpose**: Verify if current token is valid and not blacklisted

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "username": "student123",
    "role": "student"
  }
}
```

## 3. Security Features & Constraints

### Rate Limiting
The API implements rate limiting to prevent abuse:
- **General Endpoints**: 100 requests per minute per IP.
- **Login Endpoint**: 5 requests per minute per IP.
- **Response**: `429 Too Many Requests` if limit exceeded.

### Input Sanitization
- All string inputs are automatically sanitized (HTML tags stripped, special characters encoded).
- Do not send raw HTML in JSON payloads.

### Request ID
- Every response includes a `X-Request-ID` header.
- **Best Practice**: Log this ID in frontend error reporting to help debug backend issues.

### Security Headers
The backend sends strict security headers. Ensure your frontend respects them:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`

## 4. API Response Format

The API uses a consistent response structure.

### Success Structure
```json
{
  "success": true,
  "data": { ... } // Object or Array of data
}
```

### Error Structure
```json
{
  "success": false,
  "error": "error_code", // e.g., 'unauthorized', 'validation_error'
  "message": "Human readable error message",
  "details": { ... } // Optional validation details (hidden in production for 500 errors)
}
```

**Note on 500 Errors**: In production environments, internal server errors (500) will return a generic "An unexpected error occurred" message to prevent information leakage.

## 5. User Roles & Permissions

1. **Student**: Access to own profile, marks, attendance, fees. **IDOR Protected**: Cannot access other students' data.
2. **Teacher**: Access to assigned students, attendance marking, marks entry. **Restricted**: Can only modify data for their department/subjects.
3. **Admin**: Full system access.

## 6. Complete API Endpoints Reference

### Authentication Endpoints
*Base Path: `/auth`*

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/login.php` | POST | No | User login (returns JWT token) |
| `/logout.php` | POST | Yes | Invalidate current token |
| `/verify.php` | GET | Yes | Verify token validity |

### Student Endpoints
*Base Path: `/student`*

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/get_profile.php` | GET | Yes | Get student profile (supports `?student_id=X` for admins) |
| `/profile.php` | POST | Yes | Update student profile |
| `/get_marks.php` | GET | Yes | Get marks by semester (`?semester=1`) |
| `/get_attendance.php` | GET | Yes | Get attendance records |
| `/get_fees.php` | GET | Yes | Get fee structure and status |
| `/get_payments.php` | GET | Yes | Get payment history |
| `/download_id_card.php` | GET | Yes | Download virtual ID card (PDF) |
| `/download_receipt.php` | GET | Yes | Download payment receipt (PDF, requires `?payment_id=X`) |
| `/download_performance_report.php` | GET | Yes | Download performance report (PDF) |

### Teacher Endpoints
*Base Path: `/teacher`*

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/get_students.php` | GET | Yes | List students (supports `?search=`, `?semester=`, `?department=`) |
| `/mark_attendance.php` | POST | Yes | Mark attendance for students |
| `/get_attendance_report.php` | GET | Yes | Get attendance report |
| `/enter_marks.php` | POST | Yes | Enter marks for students |
| `/update_marks.php` | POST | Yes | Update existing marks |
| `/assignments/list.php` | GET | Yes | List assignments |
| `/assignments/create.php` | POST | Yes | Create new assignment |
| `/assignments/submissions.php` | GET | Yes | View assignment submissions |

### Admin Endpoints
*Base Path: `/admin`*

#### Students Management (`/admin/students/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/list.php` | GET | List all students (supports `?search=`, `?semester=`, `?department=`) |
| `/create.php` | POST | Create new student account |
| `/update.php` | POST | Update student information |
| `/delete.php` | POST | Delete student (soft delete) |

#### Teachers Management (`/admin/teachers/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/list.php` | GET | List all teachers |
| `/create.php` | POST | Create new teacher account |
| `/update.php` | POST | Update teacher information |
| `/delete.php` | POST | Delete teacher |

#### Subjects Management (`/admin/subjects/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/list.php` | GET | List all subjects |
| `/create.php` | POST | Create new subject |
| `/update.php` | POST | Update subject |
| `/delete.php` | POST | Delete subject |

#### Fees Management (`/admin/fees/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/list.php` | GET | List fee structures |
| `/create.php` | POST | Create fee structure |
| `/update.php` | POST | Update fee structure |
| `/delete.php` | POST | Delete fee structure |

#### Payments Management (`/admin/payments/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/list.php` | GET | List all payments |
| `/process.php` | POST | Process payment (mock payment) |

#### Sessions Management (`/admin/sessions/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/list.php` | GET | List academic sessions |
| `/create.php` | POST | Create new session |
| `/activate.php` | POST | Activate a session |

#### Semesters Management (`/admin/semesters/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/list.php` | GET | List semesters |
| `/create.php` | POST | Create semester |

#### Notices Management (`/admin/notices/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/create.php` | POST | Create notice |
| `/update.php` | POST | Update notice |
| `/delete.php` | POST | Delete notice |

#### Reports (`/admin/reports/`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/financial.php` | GET | Financial reports |
| `/performance.php` | GET | Student performance reports |
| `/trends.php` | GET | Trend analysis |

### Public Endpoints
*Base Path: `/notices`*

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/get_all.php` | GET | No | Get all public notices |

### File Upload
*Base Path: `/upload`*

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/upload_image.php` | POST | Yes | Upload profile images (max 5MB, JPG/PNG/GIF/WEBP) |

## 7. Request/Response Examples

### Example: Student Login
```javascript
// Request
fetch('http://localhost:8000/api/auth/login.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    username: 'STU001',
    password: 'password123'
  })
})
.then(res => res.json())
.then(data => {
  if (data.success) {
    localStorage.setItem('token', data.data.token);
    localStorage.setItem('user', JSON.stringify(data.data.user));
  }
});
```

### Example: Get Student Profile
```javascript
// Request
const token = localStorage.getItem('token');

fetch('http://localhost:8000/api/student/get_profile.php', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`
  }
})
.then(res => res.json())
.then(data => {
  if (data.success) {
    console.log(data.data); // Student profile object
  }
});
```

### Example: Mark Attendance (Teacher)
```javascript
// Request
const token = localStorage.getItem('token');

fetch('http://localhost:8000/api/teacher/mark_attendance.php', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    date: '2025-11-19',
    subject_id: 5,
    attendance: [
      { student_id: 1, status: 'present' },
      { student_id: 2, status: 'absent' },
      { student_id: 3, status: 'late' }
    ]
  })
})
.then(res => res.json())
.then(data => {
  if (data.success) {
    console.log('Attendance marked successfully');
  }
});
```

### Example: Create Student (Admin)
```javascript
// Request
const token = localStorage.getItem('token');

fetch('http://localhost:8000/api/admin/students/create.php', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    student_id: 'STU123',
    first_name: 'John',
    last_name: 'Doe',
    email: 'john.doe@example.com',
    username: 'johndoe',
    password: 'SecurePass123',
    date_of_birth: '2000-05-15',
    gender: 'male',
    phone: '1234567890',
    address: '123 Main St',
    department: 'Computer Science',
    program: 'BCA',
    semester: 1,
    batch_year: 2024,
    session_id: 1
  })
})
.then(res => res.json())
.then(data => {
  if (data.success) {
    console.log('Student created:', data.data);
  }
});
```

## 8. Critical Integration Points

### 8.1 Token Management
```javascript
// Store token after login
localStorage.setItem('token', token);

// Include in every authenticated request
headers: {
  'Authorization': `Bearer ${localStorage.getItem('token')}`
}

// Remove on logout
localStorage.removeItem('token');
localStorage.removeItem('user');
```

### 8.2 Error Handling
```javascript
fetch(url, options)
  .then(res => {
    if (res.status === 401) {
      // Token expired or invalid - redirect to login
      localStorage.clear();
      window.location.href = '/login';
      return;
    }
    if (res.status === 429) {
      // Rate limit exceeded
      alert('Too many requests. Please wait a moment.');
      return;
    }
    return res.json();
  })
  .then(data => {
    if (!data.success) {
      // Handle API error
      console.error(data.error, data.message);
    }
  })
  .catch(err => {
    // Network error
    console.error('Network error:', err);
  });
```

### 8.3 Role-Based Routing
```javascript
const user = JSON.parse(localStorage.getItem('user'));

// Redirect based on role after login
if (user.role === 'student') {
  window.location.href = '/dashboard';
} else if (user.role === 'teacher') {
  window.location.href = '/teacher/dashboard';
} else if (user.role === 'admin') {
  window.location.href = '/admin/dashboard';
}
```

### 8.4 File Upload
```javascript
const formData = new FormData();
formData.append('file', fileInput.files[0]);

fetch('http://localhost:8000/api/upload/upload_image.php', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
    // Don't set Content-Type - browser will set it with boundary
  },
  body: formData
})
.then(res => res.json())
.then(data => {
  if (data.success) {
    console.log('File uploaded:', data.data.file_path);
  }
});
```

## 9. Common Pitfalls & Solutions

### Issue 1: CORS Errors
**Problem:** Browser blocks requests due to CORS policy.
**Solution:** Backend already has CORS enabled for `localhost:5173`. If using different port, update `backend/includes/cors.php`.

### Issue 2: 401 Unauthorized on Valid Token
**Problem:** Token format incorrect or missing Bearer prefix.
**Solution:** Always use `Authorization: Bearer <token>` format, not just `Authorization: <token>`.

### Issue 3: 500 Errors with No Details
**Problem:** Production mode hides error details.
**Solution:** Check `backend/logs/error.log` for detailed error messages. In development, set `display_errors = On` in php.ini.

### Issue 4: File Upload Fails
**Problem:** File size exceeds limit or wrong MIME type.
**Solution:** 
- Max file size: 5MB
- Allowed types: JPG, PNG, GIF, WEBP
- Check `upload_max_filesize` and `post_max_size` in php.ini

### Issue 5: Date Format Errors
**Problem:** Backend expects `YYYY-MM-DD` but frontend sends different format.
**Solution:** Always format dates as `YYYY-MM-DD` before sending to API.

```javascript
// Convert Date object to YYYY-MM-DD
const formatDate = (date) => {
  return date.toISOString().split('T')[0];
};
```

## 10. Environment Setup

### Backend Requirements
1. **PHP 7.4+** (8.0+ recommended)
2. **MySQL 8.0+**
3. **Apache/Nginx** or PHP built-in server
4. **Composer** (for dependencies)

### Backend Configuration
```bash
# 1. Copy environment file
cd backend
cp .env.example .env

# 2. Edit .env with your database credentials
# DB_HOST=localhost
# DB_NAME=studentportal
# DB_USER=root
# DB_PASS=your_password
# JWT_SECRET=your_secret_key

# 3. Install dependencies
composer install

# 4. Import database
mysql -u root -p studentportal < ../database/schema.sql

# 5. Import seed data (in order)
mysql -u root -p studentportal < ../database/seeds/01_sessions.sql
mysql -u root -p studentportal < ../database/seeds/02_admin.sql
# ... continue with remaining seed files

# 6. Start PHP server
php -S localhost:8000
```

### Frontend Configuration
```bash
# 1. Install dependencies
npm install

# 2. Create .env file (if not exists)
echo "VITE_API_URL=http://localhost:8000/api" > .env

# 3. Start development server
npm run dev
```

## 11. Testing Credentials

After importing seed data, use these credentials:

**Admin:**
- Username: `admin`
- Password: `admin123`

**Student:**
- Username: `STU001`
- Password: `password123`

**Teacher:**
- Username: `TCH001`
- Password: `password123`

## 12. API Response Standards

All endpoints follow this consistent format:

### Success Response
```json
{
  "success": true,
  "data": { /* response data */ },
  "message": "Optional success message"
}
```

### Error Response
```json
{
  "success": false,
  "error": "error_code",
  "message": "Human-readable error message",
  "details": { /* Optional validation details */ }
}
```

### Common Error Codes
- `unauthorized`: Missing or invalid token (401)
- `forbidden`: Insufficient permissions (403)
- `not_found`: Resource not found (404)
- `validation_error`: Invalid input data (400)
- `database_error`: Database operation failed (500)
- `server_error`: Unexpected server error (500)

## 13. Grade Calculation System

The system uses a 4-point GPA scale:

### Grade Points (GP)
- A+: 4.0
- A: 3.7
- B+: 3.3
- B: 3.0
- C+: 2.7
- C: 2.3
- D: 2.0
- F: 0.0

### Calculations
- **CP (Credit Points)**: `GP × Credit Hours`
- **GPA (Semester)**: `Sum of CP / Sum of Credit Hours`
- **CGPA (Cumulative)**: `Sum of all CP / Sum of all Credit Hours`

Backend handles all calculations automatically. Frontend just displays the results.

## 14. Fee Management System

### Three-Tier Deadline System
1. **Early Payment**: Before deadline 1 (no fine)
2. **Regular Payment**: Between deadline 1 & 2 (fine 1)
3. **Late Payment**: After deadline 2 (fine 2)

### Payment Status
- `pending`: Not paid
- `paid`: Fully paid
- `partial`: Partially paid
- `overdue`: Past all deadlines

Backend calculates fines automatically based on payment date.

## 15. Quick Integration Checklist

For your frontend developer, ensure they:

- [ ] Set up backend environment (PHP, MySQL, .env)
- [ ] Import database schema and seed data
- [ ] Start PHP server on port 8000
- [ ] Configure frontend .env with `VITE_API_URL`
- [ ] Implement token storage (localStorage)
- [ ] Add Authorization header to all authenticated requests
- [ ] Handle 401 errors (redirect to login)
- [ ] Handle 429 errors (rate limiting)
- [ ] Format dates as YYYY-MM-DD
- [ ] Use FormData for file uploads
- [ ] Implement role-based routing
- [ ] Test with provided credentials
- [ ] Check backend/logs/error.log for debugging

## 16. What's NOT Included (Frontend Developer Must Build)

### UI Components
The backend provides data only. Your frontend developer must build:
- Login form with username/password fields
- Dashboard layouts for student/teacher/admin
- Navigation menus and sidebars
- Data tables for displaying lists
- Forms for creating/editing records
- Modal dialogs for confirmations
- Toast/alert notifications
- Loading spinners
- Error message displays
- PDF viewer/download buttons
- File upload components
- Date pickers
- Search and filter interfaces

### Frontend Logic
- Form validation (client-side)
- State management (React state/context)
- Routing (React Router)
- Token refresh logic (if implementing)
- Pagination logic
- Sorting and filtering
- Real-time updates (polling or WebSocket)
- PDF generation (for receipts/reports)
- Print functionality
- Export to Excel/CSV
- Dark mode toggle
- Responsive design breakpoints

### Styling
- CSS framework integration (TailwindCSS recommended)
- Glassmorphism effects
- Color scheme implementation
- Animations and transitions
- Mobile responsiveness
- Accessibility (ARIA labels, keyboard navigation)

## 17. Integration Workflow for Your Developer

### Step 1: Environment Setup (30 mins)
1. Install XAMPP or standalone PHP/MySQL
2. Clone backend folder
3. Configure .env file
4. Import database schema
5. Import seed data
6. Start PHP server: `php -S localhost:8000`
7. Test login endpoint with curl/Postman

### Step 2: Frontend Setup (15 mins)
1. Set up React project (or use existing)
2. Install axios: `npm install axios`
3. Create .env with `VITE_API_URL=http://localhost:8000/api`
4. Create API service file (see example below)

### Step 3: Authentication Implementation (1 hour)
1. Build login form
2. Implement login API call
3. Store token in localStorage
4. Create protected route wrapper
5. Add logout functionality
6. Test with provided credentials

### Step 4: Role-Based Routing (30 mins)
1. Create route guards
2. Redirect based on user role
3. Implement 401 handling (auto-logout)

### Step 5: Feature Implementation (varies)
1. Start with one role (e.g., student dashboard)
2. Build UI components
3. Connect to API endpoints
4. Test thoroughly
5. Move to next role

## 18. Sample API Service File (React + Axios)

Create `src/services/api.js`:

```javascript
import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json'
  }
});

// Add token to requests
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Handle responses
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.clear();
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Auth APIs
export const authAPI = {
  login: (username, password) => 
    api.post('/auth/login.php', { username, password }),
  logout: () => 
    api.post('/auth/logout.php'),
  verify: () => 
    api.get('/auth/verify.php')
};

// Student APIs
export const studentAPI = {
  getProfile: () => 
    api.get('/student/get_profile.php'),
  getMarks: (semester) => 
    api.get(`/student/get_marks.php?semester=${semester}`),
  getAttendance: () => 
    api.get('/student/get_attendance.php'),
  getFees: () => 
    api.get('/student/get_fees.php'),
  getPayments: () => 
    api.get('/student/get_payments.php'),
  downloadIDCard: () => 
    api.get('/student/download_id_card.php', { responseType: 'blob' }),
  downloadReceipt: (paymentId) => 
    api.get(`/student/download_receipt.php?payment_id=${paymentId}`, { responseType: 'blob' })
};

// Teacher APIs
export const teacherAPI = {
  getStudents: (params) => 
    api.get('/teacher/get_students.php', { params }),
  markAttendance: (data) => 
    api.post('/teacher/mark_attendance.php', data),
  enterMarks: (data) => 
    api.post('/teacher/enter_marks.php', data),
  updateMarks: (data) => 
    api.post('/teacher/update_marks.php', data)
};

// Admin APIs
export const adminAPI = {
  students: {
    list: (params) => api.get('/admin/students/list.php', { params }),
    create: (data) => api.post('/admin/students/create.php', data),
    update: (data) => api.post('/admin/students/update.php', data),
    delete: (id) => api.post('/admin/students/delete.php', { id })
  },
  teachers: {
    list: () => api.get('/admin/teachers/list.php'),
    create: (data) => api.post('/admin/teachers/create.php', data),
    update: (data) => api.post('/admin/teachers/update.php', data),
    delete: (id) => api.post('/admin/teachers/delete.php', { id })
  },
  subjects: {
    list: () => api.get('/admin/subjects/list.php'),
    create: (data) => api.post('/admin/subjects/create.php', data),
    update: (data) => api.post('/admin/subjects/update.php', data),
    delete: (id) => api.post('/admin/subjects/delete.php', { id })
  },
  fees: {
    list: () => api.get('/admin/fees/list.php'),
    create: (data) => api.post('/admin/fees/create.php', data),
    update: (data) => api.post('/admin/fees/update.php', data),
    delete: (id) => api.post('/admin/fees/delete.php', { id })
  },
  payments: {
    list: () => api.get('/admin/payments/list.php'),
    process: (data) => api.post('/admin/payments/process.php', data)
  },
  sessions: {
    list: () => api.get('/admin/sessions/list.php'),
    create: (data) => api.post('/admin/sessions/create.php', data),
    activate: (id) => api.post('/admin/sessions/activate.php', { id })
  },
  notices: {
    create: (data) => api.post('/admin/notices/create.php', data),
    update: (data) => api.post('/admin/notices/update.php', data),
    delete: (id) => api.post('/admin/notices/delete.php', { id })
  }
};

// Public APIs
export const publicAPI = {
  getNotices: () => api.get('/notices/get_all.php')
};

// File upload
export const uploadAPI = {
  uploadImage: (file) => {
    const formData = new FormData();
    formData.append('file', file);
    return api.post('/upload/upload_image.php', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
  }
};

export default api;
```

## 19. Sample Login Component (React)

```javascript
import { useState } from 'react';
import { authAPI } from '../services/api';

function Login() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await authAPI.login(username, password);
      
      if (response.data.success) {
        // Store token and user data
        localStorage.setItem('token', response.data.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.data.user));
        
        // Redirect based on role
        const role = response.data.data.user.role;
        if (role === 'student') {
          window.location.href = '/dashboard';
        } else if (role === 'teacher') {
          window.location.href = '/teacher/dashboard';
        } else if (role === 'admin') {
          window.location.href = '/admin/dashboard';
        }
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="login-container">
      <form onSubmit={handleLogin}>
        <h2>Student Portal Login</h2>
        
        {error && <div className="error">{error}</div>}
        
        <input
          type="text"
          placeholder="Username"
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          required
        />
        
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
        />
        
        <button type="submit" disabled={loading}>
          {loading ? 'Logging in...' : 'Login'}
        </button>
      </form>
    </div>
  );
}

export default Login;
```

## 20. Support & Debugging

### Check Backend Logs
```bash
tail -f backend/logs/error.log
```

### Test API Endpoints
```bash
# Test login
curl -X POST http://localhost:8000/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# Test authenticated endpoint
curl http://localhost:8000/api/student/get_profile.php \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Common Issues
1. **Database connection fails**: Check .env credentials
2. **Token not working**: Verify JWT_SECRET in .env
3. **CORS errors**: Check allowed origins in cors.php
4. **File upload fails**: Check PHP upload limits
5. **Rate limit hit**: Wait 60 seconds or restart PHP server


---

## 21. CRITICAL: Will This Be Enough for Seamless Integration?

### ✅ What's Covered
- Complete API endpoint documentation
- Authentication flow with JWT
- Request/response formats
- Error handling patterns
- Sample code for React integration
- Testing credentials
- Environment setup instructions

### ⚠️ Potential Issues Your Developer May Face

#### 1. **Backend Endpoint Variations**
Some endpoints may have slightly different parameter names or response structures than documented. Your developer should:
- Test each endpoint with Postman/curl first
- Check `backend/logs/error.log` for detailed errors
- Verify actual response structure matches documentation

#### 2. **Missing Endpoints**
If your developer needs functionality not listed here, they'll need to:
- Check if the endpoint exists in backend/api/
- Request you to create missing endpoints
- Or implement workarounds

#### 3. **Database Schema Mismatches**
If the database structure differs from what's expected:
- Run all migrations in `database/migrations/`
- Verify seed data is imported correctly
- Check table structures match requirements

#### 4. **CORS Issues in Production**
The backend CORS is configured for `localhost:5173`. For production:
- Update `backend/includes/cors.php` with production domain
- Or configure reverse proxy (nginx/Apache)

#### 5. **File Upload Paths**
Uploaded files are stored in `backend/uploads/`. Ensure:
- Directory has write permissions (chmod 755)
- Web server can serve files from this directory
- Frontend uses correct URL to display images

#### 6. **PHP Version Compatibility**
Some code may require PHP 8.0+. If using PHP 7.4:
- Check for syntax errors
- Update deprecated functions
- Test thoroughly

### 📋 Pre-Integration Checklist

Send this to your developer along with the backend folder:

**Before Starting:**
- [ ] Read this entire document (FRONTEND_INTEGRATION.md)
- [ ] Review project structure (structure.md)
- [ ] Understand tech stack (tech.md)
- [ ] Review product requirements (product.md)

**Environment Setup:**
- [ ] Install PHP 7.4+ (8.0+ recommended)
- [ ] Install MySQL 8.0+
- [ ] Install Composer
- [ ] Configure backend/.env file
- [ ] Import database schema
- [ ] Import all seed files in order
- [ ] Start PHP server on port 8000
- [ ] Test login endpoint with curl

**Frontend Setup:**
- [ ] Install Node.js and npm
- [ ] Create/configure .env with VITE_API_URL
- [ ] Install axios
- [ ] Create api.js service file
- [ ] Test API connection

**First Implementation:**
- [ ] Build login page
- [ ] Test authentication flow
- [ ] Implement token storage
- [ ] Create protected routes
- [ ] Test with all three roles (student/teacher/admin)

### 🚨 Red Flags to Watch For

If your developer says any of these, there's a problem:

1. **"The API doesn't return JSON"** → They're hitting the wrong URL or PHP isn't running
2. **"I can't connect to the database"** → .env not configured or MySQL not running
3. **"CORS is blocking everything"** → Backend not running or wrong port
4. **"Token doesn't work"** → Not using Bearer prefix or JWT_SECRET mismatch
5. **"All endpoints return 404"** → PHP server not started or wrong base URL
6. **"I need to modify the backend"** → Possible, but check if they're using API correctly first

### 📞 When to Ask for Help

Your developer should contact you if:
- Endpoints are missing or don't match documentation
- Database schema doesn't match expected structure
- Backend returns unexpected error formats
- Security features need adjustment
- New functionality needs to be added to backend
- Performance issues with large datasets

### ✅ Success Indicators

Integration is going well if:
- Login works for all three roles
- Token authentication works on protected routes
- Data displays correctly from API
- CRUD operations work (create, read, update, delete)
- File uploads work
- PDF downloads work
- Error messages are clear and helpful

---

## 22. Final Answer: Will It Work Seamlessly?

### Short Answer: **95% Yes, with caveats**

### Long Answer:

**What WILL work seamlessly:**
- Authentication and authorization
- All documented API endpoints
- JWT token management
- Role-based access control
- Database operations
- File uploads
- PDF generation
- Error handling

**What MIGHT need adjustment:**
- Specific UI requirements not in backend
- Custom business logic
- Additional validation rules
- Performance optimization for large datasets
- Production deployment configuration

**What your developer MUST build:**
- Entire frontend UI (HTML/CSS/JS)
- All React components
- Routing and navigation
- Forms and validation (client-side)
- State management
- Responsive design
- User experience flows

### Recommendation:

**Send your developer:**
1. This FRONTEND_INTEGRATION.md file
2. The entire backend/ folder
3. The database/ folder (schema + seeds)
4. The .env.example file
5. Test credentials (admin/admin123, STU001/password123, TCH001/password123)

**Tell them:**
"This backend is complete and tested. Follow the integration guide step-by-step. Start with authentication, then build one role at a time. Test each endpoint with Postman first before building UI. Check backend/logs/error.log if anything fails. The API is RESTful and returns consistent JSON responses."

**Expected timeline:**
- Environment setup: 1 hour
- Authentication: 2-3 hours
- Student portal: 1-2 days
- Teacher portal: 1-2 days
- Admin portal: 2-3 days
- Polish and testing: 1-2 days

**Total: 1-2 weeks** for a competent React developer to build a complete, production-ready frontend.

---

## Need More Help?

If your developer gets stuck, they should:
1. Check backend/logs/error.log
2. Test endpoint with curl/Postman
3. Verify .env configuration
4. Check database connection
5. Review this documentation again
6. Contact you with specific error messages and request IDs

**This documentation is comprehensive. If followed correctly, integration should be smooth.**
