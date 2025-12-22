<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0a1744; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #c9a227; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Bienvenido!</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>

        <p>Tu cuenta ha sido creada exitosamente en {{ config('app.name') }}.</p>

        <p>Ya puedes acceder al dashboard de tu empresa y disfrutar de todas las funcionalidades disponibles:</p>
        <ul>
            <li>Consultar el calendario de eventos</li>
            <li>Ver el directorio de contactos</li>
            <li>Acceder a los módulos configurados</li>
            <li>Revisar las noticias y anuncios</li>
        </ul>

        <p style="text-align: center;">
            <a href="{{ config('app.url') }}" class="button">Ir al Dashboard</a>
        </p>

        <p>Si tienes alguna pregunta, contacta al administrador de tu empresa.</p>

        <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Este correo fue enviado automáticamente. Por favor no responda a este mensaje.</p>
    </div>
</body>
</html>
