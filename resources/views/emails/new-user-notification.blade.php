<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario Registrado</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0a1744; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .user-info { background: #fff; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #10b981; }
        .button { display: inline-block; background: #c9a227; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nuevo Usuario Registrado</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $admin->name }}</strong>,</p>

        <p>Se ha registrado un nuevo usuario en {{ config('app.name') }}.</p>

        <div class="user-info">
            <p><strong>Información del nuevo usuario:</strong></p>
            <p><strong>Nombre:</strong> {{ $newUser->full_name }}</p>
            <p><strong>Email:</strong> {{ $newUser->email }}</p>
            <p><strong>Rol:</strong> {{ ucfirst($newUser->role) }}</p>
            <p><strong>Fecha de registro:</strong> {{ $newUser->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        <p style="text-align: center;">
            <a href="{{ config('app.url') }}/admin/users/{{ $newUser->id }}" class="button">Ver Usuario</a>
        </p>

        <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Este correo fue enviado automáticamente. Por favor no responda a este mensaje.</p>
    </div>
</body>
</html>
