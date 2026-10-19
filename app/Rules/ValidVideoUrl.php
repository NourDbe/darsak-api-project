<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidVideoUrl implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        // السماح بترك الرابط فارغاً لأن الحقل اختياري.
        if ($value === null || $value === '') {
            return;
        }

        // التأكد من أن القيمة رابط صالح.
        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_URL)) {
            $fail('رابط الفيديو غير صالح.');
            return;
        }

        $host = strtolower((string) parse_url($value, PHP_URL_HOST));
        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

        // السماح ببروتوكول HTTP أو HTTPS فقط.
        if (! in_array($scheme, ['http', 'https'], true)) {
            $fail('رابط الفيديو يجب أن يبدأ بـ HTTP أو HTTPS.');
            return;
        }

        // النطاقات المسموح بها فقط.
        $allowedHosts = [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtu.be',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
            'vimeo.com',
            'www.vimeo.com',
            'player.vimeo.com',
        ];

        if (! in_array($host, $allowedHosts, true)) {
            $fail('يُسمح بروابط YouTube وVimeo فقط.');
        }
    }
}
