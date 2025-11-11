<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sabify - Login</title>
    <link rel="icon" href="{{ asset('storage/images/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #afb6b0 0%, #2faa4f 50%, #2faa4f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        .bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: float-shapes 5s infinite ease-in-out;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.4);
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 15%;
            animation-delay: -5s;
            border-radius: 20%;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: -10s;
        }
        
        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 10%;
            right: 25%;
            animation-delay: -7s;
            border-radius: 0;
            transform: rotate(45deg);
        }
        
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.8) 3px, transparent 3px),
                        radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.6) 2px, transparent 2px),
                        radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.4) 2px, transparent 2px);
            background-size: 60px 60px, 90px 90px, 120px 120px;
            animation: particles-move 8s infinite linear;
        }
        
        @keyframes float-shapes {
            0%, 100% {
                transform: translateY(0px) rotate(0deg) scale(1);
                opacity: 0.6;
            }
            50% {
                transform: translateY(-80px) rotate(360deg) scale(1.5);
                opacity: 1;
            }
        }
        
        @keyframes particles-move {
            0% {
                background-position: 0% 0%, 0% 0%, 0% 0%;
            }
            100% {
                background-position: 100% 100%, -100% 100%, 50% -100%;
            }
        }
        
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 900px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .login-info {
            background: linear-gradient(135deg, #2faa4f 0%, #2faa4f 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
        }
        
        .login-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="40" cy="80" r="1.5" fill="%23ffffff" opacity="0.1"/></svg>');
        }
        
        .login-container {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo img {
            max-width: 180px;
            height: auto;
            margin-bottom: 16px;
        }
        
        .logo h1 {
            color: #2faa4f;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .logo p {
            color: #2faa4f;
            font-size: 16px;
            font-weight: 400;
        }
        
        .info-content h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .info-content p {
            font-size: 18px;
            line-height: 1.6;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .features {
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }
        
        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .feature-icon {
            width: 24px;
            height: 24px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 12px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #000000;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2faa4f;
            box-shadow: 0 0 0 3px rgba(47, 170, 79, 0.1);
        }
        
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2faa4f 0%, #2faa4f 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(47, 170, 79, 0.3);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2faa4f;
        }
        
        .forgot-password {
            color: #2faa4f;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #e53e3e;
        }
        
        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
            
            .login-info {
                display: none;
            }
            
            .login-container {
                padding: 40px 30px;
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="particles"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="login-wrapper">
        <div class="login-info">
            <div class="info-content">
                <h2>Welcome to Sabify</h2>
                <p>Your comprehensive retail/restaurant management solution designed to streamline operations and boost productivity.</p>
                
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">📊</div>
                        <span>Advanced Analytics & Reporting</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🛍️</div>
                        <span>Inventory Management</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">💳</div>
                        <span>Point of Sale Integration</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🔒</div>
                        <span>Secure & Reliable</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-container">
            <div class="logo">
                {{-- <img alt="Log in to Sabsoft" src="{{ asset('storage/images/logo-black.png') }}" /> --}}
                <h1>Sabify</h1>
                <p>Professional Retail Management System</p>
            </div>
        
        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Username</label>
                <input type="text" id="username" name="username" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a>
                @endif
            </div>
            
            <button type="submit" class="login-btn">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>