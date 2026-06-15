<?php

namespace App\Helpers;

class PaymentHelper
{
    /**
     * Format the price based on default currency configurations.
     *
     * @param float $amount
     * @param int $decimals
     * @return string
     */
    public static function format($amount, $decimals = 2)
    {
        $symbol = cache('payment_currency_symbol', '$');
        $position = cache('payment_currency_position', 'before');
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
        return cache('payment_active_gateway', 'stripe');
    }

    /**
     * Get active bank details array.
     *
     * @return array
     */
    public static function bankDetails()
    {
        return [
            'name'           => cache('payment_bank_name', 'Zenith Bank PLC'),
            'account_name'   => cache('payment_bank_account_name', 'Diwebs Tech Agency Ltd'),
            'account_number' => cache('payment_bank_account_number', '1017384950'),
            'routing_number' => cache('payment_bank_routing_number', '057150013'),
            'swift_code'     => cache('payment_bank_swift_code', 'ZENINILAGXX'),
            'enabled'        => cache('payment_bank_enabled', false),
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
            'btc'     => cache('payment_crypto_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh'),
            'usdt'    => cache('payment_crypto_wallet_usdt', 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'),
            'enabled' => cache('payment_crypto_enabled', false),
        ];
    }
}
