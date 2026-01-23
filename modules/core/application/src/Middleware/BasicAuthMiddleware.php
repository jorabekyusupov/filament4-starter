<?php

namespace Modules\Application\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class BasicAuthMiddleware
{
    /**
     * The guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @param  string|null  $field
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function handle($request, Closure $next, $guard = null, $field = null)
    {
        // 1-BOSQICH: Basic Auth (Login va Parol tekshiruvi)
        // Agar login/parol xato bo'lsa, Laravel o'zi 401 qaytaradi.
        $this->auth->guard($guard)->basic($field ?: 'email');

        $this->auth->shouldUse($guard);

        // Userni olish
        $user = $this->auth->guard($guard)->user();

        // 2-BOSQICH: Status tekshiruvi (Active/Inactive)
        if ($user && isset($user->status) && !$user->status) {
            return response()->json(['message' => 'Account is inactive'], 401);
        }

        // ---------------------------------------------------------
        // 3-BOSQICH: HMAC Signature Tekshiruvi (YANGI QISM)
        // ---------------------------------------------------------

        // A) Headerlarni olish
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');

        // B) Headerlar mavjudligini tekshirish
        if (!$signature || !$timestamp) {
            return response()->json([
                'message' => 'Xavfsizlik headerlari (Signature/Timestamp) yetishmayapti'
            ], 400);
        }

        // C) Vaqt tekshiruvi (Replay Attack himoyasi - 5 daqiqa)
        if (abs(time() - $timestamp) > 300) {
            return response()->json([
                'message' => 'So\'rov vaqti eskirgan (Request timestamp expired)'
            ], 401);
        }

        // D) Userga tegishli Secret Keyni olish
        // DIQQAT: User modelida (bazada) 'secret_key' degan ustun bo'lishi kerak!
        // Yoki $user->api_secret va hokazo.
        $secretKey = $user->secret_private_key ?? null;

        if (!$secretKey) {
            // Agar user login qilgan bo'lsa-yu, lekin unga hali secret_key berilmagan bo'lsa
            return response()->json(['message' => 'Server Configuration Error: User has no secret key'], 500);
        }

        // E) Imzoni serverda qayta hisoblash
        // Body (raw content) + Timestamp
        $payload = $request->getContent();
        $stringToSign = $payload . $timestamp;

        $expectedSignature = hash_hmac('sha256', $stringToSign, $secretKey);

        // F) Kriptografik solishtirish
        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning("HMAC Error for User ID {$user->id}: Invalid signature.");

            return response()->json([
                'message' => 'Imzo xato (Invalid Signature). Ma\'lumot o\'zgartirilgan bo\'lishi mumkin.'
            ], 403);
        }

        // 4-BOSQICH: Ruxsat berish
        return $next($request);
    }

}
