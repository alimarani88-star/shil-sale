<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغییر رمز عبور</title>
    @include('Customer.Layout.head-tag')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --auth-brand: #69499C;
            --auth-brand-dark: #553a7d;
        }

        body.auth-reset-page {
            min-height: 100vh;
            margin: 0;
            font-family: 'Vazirmatn', sans-serif;
            background: #f3f4f6;
            color: #0f172a;
        }

        .auth-reset-shell {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            overflow: hidden;
        }

        .auth-reset-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 100% -10%, rgba(105, 73, 156, 0.18), transparent 55%),
                radial-gradient(ellipse 60% 40% at 0% 100%, rgba(30, 41, 59, 0.08), transparent 50%);
            pointer-events: none;
        }

        .auth-reset-shell::after {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0.035;
            background-image:
                linear-gradient(#1e293b 1px, transparent 1px),
                linear-gradient(90deg, #1e293b 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .auth-reset-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 1.25rem;
            padding: 2rem 1.75rem;
            box-shadow: 0 20px 50px -24px rgba(15, 23, 42, 0.35);
            animation: authFadeIn 0.45s ease-out;
        }

        @keyframes authFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-reset-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .auth-reset-brand img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 8px 24px -16px rgba(15, 23, 42, 0.4);
            padding: 0.35rem;
        }

        .auth-reset-brand h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .auth-reset-brand p {
            margin: 0;
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.7;
        }

        .auth-reset-card label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #334155;
        }

        .password-wrapper {
            position: relative;
            margin-bottom: 1.1rem;
        }

        .auth-reset-card .form-control {
            height: 2.85rem;
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.5rem 0.9rem 0.5rem 2.75rem !important;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .auth-reset-card .form-control:focus {
            border-color: var(--auth-brand);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(105, 73, 156, 0.18);
            outline: none;
        }

        .password-toggle {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: var(--auth-brand);
        }

        .btn-auth-submit {
            width: 100%;
            height: 2.85rem;
            border: none;
            border-radius: 0.85rem;
            background: var(--auth-brand);
            color: #fff;
            font-weight: 600;
            margin-top: 0.35rem;
            box-shadow: 0 10px 24px -12px rgba(105, 73, 156, 0.8);
            transition: transform .15s ease, background .2s ease;
        }

        .btn-auth-submit:hover {
            background: var(--auth-brand-dark);
            transform: translateY(-1px);
            color: #fff;
        }

        .auth-reset-back {
            display: block;
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.875rem;
            color: #64748b;
            text-decoration: none;
        }

        .auth-reset-back:hover {
            color: var(--auth-brand);
        }
    </style>
</head>
<body class="auth-reset-page">
    <div class="auth-reset-shell">
        <div class="auth-reset-card">
            <div class="auth-reset-brand">
                <img src="{{ asset('assets/img/logo-icon.png') }}" alt="شیل ایران">
                <div>
                    <h1>تغییر رمز عبور</h1>
                    <p>برای امنیت حساب، رمز عبور جدید خود را با دقت وارد کنید</p>
                </div>
            </div>

            <form id="passwordForm" action="{{ route('s_reset_password') }}" method="POST">
                @csrf
                <label for="old_password">رمز عبور قبلی</label>
                <div class="password-wrapper">
                    <input type="password" id="old_password" name="old_password" class="form-control" required>
                    <i class="fa-solid fas fa-eye password-toggle" onclick="togglePassword('old_password', this)"></i>
                </div>

                <label for="new_password">رمز عبور جدید</label>
                <div class="password-wrapper">
                    <input type="password" id="new_password" name="new_password" minlength="8" class="form-control" required>
                    <i class="fa-solid fas fa-eye password-toggle" onclick="togglePassword('new_password', this)"></i>
                </div>

                <label for="confirm_new_password">تکرار رمز عبور جدید</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_new_password" name="confirm_new_password" minlength="8" class="form-control" required>
                    <i class="fa-solid fas fa-eye password-toggle" onclick="togglePassword('confirm_new_password', this)"></i>
                </div>

                <button type="submit" id="verifyBtn" class="btn btn-auth-submit">
                    ذخیره رمز جدید
                </button>
            </form>

            <a href="{{ route('profile_personal_info') }}" class="auth-reset-back">بازگشت به حساب کاربری</a>
        </div>
    </div>

    @include('Customer.Layout.script')

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    background: '#fff',
                    color: '#fff',
                    iconColor: '#fff',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                @foreach ($errors->all() as $error)
                    Toast.fire({
                        icon: 'error',
                        title: '{{ $error }}'
                    });
                @endforeach
            });
        </script>
    @endif

    @include('Customer.Alerts.Sweetalert.error')
    @include('Customer.Alerts.Sweetalert.success')

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
