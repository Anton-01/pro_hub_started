<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0a1744; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #c9a227; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fef3cd; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Restablecer Contraseña</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>

        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en {{ config('app.name') }}.</p>

        <p style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Restablecer Contraseña</a>
        </p>

        <div class="warning">
            <p><strong>Importante:</strong></p>
            <ul style="margin: 0;">
                <li>Este enlace expirará en 1 hora.</li>
                <li>Si no solicitaste este cambio, ignora este correo.</li>
                <li>Tu contraseña no cambiará hasta que hagas clic en el enlace.</li>
            </ul>
        </div>

        <p>Si tienes problemas con el botón, copia y pega este enlace en tu navegador:</p>
        <p style="word-break: break-all; font-size: 12px; color: #666;">{{ $resetUrl }}</p>

        <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Este correo fue enviado automáticamente. Por favor no responda a este mensaje.</p>
    </div>
</body>
</html>
