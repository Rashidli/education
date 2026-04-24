<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background-color: #fafafa; }
        .auth-wrap { min-height: 100vh; display: grid; place-items: center; padding: 1rem; }
        .auth-card { width: 100%; max-width: 400px; }
        .auth-brand { display: flex; align-items: center; gap: 0.625rem; justify-content: center; margin-bottom: 1.5rem; font-weight: 600; font-size: 1.125rem; }
        .auth-brand > div { width: 36px; height: 36px; background: var(--primary); color: var(--primary-foreground); border-radius: calc(var(--radius) - 2px); display: grid; place-items: center; font-weight: 700; }
    </style>
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-brand">
                <div>E</div>
                <span>Education</span>
            </div>

            <div class="card">
                <div class="card__header">
                    <div>
                        <div class="card__title">Admin panelə giriş</div>
                        <div class="card__description">Email və şifrənizlə daxil olun</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="card__body">
                        @if ($errors->any())
                            <div class="alert alert--error">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>
                                </svg>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="label label--required" for="email">Email</label>
                            <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" autofocus required>
                        </div>

                        <div class="form-group">
                            <label class="label label--required" for="password">Şifrə</label>
                            <input type="password" id="password" name="password" class="input" required>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-wrap">
                                <input type="checkbox" name="remember" value="1">
                                <span>Məni xatırla</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-actions" style="padding: 0.875rem 1.5rem;">
                        <button type="submit" class="btn btn--primary" style="width:100%">Daxil ol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
