<?php

/**
 * Price Calculation Utilities
 * 
 * This file contains shared functions for calculating subscription prices
 * across different time periods with accurate time constants.
 */

// Time period constants based on 365.25 days per year (accounting for leap years)
const DAYS_PER_YEAR = 365.25;
const WEEKS_PER_YEAR = DAYS_PER_YEAR / 7;  // ≈ 52.18
const MONTHS_PER_YEAR = 12;
const DAYS_PER_MONTH = DAYS_PER_YEAR / MONTHS_PER_YEAR;  // ≈ 30.44
const WEEKS_PER_MONTH = WEEKS_PER_YEAR / MONTHS_PER_YEAR;  // ≈ 4.35
const DAYS_PER_WEEK = 7;

/**
 * Calculate the monthly price for a subscription
 * 
 * @param int $cycle Billing cycle (1=daily, 2=weekly, 3=monthly, 4=yearly)
 * @param int $frequency Frequency of billing within the cycle
 * @param float $price Price per billing period
 * @return float Monthly price
 */
function getPricePerMonth($cycle, $frequency, $price)
{
    switch ($cycle) {
        case 1: // Daily
            $numberOfPaymentsPerMonth = DAYS_PER_MONTH / $frequency;
            return $price * $numberOfPaymentsPerMonth;
        case 2: // Weekly
            $numberOfPaymentsPerMonth = WEEKS_PER_MONTH / $frequency;
            return $price * $numberOfPaymentsPerMonth;
        case 3: // Monthly
            $numberOfPaymentsPerMonth = 1 / $frequency;
            return $price * $numberOfPaymentsPerMonth;
        case 4: // Yearly
            $numberOfPaymentsPerMonth = 1 / ($frequency * MONTHS_PER_YEAR);
            return $price * $numberOfPaymentsPerMonth;
    }
    return null;
}

/**
 * Calculate the weekly price for a subscription
 * 
 * @param int $cycle Billing cycle (1=daily, 2=weekly, 3=monthly, 4=yearly)
 * @param int $frequency Frequency of billing within the cycle
 * @param float $price Price per billing period
 * @return float Weekly price
 */
function getPricePerWeek($cycle, $frequency, $price)
{
    switch ($cycle) {
        case 1: // Daily
            $numberOfPaymentsPerWeek = DAYS_PER_WEEK / $frequency;
            return $price * $numberOfPaymentsPerWeek;
        case 2: // Weekly
            $numberOfPaymentsPerWeek = 1 / $frequency;
            return $price * $numberOfPaymentsPerWeek;
        case 3: // Monthly
            $numberOfPaymentsPerWeek = 1 / ($frequency * WEEKS_PER_MONTH);
            return $price * $numberOfPaymentsPerWeek;
        case 4: // Yearly
            $numberOfPaymentsPerWeek = 1 / ($frequency * WEEKS_PER_YEAR);
            return $price * $numberOfPaymentsPerWeek;
    }
    return null;
}

/**
 * Calculate the yearly price for a subscription
 * 
 * @param int $cycle Billing cycle (1=daily, 2=weekly, 3=monthly, 4=yearly)
 * @param int $frequency Frequency of billing within the cycle
 * @param float $price Price per billing period
 * @return float Yearly price
 */
function getPricePerYear($cycle, $frequency, $price)
{
    switch ($cycle) {
        case 1: // Daily
            $numberOfPaymentsPerYear = DAYS_PER_YEAR / $frequency;
            return $price * $numberOfPaymentsPerYear;
        case 2: // Weekly
            $numberOfPaymentsPerYear = WEEKS_PER_YEAR / $frequency;
            return $price * $numberOfPaymentsPerYear;
        case 3: // Monthly
            $numberOfPaymentsPerYear = MONTHS_PER_YEAR / $frequency;
            return $price * $numberOfPaymentsPerYear;
        case 4: // Yearly
            $numberOfPaymentsPerYear = 1 / $frequency;
            return $price * $numberOfPaymentsPerYear;
    }
    return null;
}