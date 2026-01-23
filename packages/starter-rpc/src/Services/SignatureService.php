<?php

namespace Jora\StarterRpc\Services;

class SignatureService
{
    /**
     * Imzo yaratish (Generate Signature)
     * * @param string $body       - So'rov tanasi (JSON string)
     * @param int    $timestamp  - Vaqt
     * @param string $secretKey  - Maxfiy kalit
     * @return string            - HMAC hash
     */
    public function make(string $body, int $timestamp, string $secretKey): string
    {
        $stringToSign = $body . $timestamp;

        return hash_hmac('sha256', $stringToSign, $secretKey);
    }

    /**
     * Imzoni tekshirish (Verify Signature)
     * (Agar server tomonida ham ishlatsangiz kerak bo'ladi)
     * * @param string $signature  - Kelgan imzo
     * @param string $body       - Kelgan body
     * @param int    $timestamp  - Kelgan vaqt
     * @param string $secretKey  - Maxfiy kalit
     * @return bool
     */
    public function verify(string $signature, string $body, int $timestamp, string $secretKey): bool
    {
        $expectedSignature = $this->make($body, $timestamp, $secretKey);

        return hash_equals($expectedSignature, $signature);
    }
}