<?php

namespace App\Http\Controllers;

use App\Models\Turn;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode as ChillerlanQrCode;
use chillerlan\QRCode\QROptions;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TurnQrController extends Controller
{
    public function show(Turn $turn)
    {
        abort_if($turn->user_id !== auth()->id(), 403);
        abort_if($turn->status !== 'booked' || ! $turn->qr_code, 404);

        return response(QrCode::size(250)->generate($turn->qr_code))
            ->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Variante sin sesion, accesible solo con una firma valida (ver
     * URL::temporarySignedRoute). Se usa como mediaUrl en los recordatorios
     * de WhatsApp: WhatsApp/Twilio no soportan SVG como imagen, por eso esta
     * variante devuelve PNG (via GD, sin depender de la extension Imagick).
     */
    public function showSigned(Turn $turn)
    {
        abort_if($turn->status !== 'booked' || ! $turn->qr_code, 404);

        $png = (new ChillerlanQrCode(new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => false,
            'scale' => 10,
        ])))->render($turn->qr_code);

        return response($png)->header('Content-Type', 'image/png');
    }
}
