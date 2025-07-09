<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Einspot</title>
    {{-- Basic styles for centering, can be replaced by Tailwind/Bootstrap later --}}
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: sans-serif; background-color: #f3f4f6; margin: 0; padding: 1rem 0; }
        .container { background-color: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h1 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input[type="text"], input[type="email"], input[type="password"] { width: calc(100% - 1.2rem); padding: 0.6rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 0.25rem; }
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
        <h1>Register</h1>

        @if ($errors->any())
            <ul class="errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="firstName">First Name</label>
                <input id="firstName" type="text" name="firstName" value="{{ old('firstName') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input id="lastName" type="text" name="lastName" value="{{ old('lastName') }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <div>
                <button type="submit">
                    Register
                </button>
            </div>
            <div class="form-footer">
                <p>
                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
