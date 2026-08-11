<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Account · DentaCare HRMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .login-shell { min-height:100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(160deg, var(--clr-primary) 0%, #0B2624 100%); padding:20px; }
        .login-card { background: var(--clr-surface); border-radius: var(--radius-lg); box-shadow: 0 20px 50px rgba(0,0,0,0.25); padding:40px 36px; width:100%; max-width:400px; }
        .login-brand-title { font-family:'Outfit', sans-serif; font-size:1.2rem; font-weight:600; color: var(--clr-primary); }
        .login-sub { color: var(--clr-muted); font-size:0.86rem; margin-bottom:26px; }
        .login-card .btn-clinic { width:100%; justify-content:center; padding:12px; margin-top:6px; }
        .question-box { background: var(--clr-accent-soft); color: var(--clr-primary-light); border-radius: var(--radius-md); padding:14px 16px; margin-bottom:20px; font-size:0.92rem; font-weight:500; }
    </style>
</head>
<body>
<div class="login-shell">
    <div class="login-card">
        <div class="login-brand-title"><i class="fa-solid fa-tooth" style="color:#3FBFAD;"></i> Recover Access</div>
        <div class="login-sub">Step 2 of 3 — answer your security question</div>

        @if($errors->any())
            <div class="alert-clinic" style="background: var(--clr-warn-soft); color: var(--clr-warn);">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <div class="question-box">
            <i class="fa-solid fa-circle-question"></i> {{ $question }}
        </div>

        <form action="{{ route('recovery.verify') }}" method="POST">
            @csrf
            <label class="form-label-clinic">Your Answer</label>
            <input type="text" name="answer" class="form-control-clinic" required autofocus
                   placeholder="Type your answer" style="margin-bottom:18px;">

            <button type="submit" class="btn-clinic">
                <i class="fa-solid fa-arrow-right"></i> Verify Answer
            </button>
        </form>

        <div style="text-align:center; margin-top:20px;">
            <a href="{{ route('login') }}" style="color: var(--clr-muted); font-size:0.85rem; text-decoration:none;">
                <i class="fa-solid fa-arrow-left"></i> Back to login
            </a>
        </div>
    </div>
</div>
</body>
</html>