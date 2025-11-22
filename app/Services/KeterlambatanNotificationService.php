<?php

namespace App\Services;

use App\Models\Keterlambatan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeterlambatanNotificationService
{
    /**
     * Send notification to internal system when keterlambatan is created.
     */
    public function sendNotification(Keterlambatan $keterlambatan): void
    {
        $endpoint = config('services.keterlambatan_notification.endpoint');

        // Skip if endpoint is not configured
        if (empty($endpoint)) {
            return;
        }

        // Load walikelas relationship to get nomor_telepon
        $keterlambatan->load('walikelas');

        // Get nomor_telepon from walikelas
        $nomorTelepon = $keterlambatan->walikelas?->nomor_telepon;

        // Skip if walikelas doesn't have nomor_telepon
        if (empty($nomorTelepon)) {
            Log::warning('Keterlambatan notification skipped: walikelas has no nomor_telepon', [
                'keterlambatan_id' => $keterlambatan->id,
                'walikelas_username' => $keterlambatan->username,
            ]);

            return;
        }

        // Convert nomor telepon: 0xxxxx -> 62xxxxx
        if (str_starts_with($nomorTelepon, '0')) {
            $nomorTelepon = '62'.substr($nomorTelepon, 1);
        }

        // Add @c.us suffix to nomor telepon
        $nomorTeleponWithSuffix = $nomorTelepon.'@c.us';

        // Get nama lengkap walikelas
        $namaWalikelas = $keterlambatan->walikelas?->nama_lengkap ?? 'Bapak/Ibu';

        // Format message with formal greeting and keterlambatan data
        // Using WhatsApp formatting: *text* for bold, \n for newline
        $message = sprintf(
            "Halo Bapak/Ibu *%s*,\n\n".
            "Ijin melaporkan bahwa ada siswa yang terlambat atas nama berikut :\n\n".
            "*Nama:* %s\n".
            "*NIS:* %s\n".
            "*Gender:* %s\n".
            "*Kelas:* %s\n".
            "*Tanggal:* %s\n".
            "*Waktu:* %s\n\n".
            'Demikian yang dapat kami sampaikan, Terimakasih.',
            $namaWalikelas,
            $keterlambatan->nama_murid ?? '-',
            $keterlambatan->NIS ?? '-',
            $keterlambatan->gender ?? '-',
            $keterlambatan->kelas ?? '-',
            $keterlambatan->tanggal->format('Y-m-d'),
            $keterlambatan->waktu->format('H:i')
        );

        // Prepare data to send
        $data = [
            'message' => $message,
            'id' => $nomorTeleponWithSuffix,
        ];

        try {
            $response = Http::timeout(10)
                ->post($endpoint, $data);

            // Log if request failed
            if (! $response->successful()) {
                Log::error('Keterlambatan notification failed', [
                    'keterlambatan_id' => $keterlambatan->id,
                    'endpoint' => $endpoint,
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                    'data_sent' => $data,
                ]);
            }
        } catch (\Exception $e) {
            // Log exception if request fails
            Log::error('Keterlambatan notification exception', [
                'keterlambatan_id' => $keterlambatan->id,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'data_sent' => $data,
            ]);
        }
    }
}
