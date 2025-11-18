# API Specifications - University Student Portal

## API Overview

Base URL: `http://your-domain.com/api/v1`

All API endpoints return JSON responses and require authentication (except login/register).

## Authentication

All authenticated requests must include JWT token in header:
```
Authorization: Bearer <jwt_token>
```

## Response Format

### Success Response
```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

### Error Response
```json
{
    "success": false,
    "error": {
        "code": "ERROR_CODE",
        "message": "Error description"
    }
}
```

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

---

## 1. Authentication APIs

### 1.1 Login

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
    "username": "sarah.lee",
    "password": "password123",
    "role": "student"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "user": {
            "id": 1,
            "username": "sarah.lee",
            "email": "sarah.lee@university.edu",
            "role": "student",
            "profile": {
                "student_id": "S2025108",
                "first_name": "Sarah",
                "last_name": "Lee",
                "profile_photo": "/uploads/profiles/sarah.jpg"
            }
        }
    },
    "message": "Login successful"
}
```

---

### 1.2 Logout

**Endpoint:** `POST /auth/logout`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

### 1.3 Refresh Token

**Endpoint:** `POST /auth/refresh`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "new_jwt_token_here"
    }
}
```

---

### 1.4 Forgot Password

**Endpoint:** `POST /auth/forgot-password`

**Request Body:**
```json
{
    "email": "sarah.lee@university.edu"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password reset link sent to your email"
}
```

---

### 1.5 Reset Password

**Endpoint:** `POST /auth/reset-password`

**Request Body:**
```json
{
    "token": "reset_token_from_email",
    "password": "new_password123",
    "password_confirmation": "new_password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password reset successful"
}
```

---

## 2. Dashboard APIs

### 2.1 Get Dashboard Data

**Endpoint:** `GET /dashboard`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "student": {
            "student_id": "S2025108",
            "first_name": "Sarah",
            "last_name": "Lee",
            "profile_photo": "/uploads/profiles/sarah.jpg",
            "department": "Computer Science",
            "current_semester": 6,
            "enrollment_year": 2022
        },
        "academic": {
            "sgpa": 9.10,
            "cgpa": 8.70,
            "attendance_percentage": 92,
            "class_rank": 7,
            "total_students": 120
        },
        "upcoming_assignments": [
            {
                "id": 1,
                "subject": "CS101: Final Project",
                "due_date": "2025-05-15",
                "status": "pending"
            }
        ],
        "notifications": [
            {
                "id": 1,
                "title": "Annual Tech Fest",
                "message": "Join us for innovation...",
                "type": "event",
                "date": "2025-10-01",
                "is_read": false
            }
        ],
        "pending_fees": {
            "total_pending": 18800,
            "count": 3
        }
    }
}
```

---

## 3. Subject APIs

### 3.1 Get Enrolled Subjects

**Endpoint:** `GET /subjects/enrolled`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `semester` (optional): Filter by semester

**Response:**
```json
{
    "success": true,
    "data": {
        "subjects": [
            {
                "id": 1,
                "subject_code": "MA101",
                "subject_name": "Mathematics",
                "credits": 4,
                "subject_type": "theory",
                "instructor": {
                    "id": 1,
                    "name": "Dr. Rajesh Kumar",
                    "designation": "Professor",
                    "department": "Mathematics",
                    "profile_photo": "/uploads/faculty/rajesh.jpg"
                },
                "attendance_percentage": 95,
                "topics": [
                    "Calculus",
                    "Linear Algebra"
                ]
            }
        ]
    }
}
```

---

### 3.2 Get Subject Details

**Endpoint:** `GET /subjects/{subject_id}`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "subject": {
            "id": 1,
            "subject_code": "MA101",
            "subject_name": "Mathematics",
            "credits": 4,
            "semester": 1,
            "description": "Introduction to advanced mathematics",
            "syllabus_url": "/uploads/syllabus/ma101.pdf",
            "instructor": {
                "name": "Dr. Rajesh Kumar",
                "email": "rajesh.kumar@university.edu",
                "phone": "+91-9876543210"
            },
            "schedule": {
                "monday": "10:00 AM - 11:00 AM",
                "wednesday": "10:00 AM - 11:00 AM",
                "friday": "10:00 AM - 11:00 AM"
            },
            "classroom": "Room 301, Block A"
        }
    }
}
```

---

## 4. Results APIs

### 4.1 Get All Results

**Endpoint:** `GET /results`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `semester` (optional): Filter by semester
- `academic_year` (optional): Filter by academic year

**Response:**
```json
{
    "success": true,
    "data": {
        "semesters": [
            {
                "semester": 6,
                "academic_year": "2024-25",
                "sgpa": 9.10,
                "total_marks": 455,
                "max_marks": 500,
                "percentage": 91.0,
                "subjects": [
                    {
                        "subject_code": "CS601",
                        "subject_name": "Advanced Algorithms",
                        "internal_marks": 19,
                        "theory_marks": 76,
                        "total_marks": 95,
                        "max_marks": 100,
                        "grade": "A+",
                        "grade_points": 10
                    }
                ]
            }
        ]
    }
}
```

---

### 4.2 Get Semester Result

**Endpoint:** `GET /results/semester/{semester}`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "semester": 6,
        "academic_year": "2024-25",
        "sgpa": 9.10,
        "cgpa": 8.70,
        "total_marks_obtained": 455,
        "total_marks_maximum": 500,
        "percentage": 91.0,
        "class_rank": 7,
        "subjects": [
            {
                "subject_code": "CS601",
                "subject_name": "Advanced Algorithms",
                "subject_type": "theory",
                "internal_marks": 19,
                "max_internal": 20,
                "theory_marks": 76,
                "max_theory": 80,
                "total_marks": 95,
                "max_total": 100,
                "grade": "A+",
                "grade_points": 10
            }
        ]
    }
}
```

---

### 4.3 Download Result PDF

**Endpoint:** `GET /results/semester/{semester}/download`

**Headers:** `Authorization: Bearer <token>`

**Response:** PDF file download

---

## 5. Payment APIs

### 5.1 Get Payment Summary

**Endpoint:** `GET /payments/summary`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "academic_year": "2024-25",
        "total_paid": 32400,
        "total_pending": 18800,
        "total_fees": 51200,
        "pending_payments": [
            {
                "id": 1,
                "fee_type": "semester",
                "semester": 5,
                "amount": 17000,
                "due_date": "2025-10-15",
                "late_fee": 0,
                "status": "pending"
            }
        ]
    }
}
```

---

### 5.2 Get Payment History

**Endpoint:** `GET /payments/history`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 10)

**Response:**
```json
{
    "success": true,
    "data": {
        "payments": [
            {
                "id": 1,
                "payment_id": "PAY2024091234567",
                "fee_type": "semester",
                "semester": 4,
                "amount": 16500,
                "late_fee": 0,
                "total_amount": 16500,
                "payment_method": "upi",
                "payment_status": "completed",
                "transaction_id": "TXN2024091234567",
                "payment_date": "2025-02-15T10:30:00Z",
                "receipt_number": "REC2024091234567",
                "receipt_url": "/receipts/REC2024091234567.pdf"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total_items": 25,
            "items_per_page": 10
        }
    }
}
```

---

### 5.3 Initiate Payment

**Endpoint:** `POST /payments/initiate`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "fee_type": "semester",
    "semester": 5,
    "amount": 17000,
    "payment_method": "upi"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "payment_id": "PAY2025101234568",
        "amount": 17000,
        "gateway_order_id": "order_MxYzABCD1234",
        "payment_url": "https://gateway.com/pay/order_MxYzABCD1234",
        "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
        "upi_intent": "upi://pay?pa=university@upi&pn=University&am=17000&tr=PAY2025101234568"
    }
}
```

---

### 5.4 Payment Callback (Webhook)

**Endpoint:** `POST /payments/callback`

**Request Body:** (From payment gateway)
```json
{
    "payment_id": "PAY2025101234568",
    "gateway_payment_id": "pay_MxYzABCD1234",
    "status": "success",
    "amount": 17000,
    "signature": "gateway_signature_hash"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "payment_status": "completed",
        "receipt_number": "REC2025101234568",
        "receipt_url": "/receipts/REC2025101234568.pdf"
    }
}
```

---

### 5.5 Download Receipt

**Endpoint:** `GET /payments/receipt/{receipt_number}`

**Headers:** `Authorization: Bearer <token>`

**Response:** PDF file download

---

## 6. Notice APIs

### 6.1 Get All Notices

**Endpoint:** `GET /notices`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `category` (optional): Filter by category
- `page` (optional): Page number
- `limit` (optional): Items per page

**Response:**
```json
{
    "success": true,
    "data": {
        "notices": [
            {
                "id": 1,
                "title": "Final Reminder: Tuition Fee Payment",
                "content": "This is a final reminder...",
                "category": "urgent",
                "priority": "high",
                "published_date": "2025-10-01T09:00:00Z",
                "expiry_date": "2025-10-15",
                "attachment_url": null,
                "is_read": false
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 5,
            "total_items": 48
        }
    }
}
```

---

### 6.2 Get Notice Details

**Endpoint:** `GET /notices/{notice_id}`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "notice": {
            "id": 1,
            "title": "Final Reminder: Tuition Fee Payment",
            "content": "This is a final reminder that the tuition fee for the 5th semester is due on October 10, 2025...",
            "category": "urgent",
            "priority": "high",
            "published_by": "Admin Office",
            "published_date": "2025-10-01T09:00:00Z",
            "expiry_date": "2025-10-15",
            "attachment_url": null,
            "view_count": 245,
            "is_read": true
        }
    }
}
```

---

### 6.3 Mark Notice as Read

**Endpoint:** `POST /notices/{notice_id}/read`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "message": "Notice marked as read"
}
```

---

## 7. Analytics APIs

### 7.1 Get Performance Analytics

**Endpoint:** `GET /analytics/performance`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "overall": {
            "cgpa": 8.70,
            "attendance_percentage": 92,
            "class_rank": 7,
            "total_students": 120
        },
        "semester_wise": [
            {
                "semester": 1,
                "sgpa": 7.50,
                "percentage": 75,
                "total_marks": 375,
                "max_marks": 500
            },
            {
                "semester": 2,
                "sgpa": 8.20,
                "percentage": 82,
                "total_marks": 410,
                "max_marks": 500
            }
        ],
        "subject_wise": [
            {
                "subject_name": "Programming",
                "average_marks": 91,
                "grade": "A+",
                "attendance": 95
            }
        ]
    }
}
```

---

### 7.2 Get Attendance Analytics

**Endpoint:** `GET /analytics/attendance`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `semester` (optional): Filter by semester

**Response:**
```json
{
    "success": true,
    "data": {
        "overall_percentage": 92,
        "total_classes": 250,
        "attended_classes": 230,
        "subject_wise": [
            {
                "subject_code": "MA101",
                "subject_name": "Mathematics",
                "total_classes": 45,
                "attended": 43,
                "percentage": 95.6
            }
        ],
        "monthly_trend": [
            {
                "month": "January",
                "percentage": 94
            },
            {
                "month": "February",
                "percentage": 91
            }
        ]
    }
}
```

---

## 8. Profile APIs

### 8.1 Get Profile

**Endpoint:** `GET /profile`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "success": true,
    "data": {
        "student_id": "S2025108",
        "first_name": "Sarah",
        "last_name": "Lee",
        "email": "sarah.lee@university.edu",
        "phone": "+91-9876543210",
        "date_of_birth": "2004-03-15",
        "gender": "female",
        "blood_group": "O+",
        "address": "123 Main Street",
        "city": "Mumbai",
        "state": "Maharashtra",
        "pincode": "400001",
        "profile_photo": "/uploads/profiles/sarah.jpg",
        "department": "Computer Science",
        "current_semester": 6,
        "enrollment_year": 2022,
        "parent_name": "John Lee",
        "parent_phone": "+91-9876543211",
        "emergency_contact": "+91-9876543211"
    }
}
```

---

### 8.2 Update Profile

**Endpoint:** `PUT /profile`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "phone": "+91-9876543210",
    "address": "123 Main Street",
    "city": "Mumbai",
    "emergency_contact": "+91-9876543211"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Profile updated successfully"
}
```

---

### 8.3 Upload Profile Photo

**Endpoint:** `POST /profile/photo`

**Headers:** 
- `Authorization: Bearer <token>`
- `Content-Type: multipart/form-data`

**Request Body:**
- `photo`: Image file (JPG, PNG, max 2MB)

**Response:**
```json
{
    "success": true,
    "data": {
        "photo_url": "/uploads/profiles/sarah_1234567890.jpg"
    },
    "message": "Profile photo updated successfully"
}
```

---

## 9. Admin APIs (Faculty/Admin Only)

### 9.1 Get All Students

**Endpoint:** `GET /admin/students`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `department` (optional)
- `semester` (optional)
- `page`, `limit`

**Response:**
```json
{
    "success": true,
    "data": {
        "students": [
            {
                "id": 1,
                "student_id": "S2025108",
                "name": "Sarah Lee",
                "department": "Computer Science",
                "semester": 6,
                "cgpa": 8.70,
                "email": "sarah.lee@university.edu"
            }
        ],
        "pagination": { ... }
    }
}
```

---

### 9.2 Upload Results

**Endpoint:** `POST /admin/results/upload`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "semester": 6,
    "academic_year": "2024-25",
    "subject_id": 1,
    "results": [
        {
            "student_id": 1,
            "internal_marks": 19,
            "theory_marks": 76,
            "grade": "A+"
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Results uploaded successfully",
    "data": {
        "uploaded": 120,
        "failed": 0
    }
}
```

---

### 9.3 Create Notice

**Endpoint:** `POST /admin/notices`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "title": "Important Announcement",
    "content": "This is an important announcement...",
    "category": "urgent",
    "priority": "high",
    "target_audience": "all",
    "expiry_date": "2025-12-31"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "notice_id": 123
    },
    "message": "Notice created successfully"
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| `AUTH_FAILED` | Authentication failed |
| `INVALID_TOKEN` | Invalid or expired token |
| `VALIDATION_ERROR` | Input validation failed |
| `NOT_FOUND` | Resource not found |
| `UNAUTHORIZED` | Insufficient permissions |
| `PAYMENT_FAILED` | Payment processing failed |
| `SERVER_ERROR` | Internal server error |

---

## Rate Limiting

- **General APIs**: 100 requests per minute per user
- **Login API**: 5 requests per minute per IP
- **Payment APIs**: 10 requests per minute per user

---

## Pagination

All list endpoints support pagination:
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 10, max: 100)

Response includes:
```json
{
    "pagination": {
        "current_page": 1,
        "total_pages": 10,
        "total_items": 95,
        "items_per_page": 10
    }
}
```

---

**Document Version**: 1.0.0  
**Last Updated**: November 19, 2025
