<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | {{ config('app.name', 'DriveFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center px-3 py-5">
    <main class="card shadow-sm border-0" style="max-width: 460px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <div class="mb-4 text-center">
                <div class="fw-bold fs-4 mb-2">DriveFlow</div>
                <h1 class="h3 mb-1">Se connecter</h1>
                <p class="text-muted mb-0">Accédez à votre espace auto-école.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="row g-3">
                @csrf

                <div class="col-12">
                    <label for="email" class="form-label">Adresse e-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="form-control" placeholder="contact@driveflow.fr">
                </div>

                <div class="col-12">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="position-relative">
                        <input id="password" name="password" type="password" required autocomplete="current-password" class="form-control pe-5" placeholder="••••••••">
                        <button type="button" data-toggle-password="password" class="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-decoration-none">Afficher</button>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input name="remember" type="checkbox" value="1" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary w-100">Connexion</button>
                </div>
            </form>

            <p class="text-center mt-4 mb-0 text-muted">
                Pas encore de compte ? <a href="{{ route('register') }}" class="text-decoration-none">Créer un compte</a>
            </p>
        </div>
    </main>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.togglePassword);
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.textContent = visible ? 'Afficher' : 'Masquer';
            });
        });
    </script>
</body>
</html>
