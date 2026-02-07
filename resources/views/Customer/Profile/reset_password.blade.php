<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغییر رمز عبور</title>
        @include('Customer.Layout.head-tag')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Vazirmatn', sans-serif;
        }
        .verify-box {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
        }
        .form-control{
            border-radius: 10px !important;
        }
        .circle-icon {
            width: 80px;
            height: 80px;
            background: #6a1b9a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #fff;
        }
        .code-input:focus {
            border-color: #6a1b9a;
            box-shadow: 0 0 0 0.25rem rgba(106, 27, 154, 0.25);
        }
        .btn-purple {
            background-color: #6a1b9a;
            border: none;
        }
        .btn-purple:hover {
            background-color: #8b2a94;
        }
        .password-wrapper {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
        }
        .password-toggle:hover {
            color: #6a1b9a;
        }
        .form-control {
            padding-left: 45px !important;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="verify-box w-100" style="max-width: 420px; margin-top: 10rem;">
        <!-- Icon -->
        <div class="circle-icon">
            <i class="fa-solid fas fa-key fa-2x"></i>
        </div>
        <!-- Title -->
        <h4 class="fw-bold text-center mb-2">تغییر رمز عبور</h4>
        <form id="passwordForm" action="{{ route('s_reset_password') }}" method="POST">   
            @csrf
            <label for="old_password">رمز عبور قبلی</label>
            <div class="password-wrapper">
                <input type="password" id="old_password" name="old_password" class="form-control" required>
                <i class="fa-solid fas fa-eye password-toggle" onclick="togglePassword('old_password', this)"></i>
            </div>
            <br>
            <label for="new_password">رمز عبور جدید</label>
            <div class="password-wrapper">
                <input type="password" id="new_password" name="new_password" minlength="8" class="form-control" required>
                <i class="fa-solid fas fa-eye password-toggle" onclick="togglePassword('new_password', this)"></i>
            </div>
            <br>
            <label for="confirm_new_password">تکرار رمز عبور جدید</label>
            <div class="password-wrapper">
                <input type="password" id="confirm_new_password" name="confirm_new_password" minlength="8" class="form-control" required>
                <i class="fa-solid fas fa-eye password-toggle" onclick="togglePassword('confirm_new_password', this)"></i>
            </div>
            <br>
            <button type="submit" id="verifyBtn" class="btn btn-purple w-100 py-3 fw-semibold text-white">
                تغییر رمز عبور
            </button>
        </form>
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