# Database Schema Diagram

## Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           UNIVERSITY PORTAL DATABASE                     │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│     USERS        │
├──────────────────┤
│ PK id            │
│ UK username      │
│ UK email         │
│    password_hash │
│    role          │◄─────────────────────┐
│    full_name     │                      │
│    phone         │                      │
│    profile_image │                      │
│    is_active     │                      │
│    last_login    │                      │
│    created_at    │                      │
│    updated_at    │                      │
└────────┬─────────┘                      │
         │                                │
         │ 1:1                            │
    ┌────┴────┐                           │
    │         │                           │
    ▼         ▼                           │
┌─────────┐ ┌─────────┐                  │
│STUDENTS │ │TEACHERS │                  │
├─────────┤ ├─────────┤                  │
│ PK id   │ │ PK id   │                  │
│ FK user │ │ FK user │                  │
│    stud │ │    tchr │                  │
│    dept │ │    dept │                  │
│    sem  │ │    qual │                  │
│    year │ │    spec │                  │
│    dob  │ │    join │                  │
│    addr │ └────┬────┘                  │
│    guar │      │                       │
└────┬────┘      │                       │
     │           │                       │
     │           │ 1:N                   │
     │           ▼                       │
     │      ┌──────────┐                 │
     │      │ SUBJECTS │                 │
     │      ├──────────┤                 │
     │      │ PK id    │                 │
     │      │ UK code  │                 │
     │      │    name  │                 │
     │      │    dept  │                 │
     │      │    sem   │                 │
     │      │    cred  │                 │
     │      │    lab?  │                 │
     │      │ FK tchr  │                 │
     │      │    desc  │                 │
     │      └────┬─────┘                 │
     │           │                       │
     │ N:M       │ 1:N                   │
     │           │                       │
     ▼           ▼                       │
┌──────────────────┐                     │
│ STUDENT_SUBJECTS │                     │
├──────────────────┤                     │
│ PK id            │                     │
│ FK student_id    │                     │
│ FK subject_id    │                     │
│    academic_year │                     │
│    enroll_date   │                     │
└──────────────────┘                     │
                                         │
     ┌───────────────┬─────────────┐     │
     │               │             │     │
     ▼               ▼             ▼     │
┌─────────┐    ┌──────────┐  ┌──────────┐
│ RESULTS │    │ATTENDANCE│  │ PAYMENTS │
├─────────┤    ├──────────┤  ├──────────┤
│ PK id   │    │ PK id    │  │ PK id    │
│ FK stud │    │ FK stud  │  │ FK stud  │
│ FK subj │    │ FK subj  │  │    type  │
│    sem  │    │    date  │  │    desc  │
│    year │    │    stat  │  │    amt   │
│    int  │    │ FK mark  │  │    fine  │
│    theo │    │    rem   │  │    total │
│    lab  │    │    time  │  │    due   │
│    tot  │    └──────────┘  │    paid  │
│    grd  │                  │    meth  │
│    gp   │                  │    txn   │
│    cp   │                  │    stat  │
│    stat │                  │    rcpt  │
│    pub  │                  │    sem   │
└─────────┘                  │    year  │
                             │    time  │
                             └──────────┘

┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   SESSIONS   │    │ACTIVITY_LOGS │    │   NOTICES    │
├──────────────┤    ├──────────────┤    ├──────────────┤
│ PK id        │    │ PK id        │    │ PK id        │
│ FK user_id   │    │ FK user_id   │    │    title     │
│    ip_addr   │    │    action    │    │    content   │
│    user_agt  │    │    ent_type  │    │    category  │
│    last_act  │    │    ent_id    │    │    audience  │
│    data      │    │    desc      │    │    active?   │
└──────────────┘    │    ip_addr   │    │    priority  │
                    │    time      │    │ FK pub_by    │─┘
                    └──────────────┘    │    pub_date  │
                                        │    exp_date  │
                                        └──────────────┘
```

## Table Descriptions

### Core Tables

**USERS** - Central authentication table
- Stores all system users (students, teachers, admins)
- Indexed: username, email, role
- Password hashed with bcrypt cost 12

**STUDENTS** - Student profile data
- One-to-one with users
- Tracks department, semester, admission details
- Indexed: student_id, department, semester

**TEACHERS** - Teacher profile data
- One-to-one with users
- Tracks qualifications and specializations
- Indexed: teacher_id, department

**SESSIONS** - Active user sessions
- Tracks logged-in users
- Stores session data and activity
- Indexed: user_id, last_activity

**ACTIVITY_LOGS** - Audit trail
- Logs all important actions
- Tracks who did what and when
- Indexed: user_id, action, created_at

### Academic Tables

**SUBJECTS** - Course catalog
- Stores all available courses
- Links to teacher (instructor)
- Indexed: subject_code, department+semester
- Supports lab subjects

**STUDENT_SUBJECTS** - Enrollment junction table
- Many-to-many: students ↔ subjects
- Tracks enrollment by academic year
- Unique: student+subject+year

**RESULTS** - Examination results
- Stores marks (internal, theory, lab)
- Calculates grades and grade points
- Unique: student+subject+semester+year
- Indexed: student_id+semester

**ATTENDANCE** - Daily attendance
- Tracks present/absent/leave
- Links to teacher who marked
- Unique: student+subject+date
- Indexed: student+subject, date

### Administrative Tables

**PAYMENTS** - Fee management
- Tracks all student payments
- Multiple fee types and methods
- Status: pending, paid, overdue, cancelled
- Indexed: student_id+status, due_date

**NOTICES** - Announcements
- System-wide notifications
- Category-based (academic, events, urgent, general)
- Target audience filtering
- Indexed: category, is_active, published_date

## Indexes Summary

### Primary Keys (11)
- All tables have auto-increment integer primary keys

### Foreign Keys (15)
- students.user_id → users.id
- teachers.user_id → users.id
- sessions.user_id → users.id
- activity_logs.user_id → users.id
- subjects.teacher_id → teachers.id
- student_subjects.student_id → students.id
- student_subjects.subject_id → subjects.id
- results.student_id → students.id
- results.subject_id → subjects.id
- attendance.student_id → students.id
- attendance.subject_id → subjects.id
- attendance.marked_by → teachers.id
- payments.student_id → students.id
- notices.published_by → users.id

### Unique Constraints (7)
- users.username
- users.email
- students.user_id
- students.student_id
- teachers.user_id
- teachers.teacher_id
- subjects.subject_code
- student_subjects(student_id, subject_id, academic_year)
- results(student_id, subject_id, semester, academic_year)
- attendance(student_id, subject_id, attendance_date)

### Performance Indexes (14)
- users: idx_username, idx_email, idx_role
- students: idx_student_id, idx_department, idx_semester
- teachers: idx_teacher_id, idx_department
- subjects: idx_subject_code, idx_department_semester
- student_subjects: idx_student, idx_subject
- results: idx_student_semester
- attendance: idx_student_subject, idx_date
- payments: idx_student_status, idx_due_date
- notices: idx_category, idx_active, idx_published_date
- sessions: idx_user, idx_last_activity
- activity_logs: idx_user, idx_action, idx_created_at

## Data Types

### String Fields
- VARCHAR(20-255) for codes, names, descriptions
- TEXT for long content (notices, descriptions)
- ENUM for controlled values (role, status, category)

### Numeric Fields
- INT for IDs and counts
- DECIMAL(10,2) for monetary values
- BOOLEAN for flags

### Date/Time Fields
- DATE for dates only
- TIMESTAMP for date+time with timezone
- DEFAULT CURRENT_TIMESTAMP for auto-timestamps

## Constraints

### NOT NULL
- All primary keys
- All foreign keys
- Critical fields (username, email, password_hash, role, etc.)

### DEFAULT VALUES
- is_active: TRUE
- payment_status: 'pending'
- target_audience: 'all'
- created_at: CURRENT_TIMESTAMP

### ON DELETE
- CASCADE: Delete related records (students, teachers, enrollments, etc.)
- SET NULL: Keep record but clear reference (subjects.teacher_id)

## Character Set
- Charset: utf8mb4
- Collation: utf8mb4_unicode_ci
- Supports emoji and international characters
