<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; display: grid; place-items: center; min-height: 100vh; }
        main { max-width: 36rem; padding: 2rem; text-align: center; }
        h1 { font-size: 2rem; margin-bottom: .5rem; }
        p { color: #94a3b8; line-height: 1.6; }
        a.btn { display: inline-block; margin-top: 1.5rem; padding: .75rem 1.5rem; background: #6366f1; color: #fff; border-radius: .5rem; text-decoration: none; font-weight: 600; }
        code { background: #1e293b; padding: .15rem .4rem; border-radius: .25rem; }
    </style>
</head>
<body>
<main>
    <h1>{{ config('app.name') }}</h1>
    <p>Laravel {{ app()->version() }} demo met het <a href="https://orchid.software" style="color:#a5b4fc">Orchid</a> adminpanel en gegenereerde mockup-data.</p>
    <p>Inloggen kan met <code>admin@example.com</code> / <code>password</code>.</p>
    <a class="btn" href="{{ route('platform.main') }}">Open het adminpanel</a>
</main>
</body>
</html>
