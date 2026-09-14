<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Postuler à {{ $team->name }} | DriveFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, sans-serif; color: #17342d; background: #f5f7f2; }
        .container { width: min(100% - 32px, 850px); margin: 0 auto; }
        .py-5 { padding: 48px 0; }
        .mb-4 { margin-bottom: 24px; }
        .mb-5 { margin-bottom: 38px; }
        .text-decoration-none { text-decoration: none; }
        .text-muted { color: #668078; }
        .text-center { text-align: center; }
        .h2 { margin: 0 0 10px; font-size: clamp(1.7rem, 5vw, 2.15rem); }
        .fw-bold { font-weight: 800; }
        .card { background: #fff; }
        .shadow-sm { box-shadow: 0 18px 45px rgb(23 52 45 / 10%); }
        .border-0 { border: 0; }
        .rounded-4 { border-radius: 24px; }
        .overflow-hidden { overflow: hidden; }
        .card-body { padding: 32px; }
        .row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .col-md-6 { min-width: 0; }
        .col-12 { grid-column: 1 / -1; }
        .form-label { display: block; font-weight: 700; color: #17342d; margin-bottom: 8px; }
        .form-control, .form-select { width: 100%; color: #17342d; background: #fff; border-radius: 12px; padding: 12px 16px; border: 1px solid #d7e2db; font: inherit; }
        .form-control:focus, .form-select:focus { outline: 0; border-color: #e6a43a; box-shadow: 0 0 0 4px rgba(230, 164, 58, 0.16); }
        .form-check { display: flex; align-items: flex-start; gap: 10px; }
        .form-check-input { width: 18px; height: 18px; margin-top: 2px; accent-color: #17342d; }
        .border { border: 1px solid #d7e2db; }
        .rounded-3 { border-radius: 14px; }
        .p-3 { padding: 16px; }
        .bg-white { background: #fff; }
        .alert { padding: 16px; }
        .alert-danger { color: #9f2828; background: #fff0f0; border: 1px solid #f2c5c5; }
        .w-100 { width: 100%; }
        .btn-postule { background: #17342d; color: white; padding: 14px; border-radius: 16px; font-weight: 800; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-postule:hover { background: #285347; transform: translateY(-2px); }
        @media (max-width: 640px) { .py-5 { padding: 24px 0; } .card-body { padding: 24px 18px; } .row { grid-template-columns: 1fr; } .col-12 { grid-column: auto; } }
    </style>
</head>
<body class="py-5">
    <div class="container" style="max-width: 850px;">
        <div class="mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted" style="font-weight: 600;">← Retour à l'accueil</a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div style="height: 10px; background: #e6a43a;"></div>
            <div class="card-body">
                <div class="mb-5 text-center">
                    <div class="h2 fw-bold" style="letter-spacing: -0.03em;">Candidature : {{ $team->name }}</div>
                    <p class="text-muted">Inscrivez-vous pour rejoindre notre établissement et commencer votre formation.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('postuler.store', $team->slug) }}" class="row">
                    @csrf
                    <input type="hidden" name="auto_ecole_id" value="{{ $team->id }}">
                    <input type="hidden" name="auto_ecole" value="{{ $team->name }}">

                    <div class="col-md-6">
                        <label class="form-label">Nom Complet</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="Ex. Jean Dupont">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control" placeholder="exemple@mail.com">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" class="form-control" placeholder="+237 ...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville') }}" class="form-control" placeholder="Ex. Douala">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Genre</label>
                        <select name="sexe" class="form-select">
                            <option value="">Sélectionner</option>
                            <option value="Homme" @selected(old('sexe') == 'Homme')>Homme</option>
                            <option value="Femme" @selected(old('sexe') == 'Femme')>Femme</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Catégorie de permis</label>
                        <select name="categorie_permis" class="form-select">
                            <option value="B" @selected(old('categorie_permis') == 'B')>Permis B (Voiture)</option>
                            <option value="A" @selected(old('categorie_permis') == 'A')>Permis A (Moto)</option>
                            <option value="C" @selected(old('categorie_permis') == 'C')>Permis C (Poids lourd)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Préférence horaire</label>
                        <select name="horaire_formation" class="form-select">
                            <option value="Matin" @selected(old('horaire_formation') == 'Matin')>Session du Matin</option>
                            <option value="Après-midi" @selected(old('horaire_formation') == 'Après-midi')>Session d'Après-midi</option>
                            <option value="Soir" @selected(old('horaire_formation') == 'Soir')>Session du Soir</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" required class="form-control" placeholder="••••••••">
                        <small class="text-muted">Minimum 8 caractères. Servira pour l'accès mobile.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" required class="form-control" placeholder="••••••••">
                    </div>

                    <div class="col-12">
                        <div class="form-check p-3 border rounded-3 bg-white">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="has_cni" id="has_cni" value="1" @checked(old('has_cni'))>
                            <label class="form-check-label fw-bold" for="has_cni">
                                Je confirme posséder une pièce d'identité valide (CNI ou Passeport)
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn-postule w-100 py-3 shadow-sm">ENVOYER MA DEMANDE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
