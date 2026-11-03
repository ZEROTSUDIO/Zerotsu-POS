<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - {{ config('app.name', 'Laravel Starter') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        :root {
            --marble-white: #f8f9fa;
            --marble-cream: #fdfbf7;
            --marble-grey: #e8e6e3;
            --accent-gold: #c9a961;
            --accent-dark: #2c3e50;
            --text-primary: #1a1a1a;
            --text-secondary: #6c757d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .auth-container {
            width: 100%;
            max-width: 1200px;
            margin: 2rem;
        }

        .auth-card {
            background: var(--marble-cream);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.5);
            position: relative;
        }

        .auth-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 30%,
                    rgba(201, 169, 97, 0.1) 0%,
                    transparent 50%),
                radial-gradient(circle at 80% 70%,
                    rgba(44, 62, 80, 0.05) 0%,
                    transparent 50%);
            pointer-events: none;
        }

        .marble-texture {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.03) 2px,
                    rgba(255, 255, 255, 0.03) 4px),
                repeating-linear-gradient(-45deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 0, 0, 0.02) 2px,
                    rgba(0, 0, 0, 0.02) 4px);
            opacity: 0.5;
            pointer-events: none;
        }

        .auth-split {
            display: flex;
            min-height: 600px;
        }

        .auth-visual {
            flex: 1;
            background: linear-gradient(135deg,
                    var(--accent-dark) 0%,
                    #34495e 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .auth-visual::before {
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle,
                    rgba(201, 169, 97, 0.2) 0%,
                    transparent 70%);
            border-radius: 50%;
            top: -100px;
            right: -100px;
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.2;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.3;
            }
        }

        .visual-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
        }

        .visual-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3rem;
            color: var(--accent-gold);
        }

        .visual-content h2 {
            font-size: 2rem;
            font-weight: 300;
            margin-bottom: 1rem;
            letter-spacing: 1px;
        }

        .visual-content p {
            font-size: 1rem;
            opacity: 0.8;
            line-height: 1.6;
        }

        .auth-form-section {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo {
            font-size: 2rem;
            font-weight: 300;
            color: var(--accent-dark);
            letter-spacing: 2px;
            margin-bottom: 0.5rem;
        }

        .logo-accent {
            color: var(--accent-gold);
            font-weight: 600;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 300;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .form-floating {
            margin-bottom: 1.25rem;
        }

        .form-control {
            border: 2px solid var(--marble-grey);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            background: white;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 4px rgba(201, 169, 97, 0.1);
            background: white;
        }

        .form-floating>label {
            padding: 1rem 1.25rem;
            color: var(--text-secondary);
        }

        .form-check {
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid var(--marble-grey);
            border-radius: 6px;
        }

        .form-check-input:checked {
            background-color: var(--accent-gold);
            border-color: var(--accent-gold);
        }

        .btn-primary {
            background: linear-gradient(135deg,
                    var(--accent-gold) 0%,
                    #b8935a 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(201, 169, 97, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 169, 97, 0.4);
            background: linear-gradient(135deg,
                    #d4b574 0%,
                    var(--accent-gold) 100%);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--marble-grey);
        }

        .divider span {
            padding: 0 1rem;
        }

        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn-social {
            background: white;
            border: 2px solid var(--marble-grey);
            border-radius: 12px;
            padding: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            color: var(--text-primary);
        }

        .btn-social:hover {
            border-color: var(--accent-gold);
            background: rgba(201, 169, 97, 0.05);
            transform: translateY(-2px);
        }

        .btn-social i {
            font-size: 1.2rem;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: var(--accent-dark);
        }

        .forgot-password {
            text-align: right;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
        }

        .forgot-password a {
            color: var(--accent-gold);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--accent-dark);
        }

        .page-indicator {
            display: none;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--marble-grey);
            transition: all 0.3s ease;
        }

        .indicator-dot.active {
            background: var(--accent-gold);
            width: 24px;
            border-radius: 4px;
        }

        @media (max-width: 992px) {
            .auth-split {
                flex-direction: column;
            }

            .auth-visual {
                min-height: 300px;
            }

            .auth-form-section {
                padding: 2rem;
            }
        }

        @media (max-width: 576px) {
            .auth-container {
                margin: 1rem;
            }

            .social-login {
                grid-template-columns: 1fr;
            }

            .logo {
                font-size: 1.5rem;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .page-indicator {
                display: flex;
            }
        }

        .hidden {
            display: none;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="marble-texture"></div>

            <div class="auth-split">
                <!-- Visual Side -->
                <div class="auth-visual">
                    <div class="visual-content">
                        <div class="visual-icon">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h2>Admin Dashboard</h2>
                        <p>Manage your products, content, and business.</p>
                    </div>
                </div>

                <!-- Form Side -->
                <div class="auth-form-section">
                    <!-- Logo -->
                    <div class="logo-section">
                        <div class="logo">
                            <span class="logo-accent">{{ config('app.name', 'Laravel') }}</span> PORTAL
                        </div>
                        <p class="subtitle">Admin Portal</p>
                    </div>

                    <!-- Login Form -->
                    <div id="loginForm" class="auth-form">
                        <h3 class="form-title">Admin Login</h3>
                        <p class="form-subtitle">
                            Enter your credentials to continue
                        </p>

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="loginEmail" name="email" placeholder="name@example.com"
                                    value="{{ old('email') }}" required autofocus />
                                <label for="loginEmail">Email Address</label>
                            </div>

                            <div class="form-floating">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="loginPassword" name="password" placeholder="Password" required />
                                <label for="loginPassword">Password</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
