<x-layouts::app :title="__('Demandes d\'inscription')">
    <style>
        .demandes-container {
            padding: 22px 34px 28px;
        }
        .demandes-card {
            border: 1px solid #e7edf2;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 5px 18px rgb(31 49 65 / 4%);
            padding: 20px;
            overflow-x: auto;
        }
        .demandes-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }
        .demandes-table th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #f0f3f5;
            color: #718096;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .demandes-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f0f3f5;
            font-size: 0.85rem;
        }
        .action-btn {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        .btn-validate {
            background-color: #e5f8f3;
            color: #087a62;
        }
        .btn-delete {
            background-color: #fdeaea;
            color: #dc3545;
        }
        .btn-view {
            background-color: #e9f0ff;
            color: #2458bb;
        }
        @media (max-width: 800px) {
            .demandes-container { padding: 18px; }
        }
    </style>

    <div class="demandes-container">
        <header style="margin-bottom: 24px;">
            <h1 style="font-size: 2rem; letter-spacing: -0.03em;">Demandes d'inscription</h1>
            <p style="color: #718096;">Gérez les candidatures des nouveaux apprenants.</p>
        </header>

        @if (session('success'))
            <div style="background: #e5f8f3; color: #087a62; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="demandes-card">
            <table class="demandes-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Permis</th>
                        <th>Statut</th>
                        <th>Date Demande</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td style="font-weight: 700;">{{ $request->name }}</td>
                            <td>{{ $request->email }}</td>
                            <td>{{ $request->telephone }}</td>
                            <td><span style="padding: 4px 8px; background: #f0f3f5; border-radius: 6px;">{{ $request->categorie_permis ?? 'B' }}</span></td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 6px; font-size: .75rem; font-weight: 700; {{ $request->status === 'validated' ? 'background:#e5f8f3;color:#087a62' : 'background:#fff4d8;color:#9a6700' }}">
                                    {{ $request->status === 'validated' ? 'Validée' : 'En attente' }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td style="display: flex; gap: 8px;">
                                <a href="{{ route('demandes.show', [$team->slug, $request->id]) }}" class="action-btn btn-view">Détails</a>
                                @if ($request->status === 'pending')
                                    <form action="{{ route('demandes.validate', [$team->slug, $request->id]) }}" method="POST" onsubmit="return confirm('Valider cette inscription ?')">
                                        @csrf
                                        <button type="submit" class="action-btn btn-validate">Valider</button>
                                    </form>
                                @endif
                                <form action="{{ route('demandes.destroy', [$team->slug, $request->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette demande ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #718096; padding: 40px;">Aucune demande reçue.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top: 20px;">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</x-layouts::app>
