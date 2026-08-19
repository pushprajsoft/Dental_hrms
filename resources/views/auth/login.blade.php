<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In · ShubhHMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #0f2027 0%, #123C3A 50%, #17847A 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            overflow: hidden;
            position: relative;
        }

        /* Animated Floating Background Shapes */
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            animation: float 6s ease-in-out infinite;
        }
        .shape-1 { width: 300px; height: 300px; top: -50px; left: -50px; animation-delay: 0s; }
        .shape-2 { width: 200px; height: 200px; bottom: -30px; right: -30px; animation-delay: 2s; background: rgba(63, 191, 173, 0.1); }
        .shape-3 { width: 150px; height: 150px; top: 40%; right: 10%; animation-delay: 4s; background: rgba(255, 255, 255, 0.03); }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }

        /* Login Card */
        .login-card {
            background: #ffffff;
            width: 420px;
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            z-index: 10;
            position: relative;
            animation: slideUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo & Header */
        .brand-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #3FBFAD, #123C3A);
            margin: 0 auto 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(63, 191, 173, 0.3);
        }
        .brand-logo i { font-size: 32px; color: #fff; }

        .login-card h2 { 
            text-align: center; 
            margin: 0 0 5px 0; 
            font-family: 'Outfit', sans-serif; 
            font-size: 28px; 
            font-weight: 700; 
            color: #123C3A; 
        }
        .login-card p.sub-title { 
            text-align: center; 
            color: #64748b; 
            font-size: 14px; 
            margin-bottom: 35px; 
        }

        /* Form Elements */
        .form-group { margin-bottom: 24px; position: relative; }
        .form-group label { 
            display: block; 
            font-size: 13px; 
            font-weight: 600; 
            color: #334155; 
            margin-bottom: 8px; 
        }

        .input-wrap { position: relative; }
        .input-wrap > i { 
            position: absolute; 
            left: 18px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #94a3b8; 
            font-size: 16px;
        }
        
        .form-input {
            width: 100%;
            padding: 16px 18px 16px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 15px;
            color: #123C3A;
            background: #f8fafc;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #3FBFAD;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(63, 191, 173, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            font-size: 16px;
        }

        /* Remember & Forgot */
        .row-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }
        .checkbox-wrap input { width: 16px; height: 16px; accent-color: #3FBFAD; cursor: pointer; }

        .forgot-link {
            color: #3FBFAD;
            text-decoration: none;
            font-weight: 600;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* Submit Button */
        .btn-signin {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #3FBFAD, #123C3A);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(63, 191, 173, 0.25);
        }
        .btn-signin:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(63, 191, 173, 0.4);
        }
        .btn-signin:active { transform: translateY(0); }

        /* Alerts */
        .alert-box {
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FCA5A5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border-color: #A7F3D0;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card { width: 90%; padding: 40px 25px; }
        }
    </style>
</head>
<body>

    <!-- Floating Background Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="login-card">
        <!-- Logo -->
        <div class="brand-logo">
            <i class="fa-solid fa-heart-pulse"></i>
        </div>

        <h2>ShubhHMS</h2>
        <p class="sub-title">Advanced Hospital Management System</p>

        <!-- Alerts -->
        @if(session('status'))
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-box">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login.attempt') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-input" value="{{ old('email', 'admin@dentalhrms.com') }}" placeholder="Enter your email" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
                </div>
            </div>

            <div class="row-options">
                <label class="checkbox-wrap">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="{{ route('recovery.find') }}" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-signin">
                Sign In <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>