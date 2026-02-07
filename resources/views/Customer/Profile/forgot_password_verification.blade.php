<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تایید شماره موبایل</title>

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

        .code-input {
            width: 65px;
            height: 65px;
            font-size: 1.8rem;
            text-align: center;
            font-weight: bold;
            border: 2px solid #ced4da;
            border-radius: 0.75rem;
            transition: 0.2s;
            margin-right: 9px;
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
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

    <div class="verify-box w-100" style="max-width: 420px; margin-top: 10rem;">

        <!-- Icon -->
        <div class="circle-icon">
            <i class="fa-solid fas fa-envelope fa-2x"></i>
        </div>

        <!-- Title -->
        <h4 class="fw-bold text-center mb-2">تایید شماره موبایل</h4>
        <p class="text-center text-secondary mb-4">
            کد تایید ۴ رقمی به شماره <span class="fw-semibold text-purple"><span
                    style="color: #6a1b9a;">{{$username}}</span></span> ارسال شد
        </p>

        <!-- Success Message -->
        <div id="successMessage" class="alert alert-success text-center py-2 d-none">
            ✅ کد با موفقیت تایید شد
        </div>

        <!-- Form -->
        <form action="{{ route('s_forgot_password_verification') }}" method="POST">
            @csrf
            <div class="d-flex justify-content-center gap-3 mb-4" dir="ltr">
                <input type="text" id="code1" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]"
                    required>
                <input type="text" id="code2" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]"
                    required>
                <input type="text" id="code3" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]"
                    required>
                <input type="text" id="code4" maxlength="1" class="code-input" inputmode="numeric" pattern="[0-9]"
                    required>
            </div>

            <input type="hidden" id="fullCode" name="verification_code">
            <input type="hidden" id="username" name="username" value="{{ $username }}">

            <button type="submit" id="verifyBtn" class="btn btn-purple w-100 py-3 fw-semibold text-white" disabled>
                تایید کد
            </button>
        </form>

        <!-- Timer -->
        <div class="text-center mt-4 small text-muted">
            <p id="timerText">ارسال مجدد کد تا <span id="timer" class="fw-bold text-purple">2:00</span></p>
            <p id="resendText" class="d-none">
                کد را دریافت نکردید؟
                <button id="resendBtn" type="button"
                    class="btn btn-link p-0 fw-semibold text-purple text-decoration-underline custom-cl-primary">
                    ارسال مجدد
                </button>
            </p>
        </div>
    </div>

    @include('Customer.Layout.script')

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    background: '#ffff',
                    color: '#fff',
                    iconColor: '#fff',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            });
        </script>
    @elseif(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    background: '#ffff',
                    color: '#fff',
                    iconColor: '#fff',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });

                setTimeout(function () {
                    window.location.href = "{{ route('login') }}"; 
                }, 3500);
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            const $inputs = $('.code-input');
            const $fullCodeInput = $('#fullCode');
            const $verifyBtn = $('#verifyBtn');
            const $timerElement = $('#timer');
            const $timerText = $('#timerText');
            const $resendText = $('#resendText');
            const $resendBtn = $('#resendBtn');

            let timeLeft = 60;
            let countdownInterval;

            $inputs.first().focus();

            function showToast(icon, title) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    background: '#ffff',
                    color: '#fff',
                    iconColor: '#fff',
                    didOpen: (toast) => {
                        $(toast).on('mouseenter', Swal.stopTimer);
                        $(toast).on('mouseleave', Swal.resumeTimer);
                    }
                });

                Toast.fire({ icon, title });
            }

            function updateCode() {
                let code = '';
                $inputs.each(function () {
                    code += $(this).val();
                });
                $fullCodeInput.val(code);
                $verifyBtn.prop('disabled', code.length !== 4);
            }

            $inputs.on('input', function (e) {
                const $this = $(this);
                const value = $this.val().replace(/\D/g, '');
                $this.val(value);

                const index = $inputs.index($this);
                if (value && index < 3) {
                    $inputs.eq(index + 1).focus();
                }

                updateCode();
            });

            $inputs.on('keydown', function (e) {
                const $this = $(this);
                const index = $inputs.index($this);

                if (e.key === 'Backspace' && !$this.val() && index > 0) {
                    $inputs.eq(index - 1).focus();
                }
            });

            $inputs.on('paste', function (e) {
                e.preventDefault();
                const paste = e.originalEvent.clipboardData.getData('text')
                    .slice(0, 4)
                    .replace(/\D/g, '');

                $.each(paste.split(''), function (i, num) {
                    if ($inputs.eq(i).length) {
                        $inputs.eq(i).val(num);
                    }
                });

                $inputs.eq(Math.min(paste.length - 1, 3)).focus();
                updateCode();
            });

            function startTimer() {
                clearInterval(countdownInterval);
                timeLeft = 60;

                countdownInterval = setInterval(function () {
                    timeLeft--;
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = String(timeLeft % 60).padStart(2, '0');
                    $timerElement.text(`${minutes}:${seconds}`);

                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        $timerText.addClass('d-none');
                        $resendText.removeClass('d-none');
                    }
                }, 1000);
            }

            startTimer();

            $resendBtn.on('click', function () {
                $inputs.val('');
                $inputs.first().focus();
                updateCode();

                $timerText.removeClass('d-none');
                $resendText.addClass('d-none');
                startTimer();

                $.ajax({
                    url: '{{ route('resend_forgot_password_verification') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        username: '{{ $username }}'
                    },
                    success: function (response) {
                        showToast('success', response.message);
                    },
                    error: function (xhr) {
                        showToast('error', 'خطایی رخ داد. لطفا دوباره تلاش کنید.');
                    }
                });
            });
        });
    </script>

</body>

</html>