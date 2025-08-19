<?php

// Decimal formatting constants
const DECIMALS_WHOLE = 0;      // For whole numbers (yearly view)
const DECIMALS_CURRENCY = 2;   // For currency display (monthly/weekly view)

final class CurrencyFormatter
{
    private static $instance;

    private static function getInstance()
    {
        if (self::$instance === null) {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                self::$instance = new NumberFormatter(Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']), NumberFormatter::CURRENCY);
            } else {
                self::$instance = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
            }
        }

        return self::$instance;
    }

    public static function format($amount, $currency)
    {
        return self::getInstance()->formatCurrency($amount, $currency);
    }
}
