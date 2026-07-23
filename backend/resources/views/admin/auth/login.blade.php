<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Indian Panorama CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.webp') }}" type="image/x-icon">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.05);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(139, 92, 246, 0.05);
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #ffffff;
            /* border: 1px solid #e2e8f0; */
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 8px 24px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #007bff 0%, #6f42c1 100%);
            padding: 32px 36px 28px;
            text-align: center;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.5), transparent);
        }

        .login-logo {
            max-width: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
            margin-bottom: 8px;
        }

        .login-logo-text {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .login-logo-sub {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            font-weight: 400;
        }

        .login-body {
            padding: 32px 36px 36px;
        }

        .login-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            padding: 10px 14px;
            color: #0f172a;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .input-group-text {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #94a3b8;
            font-size: 14px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #2563eb;
        }

        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            padding: 11px 20px;
            width: 100%;
            cursor: pointer;
            transition: all .2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            color: #fff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            padding: 0 36px 28px;
            font-size: 12px;
            color: #94a3b8;
        }

        .invalid-feedback.d-block {
            font-size: 12px;
            color: #ef4444;
            margin-top: 4px;
        }

    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-header">
                @php $logo = public_path('indian-panorama-logo.png'); @endphp
                @if(file_exists($logo))
                <img src="{{ asset('indian-panorama-logo.png') }}" alt="Indian Panorama" class="login-logo">
                @else
                <div class="login-logo-text">Indian Panorama</div>
                @endif
                <div class="login-logo-sub">CRM Admin Panel</div>
            </div>

            <div class="login-body">
                <div class="login-title">Welcome back</div>
                <p class="login-subtitle">Sign in to your admin account to continue.</p>

                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf

                    @if($errors->any())
                    <div class="alert alert-danger mb-3 p-3" style="border-radius:10px; border:none; background:rgba(239,68,68,.08); border-left:3px solid #ef4444; font-size:13px; color:#991b1b;">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach($errors->all() as $error)
                        {{ $error }}
                        @endforeach
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-envelope me-1 opacity-75"></i> Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="admin@example.com"
                                autofocus
                                required>
                        </div>
                        @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock me-1 opacity-75"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password"
                                class="form-control"
                                name="password"
                                placeholder="••••••••"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In to Dashboard
                    </button>
                </form>
            </div>

            <div class="login-footer">
                &copy; {{ date('Y') }} Indian Panorama. All rights reserved.
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>