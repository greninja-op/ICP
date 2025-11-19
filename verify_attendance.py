#!/usr/bin/env python3
"""
Attendance Verification Script
Connects to the database and verifies attendance calculations for BCA student (STUBCA001)
"""

import mysql.connector
from mysql.connector import Error

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'studentportal'
}

def create_connection():
    """Create database connection"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        if connection.is_connected():
            print("[OK] Connected to MySQL database")
            return connection
    except Error as e:
        print(f"[ERROR] Error connecting to MySQL: {e}")
        return None

def get_student_info(cursor, student_id_str):
    """Get student information"""
    cursor.execute("""
        SELECT s.id, s.student_id, s.first_name, s.last_name, s.department, s.semester
        FROM students s
        WHERE s.student_id = %s
    """, (student_id_str,))
    
    result = cursor.fetchone()
    if result:
        return {
            'id': result[0],
            'student_id': result[1],
            'first_name': result[2],
            'last_name': result[3],
            'department': result[4],
            'semester': result[5]
        }
    return None

def get_attendance_by_subject(cursor, student_db_id):
    """Get detailed attendance records grouped by subject"""
    cursor.execute("""
        SELECT 
            subj.subject_code,
            subj.subject_name,
            a.attendance_date,
            a.status
        FROM attendance a
        JOIN subjects subj ON a.subject_id = subj.id
        WHERE a.student_id = %s
        ORDER BY a.attendance_date, subj.subject_code
    """, (student_db_id,))
    
    return cursor.fetchall()

def get_attendance_summary(cursor, student_db_id):
    """Get attendance summary by subject"""
    cursor.execute("""
        SELECT 
            subj.subject_code,
            subj.subject_name,
            COUNT(*) as total_records,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count,
            SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused_count
        FROM attendance a
        JOIN subjects subj ON a.subject_id = subj.id
        WHERE a.student_id = %s
        GROUP BY subj.id, subj.subject_code, subj.subject_name
        ORDER BY subj.subject_code
    """, (student_db_id,))
    
    return cursor.fetchall()

def calculate_percentage(present, total):
    """Calculate attendance percentage"""
    if total == 0:
        return 0.0
    return (present / total) * 100

def verify_bunking_scenario(summary):
    """Check if the bunking scenario is present in the data"""
    print("\n" + "=" * 80)
    print("BUNKING SCENARIO DETECTION")
    print("=" * 80)
    
    bunking_detected = False
    
    for record in summary:
        subject_code = record[0]
        subject_name = record[1]
        total = record[2]
        present = record[3]
        absent = record[4]
        
        percentage = calculate_percentage(present, total)
        
        # Check if this subject has significantly lower attendance
        if percentage < 50 and absent > present:
            bunking_detected = True
            print(f"\n[WARN] BUNKING DETECTED in {subject_code} ({subject_name})")
            print(f"  Present: {present}/{total} ({percentage:.2f}%)")
            print(f"  Absent: {absent} classes")
    
    if not bunking_detected:
        print("\nNo significant bunking pattern detected.")
    
    return bunking_detected

def main():
    """Main verification function"""
    print("=" * 80)
    print("ATTENDANCE VERIFICATION SCRIPT")
    print("=" * 80)
    
    # Connect to database
    conn = create_connection()
    if not conn:
        return
    
    cursor = conn.cursor()
    
    try:
        # Target student ID
        target_student = "STUBCA001"
        print(f"\nTarget Student: {target_student}")
        print("-" * 80)
        
        # Get student info
        student = get_student_info(cursor, target_student)
        
        if not student:
            print(f"\n[ERROR] Student {target_student} not found in database!")
            print("\nFetching available BCA students...")
            cursor.execute("""
                SELECT student_id, first_name, last_name, department 
                FROM students 
                WHERE department = 'BCA' 
                ORDER BY student_id 
                LIMIT 10
            """)
            bca_students = cursor.fetchall()
            
            if bca_students:
                print("\nAvailable BCA students:")
                for stu in bca_students:
                    print(f"  - {stu[0]}: {stu[1]} {stu[2]} ({stu[3]})")
            else:
                print("\n[ERROR] No BCA students found in database!")
            
            return
        
            print(f"\n[OK] Student Found:")
        print(f"  ID: {student['student_id']}")
        print(f"  Name: {student['first_name']} {student['last_name']}")
        print(f"  Department: {student['department']}")
        print(f"  Semester: {student['semester']}")
        
        # Get detailed attendance records
        print("\n" + "=" * 80)
        print("DETAILED ATTENDANCE RECORDS")
        print("=" * 80)
        
        attendance_records = get_attendance_by_subject(cursor, student['id'])
        
        if not attendance_records:
            print("\n[ERROR] No attendance records found for this student!")
            return
        
        print(f"\nTotal Records: {len(attendance_records)}")
        print("\n{:<15} {:<30} {:<15} {:<10}".format("Subject Code", "Subject Name", "Date", "Status"))
        print("-" * 80)
        
        current_subject = None
        for record in attendance_records:
            subject_code = record[0]
            subject_name = record[1]
            att_date = record[2]
            status = record[3]
            
            # Print subject header if changed
            if subject_code != current_subject:
                if current_subject is not None:
                    print()
                current_subject = subject_code
            
            print(f"{subject_code:<15} {subject_name:<30} {att_date} {status:<10}")
        
        # Get attendance summary
        print("\n" + "=" * 80)
        print("ATTENDANCE SUMMARY BY SUBJECT")
        print("=" * 80)
        
        summary = get_attendance_summary(cursor, student['id'])
        
        print("\n{:<15} {:<30} {:<8} {:<8} {:<8} {:<12}".format(
            "Subject Code", "Subject Name", "Total", "Present", "Absent", "Percentage"
        ))
        print("-" * 80)
        
        total_all = 0
        present_all = 0
        
        for record in summary:
            subject_code = record[0]
            subject_name = record[1]
            total = record[2]
            present = record[3]
            absent = record[4]
            late = record[5]
            excused = record[6]
            
            percentage = calculate_percentage(present, total)
            
            print(f"{subject_code:<15} {subject_name:<30} {total:<8} {present:<8} {absent:<8} {percentage:>6.2f}%")
            
            # Additional details if needed
            if late > 0 or excused > 0:
                print(f"{'':>15} {'':>30} Late: {late}, Excused: {excused}")
            
            total_all += total
            present_all += present
        
        print("-" * 80)
        overall_percentage = calculate_percentage(present_all, total_all)
        print(f"{'OVERALL':<15} {'':>30} {total_all:<8} {present_all:<8} {total_all - present_all:<8} {overall_percentage:>6.2f}%")
        
        # Manual calculation verification
        print("\n" + "=" * 80)
        print("MANUAL CALCULATION VERIFICATION")
        print("=" * 80)
        
        print(f"\nTotal Classes: {total_all}")
        print(f"Present: {present_all}")
        print(f"Absent: {total_all - present_all}")
        print(f"\nCalculation: ({present_all} / {total_all}) × 100 = {overall_percentage:.2f}%")
        
        # Expected vs Actual
        print("\n" + "=" * 80)
        print("EXPECTED vs ACTUAL")
        print("=" * 80)
        
        print("\nBased on setup_full_system.py logic:")
        print("  - Date 1 (2023-09-01): Present in ALL 5 subjects")
        print("  - Date 2 (2023-09-02): Present in 1st subject, Absent in 4 subjects (BUNKING)")
        print("\nExpected:")
        print("  - Total Records: 10 (5 subjects × 2 dates)")
        print("  - Present: 6 (5 on date 1 + 1 on date 2)")
        print("  - Absent: 4 (4 subjects on date 2)")
        print("  - Expected Percentage: (6/10) × 100 = 60.00%")
        
        print(f"\nActual:")
        print(f"  - Total Records: {total_all}")
        print(f"  - Present: {present_all}")
        print(f"  - Absent: {total_all - present_all}")
        print(f"  - Actual Percentage: {overall_percentage:.2f}%")
        
        if total_all == 10 and present_all == 6:
            print("\n[OK] VERIFICATION PASSED: Data matches expected bunking scenario!")
        elif total_all > 0:
            print("\n[WARN] VERIFICATION WARNING: Data exists but doesn't match expected scenario.")
            print("  This might be due to different test data or multiple data generation runs.")
        else:
            print("\n[ERROR] VERIFICATION FAILED: No attendance data found!")
        
        # Check for bunking pattern
        verify_bunking_scenario(summary)
        
    except Error as e:
        print(f"\n[ERROR] Database Error: {e}")
    finally:
        cursor.close()
        conn.close()
        print("\n" + "=" * 80)
        print("[OK] Database connection closed")
        print("=" * 80)

if __name__ == "__main__":
    main()
