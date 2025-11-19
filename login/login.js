// This function sets up the slide-on-hover animation for the role selector.
function setupRoleSelectorAnimation() {
    const selector = document.querySelector('.role-selector');
    const highlight = document.querySelector('.role-highlight');
    const inputs = document.querySelectorAll('input[name="role"]');

    // Exit if the necessary elements don't exist on the page.
    if (!selector || !highlight || !inputs.length) {
        return;
    }

    // This function moves the highlight to a specific target label.
    function moveHighlightTo(targetLabel) {
        if (!targetLabel) return;
        const labelWidth = targetLabel.offsetWidth;
        const labelLeft = targetLabel.offsetLeft;

        highlight.style.width = `${labelWidth}px`;
        highlight.style.transform = `translateX(${labelLeft}px)`;
    }

    // --- SETUP EVENT LISTENERS ---

    // 1. Initial position: Find the currently checked input and move the highlight there.
    const initiallyCheckedInput = document.querySelector('input[name="role"]:checked');
    const initialLabel = document.querySelector(`label[for="${initiallyCheckedInput.id}"]`);
    moveHighlightTo(initialLabel);


    // 2. Hover effect: When the mouse enters a label, slide the highlight to it.
    const allLabels = document.querySelectorAll('.role-selector label');
    allLabels.forEach(label => {
        label.addEventListener('mouseenter', () => {
            moveHighlightTo(label);
        });
    });


    // 3. Mouse leave effect: When the mouse leaves the entire container,
    //    slide the highlight back to the currently selected (checked) option.
    selector.addEventListener('mouseleave', () => {
        const checkedInput = document.querySelector('input[name="role"]:checked');
        const checkedLabel = document.querySelector(`label[for="${checkedInput.id}"]`);
        moveHighlightTo(checkedLabel);
    });

    // 4. Click Persistence: No extra JS needed. When a label is clicked,
    //    the radio button becomes checked. The 'mouseleave' event will then
    //    automatically know the new correct position to slide back to.
    
    // 5. Responsive Resizing: Recalculate position on window resize.
    window.addEventListener('resize', () => {
        const checkedInput = document.querySelector('input[name="role"]:checked');
        const checkedLabel = document.querySelector(`label[for="${checkedInput.id}"]`);
        moveHighlightTo(checkedLabel);
    });
}

// Handle Login Form Submission
async function handleLogin(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerText;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerText = 'Logging in...';
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('../backend/api/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            // Store auth data
            localStorage.setItem('token', result.data.token);
            localStorage.setItem('user', JSON.stringify(result.data.user));
            
            // Redirect based on role
            const role = result.data.user.role;
            if (role === 'admin') {
                window.location.href = '../dashboard/admin.html';
            } else if (role === 'student') {
                window.location.href = '../dashboard/dashboard.html';
            } else if (role === 'teacher') {
                window.location.href = '../dashboard/teacher.html'; // Assuming teacher dashboard exists
            } else {
                alert('Unknown role');
            }
        } else {
            alert(result.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('An error occurred during login. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = originalBtnText;
    }
}

// Run the setup function after the page has finished loading.
document.addEventListener('DOMContentLoaded', () => {
    setupRoleSelectorAnimation();
    
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});