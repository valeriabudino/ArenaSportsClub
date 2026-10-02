#!/usr/bin/env bash
#
# Levanta un tunel publico de Cloudflare hacia la app (http://localhost) y
# actualiza APP_URL y MP_WEBHOOK_URL en el .env, para que Mercado Pago pueda
# avisar los pagos (webhook) y Twilio pueda descargar la imagen del QR.
#
#   ./scripts/tunel.sh          levanta el tunel (o uno nuevo si ya habia)
#   ./scripts/tunel.sh status   muestra si el tunel esta activo y su URL
#   ./scripts/tunel.sh stop     apaga el tunel y vuelve el .env a localhost
#
# Usa la imagen oficial cloudflare/cloudflared en Docker: no hace falta
# instalar nada. La URL (https://....trycloudflare.com) cambia cada vez que
# se levanta el tunel. Si se cae, volver a correr el script.

set -euo pipefail

CONTAINER=arenasports-tunel
cd "$(dirname "$0")/.."

set_env() {
    # set_env CLAVE valor  -> reemplaza la linea CLAVE=... del .env
    sed -i "s#^$1=.*#$1=$2#" .env
}

stop_tunnel() {
    docker rm -f "$CONTAINER" >/dev/null 2>&1 || true
}

case "${1:-start}" in
    stop)
        stop_tunnel
        set_env APP_URL "http://localhost"
        set_env MP_WEBHOOK_URL ""
        echo "Tunel apagado. El .env volvio a APP_URL=http://localhost."
        exit 0
        ;;
    status)
        if docker ps --format '{{.Names}}' | grep -qx "$CONTAINER"; then
            echo "Tunel activo: $(grep -E '^APP_URL=' .env | cut -d= -f2-)"
        else
            echo "No hay tunel activo."
        fi
        exit 0
        ;;
    start) ;;
    *)
        echo "Uso: $0 [start|status|stop]"
        exit 1
        ;;
esac

if ! curl -s -o /dev/null --max-time 5 http://localhost; then
    echo "La app no responde en http://localhost. Levantala primero con: ./vendor/bin/sail up -d"
    exit 1
fi

cp .env .env.backup
stop_tunnel

echo "Levantando el tunel..."
# --protocol http2: va por TCP, mas estable que QUIC (UDP) en redes WiFi.
docker run -d --name "$CONTAINER" --network host cloudflare/cloudflared:latest \
    tunnel --no-autoupdate --protocol http2 --url http://localhost >/dev/null

URL=""
for _ in $(seq 1 60); do
    URL=$(docker logs "$CONTAINER" 2>&1 | grep -oE 'https://[a-z0-9-]+\.trycloudflare\.com' | head -1 || true)
    [ -n "$URL" ] && break
    sleep 2
done

if [ -z "$URL" ]; then
    echo "No se pudo obtener la URL del tunel. Revisar la conexion a internet y volver a intentar."
    docker logs --tail 5 "$CONTAINER" 2>&1 || true
    exit 1
fi

set_env APP_URL "$URL"
set_env MP_WEBHOOK_URL "$URL/api/webhooks/mercadopago"

echo "Esperando a que la URL responda..."
for _ in $(seq 1 20); do
    if [ "$(curl -s -o /dev/null -w '%{http_code}' --max-time 10 "$URL")" = "200" ]; then
        break
    fi
    sleep 3
done

echo
echo "Tunel listo:    $URL"
echo "Webhook de MP:  $URL/api/webhooks/mercadopago"
echo "Validar QR:     $URL/api/reservations/validate   (pasarsela al otro grupo)"
echo
echo "El .env ya quedo actualizado (copia anterior en .env.backup)."
echo "Para navegar podes usar http://localhost; al pagar, volver a mano a http://localhost/reservas."
