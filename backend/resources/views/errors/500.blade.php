<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | Indian Panorama CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: #1e293b;
            border-radius: 16px;
            padding: 60px 48px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 24px 64px rgba(0,0,0,0.4);
        }
        .error-code {
            font-size: 96px;
            font-weight: 800;
            color: #ef4444;
            line-height: 1;
            margin-bottom: 8px;
        }
        .error-title {
            font-size: 22px;
            font-weight: 600;
            color: #f1f5f9;
            margin-bottom: 12px;
        }
        .error-msg {
            color: #94a3b8;
            font-size: 15px;
            margin-bottom: 32px;
        }
        .btn-home {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s;
        }
        .btn-home:hover { background: #ea6c0a; color: #fff; }
        .logo-text {
            color: #2563eb;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 32px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <span class="logo-text"><i class="fas fa-globe-asia me-2"></i>Indian Panorama CRM</span>
        <div class="error-code">500</div>
        <div class="error-title">Internal Server Error</div>
        <p class="error-msg">Something went wrong on our end. Please try again later or contact the administrator.</p>
        <a href="{{ url('/admin') }}" class="btn-home">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
