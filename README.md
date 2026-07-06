# Arena Sport Club

Sistema de gestión de turnos para un club deportivo: reserva de canchas, pago con seña vía Mercado Pago, control de acceso por QR y notificaciones automáticas por WhatsApp.

Proyecto académico (cátedra Programación III), Laravel 12 + Livewire/Volt.

## Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Livewire 3 + Volt (componentes de página como single-file components), Tailwind CSS, Vite
- **Base de datos:** SQLite (desarrollo)
- **Pagos:** Mercado Pago Checkout Pro (`mercadopago/dx-php` v3)
- **WhatsApp:** Twilio (`twilio/sdk`)
- **QR:** `simplesoftwareio/simple-qrcode` (SVG, para mostrar en el navegador) y `chillerlan/php-qrcode` (PNG vía GD, para adjuntar como imagen en WhatsApp)

## Funcionalidades

- Registro/login, roles de usuario y admin
- Alta de deportes y canchas, generación automática de turnos disponibles
- Búsqueda y reserva de canchas con seña a través de Mercado Pago (Checkout Pro + webhook de confirmación)
- Cancelación de reservas con política de reembolso según anticipación
- Generación automática de un QR de acceso al confirmarse el pago de un turno
- Confirmación instantánea por WhatsApp al aprobarse el pago
- Recordatorio automático por WhatsApp 12hs antes del turno, con el QR de acceso adjunto
- Endpoint de validación de QR (`POST /api/reservations/validate`) para el sistema de control de accesos de otro grupo de la cátedra
- Panel de administración: canchas, deportes, configuración del club, turnos, reembolsos pendientes
- Valoraciones de cancha, torneos, botón flotante de contacto por WhatsApp

## Requisitos

- PHP 8.2+ con extensión `gd` habilitada
- Composer
- Node.js + npm
- Cuenta de Mercado Pago (credenciales de prueba) y de Twilio (para WhatsApp) si vas a probar esas integraciones

## Instalación

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

npm run build
php artisan serve
```

La app queda en `http://localhost:8000`.

### Usuarios de prueba (creados por el seeder)

| Rol | Email | Contraseña |
|---|---|---|
| Usuario | `test@example.com` | `password` |
| Admin | `arenasportsclub@email.com` | `arenasport` |

### Generar turnos disponibles

El seeder no crea turnos; se generan con:

```bash
php artisan turns:generate --days=30
```

## Variables de entorno relevantes

Además de las estándar de Laravel, este proyecto usa:

```
# Mercado Pago (Checkout Pro)
MP_ACCESS_TOKEN=
MP_PUBLIC_KEY=
# Opcional: URL publica (tunel cloudflared/ngrok) para recibir el webhook en local
MP_WEBHOOK_URL=

# Twilio (recordatorios por WhatsApp)
TWILIO_SID=
TWILIO_AUTH_TOKEN=
TWILIO_WHATSAPP_FROM=
```

Sin estas credenciales, el flujo de pago y los mensajes de WhatsApp no van a funcionar, pero el resto de la app (navegación, canchas, admin) sí.

Para probar el webhook de Mercado Pago o el envío de WhatsApp con el QR adjunto en local, `localhost` no es alcanzable desde afuera — hace falta un túnel público (`cloudflared tunnel --url http://localhost:8000` o `ngrok`) y setear `APP_URL` y `MP_WEBHOOK_URL` con esa URL pública mientras se prueba.

## El recordatorio de WhatsApp

Corre como un comando programado cada hora (`routes/console.php`):

```bash
php artisan turnos:recordatorio
```

Para que corra automáticamente hace falta el scheduler de Laravel activo (`php artisan schedule:work` en desarrollo, o un cron real en producción apuntando a `php artisan schedule:run`).

## Tests

```bash
php artisan test
```

## Probar el endpoint de validación de QR

En `postman/` hay una colección de Postman (`ArenaSportClub-QR-Validation.postman_collection.json`) con los casos de aprobado/rechazado ya armados para `POST /api/reservations/validate`. Ver `postman/README.md` para generar turnos de prueba con QR válidos.
