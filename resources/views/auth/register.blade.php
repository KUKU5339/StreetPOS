<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StreetPOS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #FFD700 0%, #ffc107 50%, #800000 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .auth-container {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 48px;
            color: #800000;
            margin-bottom: 10px;
        }

        .logo-text {
            font-size: 28px;
            font-weight: bold;
            color: #800000;
            margin-bottom: 5px;
        }

        .logo-tagline {
            font-size: 13px;
            color: #666;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: #fee;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }

        input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: #fafafa;
        }

        input:focus {
            outline: none;
            border-color: #FFD700;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #800000;
            font-size: 18px;
            transition: all 0.3s;
        }

        .toggle-password:hover {
            color: #a00000;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }

        .strength-bar {
            height: 3px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }

        .strength-weak {
            background: #dc3545;
            width: 33%;
        }

        .strength-medium {
            background: #ffc107;
            width: 66%;
        }

        .strength-strong {
            background: #28a745;
            width: 100%;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #FFD700, #ffc107);
            color: #800000;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            background: #fff;
            padding: 0 15px;
            color: #999;
            font-size: 13px;
            position: relative;
        }

        .switch-link {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .switch-link a {
            color: #800000;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .switch-link a:hover {
            color: #a00000;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 25px;
            }

            .logo-text {
                font-size: 24px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="logo">
                <div class="logo-icon">🍢</div>
                <div class="logo-text">StreetPOS</div>
                <div class="logo-tagline">Start Managing Your Business Today</div>
            </div>

            <h2>Create Your Account</h2>

            @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="name" name="name" placeholder="Juan Dela Cruz" required autofocus value="{{ old('name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required value="{{ old('email') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <small id="strength-text"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password_confirmation', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="divider">
                <span>Already have an account?</span>
            </div>

            <div class="switch-link">
                <a href="{{ route('login') }}">← Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthFill = document.getElementById('strength-fill');
        const strengthText = document.getElementById('strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            strengthFill.className = 'strength-fill';

            if (strength <= 2) {
                strengthFill.classList.add('strength-weak');
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#dc3545';
            } else if (strength <= 3) {
                strengthFill.classList.add('strength-medium');
                strengthText.textContent = 'Medium strength';
                strengthText.style.color = '#ffc107';
            } else {
                strengthFill.classList.add('strength-strong');
                strengthText.textContent = 'Strong password!';
                strengthText.style.color = '#28a745';
            }
        });
    </script>
</body>

</html>