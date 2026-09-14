<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Creer une auto-ecole | {{ config('app.name', 'DriveFlow') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center px-3 py-5">
    <main class="container" style="max-width: 760px;">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 text-center">
                    <div class="fw-bold fs-4 mb-2">DriveFlow</div>
                    <h1 class="h3 mb-1">Creer votre auto-ecole</h1>
                    <p class="text-muted mb-0">Renseignez les informations de votre établissement pour démarrer.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="row g-4">
                    @csrf

                    <div class="col-md-6">
                        <label for="team_name" class="form-label">Nom de l’auto-ecole</label>
                        <input id="team_name" name="team_name" value="{{ old('team_name') }}" required class="form-control" placeholder="Ex. DriveFlow Academy">
                    </div>

                    <div class="col-md-6">
                        <label for="ville" class="form-label">Ville</label>
                        <input id="ville" name="ville" value="{{ old('ville') }}" required class="form-control" placeholder="Ex. Paris">
                    </div>

                    <div class="col-12">
                        <span class="form-label d-block mb-3">Types de permis proposes (au moins 2)</span>
                        <div class="row g-3">
                            @foreach ($permitCategories as $category)
                                <div class="col-6 col-sm-4">
                                    <label class="d-flex align-items-center gap-2 border rounded p-3 h-100 mb-0 bg-white">
                                        <input type="checkbox" name="categories_permis[]" value="{{ $category }}" class="form-check-input mt-0" @checked(in_array($category, old('categories_permis', []), true))>
                                        <span class="fw-medium">Permis {{ $category }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="form-control" placeholder="contact@driveflow.fr">
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="position-relative">
                            <input id="password" name="password" type="password" required autocomplete="new-password" class="form-control pe-5" placeholder="••••••••">
                            <button type="button" class="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-decoration-none" data-toggle-password="password">Afficher</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <div class="position-relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="form-control pe-5" placeholder="••••••••">
                            <button type="button" class="btn btn-link btn-sm position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-decoration-none" data-toggle-password="password_confirmation">Afficher</button>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary w-100">Creer mon auto-ecole</button>
                    </div>
                </form>

                <p class="text-center mt-4 mb-0 text-muted">
                    Déjà inscrit ? <a href="{{ route('login') }}" class="text-decoration-none">Se connecter</a>
                </p>
            </div>
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
