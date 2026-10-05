<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | {{ config('app.name', 'DriveFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #17342d; /* Vert pâle */
        }
        .btn-success-custom {
            background-color: #17342d;
            border-color: #17342d;
            color: #ffffff;
        }
        .btn-success-custom:hover {
            background-color: #17342d;
            border-color: #17342d;
            color: #ffffff;
        }
        .brand-text, .link-custom {
            color: #17342d;
        }
        .link-custom:hover {
            color: #17342d;
        }
        .form-control:focus, .form-check-input:focus {
            border-color: #81c784;
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.2);
        }
        .form-check-input:checked {
            background-color: #17342d;
            border-color: #17342d;
        }
    </style>
</head>
<<!-- ICI SUR LE BODY : -->
<body class="min-h-screen flex items-center justify-center p-4">
    <main class="card bg-white shadow rounded-4 border-0" style="max-width: 460px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <div class="mb-4 text-center">
                <div class="fw-bold fs-3 mb-2 brand-text">DriveFlow</div>
                <h1 class="h4 mb-1 text-dark">Se connecter</h1>
                <p class="text-muted small mb-0">Accédez à votre espace auto-école.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mb-4 py-2 px-3 small" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="row g-3">
                @csrf

                <div class="col-12">
                    <label for="email" class="form-label text-secondary small fw-semibold">Adresse e-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="form-control form-control-lg fs-6" placeholder="contact@driveflow.fr">
                </div>

                <div class="col-12">
                    <label for="password" class="form-label text-secondary small fw-semibold">Mot de passe</label>
                    <div class="position-relative">
                        <input id="password" name="password" type="password" required autocomplete="current-password" class="form-control form-control-lg fs-6 pe-5" placeholder="••••••••">
                        <button type="button" data-toggle-password="password" class="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-decoration-none text-muted">Afficher</button>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input name="remember" type="checkbox" value="1" class="form-check-input" id="rememberMe">
                        <label class="form-check-label text-muted small" for="rememberMe">Se souvenir de moi</label>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-success-custom w-100 py-2 fw-semibold shadow-sm">Connexion</button>
                </div>
            </form>

            <p class="text-center mt-4 mb-0 text-muted small">
                Pas encore de compte ? <a href="{{ route('register') }}" class="link-custom text-decoration-none fw-semibold">Créer un compte</a>
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
