const API_BASE = '../backend/api';

// Auth Check
function checkAuth() {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    if (!token || user.role !== 'student') {
        window.location.href = '../login/login.html';
    }
    
    // Update UI with user info
    const nameElements = document.querySelectorAll('.user-name, .welcome-text h2');
    if (nameElements.length > 0) {
        // Simple update, might need refinement based on exact DOM structure
        document.querySelector('.user-name').innerText = user.full_name || user.username;
        document.querySelector('.welcome-text h2').innerText = `Welcome Back, ${user.first_name || user.username}!`;
    }
}

checkAuth();

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
        if (error.message.includes('Unauthorized')) {
            window.location.href = '../login/login.html';
        }
        throw error;
    }
}

// Load Attendance Data
async function loadAttendance() {
    try {
        const result = await apiCall('student/attendance.php');
        
        // Update Stats
        if (result.data && result.data.stats) {
            const stats = result.data.stats;
            document.getElementById('att-present').innerText = stats.present_count || 0;
            document.getElementById('att-absent').innerText = stats.absent_count || 0;
            
            const total = parseInt(stats.total_classes) || 0;
            const present = parseInt(stats.present_count) || 0;
            const percentage = total > 0 ? Math.round((present / total) * 100) : 0;
            
            document.getElementById('att-percentage').innerText = `${percentage}%`;
        }
        
        // Update List
        const listContainer = document.getElementById('attendance-list');
        listContainer.innerHTML = '';
        
        if (result.data && result.data.history && result.data.history.length > 0) {
            result.data.history.slice(0, 10).forEach(record => {
                let statusColor = 'text-slate-500';
                if (record.status === 'present') statusColor = 'text-green-600';
                if (record.status === 'absent') statusColor = 'text-red-600';
                if (record.status === 'late') statusColor = 'text-yellow-600';
                
                listContainer.innerHTML += `
                    <div class="flex justify-between items-center text-sm border-b border-slate-100 pb-1 last:border-0">
                        <div>
                            <span class="font-medium text-slate-700">${record.subject_code}</span>
                            <span class="text-xs text-slate-400 ml-2">${new Date(record.attendance_date).toLocaleDateString()}</span>
                        </div>
                        <span class="font-bold capitalize ${statusColor}">${record.status}</span>
                    </div>
                `;
            });
        } else {
            listContainer.innerHTML = '<p class="text-xs text-slate-400 text-center">No attendance records found for this month.</p>';
        }
        
    } catch (e) {
        console.log('Error loading attendance', e);
        document.getElementById('attendance-list').innerHTML = '<p class="text-xs text-red-400 text-center">Failed to load data.</p>';
    }
}

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    loadAttendance();
});
