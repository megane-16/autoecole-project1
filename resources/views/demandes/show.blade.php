<x-layouts::app :title="__('Détails de la demande')">
    <style>
        .details-container {
            padding: 22px 34px 28px;
        }
        .details-card {
            border: 1px solid #e7edf2;
            border-radius: 14px;
            background: #fff;
            padding: 30px;
            box-shadow: 0 5px 18px rgb(31 49 65 / 4%);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }
        .info-item label {
            display: block;
            color: #718096;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .info-item span {
            font-size: 1rem;
            font-weight: 600;
            color: #17212b;
        }
        .actions-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid #f0f3f5;
            padding-top: 20px;
        }
    </style>

    <div class="details-container">
        <header style="margin-bottom: 24px; display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('demandes.index', $team->slug) }}" style="text-decoration: none; font-size: 1.5rem; color: #718096;">←</a>
            <h1 style="font-size: 2rem; letter-spacing: -0.03em;">Détails de {{ $request->name }}</h1>
        </header>

        <div class="details-card">
            <div class="info-grid">
                <div class="info-item"><label>Nom Complet</label><span>{{ $request->name }}</span></div>
                <div class="info-item"><label>Email</label><span>{{ $request->email }}</span></div>
                <div class="info-item"><label>Téléphone</label><span>{{ $request->telephone ?? 'Non renseigné' }}</span></div>
                <div class="info-item"><label>Ville</label><span>{{ $request->ville ?? 'Non renseignée' }}</span></div>
                <div class="info-item"><label>Sexe</label><span>{{ $request->sexe ?? 'Non renseigné' }}</span></div>
                <div class="info-item"><label>Date de Naissance</label><span>{{ $request->date_naissance ? $request->date_naissance->format('d/m/Y') : 'Non renseignée' }}</span></div>
                <div class="info-item"><label>Catégorie Permis</label><span>{{ $request->categorie_permis ?? 'B' }}</span></div>
                <div class="info-item"><label>Horaire souhaité</label><span>{{ $request->horaire_formation ?? 'Non renseigné' }}</span></div>
                <div class="info-item"><label>A fourni une CNI</label><span>{{ $request->has_cni ? 'Oui' : 'Non' }}</span></div>
                <div class="info-item"><label>Date de la demande</label><span>{{ $request->created_at->format('d/m/Y H:i') }}</span></div>
            </div>

            <div class="actions-footer">
                <form action="{{ route('demandes.destroy', [$team->slug, $request->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette demande ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="padding: 10px 20px; border-radius: 10px; background: #fdeaea; color: #dc3545; border: none; cursor: pointer; font-weight: 700;">Rejeter la demande</button>
                </form>
                @if ($request->status === 'pending')
                    <form action="{{ route('demandes.validate', [$team->slug, $request->id]) }}" method="POST">
                        @csrf
                        <button type="submit" style="padding: 10px 20px; border-radius: 10px; background: #2f6fed; color: #fff; border: none; cursor: pointer; font-weight: 700;">Valider et créer l'accès</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app>
