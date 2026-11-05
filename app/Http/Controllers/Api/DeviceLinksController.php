<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeviceLinksController extends Controller
{
    /**
     * Generate or redirect to WhatsApp wish link.
     * https://wa.me/{phone}?text={urlencoded message}
     */
    public function whatsApp(Request $request): JsonResponse|RedirectResponse
    {
        $phone = $request->input('phone', '');
        $message = $request->input('text', $request->input('message', 'Happy Birthday! 🎉'));

        // Phone numbers sent without spaces or "+" as stored in contacts
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $url = "https://wa.me/{$cleanPhone}?text=" . rawurlencode($message);

        if ($request->boolean('redirect')) {
            return redirect()->away($url);
        }

        return response()->json([
            'type' => 'whatsapp',
            'phone' => $cleanPhone,
            'original_phone' => $phone,
            'message' => $message,
            'url' => $url,
        ]);
    }

    /**
     * Generate or redirect to SMS wish link.
     * sms:{phone}?body={urlencoded message}
     */
    public function sms(Request $request): JsonResponse|RedirectResponse
    {
        $phone = $request->input('phone', '');
        $message = $request->input('body', $request->input('message', 'Happy Birthday! 🎉'));

        $url = "sms:{$phone}?body=" . rawurlencode($message);

        if ($request->boolean('redirect')) {
            return redirect()->away($url);
        }

        return response()->json([
            'type' => 'sms',
            'phone' => $phone,
            'message' => $message,
            'url' => $url,
        ]);
    }
}
