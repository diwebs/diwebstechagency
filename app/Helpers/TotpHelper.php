<?php

namespace App\Helpers;

class TotpHelper
{
    public static function generateSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[rand(0, 31)];
        }
        return $secret;
    }

    public static function getQrCodeUrl(string $email, string $secret, string $issuer = 'Diwebs'): string
    {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $email) . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer);
    }

    public static function verifyTotp(string $secret, string $code): bool
    {
        $timeWindow = 1; 
        $currentTimeStep = floor(time() / 30);
        
        for ($i = -$timeWindow; $i <= $timeWindow; $i++) {
            $timeStep = $currentTimeStep + $i;
            if (self::calculateTotp($secret, $timeStep) === (int)$code) {
                return true;
            }
        }
        return false;
    }

    private static function calculateTotp(string $secret, int $timeStep): int
    {
        $key = self::base32Decode($secret);
        $timeBin = pack('N*', 0) . pack('N*', $timeStep);
        $hash = hash_hmac('sha1', $timeBin, $key, true);
        
        $offset = ord($hash[19]) & 0xf;
        $temp = unpack('N', substr($hash, $offset, 4));
        $val = $temp[1] & 0x7fffffff;
        
        return $val % 1000000;
    }

    private static function base32Decode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $alphabetMap = array_flip(str_split($alphabet));
        $secret = strtoupper($secret);
        $binary = '';
        foreach (str_split($secret) as $char) {
            if (isset($alphabetMap[$char])) {
                $binary .= sprintf('%05b', $alphabetMap[$char]);
            }
        }
        $bytes = '';
        foreach (str_split($binary, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $bytes .= chr(bindec($chunk));
            }
        }
        return $bytes;
    }
}
