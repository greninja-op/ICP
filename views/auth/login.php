<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - University Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e0f2ff 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            color: #137fec;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .logo p {
            color: #666;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s;
            background: white;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #137fec;
        }

        .role-selector {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .role-option {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .role-option:hover {
            border-color: #137fec;
        }

        .role-option.active {
            background: #137fec;
            color: white;
            border-color: #137fec;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: auto;
        }

        .btn {
            width: 100%;
            padding: 0.875rem;
            background: #137fec;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #0d6edb;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: #137fec;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .success-message {
            background: #efe;
            color: #3c3;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>University Portal</h1>
            <p>Sign in to continue</p>
        </div>

        <?php if (isset($error) && $error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="/login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label>Select Role</label>
                <div class="role-selector">
                    <label class="role-option active">
                        <input type="radio" name="role" value="student" checked>
                        <span>Student</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="teacher">
                        <span>Teacher</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="admin">
                        <span>Admin</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me" style="margin-bottom: 0;">Remember my role</label>
            </div>

            <button type="submit" class="btn">Sign In</button>

            <div class="forgot-password">
                <a href="/forgot-password">Forgot Password?</a>
            </div>
        </form>
    </div>

    <script>
        // Role selector functionality
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.role-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Signing in...';

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect || '/dashboard';
                } else {
                    alert(data.message || 'Login failed. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Sign In';
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Sign In';
            }
        });

        // Load remembered role
        const rememberedRole = getCookie('remember_role');
        if (rememberedRole) {
            const roleInput = document.querySelector(`input[name="role"][value="${rememberedRole}"]`);
            if (roleInput) {
                roleInput.checked = true;
                roleInput.closest('.role-option').classList.add('active');
                document.querySelectorAll('.role-option').forEach(opt => {
                    if (opt !== roleInput.closest('.role-option')) {
                        opt.classList.remove('active');
                    }
                });
            }
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }
    </script>
</body>
</html>

