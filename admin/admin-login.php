<?php
// admin/admin-login.php
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: /admin/dashboard');
    exit;
}

$error      = $_SESSION['login_error']      ?? null;
$identifier = $_SESSION['login_identifier'] ?? '';
unset($_SESSION['login_error'], $_SESSION['login_identifier']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login · Never Give Up World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --navy:       #1e293b;
            --navy-dark:  #0f172a;
            --blue:       #1d4ed8;
            --blue-light: #60a5fa;
            --muted:      #64748b;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 60%, #334155 100%);
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #e2e8f0;
        }
        .login-wrap { width: 100%; max-width: 420px; }
        .brand { text-align: center; margin-bottom: 22px; }
        .brand-mark {
            display: inline-flex; align-items: center; gap: 10px;
            color: #fff; text-decoration: none;
            font-weight: 800; font-size: 22px; letter-spacing: .5px;
        }
        .brand-mark .icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--blue), var(--blue-light));
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 18px;
        }
        .brand-mark .accent { color: var(--blue-light); }
        .brand-sub { margin-top: 8px; font-size: 13px; color: #94a3b8; }
        .login-card {
            background: #fff; border-radius: 14px;
            box-shadow: 0 20px 50px rgba(0,0,0,.35);
            padding: 32px 28px; color: #1e293b;
        }
        .login-card h1 { font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .login-card .lead { font-size: 14px; color: var(--muted); margin-bottom: 22px; }
        .form-label { font-weight: 600; font-size: 14px; color: #334155; margin-bottom: 6px; }
        .form-control {
            padding: 10px 14px; border-radius: 8px;
            border: 1px solid #cbd5e1; font-size: 15px;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(29,78,216,.15);
        }
        .input-group .form-control { border-radius: 8px 0 0 8px; }
        .input-group .btn-toggle {
            border: 1px solid #cbd5e1; border-left: none;
            background: #f8fafc; color: var(--muted);
            padding: 0 14px; border-radius: 0 8px 8px 0;
        }
        .input-group .btn-toggle:hover { color: var(--blue); }
        .btn-primary {
            background: var(--blue); border-color: var(--blue);
            font-weight: 600; padding: 11px 16px; border-radius: 8px;
            width: 100%; transition: background .2s;
        }
        .btn-primary:hover { background: #1e40af; border-color: #1e40af; }
        .alert-danger {
            background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
            border-radius: 8px; font-size: 14px; padding: 10px 14px; margin-bottom: 18px;
        }
        .divider {
            margin-top: 18px; padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            font-size: 13px; color: var(--muted); text-align: center;
        }
        .divider a { color: var(--blue); text-decoration: none; font-weight: 600; }
        .divider a:hover { text-decoration: underline; }
        .login-foot { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 20px; }
        @media (max-width: 480px) {
            .login-card { padding: 26px 20px; border-radius: 12px; }
            .brand-mark { font-size: 20px; }
            .brand-mark .icon { width: 32px; height: 32px; font-size: 16px; }
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="brand">
            <a href="/" class="brand-mark" aria-label="Never Give Up World">
                <span class="icon">N</span>
                <span>Never Give Up <span class="accent">World</span></span>
            </a>
            <div class="brand-sub">Admin Panel</div>
        </div>

        <main class="login-card" aria-labelledby="loginHeading">
            <h1 id="loginHeading">Welcome back</h1>
            <p class="lead">Sign in to manage the Never Give Up World admin panel.</p>

            <?php if ($error): ?>
                <div class="alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <!-- ফর্ম অ্যাকশনে /admin/actions/admin-login ব্যবহার করা হলো (.php ছাড়া) -->
            <form method="post" action="/admin/actions/admin-login" autocomplete="on">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="identifier" class="form-label">Username or Email</label>
                    <input type="text" class="form-control" id="identifier" name="identifier"
                           value="<?= e($identifier) ?>" required autofocus
                           autocomplete="username"
                           placeholder="Enter your username or email">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password"
                               required autocomplete="current-password"
                               placeholder="Enter your password">
                        <button type="button" class="btn btn-toggle" id="togglePassword"
                                aria-label="Show password" aria-pressed="false">
                            <i class="bi bi-eye" id="toggleIcon" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>
                    Sign in
                </button>
            </form>

            <div class="divider">
                <a href="/">← Back to Never Give Up World</a>
            </div>
        </main>

        <div class="login-foot">© <?= date('Y') ?> Never Give Up World. All rights reserved.</div>
    </div>

    <script>
    (function () {
        var btn   = document.getElementById('togglePassword');
        var input = document.getElementById('password');
        var icon  = document.getElementById('toggleIcon');
        if (!btn || !input || !icon) return;
        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label',    show ? 'Hide password' : 'Show password');
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            input.focus();
        });
    })();
    </script>
</body>
</html>