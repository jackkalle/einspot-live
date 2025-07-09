<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Einspot</title>
    {{-- Basic styles for centering, can be replaced by Tailwind/Bootstrap later --}}
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: sans-serif; background-color: #f3f4f6; margin: 0; }
        .container { background-color: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input[type="email"], input[type="password"] { width: calc(100% - 1.2rem); padding: 0.6rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 0.25rem; }
        input[type="checkbox"] { margin-right: 0.5rem; }
        button { background-color: #ef4444; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.25rem; cursor: pointer; width: 100%; font-size: 1rem; }
        button:hover { background-color: #dc2626; }
        .errors { background-color: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.25rem; margin-bottom: 1rem; list-style-type: none;}
        .errors li { margin-bottom: 0.25rem; }
        .form-group { margin-bottom: 1rem; }
        .form-footer { margin-top: 1rem; text-align: center; }
        .form-footer a { color: #ef4444; text-decoration: none; }
        .form-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>

        @if ($errors->any())
            <ul class="errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="remember" style="display: inline-flex; align-items: center;">
                    <input id="remember" type="checkbox" name="remember">
                    <span style="margin-left: .5rem">Remember me</span>
                </label>
            </div>

            <div>
                <button type="submit">
                    Login
                </button>
            </div>
            <div class="form-footer">
                <p>
                    <a href="#">Forgot Your Password?</a> {{-- Placeholder --}}
                </p>
                <p>
                    Don't have an account? <a href="{{ route('register') }}">Register here</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
