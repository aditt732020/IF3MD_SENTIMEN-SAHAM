<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="StockSense AI - Daftarkan akun administrator baru">
    <title>Daftar Akun - StockSense AI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-page:        #0c0c0f;
            --bg-card:        #141418;
            --bg-input:       #1c1c22;
            --bg-input-focus: #1f1f27;
            --bg-info:        rgba(99, 102, 241, 0.06);
            --border-card:    rgba(255,255,255,0.07);
            --border-input:   rgba(255,255,255,0.10);
            --border-focus:   rgba(52, 211, 153, 0.55);
            --border-info:    rgba(99, 102, 241, 0.25);
            --accent:         #34d399;
            --accent-glow:    rgba(52, 211, 153, 0.18);
            --accent-info:    #818cf8;
            --text-white:     #f1f5f9;
            --text-muted:     #8892a4;
            --text-dim:       #4b5563;
            --btn-bg:         #f1f5f9;
            --btn-text:       #0c0c0f;
            --error:          #f87171;
            --error-bg:       rgba(248,113,113,0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -30%;
            right: -20%;
            width: 60%;
            height: 60%;
            background: radial-gradient(ellipse, rgba(99, 102, 241, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -30%;
            left: -20%;
            width: 50%;
            height: 50%;
            background: radial-gradient(ellipse, rgba(52, 211, 153, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 18px;
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 440px;
            position: relative;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.03), 0 24px 64px rgba(0,0,0,0.55);
            animation: fadeUp 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            background: rgba(52, 211, 153, 0.12);
            border: 1px solid rgba(52, 211, 153, 0.25);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg { color: var(--accent); }

        .brand-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin-bottom: 0.35rem;
        }

        .brand-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-white);
            letter-spacing: -0.02em;
        }

        .badge {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: rgba(52, 211, 153, 0.15);
            color: var(--accent);
            border: 1px solid rgba(52, 211, 153, 0.3);
            border-radius: 6px;
            padding: 0.2rem 0.5rem;
        }

        .brand-sub {
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        /* Info box dengan warna indigo (berbeda dari login) */
        .info-box {
            background: var(--bg-info);
            border: 1px solid var(--border-info);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
        }

        .info-box svg { color: var(--accent-info); flex-shrink: 0; margin-top: 1px; }

        .info-box-content .info-title {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent-info);
            margin-bottom: 0.2rem;
        }

        .info-box-content p { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; }

        /* Password strength */
        .password-strength {
            margin-top: 0.5rem;
        }

        .strength-bar {
            height: 3px;
            border-radius: 2px;
            background: var(--border-input);
            display: flex;
            gap: 3px;
            overflow: hidden;
        }

        .strength-seg {
            flex: 1;
            border-radius: 2px;
            background: transparent;
            transition: background 0.3s;
        }

        .strength-text {
            font-size: 0.68rem;
            color: var(--text-dim);
            margin-top: 0.3rem;
        }

        .form-group { margin-bottom: 1.1rem; }

        .form-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.45rem;
        }

        .form-label {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            pointer-events: none;
            display: flex;
        }

        .form-input {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: 10px;
            color: var(--text-white);
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            padding: 0.7rem 1rem 0.7rem 2.6rem;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder { color: var(--text-dim); }

        .form-input:focus {
            border-color: var(--border-focus);
            background: var(--bg-input-focus);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-input.is-invalid {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(248,113,113,0.12);
        }

        .toggle-password {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-dim);
            padding: 0;
            display: flex;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: var(--text-muted); }

        .error-msg {
            font-size: 0.72rem;
            color: var(--error);
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .alert-error {
            background: var(--error-bg);
            border: 1px solid rgba(248,113,113,0.2);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.8rem;
            color: var(--error);
        }

        .alert-success {
            background: rgba(52, 211, 153, 0.08);
            border: 1px solid rgba(52, 211, 153, 0.2);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.8rem;
            color: var(--accent);
        }

        /* Role info badge */
        .role-note {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            color: var(--text-dim);
            margin-top: 0.35rem;
        }

        .role-note span {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 4px;
            padding: 0.1rem 0.4rem;
            font-size: 0.63rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .btn-primary {
            width: 100%;
            background: var(--btn-bg);
            color: var(--btn-text);
            border: none;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.8rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: -0.01em;
            margin-top: 1.5rem;
        }

        .btn-primary:hover {
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(241,245,249,0.15);
            transform: translateY(-1px);
        }

        .btn-primary:active { transform: translateY(0); }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-arrow { transition: transform 0.2s; }
        .btn-primary:hover .btn-arrow { transform: translateX(3px); }

        .card-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: var(--text-dim);
        }

        .card-footer a { color: var(--accent); text-decoration: none; font-weight: 500; }
        .card-footer a:hover { text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.25rem 0;
            color: var(--text-dim);
            font-size: 0.72rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-input);
        }

        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(0,0,0,0.2);
            border-top-color: #0c0c0f;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="card">

    {{-- Logo --}}
    <div class="logo-wrap">
        <div class="logo-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                <polyline points="16 7 22 7 22 13"></polyline>
            </svg>
        </div>
    </div>

    {{-- Brand --}}
    <div class="brand-row">
        <span class="brand-name">StockSense AI</span>
        <span class="badge">Emiten Monitor</span>
    </div>
    <p class="brand-sub">Buat Akun Administrator Baru</p>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Info Box --}}
    <div class="info-box">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div class="info-box-content">
            <div class="info-title">Informasi Role Akun</div>
            <p>Akun baru terdaftar sebagai <strong>Visitor</strong>. Role Admin hanya diberikan oleh superadmin sistem.</p>
        </div>
    </div>

    {{-- Form --}}
    <form id="registerForm" action="{{ route('register.post') }}" method="POST" novalidate>
        @csrf

        {{-- Nama --}}
        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="name">Nama Lengkap</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name') }}"
                    placeholder="Nama Lengkap Anda"
                    autocomplete="name"
                    required
                >
            </div>
            @error('name')
                <div class="error-msg">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="email">Alamat Email</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}"
                    placeholder="email@contoh.com"
                    autocomplete="email"
                    required
                >
            </div>
            @error('email')
                <div class="error-msg">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="password">Kata Sandi</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Min. 8 karakter"
                    autocomplete="new-password"
                    required
                >
                <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan kata sandi">
                    <svg id="eyeIcon1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            {{-- Password Strength Indicator --}}
            <div class="password-strength" id="strengthWrap" style="display:none">
                <div class="strength-bar">
                    <div class="strength-seg" id="seg1"></div>
                    <div class="strength-seg" id="seg2"></div>
                    <div class="strength-seg" id="seg3"></div>
                    <div class="strength-seg" id="seg4"></div>
                </div>
                <div class="strength-text" id="strengthText">Terlalu pendek</div>
            </div>
            @error('password')
                <div class="error-msg">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="form-group">
            <div class="form-label-row">
                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </span>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-input"
                    placeholder="Ulangi kata sandi"
                    autocomplete="new-password"
                    required
                >
                <button type="button" class="toggle-password" id="togglePassword2" aria-label="Tampilkan konfirmasi kata sandi">
                    <svg id="eyeIcon2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <div class="role-note">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Role default: <span>Visitor</span>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" id="btnRegister" class="btn-primary">
            <div class="spinner" id="registerSpinner"></div>
            <span id="btnText">Buat Akun</span>
            <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </button>
    </form>

    <div class="divider">sudah punya akun?</div>

    <div class="card-footer">
        <a href="{{ route('login') }}">Kembali ke halaman login</a>
    </div>

</div>

<script>
    // Toggle password 1
    const tp1 = document.getElementById('togglePassword');
    const pw1 = document.getElementById('password');
    const ey1 = document.getElementById('eyeIcon1');

    // Toggle password 2
    const tp2 = document.getElementById('togglePassword2');
    const pw2 = document.getElementById('password_confirmation');
    const ey2 = document.getElementById('eyeIcon2');

    const eyeOpenSVG = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
    const eyeOffSVG  = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;

    tp1.addEventListener('click', function () {
        const show = pw1.type === 'password';
        pw1.type = show ? 'text' : 'password';
        ey1.innerHTML = show ? eyeOffSVG : eyeOpenSVG;
    });

    tp2.addEventListener('click', function () {
        const show = pw2.type === 'password';
        pw2.type = show ? 'text' : 'password';
        ey2.innerHTML = show ? eyeOffSVG : eyeOpenSVG;
    });

    // Password Strength Indicator
    const strengthWrap = document.getElementById('strengthWrap');
    const strengthText = document.getElementById('strengthText');
    const segs = [document.getElementById('seg1'), document.getElementById('seg2'), document.getElementById('seg3'), document.getElementById('seg4')];
    const colors = ['#f87171', '#fb923c', '#facc15', '#34d399'];
    const labels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat'];

    function calcStrength(pw) {
        let score = 0;
        if (pw.length >= 8) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    }

    pw1.addEventListener('input', function () {
        const val = pw1.value;
        if (val.length === 0) { strengthWrap.style.display = 'none'; return; }
        strengthWrap.style.display = 'block';
        const score = calcStrength(val);
        segs.forEach((s, i) => { s.style.background = i < score ? colors[score - 1] : 'transparent'; });
        strengthText.textContent = val.length < 8 ? 'Terlalu pendek (min. 8 karakter)' : labels[score - 1];
        strengthText.style.color = colors[score - 1];
    });

    // Loading state on submit
    document.getElementById('registerForm').addEventListener('submit', function () {
        const btn     = document.getElementById('btnRegister');
        const spinner = document.getElementById('registerSpinner');
        const text    = document.getElementById('btnText');
        btn.disabled          = true;
        spinner.style.display = 'block';
        text.textContent      = 'Memproses...';
    });
</script>

</body>
</html>
