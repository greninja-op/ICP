# Fee Management System Guide

## Overview
The comprehensive fee management system allows admins to track pending payments and send fee notices to students. The system has two main sections:

## 1. Pending Students List

### Features:
- **View All Pending Payments**: See complete list of students who haven't paid their fees
- **Filter by Fee Type**: Filter students by specific fee types (Semester Fee, Exam Fee, Annual Day Fund, etc.)
- **Filter by Department**: Filter by department (BCA, BBA, B.Com, BSc Physics, BCS)
- **Fee Type Statistics**: Quick overview cards showing count and total amount for each fee type
- **Individual Reminders**: Send personal payment reminders to specific students
- **Bulk Reminders**: Send reminders to all filtered students at once

### Student Information Displayed:
- Roll Number
- Name
- Department
- Year/Semester
- Fee Type
- Amount
- Due Date (with OVERDUE indicator)
- Fine amounts (regular and super fine)

### Reminder Features:
- Personal reminders include:
  - Student's specific fee details
  - Payment deadlines with days remaining
  - Fine structure (No Fine → Fine → Super Fine)
  - Payment methods available
- Reminders are sent to:
  - **Notice Board** (student can view in Notice section)
  - **Payment Section** (appears in student's Payments page)

## 2. Send Fee Notice

### Purpose:
Create and broadcast new fee notifications to entire departments/semesters

### Configuration Options:

#### Basic Details:
- **Department**: Select target department (BCA, BBA, B.Com, BSc Physics, BCS)
- **Semester**: Select target semester (1-6)
- **Fee Type**: 
  - Semester Fee (auto-calculates based on odd/even semester)
  - Exam Fee (₹800)
  - Other Fee/Collection (custom amount)
- **Amount**: Auto-filled for semester/exam fees, manual for others

#### Payment Deadlines:
- **Last Date (No Fine)**: Final date to pay without penalty
- **Last Date (With Fine)**: Extended deadline with fine amount
- **Fine Amount**: Penalty for late payment
- **Final Date (Super Fine)**: Absolute last date
- **Super Fine Amount**: Maximum penalty

#### Additional Information:
- Optional description field for extra instructions or details

### Fee Amounts by Course:
- **BCA**: ₹18,000 (odd sem) / ₹15,000 (even sem)
- **BBA**: ₹16,000 (odd sem) / ₹14,000 (even sem)
- **B.Com**: ₹12,000 (odd sem) / ₹10,000 (even sem)
- **BSc Physics**: ₹15,000 (odd sem) / ₹13,000 (even sem)
- **BCS**: ₹20,000 (odd sem) / ₹18,000 (even sem)
- **Exam Fee**: ₹800 (all courses)

## Student Experience

### Notice Board:
Students see fee notices with:
- Complete fee details
- All payment deadlines clearly formatted
- Fine structure
- Payment methods
- Issued by admin name and date

### Payments Section:
Students see:
- All pending fees with countdown timers
- OVERDUE indicators for late payments
- Fine amounts and extended deadlines
- "Pay Now" button for QR Code/UPI/Card payment
- Visual indicators (green for paid, orange for pending, red for overdue)

## Workflow Example

### Scenario 1: Semester Fee Collection
1. Admin goes to "Send Fee Notice"
2. Selects: BCA, Semester 5, Semester Fee
3. Amount auto-fills to ₹18,000 (odd semester)
4. Sets deadlines:
   - No Fine: Dec 15, 2025
   - With Fine (₹500): Dec 22, 2025
   - Super Fine (₹1000): Dec 31, 2025
5. Clicks "Send Fee Notice"
6. All BCA Semester 5 students receive:
   - Notice on Notice Board
   - Payment entry in Payments section

### Scenario 2: Tracking Pending Payments
1. Admin goes to "Pending Students List"
2. Filters by "Exam Fee" and "BCA"
3. Sees 2 students haven't paid
4. Clicks "Send Alert" for individual student
5. Student receives personal reminder with:
   - Their specific fee details
   - Days remaining until due date
   - Fine structure
   - Payment instructions

### Scenario 3: Bulk Reminders
1. Admin filters pending students by "Semester Fee"
2. Sees 10 students across departments
3. Clicks "Send Bulk Reminders"
4. All 10 students receive personalized reminders

## Key Features

✅ **Comprehensive Tracking**: View all pending payments in one place
✅ **Smart Filtering**: Filter by fee type and department
✅ **Statistics Dashboard**: Quick overview of pending payments by type
✅ **Personal Reminders**: Send targeted alerts to specific students
✅ **Bulk Operations**: Send reminders to multiple students at once
✅ **Dual Notification**: Notices appear in both Notice Board and Payments section
✅ **Fine Structure**: Three-tier deadline system (No Fine → Fine → Super Fine)
✅ **Auto-calculation**: Semester fees auto-calculate based on course and semester
✅ **Overdue Tracking**: Visual indicators for overdue payments
✅ **Payment Integration**: Direct link to payment methods (QR/UPI/Card)

## Data Storage

- Fee notices stored in `localStorage` under:
  - `notices` - for Notice Board display
  - `feePayments` - for Payments section display
- Pending students data includes:
  - Student details (name, roll no, department, year, semester)
  - Fee details (type, amount, due date)
  - Fine structure (fine amount, super fine amount)

## Future Enhancements

- Payment confirmation and receipt generation
- SMS/Email notifications
- Payment history and analytics
- Automatic fine calculation based on current date
- Export pending payments report
- Integration with actual payment gateway
