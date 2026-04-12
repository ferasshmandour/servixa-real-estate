<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Servixa Digital Curator</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Noto+Kufi+Arabic:wght@300;400;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=block">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Inter', 'Noto Kufi Arabic', sans-serif;
            background-color: #0a0e14;
        }
        html[lang="ar"] body { font-family: 'Noto Kufi Arabic', 'Inter', sans-serif; }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
            line-height: 1;
        }

        /* ── Page shell ── */
        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* ── Mesh background blobs ── */
        @keyframes meshMove {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(10%, 15%) scale(1.1); }
            66%  { transform: translate(-10%, 5%) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }
        .mesh-blob {
            position: fixed;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.4;
            z-index: 0;
            pointer-events: none;
            animation: meshMove 25s ease-in-out infinite;
        }
        .mesh-blob-1 { background: #6b21a8; top: -20%; left: -10%; }
        .mesh-blob-2 { background: #500088; bottom: -20%; right: -10%; animation-delay: -5s; }
        .mesh-blob-3 { background: #121c2a; top: 30%; right: 20%; animation-delay: -10s; }

        /* ── Floating particles ── */
        @keyframes floatParticle {
            0%   { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0.1; }
            50%  { transform: translateY(-100px) translateX(20px) rotate(180deg); opacity: 0.3; }
            100% { transform: translateY(-200px) translateX(-10px) rotate(360deg); opacity: 0; }
        }
        .particle {
            position: fixed;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.05);
            pointer-events: none;
            z-index: 0;
            animation: floatParticle 40s linear infinite;
        }

        /* ── Depth rings ── */
        .depth-rings {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .depth-ring {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.015);
        }

        /* ── Language switcher ── */
        .lang-switcher {
            position: fixed;
            top: 2rem;
            inset-inline-end: 2rem;
            z-index: 100;
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 9999px;
            padding: 0.25rem;
            gap: 0;
        }
        .lang-btn {
            padding: 0.375rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .lang-btn.active { background: rgba(255,255,255,0.12); color: white; }
        .lang-btn.inactive { background: transparent; color: rgba(255,255,255,0.5); }
        .lang-btn.inactive:hover { color: white; }

        /* ── Main grid ── */
        .main-grid {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1fr;
            align-items: center;
            gap: 3rem;
            position: relative;
            z-index: 1;
        }
        @media (min-width: 1024px) {
            .main-grid { grid-template-columns: 1fr 1fr; }
        }

        /* ── Left branding ── */
        .brand-col {
            display: none;
            flex-direction: column;
            gap: 2rem;
        }
        @media (min-width: 1024px) {
            .brand-col { display: flex; }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-24px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .brand-col { animation: fadeInLeft 0.7s cubic-bezier(0.22,1,0.36,1) both; }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .brand-logo-icon {
            width: 3rem; height: 3rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #500088, #8a4cfc);
            box-shadow: 0 8px 24px rgba(107,33,168,0.3);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-logo-text {
            color: white;
            font-size: 1.875rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .brand-headline {
            color: white;
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -0.025em;
        }
        .brand-headline-accent { color: #d2bbff; }
        .brand-subtext {
            color: rgba(255,255,255,0.55);
            font-size: 1.125rem;
            font-weight: 300;
            line-height: 1.7;
            max-width: 28rem;
            margin-top: 1rem;
        }
        .feature-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            padding-top: 1.5rem;
        }
        .feature-card {
            padding: 1.25rem;
            border-radius: 1rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            transition: border-color 0.3s, transform 0.3s;
        }
        .feature-card:hover {
            border-color: rgba(138,76,252,0.3);
            transform: translateY(-2px);
        }
        .feature-icon { color: #8a4cfc; margin-bottom: 0.75rem; display: block; }
        .feature-title { color: white; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem; }
        .feature-desc  { color: rgba(255,255,255,0.35); font-size: 0.75rem; line-height: 1.5; }

        /* ── Right: login card ── */
        .card-col {
            display: flex;
            justify-content: center;
        }
        @media (min-width: 1024px) {
            .card-col { justify-content: flex-end; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .glass-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            border-radius: 2rem;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.7s cubic-bezier(0.22,1,0.36,1) 0.15s both;
        }
        .glass-card-glow {
            position: absolute;
            top: -6rem; right: -6rem;
            width: 12rem; height: 12rem;
            border-radius: 50%;
            background: rgba(107,33,168,0.2);
            filter: blur(80px);
            pointer-events: none;
        }
        .card-inner { position: relative; z-index: 10; }

        /* ── Mobile logo (shows only on small screens) ── */
        .mobile-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 1024px) {
            .mobile-logo { display: none; }
        }
        .mobile-logo-icon {
            width: 2.5rem; height: 2.5rem;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #500088, #8a4cfc);
            display: flex; align-items: center; justify-content: center;
        }

        /* ── Card header ── */
        .card-header { margin-bottom: 2rem; }
        .card-title { color: white; font-size: 1.5rem; font-weight: 700; margin-bottom: 0.375rem; }
        .card-subtitle { color: rgba(255,255,255,0.45); font-size: 0.875rem; }

        /* ── Error box ── */
        .error-box {
            background: rgba(186,26,26,0.1);
            border: 1px solid rgba(186,26,26,0.25);
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            margin-bottom: 1.5rem;
            color: rgba(255,155,155,0.9);
            font-size: 0.8125rem;
        }
        .error-box li { list-style: none; }

        /* ── Form ── */
        .form-stack { display: flex; flex-direction: column; gap: 1.25rem; }
        .field-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .field-label {
            font-size: 0.6875rem;
            font-weight: 600;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding-inline-start: 0.25rem;
        }
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute;
            inset-inline-start: 1rem;
            top: 50%; transform: translateY(-50%);
            color: rgba(255,255,255,0.25);
            font-size: 1.25rem;
            pointer-events: none;
        }
        .field-input {
            width: 100%;
            height: 56px;
            padding-inline-start: 3rem;
            padding-inline-end: 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.75rem;
            color: white;
            font-size: 0.875rem;
            font-family: inherit;
            outline: none;
            transition: background 0.3s, border-color 0.3s, box-shadow 0.3s;
        }
        .field-input::placeholder { color: rgba(255,255,255,0.18); }
        .field-input:focus {
            background: rgba(255,255,255,0.08);
            border-color: #8a4cfc;
            box-shadow: 0 0 0 4px rgba(138,76,252,0.15);
        }
        .field-input.has-error {
            border-color: rgba(186,26,26,0.6);
            box-shadow: 0 0 0 4px rgba(186,26,26,0.1);
        }
        .pass-toggle {
            position: absolute;
            inset-inline-end: 1rem;
            top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,0.25);
            font-size: 1.25rem;
            transition: color 0.2s;
            display: flex; align-items: center;
        }
        .pass-toggle:hover { color: rgba(255,255,255,0.7); }

        /* ── Remember me ── */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.25rem 0;
        }
        .remember-check {
            width: 18px; height: 18px;
            border-radius: 5px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.05);
            accent-color: #6b21a8;
            cursor: pointer;
            flex-shrink: 0;
        }
        .remember-label {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.55);
            cursor: pointer;
            user-select: none;
        }

        /* ── Submit button ── */
        .submit-btn {
            width: 100%;
            height: 56px;
            background: #6b21a8;
            color: white;
            font-weight: 600;
            font-size: 0.9375rem;
            font-family: inherit;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            box-shadow: 0 4px 20px rgba(107,33,168,0.35);
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        }
        .submit-btn:hover {
            background: #7c3aed;
            box-shadow: 0 6px 28px rgba(107,33,168,0.55);
        }
        .submit-btn:active { transform: scale(0.98); }

        /* ── Card footer ── */
        .card-footer {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex; flex-direction: column; align-items: center; gap: 0.875rem;
        }
        .status-pill {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            background: rgba(34,197,94,0.08);
            border: 1px solid rgba(34,197,94,0.2);
        }
        .status-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #22c55e;
            flex-shrink: 0;
        }
        .status-text {
            font-size: 0.625rem;
            font-weight: 700;
            color: #22c55e;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .copyright {
            font-size: 0.625rem;
            color: rgba(255,255,255,0.2);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Mesh blobs --}}
    <div class="mesh-blob mesh-blob-1"></div>
    <div class="mesh-blob mesh-blob-2"></div>
    <div class="mesh-blob mesh-blob-3"></div>

    {{-- Particles --}}
    <div class="particle w-12 h-12" style="top:80%;left:15%;border-radius:0.75rem;animation-delay:0s;"></div>
    <div class="particle w-8 h-8"   style="top:60%;left:85%;border-radius:50%;animation-delay:-10s;"></div>
    <div class="particle w-16 h-16" style="top:20%;left:40%;border-radius:1.5rem;animation-delay:-20s;"></div>
    <div class="particle w-6 h-6"   style="top:40%;left:10%;border-radius:0.5rem;animation-delay:-5s;"></div>
    <div class="particle w-10 h-10" style="top:10%;left:75%;border-radius:50%;animation-delay:-15s;"></div>

    {{-- Depth rings --}}
    <div class="depth-rings">
        <div class="depth-ring" style="width:80vw;height:80vw;"></div>
        <div class="depth-ring" style="width:60vw;height:60vw;"></div>
    </div>

    {{-- Language switcher --}}
    <div class="lang-switcher">
        <a href="{{ route('locale.switch', 'en') }}"
           class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : 'inactive' }}">EN</a>
        <a href="{{ route('locale.switch', 'ar') }}"
           class="lang-btn {{ app()->getLocale() === 'ar' ? 'active' : 'inactive' }}">AR</a>
    </div>

    {{-- Main two-column grid --}}
    <main class="main-grid">

        {{-- ── LEFT: Branding ── --}}
        <div class="brand-col">
            <div>
                {{-- Logo --}}
                <div class="brand-logo" style="margin-bottom:1.5rem;">
                    <div class="brand-logo-icon">
                        <span class="material-symbols-outlined" style="color:white;font-size:1.75rem;font-variation-settings:'FILL' 1;">deployed_code</span>
                    </div>
                    <span class="brand-logo-text">Servixa</span>
                </div>

                {{-- Headline --}}
                <h1 class="brand-headline">
                    {{ __('auth.tagline_line1') }}<br>
                    <span class="brand-headline-accent">{{ __('auth.tagline_line2') }}</span>
                </h1>
                <p class="brand-subtext">{{ __('auth.tagline_desc') }}</p>
            </div>

            {{-- Feature cards --}}
            <div class="feature-cards">
                <div class="feature-card">
                    <span class="material-symbols-outlined feature-icon">verified_user</span>
                    <p class="feature-title">{{ __('auth.feature1_title') }}</p>
                    <p class="feature-desc">{{ __('auth.feature1_desc') }}</p>
                </div>
                <div class="feature-card">
                    <span class="material-symbols-outlined feature-icon">bolt</span>
                    <p class="feature-title">{{ __('auth.feature2_title') }}</p>
                    <p class="feature-desc">{{ __('auth.feature2_desc') }}</p>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Login card ── --}}
        <div class="card-col" x-data="{ showPass: false }">
            <div class="glass-card">
                <div class="glass-card-glow"></div>
                <div class="card-inner">

                    {{-- Mobile logo --}}
                    <div class="mobile-logo">
                        <div class="mobile-logo-icon">
                            <span class="material-symbols-outlined" style="color:white;font-size:1.5rem;font-variation-settings:'FILL' 1;">deployed_code</span>
                        </div>
                    </div>

                    {{-- Header --}}
                    <div class="card-header">
                        <h2 class="card-title">{{ __('auth.welcome_back') }}</h2>
                        <p class="card-subtitle">{{ __('auth.login_subtitle') }}</p>
                    </div>

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="error-box">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('admin.login.submit') }}" class="form-stack">
                        @csrf

                        {{-- Email --}}
                        <div class="field-group">
                            <label class="field-label" for="email">{{ __('auth.email_label') }}</label>
                            <div class="field-wrap">
                                <span class="material-symbols-outlined field-icon">mail</span>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    autofocus
                                    required
                                    placeholder="admin@servixa.com"
                                    class="field-input {{ $errors->has('email') ? 'has-error' : '' }}"
                                >
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="field-group">
                            <label class="field-label" for="password">{{ __('auth.password_label') }}</label>
                            <div class="field-wrap">
                                <span class="material-symbols-outlined field-icon">lock</span>
                                <input
                                    :type="showPass ? 'text' : 'password'"
                                    id="password"
                                    name="password"
                                    required
                                    placeholder="••••••••"
                                    class="field-input {{ $errors->has('password') ? 'has-error' : '' }}"
                                    style="padding-inline-end:3rem;"
                                >
                                <button type="button" class="pass-toggle" x-on:click="showPass = !showPass" tabindex="-1">
                                    <span class="material-symbols-outlined" x-text="showPass ? 'visibility_off' : 'visibility'">visibility</span>
                                </button>
                            </div>
                        </div>

                        {{-- Remember --}}
                        <div class="remember-row">
                            <input type="checkbox" name="remember" id="remember" class="remember-check">
                            <label for="remember" class="remember-label">{{ __('auth.remember_me') }}</label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="submit-btn">
                            <span>{{ __('auth.sign_in') }}</span>
                            <span class="material-symbols-outlined" style="font-size:1.25rem;">arrow_forward</span>
                        </button>
                    </form>

                    {{-- Footer --}}
                    <div class="card-footer">
                        <div class="status-pill">
                            <span class="status-dot"></span>
                            <span class="status-text">{{ __('auth.status_ok') }}</span>
                        </div>
                        <p class="copyright">{{ __('auth.copyright') }}</p>
                    </div>

                </div>
            </div>
        </div>

    </main>
</div>
</body>
</html>
