const API_BASE = '../backend/api';

// Auth Check
function checkAuth() {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    if (!token || user.role !== 'admin') {
        window.location.href = '../login/login.html';
    }
}

checkAuth();

// Tab Switching
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    
    document.querySelectorAll('nav a').forEach(el => el.classList.remove('active-nav', 'bg-slate-800', 'text-white'));
    document.getElementById('nav-' + tabId).classList.add('active-nav', 'bg-slate-800', 'text-white');
    
    if (tabId === 'students') loadStudents();
    if (tabId === 'notices') loadNotices();
    if (tabId === 'fees') loadFees();
}

// Modal Toggling
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
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

// Load Data Functions
async function loadStudents() {
    try {
        const result = await apiCall('admin/students/list.php'); // Assuming list.php exists
        const tbody = document.getElementById('students-table-body');
        tbody.innerHTML = '';
        
        if (result.data && result.data.length > 0) {
            result.data.forEach(student => {
                tbody.innerHTML += `
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="p-4 text-slate-700">${student.student_id}</td>
                        <td class="p-4 text-slate-700 font-medium">${student.first_name} ${student.last_name}</td>
                        <td class="p-4 text-slate-700">${student.department}</td>
                        <td class="p-4">
                            <button class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></button>
                            <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-500">No students found</td></tr>';
        }
    } catch (e) {
        console.log('Error loading students', e);
    }
}

async function loadNotices() {
    try {
        const result = await apiCall('notices/get_all.php');
        const container = document.getElementById('notices-list');
        container.innerHTML = '';
        
        if (result.data && result.data.length > 0) {
            result.data.forEach(notice => {
                container.innerHTML += `
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-lg text-slate-800">${notice.title}</h3>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full uppercase">${notice.type}</span>
                        </div>
                        <p class="text-slate-600 mb-3">${notice.content}</p>
                        <div class="text-xs text-slate-400 flex justify-between">
                            <span>Audience: ${notice.target_audience}</span>
                            <span>${new Date(notice.created_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                `;
            });
        } else {
            container.innerHTML = '<p class="text-center text-slate-500">No notices found</p>';
        }
    } catch (e) {
        console.log('Error loading notices', e);
    }
}

async function loadFees() {
    try {
        const result = await apiCall('admin/fees/list.php'); // Assuming list.php exists
        const container = document.getElementById('fees-list');
        const select = document.getElementById('fee-select');
        
        container.innerHTML = '';
        select.innerHTML = '<option value="">Select a fee...</option>';
        
        if (result.data && result.data.length > 0) {
            result.data.forEach(fee => {
                // Add to list
                container.innerHTML += `
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-bold text-slate-800">${fee.fee_name}</h3>
                            <span class="font-bold text-green-600">$${fee.amount}</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-2">Due: ${new Date(fee.due_date).toLocaleDateString()}</p>
                        <p class="text-sm text-slate-600">${fee.description || ''}</p>
                    </div>
                `;
                
                // Add to select dropdown
                select.innerHTML += `<option value="${fee.id}">${fee.fee_name} ($${fee.amount})</option>`;
            });
        } else {
            container.innerHTML = '<p class="col-span-2 text-center text-slate-500">No fee structures found</p>';
        }
    } catch (e) {
        console.log('Error loading fees', e);
    }
}

// Form Submissions

document.getElementById('add-student-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    try {
        await apiCall('admin/students/create.php', 'POST', data);
        alert('Student created successfully!');
        toggleModal('add-student-modal');
        e.target.reset();
        loadStudents();
    } catch (e) {
        // Error handled in apiCall
    }
});

document.getElementById('add-notice-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    try {
        await apiCall('notices/create.php', 'POST', data);
        alert('Notice posted successfully!');
        toggleModal('add-notice-modal');
        e.target.reset();
        loadNotices();
    } catch (e) {
        // Error handled in apiCall
    }
});

document.getElementById('send-fee-notification-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    try {
        await apiCall('admin/fees/notify.php', 'POST', data);
        alert('Notifications sent successfully!');
        toggleModal('send-fee-notification-modal');
        e.target.reset();
    } catch (e) {
        // Error handled in apiCall
    }
});

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '../login/login.html';
}

// Initial Load
loadDashboardStats(); // You'll need to implement this endpoint or function

async function loadDashboardStats() {
    // Placeholder for stats loading
    document.getElementById('total-students').innerText = '...';
    // Implement backend/api/admin/stats.php if needed
}
