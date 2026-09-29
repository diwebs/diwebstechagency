<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SpamFilter
{
    /**
     * Common spam keywords and patterns.
     */
    protected static array $spamKeywords = [
        'viagra', 'cialis', 'casino', 'poker', 'slot machine', 'crypto giveaway',
        'free bitcoin', 'telegram @', 'whatsapp +', 'buy followers', 'seo backlinks',
        'loan offer', 'earn $', 'make $', 'lottery winner', 'investment opportunity',
        'fast cash', 'cheap pills', 'online pharmacy', 'sex tape', 'adult dating'
    ];

    /**
     * Check if the incoming request is identified as spam.
     *
     * @param Request $request
     * @return array [isSpam (bool), reason (string)]
     */
    public static function check(Request $request): array
    {
        // 1. Honeypot check: If hidden honeypot fields are filled, it's a bot!
        $hpWebsite = $request->input('_hp_website');
        $hpName = $request->input('_hp_name');
        if (!empty($hpWebsite) || !empty($hpName)) {
            return [true, 'Honeypot trap triggered. Automated submission detected.'];
        }

        // 2. Time threshold check: Form submitted unnaturally fast (< 2 seconds)
        $formTimeToken = $request->input('_form_time');
        if ($formTimeToken) {
            $decryptedTime = base64_decode($formTimeToken);
            if (is_numeric($decryptedTime)) {
                $elapsed = time() - (int)$decryptedTime;
                if ($elapsed < 2 && $elapsed >= 0) {
                    return [true, 'Form submitted unnaturally fast (' . $elapsed . 's). Likely automated script.'];
                }
            }
        }

        // 3. Keyword / Pattern inspection across inputs
        $inputPayload = strtolower(json_encode($request->except([
            '_token', 'password', 'password_confirmation', '_hp_website', '_hp_name', '_form_time'
        ])));
        
        foreach (self::$spamKeywords as $keyword) {
            if (str_contains($inputPayload, $keyword)) {
                return [true, 'Content flagged for containing spam keyword: "' . $keyword . '"'];
            }
        }

        // 4. Excessive link check (more than 3 URLs in single request)
        $urlCount = substr_count($inputPayload, 'http://') + substr_count($inputPayload, 'https://') + substr_count($inputPayload, 'www.');
        if ($urlCount > 3) {
            return [true, 'Excessive link count (' . $urlCount . ' URLs detected). Flagged as link spam.'];
        }

        // 5. IP Cooldown Check (max 3 submissions within 15 seconds per path)
        $ipKey = 'spam_cooldown_' . md5($request->ip() . '_' . $request->path());
        $recentHits = Cache::get($ipKey, 0);
        if ($recentHits >= 3) {
            return [true, 'Submission speed limit exceeded. Please wait 15 seconds before submitting again.'];
        }
        Cache::put($ipKey, $recentHits + 1, 15);

        return [false, ''];
    }

    /**
     * Generate encoded timestamp token for form time validation.
     */
    public static function getTimeToken(): string
    {
        return base64_encode((string) time());
    }
}
