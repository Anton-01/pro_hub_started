<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraseña Cambiada</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0a1744; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .alert { background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contraseña Actualizada</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>

        <p>Te informamos que la contraseña de tu cuenta en {{ config('app.name') }} ha sido cambiada exitosamente.</p>

        <p><strong>Fecha y hora:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>

        <div class="alert">
            <p><strong>¿No realizaste este cambio?</strong></p>
            <p>Si no fuiste tú quien cambió la contraseña, tu cuenta podría estar comprometida. Por favor:</p>
            <ol style="margin: 0;">
                <li>Intenta restablecer tu contraseña inmediatamente.</li>
                <li>Contacta al administrador de tu empresa.</li>
                <li>Revisa la actividad reciente de tu cuenta.</li>
            </ol>
        </div>

        <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Este correo fue enviado automáticamente. Por favor no responda a este mensaje.</p>
    </div>
</body>
</html>
