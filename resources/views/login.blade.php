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

        /* --- Custom Notification Container & Layout Styles --- */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
            width: 100%;
        }

        .custom-toast {
            color: #ffffff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            line-height: 1.4;
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s ease;
            opacity: 0;
        }

        /* Error Specific Theme */
        .custom-toast.toast-error {
            background: #e11d48; /* Premium Rose Crimson Red */
        }

        /* Success Specific Theme */
        .custom-toast.toast-success {
            background: #10b981; /* Premium Emerald Green */
        }

        .custom-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .custom-toast .toast-icon {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .custom-toast .toast-content {
            flex-grow: 1;
        }

        .custom-toast .toast-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .custom-toast .toast-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            line-height: 1;
            width: auto;
            height: auto;
            box-shadow: none;
        }

        .custom-toast .toast-close:hover {
            color: #ffffff;
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

            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div id="toastContainer" class="toast-container"></div>

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
                    <div style="position: relative;">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required style="padding-right: 46px;">
                        <button type="button" id="togglePassword" aria-label="Show password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 32px; height: 32px; border: 1px solid rgba(46,111,187,0.14); border-radius: 10px; background: rgba(255,255,255,0.96); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0;">
                            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" fill="none" stroke="#0b2540" stroke-width="2" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" fill="none" stroke="#0b2540" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="actions">
                    <button id="submitButton" type="submit">Sign In</button>
                </div>
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

            $('#togglePassword').on('click', function () {
                const $pwd = $('#password');
                const isHidden = $pwd.attr('type') === 'password';
                $pwd.attr('type', isHidden ? 'text' : 'password');
                $(this).attr('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        });

        // Reusable Notification Trigger Engine
        function showNotification(type, title, message) {
            var toastId = 'toast-' + Date.now();
            var iconSvg = type === 'success' 
                ? `<svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
                : `<svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;

            var toastHtml = `
                <div id="${toastId}" class="custom-toast toast-${type}">
                    <div class="toast-icon">${iconSvg}</div>
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        <div>${message}</div>
                    </div>
                    <button class="toast-close" onclick="closeToast('${toastId}')">&times;</button>
                </div>
            `;

            $('#toastContainer').append(toastHtml);
            setTimeout(function() {
                $(`#${toastId}`).addClass('show');
            }, 50);

            // Auto-remove notification after 5 seconds
            setTimeout(function() {
                closeToast(toastId);
            }, 5000);
        }

        function closeToast(id) {
            var $toast = $(`#${id}`);
            if ($toast.length) {
                $toast.removeClass('show');
                setTimeout(function() {
                    $toast.remove();
                }, 400);
            }
        }

        function userlogin() {
            $('#hoot-loader').css('display', 'flex');

            var formdata = {
                email: $('#email').val().trim(),
                password: $('#password').val()
            };

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

                        // Success notification instead of inside login-box label
                        showNotification('success', 'Success', 'Login successful. Redirecting...');
                        $('#loginForm')[0].reset();

                        setTimeout(function () {
                            var roleId = Number(user && user.role_id ? user.role_id : 0);
                            var roleName = String(user && user.role_name ? user.role_name : '').toLowerCase().trim();
                            var isAdminSide = roleId === 1 || roleId === 2 || roleName.indexOf('admin') !== -1 || roleName.indexOf('manager') !== -1;
                            window.location.href = isAdminSide ? '/dashboard' : '/user-dashboard';
                        }, 800);
                    } else {
                        var errorMsg = extractError(result);
                        showNotification('error', 'Login Error', errorMsg);
                    }
                },
                error: function (xhr) {
                    var response = xhr.responseJSON || {};
                    var errorMsg = extractError(response);
                    showNotification('error', 'Login Error', errorMsg);
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