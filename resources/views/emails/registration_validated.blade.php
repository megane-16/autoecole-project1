<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre code de connexion DriveFlow</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 24px; border-radius: 12px; text-align: center; color: #fff;">
        <h1 style="margin: 0; font-size: 24px;">DriveFlow</h1>
        <p style="margin: 8px 0 0; opacity: 0.9;">Validation de votre inscription</p>
    </div>

    <div style="background: #ffffff; border: 1px solid #e0e0e0; border-radius: 12px; padding: 24px; margin-top: 20px;">
        <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
        <p>Votre demande d'inscription a été acceptée par l'auto-école <strong>{{ $user->auto_ecole ?? 'DriveFlow' }}</strong>.</p>
        
        <div style="background: #f0f7ff; border: 2px dashed #2a5298; border-radius: 8px; padding: 16px; text-align: center; margin: 24px 0;">
            <p style="margin: 0 0 8px; font-size: 14px; color: #555;">voici votre code de connexion :</p>
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #1e3c72;">{{ $code }}</span>
        </div>

        <p style="font-size: 16px; font-weight: bold; color: #1e3c72; text-align: center;">
            voici votre code de connexion veiller poursuivre l'esperience sur driveflow
        </p>

        <p style="margin-top: 24px; font-size: 13px; color: #777;">
            Ouvrez votre application mobile <strong>DriveFlow</strong> et entrez votre adresse email ainsi que ce code pour accéder à votre espace d'apprentissage.
        </p>
    </div>

    <p style="text-align: center; font-size: 12px; color: #999; margin-top: 20px;">
        &copy; {{ date('Y') }} DriveFlow. Tous droits réservés.
    </p>
</body>
</html>
