# Configuración de Correo SMTP para INFRASTOCK

## Configuración en el archivo .env

Para que los correos lleguen de verdad, necesitas configurar las siguientes variables en tu archivo `.env`:

### Opción 1: Gmail (Recomendado para pruebas)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME="INFRASTOCK"
```

**Importante para Gmail:**
- Necesitas crear una "Contraseña de aplicación" en tu cuenta de Google
- Ve a: https://myaccount.google.com/apppasswords
- Genera una contraseña de aplicación y úsala en `MAIL_PASSWORD`

### Opción 2: Outlook/Hotmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@outlook.com
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_correo@outlook.com
MAIL_FROM_NAME="INFRASTOCK"
```

### Opción 3: Servidor SMTP personalizado

```env
MAIL_MAILER=smtp
MAIL_HOST=tu_servidor_smtp.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=correo@tudominio.com
MAIL_FROM_NAME="INFRASTOCK"
```

## Después de configurar

1. **Limpia la caché de configuración:**
   ```bash
   php artisan config:clear
   ```

2. **Prueba el envío de correo:**
   ```bash
   php artisan test:email tu_correo@ejemplo.com
   ```
   
   O simplemente:
   ```bash
   php artisan test:email
   ```
   (Te pedirá el correo de destino)

3. **Verifica que recibiste el correo** en la bandeja de entrada (y en spam si no aparece)

## Solución de problemas

### Si el correo no llega:

1. **Verifica los logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Para Gmail:**
   - Asegúrate de usar una "Contraseña de aplicación", no tu contraseña normal
   - Ve a: https://myaccount.google.com/apppasswords
   - Genera una contraseña de aplicación para "Correo"

3. **Verifica que el servidor SMTP esté accesible:**
   - Gmail: smtp.gmail.com:587
   - Outlook: smtp-mail.outlook.com:587

4. **Revisa la carpeta de spam** del correo destino

## Verificación

Una vez configurado, cuando cualquier rol cree una solicitud:
- ✅ Se creará una notificación en el dashboard del administrador
- ✅ Se enviará un correo electrónico real al administrador

