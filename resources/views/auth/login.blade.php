<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servixa - Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #F8F7FF; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(107,33,168,0.08); padding: 2.5rem; width: 100%; max-width: 400px; }
        .logo { text-align: center; margin-bottom: 1.5rem; }
        .logo h1 { color: #6B21A8; font-size: 1.75rem; }
        .logo p { color: #6B7280; font-size: 0.875rem; margin-top: 0.25rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-size: 0.875rem; font-weight: 500; color: #1F2937; margin-bottom: 0.375rem; }
        input { width: 100%; padding: 0.625rem 0.75rem; border: 1px solid #DDD6FE; border-radius: 12px; font-size: 0.875rem; outline: none; transition: border-color 0.2s; }
        input:focus { border-color: #6B21A8; }
        .btn { width: 100%; padding: 0.625rem; background: #6B21A8; color: #fff; border: none; border-radius: 12px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #7C3AED; }
        .error { color: #DC2626; font-size: 0.75rem; margin-top: 0.25rem; }
        .errors-box { background: #FEE2E2; color: #DC2626; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <h1>Servixa</h1>
            <p>Admin Dashboard</p>
        </div>

        @if($errors->any())
            <div class="errors-box">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
