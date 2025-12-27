<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ExceptionSenderService
{
    private $client;

    public function __construct()
    {
        $token = config('app.exception_bot_token');
        $this->client = Http::baseUrl("https://api.telegram.org/bot{$token}");
    }

    public function send(string $chat_id, string $text, string $threadId = null): \Illuminate\Http\Client\Response
    {
        return $this->client->post('/sendMessage', [
            'chat_id' => $chat_id,
            'text' => mb_strcut($text, 0, 4096),
            'disable_web_page_preview' => true,
            'parse_mode' => 'HTML',
            'disable_notification' => true,
            'message_thread_id' => $threadId,
        ]);
    }

    public function errorSend(Throwable $e): void
    {
        rescue(function () use ($e) {
            $req = request();
            $env = app()->isProduction() ? 'Production (Rekruting)' : 'Development (Rekruting)';
            $user = $this->getUserName();
            $method = $req->method();
            $url = $req->fullUrl();
            $route = optional($req->route())->getName() ?? '—';
            $ip = $req->ip();
            $agent = Str::limit((string)$req->userAgent(), 200);
            $time = now()->toDateTimeString();

            // Request body’ni xavfsiz JSON qilib olish
            $body = $this->cleanRequestData($req->all());

            // Filament konteksti bo‘lsa uni chiqaramiz
            $filamentCtx = $this->extractFilamentContext($req->input('components', []));

            // Exception ma’lumotlari
            $trace = $this->shortTrace($e);
            $file = $e->getFile();
            $line = $e->getLine();
            $message = Str::limit($e->getMessage(), 2000);

            $html = "<b>[{$env}] {$time}</b>\n"
                . "<b>User:</b> {$user}\n"
                . "<b>Request:</b> {$method} {$url}\n"
                . "<b>Route:</b> {$route}\n"
                . "<b>IP:</b> {$ip}\n"
                . "<b>Agent:</b> {$agent}\n";

            if ($filamentCtx) {
                $html .= "<b>Filament:</b> {$filamentCtx}\n";
            }

            $html .= "<b>Message:</b> {$message}\n"
                . "<b>File:</b> {$file}:{$line}\n";

            if ($body) {
                $html .= "<b>Body:</b> <code>{$body}</code>\n";
            }

            if ($trace) {
                $html .= "<b>Trace:</b>\n<code>{$trace}</code>\n";
            }

            $html = Str::limit($html, 4096);

            if (!empty(config('app.exception_chat_id')) && !empty(config('app.exception_chat_thread_id'))) {
                $this->send(
                    config('app.exception_chat_id'),
                    $html,
                    config('app.exception_chat_thread_id')
                );
            } else {
                Log::error("Telegram exception: {$html}");
            }
        }, null, false);
    }

    public function errorMessage(Throwable $e): string
    {
        return rescue(function () use ($e) {
            $req = request();
            $env = app()->isProduction() ? 'Production (Rekruting)' : 'Development (Rekruting)';
            $user = $this->getUserName();
            $method = $req->method();
            $url = $req->fullUrl();
            $route = optional($req->route())->getName() ?? '—';
            $ip = $req->ip();
            $agent = Str::limit((string)$req->userAgent(), 200);
            $time = now()->toDateTimeString();

            // Request body’ni xavfsiz JSON qilib olish
            $body = $this->cleanRequestData($req->all());

            // Filament konteksti bo‘lsa uni chiqaramiz
            $filamentCtx = $this->extractFilamentContext($req->input('components', []));

            // Exception ma’lumotlari
            $trace = $this->shortTrace($e);
            $file = $e->getFile();
            $line = $e->getLine();
            $message = Str::limit($e->getMessage(), 2000);

            $html = "<b>[{$env}] {$time}</b>\n"
                . "<b>User:</b> {$user}\n"
                . "<b>Request:</b> {$method} {$url}\n"
                . "<b>Route:</b> {$route}\n"
                . "<b>IP:</b> {$ip}\n"
                . "<b>Agent:</b> {$agent}\n";

            if ($filamentCtx) {
                $html .= "<b>Filament:</b> {$filamentCtx}\n";
            }

            $html .= "<b>Message:</b> {$message}\n"
                . "<b>File:</b> {$file}:{$line}\n";

            if ($body) {
                $html .= "<b>Body:</b> <code>{$body}</code>\n";
            }

            if ($trace) {
                $html .= "<b>Trace:</b>\n<code>{$trace}</code>\n";
            }

            return Str::limit($html, 4096);
        }, null, false);
    }

    private function cleanRequestData(array $data): string
    {
        // Keraksiz yoki xavfli maydonlarni olib tashlaymiz
        $drop = ['_token', 'password', 'password_confirmation', 'token', 'snapshot', 'calls', 'updates', 'components'];
        foreach ($drop as $key) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
        }

        foreach ($data as $k => $v) {
            if (is_string($v) && mb_strlen($v) > 200) {
                $data[$k] = Str::limit($v, 200);
            }
        }

        try {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $json = '{}';
        }

        return Str::limit($json, 1000);
    }

    private function extractFilamentContext($components): ?string
    {
        if (!is_array($components) || empty($components)) {
            return null;
        }

        $snapshot = $components[0]['snapshot'] ?? null;
        if (!$snapshot) {
            return null;
        }

        try {
            $snap = json_decode($snapshot, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        $memo = $snap['memo'] ?? [];
        $name = $memo['name'] ?? null;
        $path = $memo['path'] ?? null;
        if ($name || $path) {
            return trim(($name ? "component={$name}" : '') . ' ' . ($path ? "path={$path}" : ''));
        }
        return null;
    }

    private function shortTrace(Throwable $e): string
    {
        $trace = '';
        foreach ($e->getTrace() as $i => $entry) {
            $file = $entry['file'] ?? null;
            $line = $entry['line'] ?? null;

            if (!$file || !$line) {
                continue;
            }

            // faqat bizning kod fayllarimizni (app/ yoki modules/) qoldiramiz
            if (
                str_contains($file, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)
                || str_contains($file, DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR)
                || str_contains($file, DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR)
            ) {
                continue;
            }

            if (
                str_contains($file, DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR)
                || str_contains($file, DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR)
            ) {
                $trace .= "[{$i}] {$file}:{$line}\n";
            }

            // maksimal 10 qator
            if (substr_count($trace, "\n") >= 10) {
                break;
            }
        }

        // fallback: agar yuqorida hech narsa topilmasa, qisqartirilgan umumiy trace
        return $trace ?: Str::limit($e->getTraceAsString(), 1000);
    }

    public function getUserName(): string
    {
        if (auth()->check()) {
            return auth()->id() . ':' . auth()->user()->pin . '|' . (auth()->user()->first_name ?? '') . ' ' . (auth()->user()->last_name ?? '');
        }
        return 'Guest';
    }
}
