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

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        animation: floatOrb 10s ease-in-out infinite alternate;
        pointer-events: none;
    }
    .orb-1 { width: 350px; height: 350px; background: rgba(139,92,246,0.18);  top: -80px;  left: -80px; animation-delay: 0s; }
    .orb-2 { width: 280px; height: 280px; background: rgba(99,102,241,0.15);  bottom: -60px; right: -60px; animation-delay: -3s; }
    .orb-3 { width: 200px; height: 200px; background: rgba(59,130,246,0.12);  top: 50%;    left: 50%;   transform: translate(-50%,-50%); animation-delay: -6s; }

    @keyframes floatOrb {
        0%   { transform: translate(0, 0) scale(1); }
        100% { transform: translate(20px, 30px) scale(1.1); }
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
    .brand-logo-text { font-size: 1.25rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; }
    .brand-logo-text span { color: #818cf8; }

    .brand-tagline {
        font-size: 2.6rem;
        font-weight: 800;
        line-height: 1.15;
        color: #f1f5f9;
        letter-spacing: -0.03em;
        margin-bottom: 1.25rem;
    }
    .brand-tagline .highlight {
        background: linear-gradient(90deg, #8b5cf6, #a78bfa, #38bdf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .brand-desc {
        font-size: 1rem;
        color: #94a3b8;
        line-height: 1.7;
        max-width: 400px;
        margin-bottom: 2.5rem;
    }

    /* Steps list */
    .onboard-steps {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }
    .step-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .step-num {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 0 12px rgba(99,102,241,0.3);
    }
    .step-text {
        font-size: 0.875rem;
        color: #cbd5e1;
        line-height: 1.4;
    }
    .step-text strong { color: #e2e8f0; }

    /* ─── RIGHT PANEL: Register card ───────────────────────── */
    .auth-form-panel {
        width: 520px;
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
        max-width: 440px;
        background: rgba(255,255,255,0.04);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 24px;
        padding: 2.25rem 2.5rem;
        box-shadow:
            0 0 0 1px rgba(139,92,246,0.05),
            0 32px 80px rgba(0,0,0,0.4),
            inset 0 1px 0 rgba(255,255,255,0.08);
        animation: fadeSlideIn 0.5s ease-out;
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .card-header {
        margin-bottom: 1.75rem;
        text-align: center;
    }
    .card-header-icon {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.2));
        border: 1px solid rgba(99,102,241,0.3);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        margin: 0 auto 1rem;
        box-shadow: 0 0 24px rgba(99,102,241,0.15);
    }
    .card-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #f1f5f9;
        letter-spacing: -0.02em;
        margin-bottom: 0.35rem;
    }
    .card-header p { font-size: 0.875rem; color: #64748b; }

    /* Two-column grid for name/email */
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group { margin-bottom: 1rem; }
    .form-group-full { margin-bottom: 1rem; }

    .form-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 500;
        color: #94a3b8;
        margin-bottom: 0.45rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .input-wrap { position: relative; }
    .input-icon {
        position: absolute; left: 0.9rem; top: 50%;
        transform: translateY(-50%);
        font-size: 0.85rem;
        pointer-events: none;
        opacity: 0.5;
    }

    .form-input {
        width: 100%;
        padding: 0.72rem 0.9rem 0.72rem 2.5rem;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        color: #f1f5f9;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        outline: none;
    }
    .form-input::placeholder { color: #475569; }
    .form-input:focus {
        border-color: rgba(139,92,246,0.6);
        background: rgba(139,92,246,0.06);
        box-shadow: 0 0 0 3px rgba(139,92,246,0.15);
    }
    .form-input:hover:not(:focus) { border-color: rgba(255,255,255,0.18); }

    /* Password strength indicator */
    .strength-bar {
        display: flex;
        gap: 4px;
        margin-top: 0.5rem;
    }
    .strength-seg {
        height: 3px;
        flex: 1;
        border-radius: 2px;
        background: rgba(255,255,255,0.08);
        transition: background 0.3s;
    }
    .strength-label {
        font-size: 0.72rem;
        color: #475569;
        margin-top: 0.3rem;
        transition: color 0.3s;
    }

    .form-error {
        font-size: 0.77rem;
        color: #f87171;
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* Terms line */
    .terms-line {
        font-size: 0.78rem;
        color: #475569;
        text-align: center;
        margin: 1rem 0 1.25rem;
        line-height: 1.5;
    }
    .terms-line a { color: #818cf8; text-decoration: none; font-weight: 500; }
    .terms-line a:hover { color: #a78bfa; }

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
        transition: transform 0.15s, box-shadow 0.15s;
        box-shadow: 0 4px 24px rgba(99,102,241,0.35);
        letter-spacing: 0.01em;
        position: relative;
        overflow: hidden;
    }
    .btn-primary::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 32px rgba(99,102,241,0.45); }
    .btn-primary:hover::before { opacity: 1; }
    .btn-primary:active { transform: translateY(0); }

    .card-footer {
        text-align: center;
        margin-top: 1.25rem;
        font-size: 0.875rem;
        color: #64748b;
    }
    .card-footer a { color: #818cf8; text-decoration: none; font-weight: 500; transition: color 0.2s; }
    .card-footer a:hover { color: #a78bfa; }

    /* Divider */
    .divider {
        display: flex; align-items: center; gap: 0.75rem;
        margin: 1rem 0;
    }
    .divider-line { flex: 1; height: 1px; background: rgba(255,255,255,0.07); }
    .divider-text { font-size: 0.75rem; color: #475569; }

    /* ─── Responsive ────────────────────────────────────────── */
    @media (max-width: 980px) {
        .auth-brand { display: none; }
        .auth-form-panel {
            width: 100%;
            border-left: none;
            background: #080812;
        }
    }
    @media (max-width: 480px) {
        .form-grid-2 { grid-template-columns: 1fr; }
        .auth-card { padding: 1.75rem 1.5rem; }
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
                Your Journey<br>Starts <span class="highlight">Here.</span>
            </h2>

            <p class="brand-desc">
                Join thousands of businesses that trust ThreeDot ERP to manage their finances, operations, and teams — all from one powerful platform.
            </p>

            <div class="onboard-steps">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-text"><strong>Create your account</strong> — takes less than 2 minutes</div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-text"><strong>Set up your company</strong> — add branches, currencies &amp; fiscal year</div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-text"><strong>Invite your team</strong> — assign roles and permissions</div>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <div class="step-text"><strong>Go live</strong> — start tracking revenue, expenses &amp; payroll</div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Register Form --}}
    <div class="auth-form-panel">
        <div class="auth-card">

            <div class="card-header">
                <div class="card-header-icon">🚀</div>
                <h1>Create Account</h1>
                <p>Set up your ERP workspace in seconds</p>
            </div>

            <form id="register-form" method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name + Email row --}}
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <div class="input-wrap">
                            <span class="input-icon">👤</span>
                            <input
                                id="name"
                                class="form-input"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="John Doe"
                                required
                                autofocus
                                autocomplete="name"
                            />
                        </div>
                        @error('name')
                            <div class="form-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-wrap">
                            <span class="input-icon">✉️</span>
                            <input
                                id="email"
                                class="form-input"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@company.com"
                                required
                                autocomplete="username"
                            />
                        </div>
                        @error('email')
                            <div class="form-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group-full">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input
                            id="password"
                            class="form-input"
                            type="password"
                            name="password"
                            placeholder="Min. 8 characters"
                            required
                            autocomplete="new-password"
                            oninput="checkStrength(this.value)"
                        />
                    </div>
                    {{-- Strength bar --}}
                    <div class="strength-bar">
                        <div class="strength-seg" id="seg1"></div>
                        <div class="strength-seg" id="seg2"></div>
                        <div class="strength-seg" id="seg3"></div>
                        <div class="strength-seg" id="seg4"></div>
                    </div>
                    <div class="strength-label" id="strength-label">Enter a password</div>
                    @error('password')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="form-group-full">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🛡️</span>
                        <input
                            id="password_confirmation"
                            class="form-input"
                            type="password"
                            name="password_confirmation"
                            placeholder="Re-enter your password"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                    @error('password_confirmation')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Terms --}}
                <div class="terms-line">
                    By registering you agree to our
                    <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </div>

                <button id="register-submit" type="submit" class="btn-primary">
                    Create My Account →
                </button>

                <div class="card-footer">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function checkStrength(val) {
    const segs   = [1,2,3,4].map(i => document.getElementById('seg'+i));
    const label  = document.getElementById('strength-label');
    let score    = 0;

    if (val.length >= 8)              score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))    score++;

    const colors  = ['#ef4444','#f59e0b','#3b82f6','#10b981'];
    const labels  = ['Weak','Fair','Good','Strong 🎉'];

    segs.forEach((s, i) => {
        s.style.background = i < score ? colors[score-1] : 'rgba(255,255,255,0.08)';
    });

    label.style.color  = score > 0 ? colors[score-1] : '#475569';
    label.textContent  = score > 0 ? labels[score-1] : 'Enter a password';
}
</script>

</x-guest-layout>
