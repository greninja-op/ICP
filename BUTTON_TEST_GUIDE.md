# Button Testing Guide - Fee Management System

## Which buttons should be working?

### Admin Fee Management Page (`/admin/fee-management`)

#### Tab 1: Pending Students List
1. **"Send Bulk Reminders" button** (top right)
   - Only appears when there are filtered students
   - Sends reminders to all students matching current filters
   
2. **"Send Alert" button** (in each table row)
   - Sends individual reminder to specific student
   - Should show alert confirmation after clicking

3. **Fee Type Statistics Cards** (clickable)
   - Clicking any card filters the list by that fee type

4. **Filter Dropdowns**
   - Fee Type dropdown
   - Department dropdown

#### Tab 2: Send Fee Notice
1. **"Send Fee Notice to Students" button** (bottom)
   - Validates all required fields are filled
   - Shows confirmation dialog
   - Sends notice to Notice Board and Payments section

### Student Payments Page (`/payments`)

1. **"Pay Now via QR Code / UPI / Card" button**
   - Appears for each pending payment
   - Processes payment when clicked

2. **"Download Payment Receipt" button**
   - Only appears if there are paid payments

## Testing Steps

### Test 1: Admin - Send Individual Reminder
1. Login as admin (username: `admin`, password: `123`)
2. Go to Fee Management
3. Stay on "Pending Students List" tab
4. Click "Send Alert" button for any student
5. **Expected**: Alert popup saying "✅ Personal reminder sent to [Student Name]!"
6. Check Notice Board - should see the reminder

### Test 2: Admin - Send Bulk Reminders
1. On Pending Students List tab
2. Select filters (e.g., "Exam Fee" and "BCA")
3. Click "Send Bulk Reminders" button at top right
4. **Expected**: Confirmation dialog, then success alert
5. Check Notice Board - should see multiple reminders

### Test 3: Admin - Send Fee Notice
1. Click "Send Fee Notice" tab
2. Fill in all fields:
   - Department: BCA
   - Semester: 5
   - Fee Type: Semester Fee
   - Amount: (auto-filled to 18000)
   - Last Date (No Fine): Pick a future date
   - Last Date (With Fine): Pick a later date
   - Fine Amount: 500
   - Final Date (Super Fine): Pick an even later date
   - Super Fine Amount: 1000
3. Click "Send Fee Notice to Students"
4. **Expected**: Confirmation dialog, then success alert
5. Logout and login as student

### Test 4: Student - View Fee Notice
1. Login as student (username: `student`, password: `123`)
   - This user is BCA, Semester 5
2. Go to Payments page
3. **Expected**: Should see the fee notice you just sent
4. Should see "Pay Now" button
5. Go to Notice Board
6. **Expected**: Should see the fee notice there too

### Test 5: Student - Pay Fee
1. On Payments page
2. Click "Pay Now via QR Code / UPI / Card" button
3. **Expected**: Payment should be processed (mock)
4. Status should change to PAID

## Troubleshooting

### If "Send Alert" button doesn't work:
- Check browser console for errors (F12)
- Verify you're logged in as admin
- Check if localStorage is enabled

### If "Send Fee Notice" button is disabled:
- Make sure ALL three date fields are filled
- Button will be grayed out if any date is missing

### If student doesn't see fee notice in Payments:
- Verify student's department and semester match the notice
- Student user 'student' is BCA Semester 5
- Student user 'diya.patel' is BBA Semester 3
- Student user 'rahul.verma' is B.Com Semester 5
- Send notice to matching department/semester

### If buttons don't respond at all:
1. Open browser console (F12)
2. Look for JavaScript errors
3. Check if React is loaded properly
4. Try refreshing the page (Ctrl+R)
5. Clear localStorage and login again

## Quick Test Commands

### Clear all notices and payments:
Open browser console (F12) and run:
```javascript
localStorage.removeItem('notices')
localStorage.removeItem('feePayments')
location.reload()
```

### Check current user:
```javascript
JSON.parse(localStorage.getItem('user'))
```

### Check stored notices:
```javascript
JSON.parse(localStorage.getItem('notices') || '[]')
```

### Check stored fee payments:
```javascript
JSON.parse(localStorage.getItem('feePayments') || '[]')
```

## Expected Behavior Summary

✅ **Admin can:**
- View all pending students (13 students total)
- Filter by fee type and department
- Send individual reminders
- Send bulk reminders to filtered students
- Create and send new fee notices

✅ **Students can:**
- View fee notices relevant to their department/semester
- See payment deadlines and fine structure
- See countdown timers for due dates
- See OVERDUE warnings
- Click "Pay Now" to process payment
- View notices on Notice Board

## Common Issues

1. **Button clicks but nothing happens**
   - Check browser console for errors
   - Verify localStorage is working
   - Try logging out and back in

2. **Student doesn't see fee notice**
   - Department/semester mismatch
   - Use matching test users (see api.js for list)

3. **Alerts not showing**
   - Browser might be blocking alerts
   - Check browser settings for popup/alert permissions

4. **Data not persisting**
   - localStorage might be disabled
   - Try incognito/private mode
   - Check browser storage settings
