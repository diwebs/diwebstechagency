<?php

namespace App\Helpers;

class PaymentHelper
{
    /**
     * Determine regional info based on user or country string.
     *
     * @param string|null $country
     * @return array [regionKey, currencyCode, symbol, rate, label, gateways]
     */
    public static function getRegionInfo($country = null): array
    {
        if (empty($country) && auth()->check()) {
            $country = auth()->user()->country ?? '';
        }

        $countryLower = strtolower(trim((string)$country));

        // South Africa & Southern Africa Region
        if (in_array($countryLower, ['south africa', 'za', 'rsa', 'namibia', 'botswana', 'lesotho', 'eswatini', 'swaziland', 'zimbabwe', 'zaf'])) {
            return [
                'region'   => 'africa_south',
                'currency' => 'ZAR',
                'symbol'   => 'R',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_zar', 18.50),
                'label'    => 'South Africa & Southern Africa (ZAR R)',
                'gateways' => ['paystack', 'flutterwave', 'stripe', 'bank_transfer', 'card']
            ];
        }

        // Kenya & East Africa Region
        if (in_array($countryLower, ['kenya', 'ke', 'tanzania', 'uganda', 'rwanda', 'ethiopia', 'ken'])) {
            return [
                'region'   => 'africa_east',
                'currency' => 'KES',
                'symbol'   => 'KSh',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_kes', 130.00),
                'label'    => 'Kenya & East Africa (KES KSh)',
                'gateways' => ['flutterwave', 'paystack', 'bank_transfer', 'card']
            ];
        }

        // Ghana Region
        if (in_array($countryLower, ['ghana', 'gh', 'gha'])) {
            return [
                'region'   => 'africa_ghana',
                'currency' => 'GHS',
                'symbol'   => 'GH₵',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_ghs', 15.50),
                'label'    => 'Ghana (GHS GH₵)',
                'gateways' => ['paystack', 'flutterwave', 'bank_transfer', 'card']
            ];
        }

        // Egypt & North Africa Region
        if (in_array($countryLower, ['egypt', 'eg', 'morocco', 'tunisia', 'algeria', 'egy'])) {
            return [
                'region'   => 'africa_north',
                'currency' => 'EGP',
                'symbol'   => 'E£',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_egp', 48.50),
                'label'    => 'Egypt & North Africa (EGP E£)',
                'gateways' => ['stripe', 'paypal', 'bank_transfer', 'card']
            ];
        }

        // Nigeria & West Africa Region
        if (in_array($countryLower, ['nigeria', 'ng', 'cameroon', 'ivory coast', 'cote d\'ivoire', 'senegal', 'benin', 'togo', 'nga', 'africa'])) {
            return [
                'region'   => 'africa_west',
                'currency' => 'NGN',
                'symbol'   => '₦',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_ngn', 1500.00),
                'label'    => 'Nigeria & West Africa (NGN ₦)',
                'gateways' => ['paystack', 'flutterwave', 'bank_transfer', 'card']
            ];
        }

        // United Kingdom Region
        if (in_array($countryLower, ['united kingdom', 'uk', 'gb', 'great britain', 'england', 'scotland', 'wales'])) {
            return [
                'region'   => 'uk',
                'currency' => 'GBP',
                'symbol'   => '£',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_gbp', 0.80),
                'label'    => 'United Kingdom (GBP £)',
                'gateways' => ['stripe', 'bank_transfer', 'card']
            ];
        }

        // European Union Region
        if (in_array($countryLower, ['germany', 'france', 'italy', 'spain', 'netherlands', 'belgium', 'ireland', 'europe', 'eu', 'austria', 'portugal', 'finland', 'greece'])) {
            return [
                'region'   => 'europe',
                'currency' => 'EUR',
                'symbol'   => '€',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_eur', 0.92),
                'label'    => 'Europe & EU SEPA (EUR €)',
                'gateways' => ['stripe', 'sepa', 'card']
            ];
        }

        // Canada
        if (in_array($countryLower, ['canada', 'ca'])) {
            return [
                'region'   => 'canada',
                'currency' => 'CAD',
                'symbol'   => 'C$',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_cad', 1.36),
                'label'    => 'Canada (CAD C$)',
                'gateways' => ['stripe', 'paypal', 'card']
            ];
        }

        // Australia
        if (in_array($countryLower, ['australia', 'au'])) {
            return [
                'region'   => 'australia',
                'currency' => 'AUD',
                'symbol'   => 'A$',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_aud', 1.52),
                'label'    => 'Australia (AUD A$)',
                'gateways' => ['stripe', 'paypal', 'card']
            ];
        }

        // India
        if (in_array($countryLower, ['india', 'in'])) {
            return [
                'region'   => 'india',
                'currency' => 'INR',
                'symbol'   => '₹',
                'rate'     => (float) SettingsHelper::get('currency_exchange_rate_inr', 83.50),
                'label'    => 'India (INR ₹)',
                'gateways' => ['razorpay', 'stripe', 'card']
            ];
        }

        // Default: North America & Global International (USD)
        return [
            'region'   => 'global',
            'currency' => 'USD',
            'symbol'   => '$',
            'rate'     => 1.00,
            'label'    => 'United States & Global International (USD $)',
            'gateways' => ['stripe', 'paypal', 'crypto', 'card']
        ];
    }

    /**
     * Format the price based on default currency configurations and user/requested region.
     *
     * @param float $amount
     * @param int $decimals
     * @param string|null $country
     * @return string
     */
    public static function format($amount, $decimals = 2, $country = null)
    {
        $info = self::getRegionInfo($country);
        $convertedAmount = $amount * $info['rate'];
        $position = SettingsHelper::get('payment_currency_position', 'before');
        $formatted = number_format($convertedAmount, $decimals);

        return $position === 'before' ? $info['symbol'] . $formatted : $formatted . $info['symbol'];
    }

    /**
     * Generate dynamic regional payment plans & pricing tiers.
     *
     * @param string|null $country
     * @return array
     */
    public static function getRegionalPlans($country = null): array
    {
        $info = self::getRegionInfo($country);
        $rate = $info['rate'];
        $symbol = $info['symbol'];

        // Base USD Prices
        $baseStarter = 29.00;
        $baseProfessional = 99.00;
        $baseEnterprise = 349.00;

        return [
            'region_info' => $info,
            'plans' => [
                'starter' => [
                    'name'         => 'Digital Starter Plan',
                    'base_usd'     => $baseStarter,
                    'price'        => round($baseStarter * $rate, 2),
                    'price_formatted' => $symbol . number_format(round($baseStarter * $rate, 2), 2),
                    'billing_period' => 'per month',
                    'description'  => 'Ideal for small businesses initiating cloud deployment and CBT setup.',
                    'features'     => [
                        'Up to 5 User Licenses',
                        'Basic Cloud Portal Hosting',
                        'Standard Email Support',
                        'CBT Exam Center Access (1 Center)'
                    ]
                ],
                'professional' => [
                    'name'         => 'Professional Growth Plan',
                    'base_usd'     => $baseProfessional,
                    'price'        => round($baseProfessional * $rate, 2),
                    'price_formatted' => $symbol . number_format(round($baseProfessional * $rate, 2), 2),
                    'billing_period' => 'per month',
                    'description'  => 'Designed for growing enterprises requiring custom modules and priority SLA.',
                    'features'     => [
                        'Up to 25 User Licenses',
                        'Multi-Center CBT Sync & Webcams',
                        'Dedicated Account Manager',
                        '24/7 Priority Support & AI Telemetry',
                        'Custom API & Database Integrations'
                    ]
                ],
                'enterprise' => [
                    'name'         => 'Global Enterprise SLA',
                    'base_usd'     => $baseEnterprise,
                    'price'        => round($baseEnterprise * $rate, 2),
                    'price_formatted' => $symbol . number_format(round($baseEnterprise * $rate, 2), 2),
                    'billing_period' => 'per month',
                    'description'  => 'Full institutional technology partnership with dedicated cloud infrastructure.',
                    'features'     => [
                        'Unlimited User Licenses',
                        'Dedicated Hybrid AWS / EKS Cluster',
                        '99.99% Guaranteed SLA Uptime',
                        'Biometric Security & Anti-Cheat Hooks',
                        'Custom Engineering & On-site Verification'
                    ]
                ]
            ]
        ];
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
