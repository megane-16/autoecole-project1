<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Information concernant votre demande</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #721c24 0%, #a71d2a 100%); padding: 24px; border-radius: 12px; text-align: center; color: #fff;">
        <h1 style="margin: 0; font-size: 24px;">DriveFlow</h1>
        <p style="margin: 8px 0 0; opacity: 0.9;">Statut de votre demande d'inscription</p>
    </div>

    <div style="background: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; padding: 24px; margin-top: 20px;">
        <p>Bonjour <strong>{{ $request->name }}</strong>,</p>
        
        <div style="background: #fff5f5; border-left: 4px solid #dc3545; padding: 16px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-size: 16px; color: #721c24; font-weight: bold;">
                votre demande a ete refuse veiller passer a l'agence pour plus d'information
            </p>
        </div>

        <p style="font-size: 14px; color: #555;">
            L'équipe de l'auto-école reste à votre entière disposition sur place pour vous accompagner dans vos démarches d'inscription.
        </p>
    </div>

    <p style="text-align: center; font-size: 12px; color: #999; margin-top: 20px;">
        &copy; {{ date('Y') }} DriveFlow. Tous droits réservés.
    </p>
</body>
</html>
