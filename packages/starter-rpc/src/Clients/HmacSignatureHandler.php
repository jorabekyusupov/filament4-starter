<?php
namespace Jora\StarterRpc\Clients;

use Psr\Http\Message\RequestInterface;
use Jora\StarterRpc\Services\SignatureService;
use Exception;


class HmacSignatureHandler
{
    protected ?string $secretKey;
    protected SignatureService $signatureService;

    /**
     * @param string|null $secretKey
     * @param SignatureService $signatureService
     */
    public function __construct(?string $secretKey, SignatureService $signatureService)
    {
        $this->secretKey = $secretKey;
        $this->signatureService = $signatureService;
    }

    /**
     * Guzzle Middleware logikasi shu yerda bajariladi.
     * __invoke metodi klassni funksiya kabi chaqirishga imkon beradi.
     *
     * @param RequestInterface $request
     * @return RequestInterface
     * @throws Exception
     */
    public function __invoke(RequestInterface $request): RequestInterface
    {
        // 1. Kalit mavjudligini tekshirish
        if (empty($this->secretKey)) {
            throw new Exception('Xavfsizlik xatoligi: Secret Key (Maxfiy kalit) topilmadi. Iltimos, konfiguratsiyani tekshiring.');
        }

        // 2. Vaqt va Body ni olish
        $timestamp = time();
        $body = (string) $request->getBody();

        // 3. Imzo yaratish (Service yordamida)
        $signature = $this->signatureService->make($body, $timestamp, $this->secretKey);

        // 4. Headerlarni qo'shib, yangilangan so'rovni qaytarish
        return $request
            ->withHeader('X-Timestamp', $timestamp)
            ->withHeader('X-Signature', $signature);
    }
}