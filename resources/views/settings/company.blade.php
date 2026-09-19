<x-app-layout>
    <x-slot name="title">Company Setup</x-slot>

<style>
/* ─── Page layout ─────────────────────────────────────── */
.settings-header {
    margin-bottom: 2rem;
}
.settings-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #f1f5f9;
    letter-spacing: -0.025em;
    margin-bottom: 0.35rem;
}
.settings-sub {
    font-size: 0.875rem;
    color: #64748b;
}

/* ─── Alert ───────────────────────────────────────────── */
.alert-success {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.9rem 1.25rem;
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.25);
    border-radius: 12px;
    color: #34d399;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.75rem;
    animation: fadeSlideIn 0.4s ease-out;
}
@keyframes fadeSlideIn {
    from { opacity:0; transform:translateY(-8px); }
    to   { opacity:1; transform:translateY(0); }
}

/* ─── Card ────────────────────────────────────────────── */
.settings-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: border-color 0.2s;
}
.settings-card:hover { border-color: rgba(99,102,241,0.2); }

.card-section-header {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1.25rem 1.75rem;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.02);
}
.section-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.icon-purple { background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.2); }
.icon-blue   { background: rgba(59,130,246,0.15);  border: 1px solid rgba(59,130,246,0.2); }
.icon-green  { background: rgba(16,185,129,0.15);  border: 1px solid rgba(16,185,129,0.2); }
.icon-amber  { background: rgba(245,158,11,0.15);  border: 1px solid rgba(245,158,11,0.2); }

.section-title   { font-size: 0.95rem; font-weight: 700; color: #e2e8f0; }
.section-subtitle{ font-size: 0.78rem; color: #64748b; margin-top: 0.1rem; }

.card-section-body {
    padding: 1.75rem;
}

/* ─── Form grid ───────────────────────────────────────── */
.form-grid {
    display: grid;
    gap: 1.25rem;
}
.cols-2 { grid-template-columns: 1fr 1fr; }
.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
.cols-1 { grid-template-columns: 1fr; }

@media (max-width: 900px) {
    .cols-2, .cols-3 { grid-template-columns: 1fr; }
}

.form-group { display: flex; flex-direction: column; gap: 0.45rem; }
.col-span-2 { grid-column: span 2; }
.col-span-3 { grid-column: span 3; }
@media (max-width: 900px) {
    .col-span-2, .col-span-3 { grid-column: span 1; }
}

.form-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.form-label .required { color: #f87171; margin-left: 2px; }

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.7rem 1rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #f1f5f9;
    font-size: 0.875rem;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}
.form-input::placeholder,
.form-textarea::placeholder { color: #334155; }
.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    border-color: rgba(99,102,241,0.55);
    background: rgba(99,102,241,0.05);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.form-input:hover:not(:focus),
.form-select:hover:not(:focus),
.form-textarea:hover:not(:focus) {
    border-color: rgba(255,255,255,0.16);
}
.form-select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px;
    cursor: pointer;
}
.form-select option { background: #1a1a2e; color: #f1f5f9; }
.form-textarea { resize: vertical; min-height: 90px; }

.form-hint {
    font-size: 0.73rem;
    color: #475569;
    margin-top: 0.15rem;
}

.form-error-msg {
    font-size: 0.77rem;
    color: #f87171;
    margin-top: 0.15rem;
}

/* ─── Submit bar ──────────────────────────────────────── */
.submit-bar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.25rem 1.75rem;
    background: rgba(255,255,255,0.02);
    border-top: 1px solid rgba(255,255,255,0.07);
}
.btn-cancel {
    padding: 0.65rem 1.4rem;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: border-color 0.15s, color 0.15s;
    text-decoration: none;
}
.btn-cancel:hover { border-color: rgba(255,255,255,0.2); color: #94a3b8; }

.btn-save {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.65rem 1.6rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    box-shadow: 0 4px 16px rgba(99,102,241,0.3);
}
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(99,102,241,0.4); }
.btn-save:active { transform: translateY(0); }
</style>

<div class="settings-header">
    <h1 class="settings-title">🏢 Company Setup</h1>
    <p class="settings-sub">Configure your organization's information, tax details, and preferences.</p>
</div>

{{-- Success Alert --}}
@if(session('success'))
    <div class="alert-success">
        ✅ {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('settings.company.update') }}">
    @csrf

    {{-- ── 1. Basic Information ────────────────────────────── --}}
    <div class="settings-card">
        <div class="card-section-header">
            <div class="section-icon icon-purple">🏢</div>
            <div>
                <div class="section-title">Basic Information</div>
                <div class="section-subtitle">Your company's primary identity details</div>
            </div>
        </div>
        <div class="card-section-body">
            <div class="form-grid cols-2">

                <div class="form-group">
                    <label class="form-label" for="company_name">
                        Company Name <span class="required">*</span>
                    </label>
                    <input id="company_name" name="company_name" type="text" class="form-input"
                           placeholder="e.g. ThreeDot Accounting Ltd."
                           value="{{ old('company_name', $company['company_name'] ?? '') }}" required>
                    @error('company_name') <div class="form-error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="legal_name">Legal / Trade Name</label>
                    <input id="legal_name" name="legal_name" type="text" class="form-input"
                           placeholder="Legal registered name"
                           value="{{ old('legal_name', $company['legal_name'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="registration_no">Registration Number</label>
                    <input id="registration_no" name="registration_no" type="text" class="form-input"
                           placeholder="e.g. C-12345/2020"
                           value="{{ old('registration_no', $company['registration_no'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="tax_id">Tax / VAT ID</label>
                    <input id="tax_id" name="tax_id" type="text" class="form-input"
                           placeholder="e.g. BIN 123456789"
                           value="{{ old('tax_id', $company['tax_id'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="industry">Industry</label>
                    <select id="industry" name="industry" class="form-select">
                        <option value="">— Select industry —</option>
                        @foreach([
                            'Accounting & Finance','Agriculture','Construction','Education',
                            'Healthcare','IT & Software','Manufacturing','Retail & E-commerce',
                            'Real Estate','Telecommunications','Transportation','Other'
                        ] as $ind)
                            <option value="{{ $ind }}"
                                {{ old('industry', $company['industry'] ?? '') === $ind ? 'selected' : '' }}>
                                {{ $ind }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="fiscal_year_start">
                        Fiscal Year Start <span class="required">*</span>
                    </label>
                    <select id="fiscal_year_start" name="fiscal_year_start" class="form-select" required>
                        @foreach([
                            'January','February','March','April','May','June',
                            'July','August','September','October','November','December'
                        ] as $month)
                            <option value="{{ $month }}"
                                {{ old('fiscal_year_start', $company['fiscal_year_start'] ?? 'July') === $month ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">The month your financial year begins</div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── 2. Contact Information ──────────────────────────── --}}
    <div class="settings-card">
        <div class="card-section-header">
            <div class="section-icon icon-blue">📞</div>
            <div>
                <div class="section-title">Contact Information</div>
                <div class="section-subtitle">How clients and partners can reach you</div>
            </div>
        </div>
        <div class="card-section-body">
            <div class="form-grid cols-3">

                <div class="form-group">
                    <label class="form-label" for="email">Business Email</label>
                    <input id="email" name="email" type="email" class="form-input"
                           placeholder="hello@company.com"
                           value="{{ old('email', $company['email'] ?? '') }}">
                    @error('email') <div class="form-error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input id="phone" name="phone" type="text" class="form-input"
                           placeholder="+880 1700-000000"
                           value="{{ old('phone', $company['phone'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="website">Website</label>
                    <input id="website" name="website" type="url" class="form-input"
                           placeholder="https://www.company.com"
                           value="{{ old('website', $company['website'] ?? '') }}">
                    @error('website') <div class="form-error-msg">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>
    </div>

    {{-- ── 3. Address ──────────────────────────────────────── --}}
    <div class="settings-card">
        <div class="card-section-header">
            <div class="section-icon icon-amber">📍</div>
            <div>
                <div class="section-title">Business Address</div>
                <div class="section-subtitle">Used on invoices, reports and legal documents</div>
            </div>
        </div>
        <div class="card-section-body">
            <div class="form-grid cols-1" style="margin-bottom:1.25rem;">
                <div class="form-group">
                    <label class="form-label" for="address">Street Address</label>
                    <textarea id="address" name="address" class="form-textarea"
                              placeholder="House/flat no., road, area…">{{ old('address', $company['address'] ?? '') }}</textarea>
                </div>
            </div>
            <div class="form-grid cols-2">
                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <input id="city" name="city" type="text" class="form-input"
                           placeholder="Dhaka"
                           value="{{ old('city', $company['city'] ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="state">State / Division</label>
                    <input id="state" name="state" type="text" class="form-input"
                           placeholder="Dhaka Division"
                           value="{{ old('state', $company['state'] ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="country">Country</label>
                    <select id="country" name="country" class="form-select">
                        <option value="">— Select country —</option>
                        @foreach([
                            'Bangladesh','India','Pakistan','Nepal','Sri Lanka',
                            'United States','United Kingdom','Canada','Australia',
                            'Singapore','Malaysia','UAE','Saudi Arabia','Other'
                        ] as $country)
                            <option value="{{ $country }}"
                                {{ old('country', $company['country'] ?? 'Bangladesh') === $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="postal_code">Postal / ZIP Code</label>
                    <input id="postal_code" name="postal_code" type="text" class="form-input"
                           placeholder="1212"
                           value="{{ old('postal_code', $company['postal_code'] ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ── 4. Financial Preferences ────────────────────────── --}}
    <div class="settings-card">
        <div class="card-section-header">
            <div class="section-icon icon-green">💱</div>
            <div>
                <div class="section-title">Financial Preferences</div>
                <div class="section-subtitle">Default currency and accounting settings</div>
            </div>
        </div>
        <div class="card-section-body">
            <div class="form-grid cols-2">

                <div class="form-group">
                    <label class="form-label" for="currency">
                        Base Currency <span class="required">*</span>
                    </label>
                    <select id="currency" name="currency" class="form-select" required>
                        @foreach([
                            'BDT' => 'BDT — Bangladeshi Taka (৳)',
                            'USD' => 'USD — US Dollar ($)',
                            'EUR' => 'EUR — Euro (€)',
                            'GBP' => 'GBP — British Pound (£)',
                            'INR' => 'INR — Indian Rupee (₹)',
                            'SAR' => 'SAR — Saudi Riyal (﷼)',
                            'AED' => 'AED — UAE Dirham (د.إ)',
                            'SGD' => 'SGD — Singapore Dollar (S$)',
                        ] as $code => $label)
                            <option value="{{ $code }}"
                                {{ old('currency', $company['currency'] ?? 'BDT') === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">All reports and invoices will use this currency</div>
                    @error('currency') <div class="form-error-msg">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>

        {{-- Save / Cancel --}}
        <div class="submit-bar">
            <a href="{{ route('dashboard') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-save">
                💾 Save Company Settings
            </button>
        </div>
    </div>

</form>

</x-app-layout>
