<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <style>
        :root {
            --blue: #6faee8;
            --blue-dark: #2e6fbb;
            --blue-soft: #eaf5ff;
            --brand-blue: #2e6fbb;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;   
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
        }

        body {
            min-height: 100vh;
            background-image: url('{{ asset('new_office.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            isolation: isolate;
        }

        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            /* darken background for contrast */
            background: rgba(8, 28, 50, 0.44);
            z-index: -1;
        }

        .card {
            width: 100%;
            max-width: 500px;
            /* frosted glass with logo-blue tint */
            background: linear-gradient(180deg, rgba(46,111,187,0.16), rgba(255,255,255,0.06));
            border: 1px solid rgba(46,111,187,0.08);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 16px 44px rgba(14,45,78,0.42);
            backdrop-filter: blur(14px) saturate(1.08);
            -webkit-backdrop-filter: blur(14px) saturate(1.08);
            position: relative;
            overflow: hidden;
            color: rgba(255,255,255,0.96);
        }

        .card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            /* subtle inner darkening to improve text contrast */
            background: linear-gradient(180deg, rgba(6,18,36,0.28), rgba(6,18,36,0.06));
            pointer-events: none;
            z-index: 0;
        }

        .card::after {
            content: '';
            position: absolute;
            inset: 0;
            border: 1px solid rgba(255,255,255,0.04);
            border-radius: 20px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
            pointer-events: none;
            z-index: 1;
        }

        /* ensure card content sits above overlays */
        .card > * {
            position: relative;
            z-index: 2;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .brand-logo {
            width: 55px;
            height: 55px;
            padding: 0;
            border-radius: 0;
            border: 0;
            background: transparent;
            display: block;
            object-fit: contain;
        }

        .brand-name {
            color: var(--brand-blue);
            font-size: 16px;
            letter-spacing: 0.16em;
            line-height: 1;
            text-transform: uppercase;
            font-weight: 700;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 40px;
            line-height: 1.1;
            color: var(--white);
            text-shadow: 0 6px 18px rgba(6,18,36,0.45);
        }

        p {
            margin: 0 0 24px;
            color: rgba(235,245,255,0.92);
            font-size: 14px;
            line-height: 1.6;
            text-shadow: 0 2px 6px rgba(6,18,36,0.35);
        }

        .field {
            margin-bottom: 14px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: rgba(235,245,255,0.92);
            font-size: 13px;
            font-weight: 700;
            text-shadow: 0 1px 0 rgba(6,18,36,0.28);
        }

        input {
            width: 100%;
            height: 50px;
            border: 1px solid rgba(46,111,187,0.14);
            border-radius: 12px;
            padding: 0 16px;
            font-size: 15px;
            color: #0b2540;
            background: rgba(255,255,255,0.96);
            outline: none;
            box-shadow: 0 2px 6px rgba(4,18,34,0.06);
        }

        input:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 2px rgba(46,111,187,0.12);
        }

        .actions {
            margin-top: 18px;
        }

        button {
            width: 100%;
            height: 54px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(46,111,187,0.98), rgba(33,90,149,0.98));
            color: var(--white);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(46,111,187,0.18);
        }

        button:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .message {
            min-height: 20px;
            margin-top: 16px;
            font-size: 13px;
            line-height: 1.5;
        }

        .message.error {
            font-weight: 1000;
            font-size: 14px;
            color: #ff0101;
        }

        .message.success {
            font-weight: 1000;
            font-size: 14px;
            color: #16a34a;
        }

        .hoot-loader {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(8, 28, 50, 0.5);
            z-index: 9999;
        }

        .hoot-loader__spinner {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.22);
            border-top-color: #ffffff;
            animation: hoot-spin 0.9s linear infinite;
        }

        @keyframes hoot-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .footer-note {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e7f1fb;
            color: #4f8fcb;
            font-size: 12px;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .card {
                padding: 28px 20px;
                border-radius: 18px;
            }

            .brand-logo {
                width: 100px;
                height: 100px;
            }

            .brand-name {
                font-size: 12px;
                letter-spacing: 0.14em;
            }

            h1 {
                font-size: 38px;
            }
        }
    </style>
</head>
<body>
    <div id="hoot-loader" class="hoot-loader" aria-hidden="true">
        <div class="hoot-loader__spinner" aria-label="Loading"></div>
    </div>

    <main class="page">
        <section class="card">
            <div class="brand">
                <img class="brand-logo" src="{{ asset('New_logo.png') }}" alt="Smart Office logo">
                <div class="brand-name">Smart Office</div>
            </div>
            <h1>Login</h1>
            <p>Sign in with your email and password to continue.</p>

            <form id="loginForm" novalidate>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" placeholder="Enter your email" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>
                </div>

                <div class="actions">
                    <button id="submitButton" type="submit">Sign In</button>
                </div>

                <div id="message" class="message" aria-live="polite"></div>
            </form>

            
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (event) {
                event.preventDefault();
                userlogin();
            });
        });

        function userlogin() {
            $('#hoot-loader').css('display', 'flex');

            var formdata = {
                email: $('#email').val().trim(),
                password: $('#password').val()
            };

            $('#message').removeClass('error success').text('');
            $('#submitButton').prop('disabled', true).text('Signing In...');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '{{ url('/api/userlogin') }}',
                data: formdata,
                success: function (result) {
                    if (result.status == 200 && result.data) {
                        var token = result.data.token || '';
                        var user = result.data.user || null;

                        if (token) {
                            localStorage.setItem('smart-office-token', token);
                        }

                        if (user) {
                            localStorage.setItem('smart-office-user', JSON.stringify(user));
                        }

                        $('#message').removeClass('error').addClass('success').text('Login successful.');
                        $('#loginForm')[0].reset();

                        setTimeout(function () {
                            var roleId = Number(user && user.role_id ? user.role_id : 0);
                            var roleName = String(user && user.role_name ? user.role_name : '').toLowerCase().trim();
                            var isAdminSide = roleId === 1 || roleId === 2 || roleName.indexOf('admin') !== -1 || roleName.indexOf('manager') !== -1;
                            window.location.href = isAdminSide ? '/dashboard' : '/user-dashboard';
                        }, 600);
                    } else {
                        $('#message').removeClass('success').addClass('error').text(extractError(result));
                    }
                },
                error: function (xhr) {
                    var response = xhr.responseJSON || {};
                    $('#message').removeClass('success').addClass('error').text(extractError(response));
                },
                complete: function () {
                    $('#hoot-loader').hide();
                    $('#submitButton').prop('disabled', false).text('Sign In');
                }
            });
        }

        function extractError(payload) {
            if (!payload) {
                return 'Login failed. Please try again.';
            }

            if (typeof payload === 'string') {
                return payload;
            }

            if (payload.error) {
                if (typeof payload.error === 'string') {
                    return payload.error;
                }

                if (typeof payload.error === 'object') {
                    return Object.values(payload.error)
                        .flat()
                        .filter(Boolean)
                        .join(' ');
                }
            }

            if (payload.message) {
                return payload.message;
            }

            return 'Login failed. Please try again.';
        }
    </script>
</body>
</html>
    