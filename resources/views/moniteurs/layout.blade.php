<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Espace Moniteur' }} - DriveFlow</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <style>
        :root {
            --df-primary: #2e7d32;
            --df-primary-hover: #1b5e20;
            --df-primary-light: #e8f5e9;
            --df-bg: #f8fafc;
            --df-card-bg: #ffffff;
            --df-border: #e2e8f0;
            --df-text-main: #1e293b;
            --df-text-muted: #64748b;
            --df-success: #16a34a;
            --df-warning: #d97706;
            --df-danger: #dc2626;
            --df-info: #2563eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--df-bg);
            color: var(--df-text-main);
            min-height: 100vh;
            display: flex;
        }

        .df-layout-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .df-main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-x: hidden;
        }

        .df-content-area {
            flex: 1;
            padding: 24px 32px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        /* Flash Messages */
        .df-alert {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .df-alert-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .df-alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Buttons & Forms Common */
        .df-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .df-btn-primary {
            background-color: var(--df-primary);
            color: #ffffff;
        }

        .df-btn-primary:hover {
            background-color: var(--df-primary-hover);
        }

        .df-btn-outline {
            border-color: var(--df-border);
            background-color: #ffffff;
            color: var(--df-text-main);
        }

        .df-btn-outline:hover {
            background-color: #f1f5f9;
        }

        .df-btn-danger {
            background-color: #fee2e2;
            color: var(--df-danger);
            border-color: #fecaca;
        }

        .df-btn-danger:hover {
            background-color: #fca5a5;
        }

        .df-btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Badge styles */
        .df-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .df-badge-green { background-color: #dcfce7; color: #15803d; }
        .df-badge-blue { background-color: #dbeafe; color: #1d4ed8; }
        .df-badge-gold { background-color: #fef3c7; color: #b45309; }
        .df-badge-red { background-color: #fee2e2; color: #b91c1c; }
        .df-badge-gray { background-color: #f1f5f9; color: #475569; }

        @media (max-width: 768px) {
            .df-content-area {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="df-layout-container">
        <!-- Moniteur Sidebar -->
        @include('moniteurs.sidebar')

        <div class="df-main-wrapper">
            <!-- Moniteur Header -->
            @include('moniteurs.hearder')

            <main class="df-content-area">
                @if (session('success'))
                    <div class="df-alert df-alert-success">
                        <span>✓ {{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="df-alert df-alert-error">
                        <span>⚠ {{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="df-alert df-alert-error">
                        <div>
                            <strong>Erreurs rencontrées :</strong>
                            <ul style="margin-top: 5px; padding-left: 18px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
