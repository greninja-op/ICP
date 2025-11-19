const API_BASE = '../backend/api';

// Auth Check
function checkAuth() {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    if (!token || user.role !== 'teacher') {
        window.location.href = '../login/login.html';
    }
    document.getElementById('teacher-name').innerText = user.full_name || user.username;
}

checkAuth();

// Tab Switching
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    
    document.querySelectorAll('nav a').forEach(el => el.classList.remove('active-nav', 'bg-slate-800', 'text-white'));
    document.getElementById('nav-' + tabId).classList.add('active-nav', 'bg-slate-800', 'text-white');
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '../login/login.html';
}

// API Helper
async function apiCall(endpoint, method = 'GET', data = null) {
    const token = localStorage.getItem('token');
    const headers = {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    };
    
    const options = { method, headers };
    if (data) options.body = JSON.stringify(data);
    
    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, options);
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || result.error || 'API Error');
        }
        return result;
    } catch (error) {
        console.error('API Error:', error);
        alert(error.message);
        if (error.message.includes('Unauthorized')) {
            window.location.href = '../login/login.html';
        }
        throw error;
    }
}

// Attendance Logic

// Event Listeners for Filters
document.getElementById('att-dept').addEventListener('change', loadSubjects);
document.getElementById('att-sem').addEventListener('change', loadSubjects);

// Set default date to today
document.getElementById('att-date').valueAsDate = new Date();

async function loadSubjects() {
    const dept = document.getElementById('att-dept').value;
    const sem = document.getElementById('att-sem').value;
    const subjectSelect = document.getElementById('att-subject');
    
    if (!dept || !sem) {
        subjectSelect.disabled = true;
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        return;
    }
    
    try {
        const result = await apiCall(`teacher/get_subjects.php?department=${dept}&semester=${sem}`);
        
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        if (result.data && result.data.length > 0) {
            result.data.forEach(sub => {
                subjectSelect.innerHTML += `<option value="${sub.id}">${sub.subject_name} (${sub.subject_code})</option>`;
            });
            subjectSelect.disabled = false;
        } else {
            subjectSelect.innerHTML = '<option value="">No subjects found</option>';
            subjectSelect.disabled = true;
        }
    } catch (e) {
        console.log('Error loading subjects', e);
    }
}

async function loadStudentsForAttendance() {
    const dept = document.getElementById('att-dept').value;
    const sem = document.getElementById('att-sem').value;
    const subjectId = document.getElementById('att-subject').value;
    
    if (!dept || !sem || !subjectId) {
        alert('Please select Department, Semester and Subject');
        return;
    }
    
    try {
        const result = await apiCall(`teacher/get_students.php?department=${dept}&semester=${sem}`);
        const tbody = document.getElementById('attendance-table-body');
        const sheet = document.getElementById('attendance-sheet');
        
        tbody.innerHTML = '';
        
        if (result.data && result.data.length > 0) {
            result.data.forEach(student => {
                tbody.innerHTML += `
                    <tr class="border-b border-slate-100 hover:bg-slate-50" data-student-id="${student.id}">
                        <td class="p-4 text-slate-700">${student.student_id}</td>
                        <td class="p-4 text-slate-700 font-medium">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 text-xs">
                                    ${student.first_name.charAt(0)}${student.last_name.charAt(0)}
                                </div>
                                ${student.first_name} ${student.last_name}
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <div class="inline-flex bg-slate-100 rounded-lg p-1">
                                <label class="cursor-pointer px-3 py-1 rounded-md transition-colors has-[:checked]:bg-green-500 has-[:checked]:text-white hover:bg-slate-200">
                                    <input type="radio" name="status_${student.id}" value="present" checked class="hidden"> P
                                </label>
                                <label class="cursor-pointer px-3 py-1 rounded-md transition-colors has-[:checked]:bg-red-500 has-[:checked]:text-white hover:bg-slate-200">
                                    <input type="radio" name="status_${student.id}" value="absent" class="hidden"> A
                                </label>
                                <label class="cursor-pointer px-3 py-1 rounded-md transition-colors has-[:checked]:bg-yellow-500 has-[:checked]:text-white hover:bg-slate-200">
                                    <input type="radio" name="status_${student.id}" value="late" class="hidden"> L
                                </label>
                            </div>
                        </td>
                    </tr>
                `;
            });
            sheet.classList.remove('hidden');
        } else {
            alert('No students found for this class');
            sheet.classList.add('hidden');
        }
    } catch (e) {
        console.log('Error loading students', e);
    }
}

function markAll(status) {
    const radios = document.querySelectorAll(`input[value="${status}"]`);
    radios.forEach(radio => radio.checked = true);
}

async function submitAttendance() {
    const subjectId = document.getElementById('att-subject').value;
    const date = document.getElementById('att-date').value;
    const rows = document.querySelectorAll('#attendance-table-body tr');
    
    const attendanceData = [];
    
    rows.forEach(row => {
        const studentId = row.dataset.studentId;
        const status = row.querySelector(`input[name="status_${studentId}"]:checked`).value;
        
        attendanceData.push({
            student_id: studentId,
            status: status
        });
    });
    
    const payload = {
        subject_id: subjectId,
        attendance_date: date,
        attendance: attendanceData
    };
    
    try {
        await apiCall('teacher/mark_attendance.php', 'POST', payload);
        alert('Attendance marked successfully!');
        document.getElementById('attendance-sheet').classList.add('hidden');
    } catch (e) {
        // Error handled in apiCall
    }
}
