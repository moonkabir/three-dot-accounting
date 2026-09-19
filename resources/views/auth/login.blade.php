<x-guest-layout>
<style>
    /* ─── Full-page split layout ─────────────────────────────── */
    .auth-wrapper {
        display: flex;
        min-height: 100vh;
        background: #080812;
    }

    /* ─── LEFT PANEL: Animated brand ───────────────────────── */
    .auth-brand {
        flex: 1;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: 3rem 4rem;
        overflow: hidden;
        background: linear-gradient(135deg, #0f0f23 0%, #1a0a2e 40%, #0d1a3a 100%);
    }

    .auth-brand::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 20% 50%, rgba(99,102,241,0.25) 0%, transparent 70%),
            radial-gradient(ellipse 60% 40% at 80% 20%, rgba(139,92,246,0.2) 0%, transparent 60%),
            radial-gradient(ellipse 50% 50% at 50% 90%, rgba(59,130,246,0.15) 0%, transparent 60%);
        animation: pulseGlow 6s ease-in-out infinite alternate;
    }

    @keyframes pulseGlow {
        0%   { opacity: 0.7; transform: scale(1); }
        100% { opacity: 1;   transform: scale(1.03); }
    }

    /* Floating orbs */
    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: floatOrb 10s ease-in-out infinite alternate;
        pointer-events: none;
    }
    .orb-1 { width: 350px; height: 350px; background: rgba(99,102,241,0.18); top: -80px; left: -80px; animation-delay: 0s; }
    .orb-2 { width: 280px; height: 280px; background: rgba(139,92,246,0.15); bottom: -60px; right: -60px; animation-delay: -3s; }
    .orb-3 { width: 200px; height: 200px; background: rgba(59,130,246,0.12); top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: -6s; }

    @keyframes floatOrb {
        0%   { transform: translate(0,0) scale(1); }
        100% { transform: translate(20px,30px) scale(1.1); }
    }

    .brand-content { position: relative; z-index: 1; }

    .brand-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 3rem;
    }
    .brand-logo-icon {
        width: 48px; height: 48px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 0 30px rgba(99,102,241,0.4);
    }
    .brand-logo-text {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f1f5f9;
        letter-spacing: -0.02em;
    }
    .brand-logo-text span { color: #818cf8; }

    .brand-tagline {
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1.15;
        color: #f1f5f9;
        letter-spacing: -0.03em;
        margin-bottom: 1.5rem;
    }
    .brand-tagline .highlight {
        background: linear-gradient(90deg, #6366f1, #a78bfa, #38bdf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .brand-desc {
        font-size: 1rem;
        color: #94a3b8;
        line-height: 1.7;
        max-width: 400px;
        margin-bottom: 3rem;
    }

    .brand-features {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
        color: #cbd5e1;
    }
    .feature-icon {
        width: 32px; height: 32px;
        background: rgba(99,102,241,0.15);
        border: 1px solid rgba(99,102,241,0.3);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    /* ─── RIGHT PANEL: Login card ───────────────────────────── */
    .auth-form-panel {
        width: 480px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: rgba(255,255,255,0.015);
        border-left: 1px solid rgba(255,255,255,0.06);
    }

    .auth-card {
        width: 100%;
        max-width: 400px;
        background: rgba(255,255,255,0.04);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow:
            0 0 0 1px rgba(99,102,241,0.05),
            0 32px 80px rgba(0,0,0,0.4),
            inset 0 1px 0 rgba(255,255,255,0.08);
        animation: fadeSlideIn 0.5s ease-out;
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .card-header {
        margin-bottom: 2rem;
        text-align: center;
    }
    .card-header h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #f1f5f9;
        letter-spacing: -0.02em;
        margin-bottom: 0.4rem;
    }
    .card-header p {
        font-size: 0.875rem;
        color: #64748b;
    }

    /* Form fields */
    .form-group {
        margin-bottom: 1.2rem;
    }
    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #94a3b8;
        margin-bottom: 0.5rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        color: #f1f5f9;
        font-size: 0.9rem;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        outline: none;
    }
    .form-input::placeholder { color: #475569; }
    .form-input:focus {
        border-color: rgba(99,102,241,0.6);
        background: rgba(99,102,241,0.06);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }
    .form-input:hover:not(:focus) {
        border-color: rgba(255,255,255,0.18);
    }

    .form-error {
        font-size: 0.78rem;
        color: #f87171;
        margin-top: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .form-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        margin-top: 0.5rem;
    }
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #64748b;
        cursor: pointer;
        user-select: none;
    }
    .checkbox-label input[type="checkbox"] {
        width: 16px; height: 16px;
        accent-color: #6366f1;
        cursor: pointer;
    }
    .forgot-link {
        font-size: 0.85rem;
        color: #818cf8;
        text-decoration: none;
        transition: color 0.2s;
    }
    .forgot-link:hover { color: #a78bfa; }

    /* Submit button */
    .btn-primary {
        width: 100%;
        padding: 0.85rem 1.5rem;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 600;
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
        box-shadow: 0 4px 24px rgba(99,102,241,0.35);
        letter-spacing: 0.01em;
        position: relative;
        overflow: hidden;
    }
    .btn-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 32px rgba(99,102,241,0.45); }
    .btn-primary:hover::before { opacity: 1; }
    .btn-primary:active { transform: translateY(0); }

    .card-footer {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }
    .card-footer a {
        color: #818cf8;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }
    .card-footer a:hover { color: #a78bfa; }

    /* Status message */
    .auth-status {
        padding: 0.75rem 1rem;
        background: rgba(52,211,153,0.1);
        border: 1px solid rgba(52,211,153,0.2);
        border-radius: 10px;
        color: #34d399;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
    }

    /* ─── Responsive ────────────────────────────────────────── */
    @media (max-width: 900px) {
        .auth-brand { display: none; }
        .auth-form-panel {
            width: 100%;
            border-left: none;
            background: #080812;
        }
    }
</style>

<div class="auth-wrapper">

    {{-- LEFT: Brand Panel --}}
    <div class="auth-brand">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="brand-content">
            <div class="brand-logo">
                <div class="brand-logo-icon">💠</div>
                <div class="brand-logo-text">Three<span>Dot</span> ERP</div>
            </div>

            <h2 class="brand-tagline">
                Enterprise Finance,<br>
                <span class="highlight">Simplified.</span>
            </h2>

            <p class="brand-desc">
                Manage your entire business from one intelligent platform — accounting, sales, procurement, inventory, and HR all in sync.
            </p>

            <div class="brand-features">
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <span>Real-time financial analytics & reporting</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <span>Bank-grade security with role-based access</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <span>Automated invoicing, payroll & reconciliation</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🌐</div>
                    <span>Multi-currency, multi-branch support</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Login Form --}}
    <div class="auth-form-panel">
        <div class="auth-card">

            <div class="card-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your ERP account</p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

            <form id="login-form" method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input
                        id="email"
                        class="form-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@company.com"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    @error('email')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        id="password"
                        class="form-input"
                        type="password"
                        name="password"
                        placeholder="••••••••••"
                        required
                        autocomplete="current-password"
                    />
                    @error('password')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="form-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember_me">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button id="login-submit" type="submit" class="btn-primary">
                    Sign In to ERP
                </button>

                @if (Route::has('register'))
                    <div class="card-footer">
                        Don't have an account?
                        <a href="{{ route('register') }}">Create one</a>
                    </div>
                @endif
            </form>

        </div>
    </div>

</div>
</x-guest-layout>
