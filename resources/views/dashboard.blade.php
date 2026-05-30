<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <style>
        :root {
            --blue: #7bbcff;
            --blue-dark: #2f6ea8;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--white);
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(123, 188, 255, 0.08), rgba(255, 255, 255, 1));
        }

        .panel {
            text-align: center;
            padding: 40px 28px;
            border: 1px solid #d7eafb;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 50px rgba(47, 110, 168, 0.08);
        }

        .panel h1 {
            margin: 0;
            font-size: 42px;
            line-height: 1;
            color: var(--blue-dark);
            letter-spacing: 0.02em;
        }

        .panel p {
            margin: 14px 0 0;
            color: var(--blue);
            font-size: 15px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <main class="panel">
        <h1>Coming Soon!!!</h1>
        <p>Dashboard</p>
    </main>
</body>
</html>
