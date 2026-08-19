<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Account · DentaCare HRMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { margin:0; padding:0; font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #123C3A, #17847A); height:100vh; display:flex; align-items:center; justify-content:center; }
        .recover-card { background: #fff; width: 420px; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .logo { text-align: center; margin-bottom: 20px; color: #123C3A; font-size: 24px; font-weight: 700; }
        .logo i { color: #3FBFAD; }
        h2 { text-align: center; color: #333; font-size: 20px; margin-bottom: 10px; }
        .sub-text { text-align: center; color: #64748b; font-size: 14px; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; }
        .input-icon { position: relative; }
        .input-icon > i { position: absolute; left: 15px; top: 13px; color: #94a3b8; }
        .input-icon input { width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border 0.2s; }
        .input-icon input:focus { outline: none; border-color: #3FBFAD; }
        
        /* Eye Toggle CSS */
        .toggle-password { position: absolute; right: 15px; top: 13px; color: #94a3b8; cursor: pointer; }
        .password-wrap input { padding-right: 40px !important; } /* Make room for the eye icon */
        
        .btn-primary { width: 100%; padding: 13px; background: #3FBFAD; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { background: #17847A; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #64748b; text-decoration: none; font-size: 13px; }
        .back-link a:hover { color: #3FBFAD; }
        .alert { background: #FEE2E2; color: #B91C1C; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        .success { background: #D1FAE5; color: #065F46; }
    </style>
</head>
<body>

<div class="recover-card">
    <div class="logo"><i class="fa-solid fa-tooth"></i> DentaCare HRMS</div>

    @if(session('status'))
        <div class="alert success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    @if($step == 1)
        <h2>Forgot Password?</h2>
        <p class="sub-text">Enter your Email or Username to find your account.</p>
        <form action="{{ route('recovery.find.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Email or Username</label>
                <div class="input-icon">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="identifier" required value="{{ old('identifier') }}" placeholder="e.g. admin@dentalhrms.com">
                </div>
            </div>
            <button type="submit" class="btn-primary">Find Account</button>
        </form>

    @elseif($step == 2)
        <h2>Security Question</h2>
        <p class="sub-text">{{ $question }}</p>
        <form action="{{ route('recovery.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <div class="form-group">
                <label>Your Answer</label>
                <div class="input-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                    <input type="text" name="answer" required placeholder="Enter your security answer">
                </div>
            </div>
            <button type="submit" class="btn-primary">Verify Answer</button>
        </form>

    @elseif($step == 3)
        <h2>Reset Password</h2>
        <p class="sub-text">Enter your new password below.</p>
        <form action="{{ route('recovery.reset') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <div class="form-group">
                <label>New Password</label>
                <div class="input-icon password-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="newPass" name="password" required placeholder="Min 8 characters">
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('newPass', this)"></i>
                </div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-icon password-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="confirmPass" name="password_confirmation" required placeholder="Re-type password">
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('confirmPass', this)"></i>
                </div>
            </div>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
    @endif

    <div class="back-link">
        <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
    </div>
</div>

<script>
    // Global Toggle Password Function
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
</script>

</body>
</html>