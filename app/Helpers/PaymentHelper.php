<?php

namespace App\Helpers;

class PaymentHelper
{
    /**
     * Format the price based on default currency configurations and authenticated user region.
     *
     * @param float $amount
     * @param int $decimals
     * @return string
     */
    public static function format($amount, $decimals = 2)
    {
        $symbol = SettingsHelper::get('payment_currency_symbol', '$');
        $position = SettingsHelper::get('payment_currency_position', 'before');
        
        if (auth()->check()) {
            $user = auth()->user();
            $country = strtolower(trim($user->country ?? ''));
            if ($country === 'nigeria') {
                $symbol = '₦';
                $rate = (float)SettingsHelper::get('currency_exchange_rate_ngn', 1500.00);
                $amount = $amount * $rate;
            } elseif (in_array($country, ['united kingdom', 'uk', 'gb', 'great britain'])) {
                $symbol = '£';
                $rate = (float)SettingsHelper::get('currency_exchange_rate_gbp', 0.80);
                $amount = $amount * $rate;
            } elseif (in_array($country, ['europe', 'germany', 'france', 'italy', 'spain', 'netherlands', 'belgium', 'ireland'])) {
                $symbol = '€';
                $rate = (float)SettingsHelper::get('currency_exchange_rate_eur', 0.92);
                $amount = $amount * $rate;
            }
        }
        
        $formatted = number_format($amount, $decimals);
        
        return $position === 'before' ? $symbol . $formatted : $formatted . $symbol;
    }

    /**
     * Retrieve the active payment gateway.
     *
     * @return string
     */
    public static function activeGateway()
    {
        return SettingsHelper::get('payment_active_gateway', 'stripe');
    }

    /**
     * Get active bank details array.
     *
     * @return array
     */
    public static function bankDetails()
    {
        return [
            'name'           => SettingsHelper::get('payment_bank_name', 'Zenith Bank PLC'),
            'account_name'   => SettingsHelper::get('payment_bank_account_name', 'Diwebs Tech Agency Ltd'),
            'account_number' => SettingsHelper::get('payment_bank_account_number', '1017384950'),
            'routing_number' => SettingsHelper::get('payment_bank_routing_number', '057150013'),
            'swift_code'     => SettingsHelper::get('payment_bank_swift_code', 'ZENINILAGXX'),
            'enabled'        => SettingsHelper::get('payment_bank_enabled', false),
        ];
    }

    /**
     * Get active crypto details array.
     *
     * @return array
     */
    public static function cryptoDetails()
    {
        return [
            'btc'     => SettingsHelper::get('payment_crypto_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh'),
            'usdt'    => SettingsHelper::get('payment_crypto_wallet_usdt', 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'),
            'enabled' => SettingsHelper::get('payment_crypto_enabled', false),
        ];
    }
}
