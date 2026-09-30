<!DOCTYPE html>
<!--
============================================================================================
 Platform     : SIDQ Commerce Engine — Administration Authentication
 Powered By   : SIDQ Technology (সিদিক টেকনোলজি)
============================================================================================
-->
<html lang="bn" data-engine="SIDQ-Commerce" data-powered-by="SIDQ Technology">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="SIDQ Commerce Engine — Powered by SIDQ Technology">
    <meta name="author" content="SIDQ Technology">
    <title>অ্যাডমিন লগইন | {{ \App\Models\Setting::get('site_name', 'SIDQ MART') }} — Powered by SIDQ Technology</title>

    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Rubik', 'Hind Siliguri', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
            padding: 36px;
        }
        .btn-admin-login {
            background-color: #f13124;
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 12px;
            border: none;
            transition: background 0.2s ease;
        }
        .btn-admin-login:hover {
            background-color: #c9251a;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex p-3 rounded-circle bg-danger bg-opacity-10 text-danger mb-2">
                <i class="fas fa-user-shield fa-2x"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ \App\Models\Setting::get('site_name', 'SIDQ MART') }}</h4>
            <p class="text-muted small">অ্যাডমিন কন্ট্রোল প্যানেল</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2 small">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-bold">অ্যাডমিন ইমেইল</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', 'admin@sidqmart.com') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-bold">পাসওয়ার্ড</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label small" for="remember">মনে রাখুন</label>
                </div>
                <span class="small text-muted">ডিফল্ট: admin123</span>
            </div>

            <button type="submit" class="btn btn-admin-login w-100">
                <i class="fas fa-sign-in-alt me-1"></i> লগইন করুন
            </button>
        </form>

        <div class="text-center mt-4 border-top pt-3">
            <a href="{{ route('home') }}" class="small text-decoration-none text-muted d-block mb-2">
                <i class="fas fa-arrow-left me-1"></i> প্রধান ওয়েবসাইটে ফিরে যান
            </a>
            <div class="text-muted" style="font-size: 11px;">
                Powered by <strong class="text-dark">SIDQ Technology</strong>
            </div>
        </div>
    </div>
</body>
</html>
