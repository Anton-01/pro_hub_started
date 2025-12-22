<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al Panel de Administración</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0a1744; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #c9a227; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .credentials { background: #fff; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #c9a227; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Bienvenido al Panel de Administración!</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>

        <p>Tu cuenta de administrador ha sido creada exitosamente en {{ config('app.name') }}.</p>

        @if($temporaryPassword)
        <div class="credentials">
            <p><strong>Datos de acceso:</strong></p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Contraseña temporal:</strong> {{ $temporaryPassword }}</p>
            <p style="color: #ef4444; font-size: 12px;">* Te recomendamos cambiar tu contraseña después de iniciar sesión.</p>
        </div>
        @endif

        <p>Como administrador, podrás:</p>
        <ul>
            <li>Gestionar usuarios de la empresa</li>
            <li>Configurar módulos del dashboard</li>
            <li>Administrar noticias y eventos</li>
            <li>Personalizar la apariencia del panel</li>
        </ul>

        <p style="text-align: center;">
            <a href="{{ config('app.url') }}/admin" class="button">Acceder al Panel</a>
        </p>

        <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>

        <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Este correo fue enviado automáticamente. Por favor no responda a este mensaje.</p>
    </div>
</body>
</html>
