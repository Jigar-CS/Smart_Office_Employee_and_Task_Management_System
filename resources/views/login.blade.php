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
            background-image: url('{{ asset('office.jpg') }}');
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
        }

        .card {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid var(--blue-soft);
            border-radius: 20px;
            padding: 42px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid var(--blue-soft);
            background: rgba(255, 255, 255, 0.95);
            object-fit: contain;
            display: block;
        }

        .brand-name {
            color: var(--brand-blue);
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 32px;
            line-height: 1.1;
            color: var(--brand-blue);
        }

        p {
            margin: 0 0 26px;
            color: #4f8fcb;
            font-size: 14px;
            line-height: 1.6;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4f8fcb;
            font-size: 13px;
            font-weight: 700;
        }

        input {
            width: 100%;
            height: 48px;
            border: 1px solid #c6def7;
            border-radius: 12px;
            padding: 0 14px;
            font-size: 15px;
            color: #2e6fbb;
            background: var(--white);
            outline: none;
        }

        input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(123, 188, 255, 0.18);
        }

        .actions {
            margin-top: 22px;
        }

        button {
            width: 100%;
            height: 50px;
            border: 0;
            border-radius: 12px;
            background: var(--blue);
            color: var(--white);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
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
            color: #dc2626;
        }

        .message.success {
            color: #16a34a;
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

            h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="card">
            <div class="brand">
                <img class="brand-logo" src="{{ asset('logo.jpg') }}" alt="Smart Office logo">
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

    <script>
        const form = document.getElementById('loginForm');
        const message = document.getElementById('message');
        const submitButton = document.getElementById('submitButton');

        function setMessage(text, type) {
            message.textContent = text;
            message.className = 'message ' + (type || '');
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

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            setMessage('', '');
            submitButton.disabled = true;
            submitButton.textContent = 'Signing In...';

            try {
                const response = await fetch('/api/userlogin', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (!response.ok || data.status !== 200) {
                    throw new Error(extractError(data));
                }

                const token = data?.data?.token || '';
                const user = data?.data?.user || null;

                if (token) {
                    localStorage.setItem('smart-office-token', token);
                }

                if (user) {
                    localStorage.setItem('smart-office-user', JSON.stringify(user));
                }

                setMessage('Login successful.', 'success');
                form.reset();

                setTimeout(function () {
                    window.location.href = '/dashboard';
                }, 600);
            } catch (error) {
                setMessage(error.message || 'Login failed. Please try again.', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Sign In';
            }
        });
    </script>
</body>
</html>
