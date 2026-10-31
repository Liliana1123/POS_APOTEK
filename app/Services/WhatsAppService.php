<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Mengubah nomor telepon ke format internasional Indonesia (62).
     */
    public function formatPhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/\D/', '', $phone);

        if (!$phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        if (!preg_match('/^628[0-9]{7,12}$/', $phone)) {
            return null;
        }

        return $phone;
    }

    /**
     * Mengirim pesan WhatsApp melalui Kinaryatama Gateway.
     */
    public function sendMessage(string $number, string $message): array
    {
        $url = config('services.whatsapp.url');
        $token = config('services.whatsapp.token');
        $sender = config('services.whatsapp.sender');

        if (!$url || !$token || !$sender) {
            return [
                'status' => false,
                'msg' => 'Konfigurasi WhatsApp Gateway belum lengkap.',
            ];
        }

        $formattedNumber = $this->formatPhoneNumber($number);

        if (!$formattedNumber) {
            return [
                'status' => false,
                'msg' => 'Format nomor WhatsApp tidak valid.',
            ];
        }

        if (trim($message) === '') {
            return [
                'status' => false,
                'msg' => 'Pesan WhatsApp tidak boleh kosong.',
            ];
        }

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->post($url, [
                    'api_key' => $token,
                    'sender' => $sender,
                    'number' => $formattedNumber,
                    'message' => $message,
                ]);

            $result = $response->json();

            // Catat status saja, jangan catat token atau isi pesan.
            Log::info('WhatsApp Gateway request completed', [
                'http_status' => $response->status(),
                'number' => $formattedNumber,
            ]);

            
            // Tangani respons HTTP gagal.
            if (!$response->successful()) {
                $statusCode = $response->status();
                $providerMessage = null;

                if (is_array($result)) {
                    $providerMessage = $result['message']
                        ?? $result['msg']
                        ?? null;
                }

                // Batasi panjang detail agar pesan error tidak terlalu panjang.
                if (
                    !is_string($providerMessage)
                    || trim($providerMessage) === ''
                ) {
                    if ($statusCode === 500) {
                        $providerMessage = 'Terjadi kesalahan pada server gateway (HTTP 500). Kemungkinan sesi perangkat WhatsApp pengirim sedang terputus/offline di server gateway Kinaryatama.';
                    } else {
                        $providerMessage = 'Respons Gateway tidak menyediakan detail error dalam format JSON.';
                    }
                } else {
                    $providerMessage = mb_substr(
                        trim($providerMessage),
                        0,
                        300
                    );
                    if ($statusCode === 500) {
                        $providerMessage .= ' (Kemungkinan perangkat pengirim sedang offline/terputus di gateway).';
                    }
                }

                Log::warning('WhatsApp Gateway HTTP error', [
                    'http_status' => $statusCode,
                    'content_type' => $response->header('Content-Type'),
                    'response_is_json' => is_array($result),
                    'response_body_length' => strlen($response->body()),
                ]);

                return [
                    'status' => false,
                    'msg' => 'Gateway menolak permintaan (HTTP '
                        . $statusCode . '). Detail: ' . $providerMessage,
                ];
            }

            // Pastikan respons memiliki format yang diharapkan.
            if (!is_array($result)) {
                return [
                    'status' => false,
                    'msg' => 'Respons Gateway tidak sesuai format JSON yang diharapkan.',
                ];
            }

            // Periksa status sukses dari gateway (bisa berupa boolean true atau string 'success').
            $isSuccess = (
                (isset($result['status']) && ($result['status'] === true || strtolower((string)$result['status']) === 'success'))
                || (isset($result['success']) && ($result['success'] === true || strtolower((string)$result['success']) === 'success'))
            );

            if (!$isSuccess) {
                $errMsg = $result['message'] ?? $result['msg'] ?? null;
                return [
                    'status' => false,
                    'msg' => is_string($errMsg) && trim($errMsg) !== ''
                        ? $errMsg
                        : 'Gateway melaporkan pengiriman gagal.',
                ];
            }

            return [
                'status' => true,
                'msg' => is_string($result['message'] ?? null) && trim($result['message']) !== ''
                    ? $result['message']
                    : 'Permintaan pengiriman berhasil diterima oleh gateway.',
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('WhatsApp Gateway connection failed', [
                'error_type' => class_basename($e),
            ]);

            return [
                'status' => false,
                'msg' => 'Tidak dapat terhubung ke WhatsApp Gateway.',
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Gateway request error', [
                'error_type' => class_basename($e),
            ]);

            return [
                'status' => false,
                'msg' => 'Terjadi kesalahan saat memproses permintaan WhatsApp.',
            ];
        }
    }
}
