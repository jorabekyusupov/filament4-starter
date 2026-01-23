<?php

namespace Modules\Application\Clients;

class SecureApiClient
{
    private $baseUrl;
    private $username;
    private $password;
    private $secretKey;

    /**
     * Konfiguratsiya sozlamalari
     * * @param string $baseUrl - Tizim manzili (masalan: https://api.mysystem.uz)
     * @param string $username - Basic Auth Logini
     * @param string $password - Basic Auth Paroli
     * @param string $secretKey - HMAC uchun maxfiy kalit
     */
    public function __construct($baseUrl, $username, $password, $secretKey)
    {
        // URL oxiridagi slashni olib tashlash
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->username = $username;
        $this->password = $password;
        $this->secretKey = $secretKey;
    }

    /**
     * So'rov yuborish funksiyasi
     * * @param string $method - GET, POST, PUT, DELETE
     * @param string $endpoint - API yo'li (masalan: /api/orders)
     * @param array $data - Yuboriladigan ma'lumotlar
     * @return array           - Javob va status kodi
     */
    public function sendRequest($method, $endpoint, $data = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $timestamp = time();

        // 1. Bodyni tayyorlash
        // Agar ma'lumot bo'lsa JSON qilamiz, bo'lmasa bo'sh qoldiramiz
        $jsonBody = !empty($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : '';

        // GET so'rovlarda body bo'lmaydi, lekin imzo uchun bo'sh string ishlatilishi kerak
        if (strtoupper($method) === 'GET') {
            $jsonBody = '';
        }

        // 2. Imzo (Signature) yaratish
        // KELISHUV: Body + Timestamp
        $stringToSign = $jsonBody . $timestamp;
        $signature = hash_hmac('sha256', $stringToSign, $this->secretKey);

        // 3. Curl so'rovini tayyorlash
        $ch = curl_init();

        // Basic Auth Headerini qo'lda yasash (yoki CURLOPT_USERPWD ishlatsa ham bo'ladi)
        $basicAuth = base64_encode("{$this->username}:{$this->password}");

        $headers = [
            "Authorization: Basic $basicAuth",
            "Content-Type: application/json",
            "Accept: application/json",
            "X-Timestamp: $timestamp",  // Bizning vaqt
            "X-Signature: $signature"   // Bizning imzo
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 soniya kutish

        // Agar POST yoki PUT bo'lsa va body bo'lsa
        if (!empty($jsonBody) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        }

        // 4. So'rovni amalga oshirish
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'status' => 0,
                'error' => $error
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'status' => $httpCode,
            'data' => json_decode($response, true) ?? $response
        ];
    }

}