<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DriveFlow') }} | Apprenez a conduire sereinement</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f5f7f2; color: #17342d; font-family: ui-sans-serif, system-ui, sans-serif; }
        a { color: inherit; }
        .home-shell { min-height: 100vh; overflow: hidden; background: radial-gradient(circle at 82% 12%, #d9eee2 0, transparent 30%), #f5f7f2; }
        .home-nav, .hero, .features { width: min(1120px, calc(100% - 40px)); margin: auto; }
        .home-nav { padding: 24px 0; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; font-weight: 800; letter-spacing: -.03em; font-size: 1.15rem; }
        .brand-mark { width: 38px; height: 38px; display: grid; place-items: center; border-radius: 12px; background: #e6a43a; box-shadow: 0 7px 18px #e6a43a44; }
        .nav-actions, .hero-actions { display: flex; align-items: center; gap: 12px; }
        .nav-link { color: #33554b; text-decoration: none; font-size: .92rem; font-weight: 650; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 45px; padding: 0 20px; border-radius: 999px; text-decoration: none; font-weight: 750; transition: transform .2s ease, box-shadow .2s ease, background .2s ease; }
        .button:hover { transform: translateY(-2px); }
        .button-primary { background: #17342d; color: white; box-shadow: 0 10px 22px #17342d26; }
        .button-primary:hover { background: #285347; }
        .button-light { background: white; border: 1px solid #d7e2db; }
        .hero { padding: 72px 0 92px; display: grid; grid-template-columns: 1.05fr .95fr; gap: 72px; align-items: center; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; color: #b56d12; font-size: .78rem; text-transform: uppercase; letter-spacing: .13em; font-weight: 850; }
        .eyebrow::before { content: ''; width: 28px; height: 2px; background: #e6a43a; }
        h1 { max-width: 650px; margin: 18px 0; font-size: clamp(3rem, 7vw, 5.5rem); line-height: .98; letter-spacing: -.075em; font-weight: 850; }
        .hero-copy { max-width: 520px; margin: 0 0 30px; color: #527066; font-size: 1.08rem; line-height: 1.7; }
        .hero-note { margin-top: 22px; color: #6f877e; font-size: .85rem; }
        .visual { position: relative; min-height: 440px; display: grid; place-items: center; }
        .visual::before { content: ''; position: absolute; width: 390px; height: 390px; border-radius: 50%; background: #d6ecdf; }
        .road-card { position: relative; width: min(100%, 430px); min-height: 340px; padding: 28px; border-radius: 28px; background: #17342d; color: white; box-shadow: 0 26px 55px #17342d30; transform: rotate(3deg); }
        .road-card::after { content: ''; position: absolute; right: -18px; bottom: -18px; width: 105px; height: 105px; border-radius: 24px; background: #e6a43a; z-index: -1; }
        .card-top { display: flex; justify-content: space-between; color: #b8d2c6; font-size: .78rem; }
        .status { color: #f4ce82; }
        .road-card h2 { max-width: 250px; margin: 65px 0 12px; font-size: 2.25rem; line-height: 1.02; letter-spacing: -.05em; }
        .road-card p { color: #b8d2c6; line-height: 1.5; }
        .route-line { position: absolute; left: 28px; right: 28px; bottom: 30px; height: 3px; background: repeating-linear-gradient(90deg, #e6a43a 0 34px, transparent 34px 53px); }
        .features { padding: 28px 0 80px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .feature { padding: 25px; border-top: 1px solid #cbdcd3; }
        .feature-number { color: #b56d12; font-weight: 850; font-size: .8rem; }
        .feature h3 { margin: 14px 0 8px; font-size: 1.15rem; }
        .feature p { margin: 0; color: #668078; line-height: 1.55; font-size: .92rem; }
        @media (max-width: 760px) {
            .home-nav, .hero, .features { width: min(100% - 28px, 1120px); }
            .nav-actions { gap: 8px; }
            .nav-link { display: none; }
            .hero { padding: 52px 0 64px; grid-template-columns: 1fr; gap: 35px; }
            h1 { font-size: clamp(3.1rem, 16vw, 4.8rem); }
            .visual { min-height: 340px; }
            .visual::before { width: 300px; height: 300px; }
            .road-card { min-height: 300px; }
            .features { grid-template-columns: 1fr; padding-bottom: 45px; }
        }
    </style>
</head>
<body>
    <div class="home-shell">
        <header class="home-nav">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark">↗</span>
                <span>{{ config('app.name', 'DriveFlow') }}</span>
            </a>
            @guest
                <nav class="nav-actions" aria-label="Navigation d'authentification">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    @if (Route::has('register'))
                        <a class="button button-primary" href="{{ route('register') }}">Register</a>
                    @endif
                </nav>
            @else
                @php
                    $team = auth()->user()->currentTeam ?? auth()->user()->personalTeam();
                @endphp
                <nav class="nav-actions" aria-label="Navigation utilisateur">
                    @if ($team)
                        <a class="button button-primary" href="{{ route('dashboard', ['current_team' => $team->slug]) }}">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link">Logout</button>
                    </form>
                </nav>
            @endguest
        </header>

        <main>
            <section class="hero">
                <div>
                    <span class="eyebrow">Votre route commence ici</span>
                    <h1>Conduisez votre avenir avec confiance.</h1>
                    <p class="hero-copy">DriveFlow simplifie le suivi de votre auto-ecole et accompagne chaque apprenant, de la premiere lecon jusqu'a la reussite.</p>
                    <div class="hero-actions">
                        @guest
                            <a class="button button-primary" href="{{ route('register') }}">Commencer maintenant&nbsp; ↗</a>
                            <a class="button button-light" href="{{ route('login') }}">J'ai deja un compte</a>
                        @else
                            @if ($team)
                                <a class="button button-primary" href="{{ route('dashboard', ['current_team' => $team->slug]) }}">Acceder au tableau de bord</a>
                            @endif
                        @endguest
                    </div>
                    <p class="hero-note">Une experience simple, claire et pensee pour avancer sereinement.</p>
                </div>
                <div class="visual" aria-label="Illustration d'un parcours de conduite">
                    <div class="road-card">
                        <div class="card-top"><span>PARCOURS DU JOUR</span><span class="status">En route</span></div>
                        <h2>Chaque progres compte.</h2>
                        <p>Organisez vos etapes et gardez le cap sur votre objectif.</p>
                        <div class="route-line"></div>
                    </div>
                </div>
            </section>
            <section class="features" aria-label="Avantages de DriveFlow">
                <article class="feature"><span class="feature-number">01 / SUIVI</span><h3>Voyez vos progres</h3><p>Retrouvez les informations importantes au meme endroit.</p></article>
                <article class="feature"><span class="feature-number">02 / CLARTE</span><h3>Gagnez du temps</h3><p>Un espace organise pour les auto-ecoles et les apprenants.</p></article>
                <article class="feature"><span class="feature-number">03 / CONFIANCE</span><h3>Avancez ensemble</h3><p>Chaque prochaine etape devient plus evidente.</p></article>
            </section>

            <section style="width: min(1120px, calc(100% - 40px)); margin: 40px auto 100px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 40px; letter-spacing: -0.04em; font-weight: 850;">Nos Auto-écoles Partenaires</h2>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
                    @forelse($partnerSchools as $school)
                         <div style="background: white; border: 1px solid #d7e2db; border-radius: 24px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div style="width: 60px; height: 60px; background: #e5f8f3; border-radius: 16px; display: grid; place-items: center; margin-bottom: 24px; color: #087a62; font-size: 1.5rem;">🏫</div>
                            <h3 style="font-size: 1.5rem; margin: 0 0 12px; font-weight: 800;">{{ $school->name }}</h3>
                            <p style="color: #6f877e; line-height: 1.6; margin-bottom: 24px;">Formation de qualité supérieure avec des moniteurs expérimentés. Rejoignez-nous pour obtenir votre permis rapidement.</p>
                            <a href="{{ route('postuler.show', ['team' => $school->slug]) }}" class="button button-primary" style="width: 100%;">Postuler</a>
                        </div>  
                       
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 24px; border: 1px dashed #cbdcd3;">
                            <p style="color: #6f877e;">Bientôt de nouvelles auto-écoles partenaires.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</body>
</html>
