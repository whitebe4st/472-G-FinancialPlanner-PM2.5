<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Financial Planner</title>
    <link rel="stylesheet" href="/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Logo</h1>
            <p class="subtitle">Welcome back! Please login to your account.</p>
            
            <form class="auth-form" action="/login" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A0A0A0">
                                <path d="M12 4C4 4 1 12 1 12C1 12 4 20 12 20C20 20 23 12 23 12C23 12 20 4 12 4Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Login</button>
                
                <div class="divider">
                    <span>or</span>
                </div>

                <button type="button" class="google-btn">
                    <img src="/images/google.png" alt="Google">
                    <span>Continue with Google</span>
                </button>
            </form>

            <p class="switch-auth">
                Don't have an account? <a href="/register">Register</a>
            </p>
        </div>
    </div>

    <script>
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const password = document.querySelector('#password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
        });

        document.querySelector('.google-btn').addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.add('clicked');
            
            // Wait for animation to complete + 1 second before redirecting
            setTimeout(() => {
                window.location.href = 'your-google-login-url';
            }, 1500); // 500ms for animation + 1000ms hold
        });
    </script>
</body>
</html> 