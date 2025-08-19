<?php

require_once 'i18n/getlang.php';
require_once 'price_calculations.php';

// Decimal formatting constants
const DECIMALS_WHOLE = 0;      // For whole numbers (yearly view)
const DECIMALS_CURRENCY = 2;   // For currency display (monthly/weekly view)

function getBillingCycle($cycle, $frequency, $i18n)
{
    switch ($cycle) {
        case 1:
            return $frequency == 1 ? translate('Daily', $i18n) : $frequency . " " . translate('days', $i18n);
        case 2:
            return $frequency == 1 ? translate('Weekly', $i18n) : $frequency . " " . translate('weeks', $i18n);
        case 3:
            return $frequency == 1 ? translate('Monthly', $i18n) : $frequency . " " . translate('months', $i18n);
        case 4:
            return $frequency == 1 ? translate('Yearly', $i18n) : $frequency . " " . translate('years', $i18n);
    }
}

function getSubscriptionProgress($cycle, $frequency, $next_payment)
{
    $nextPaymentDate = new DateTime($next_payment);
    $currentDate = new DateTime('now');

    // Calculate the interval to go back based on the cycle using proper DateInterval
    $intervalSpec = "P";
    switch ($cycle) {
        case 1: // Daily
            $intervalSpec .= "{$frequency}D";
            break;
        case 2: // Weekly
            $intervalSpec .= "{$frequency}W";
            break;
        case 3: // Monthly
            $intervalSpec .= "{$frequency}M";
            break;
        case 4: // Yearly
            $intervalSpec .= "{$frequency}Y";
            break;
        default:
            $intervalSpec .= "1M"; // Default to monthly
    }

    $lastPaymentDate = clone $nextPaymentDate;
    $lastPaymentDate->sub(new DateInterval($intervalSpec));

    $totalCycleDays = $lastPaymentDate->diff($nextPaymentDate)->days;
    $daysSinceLastPayment = $lastPaymentDate->diff($currentDate)->days;

    $subscriptionProgress = 0;
    if ($totalCycleDays > 0) {
        $subscriptionProgress = ($daysSinceLastPayment / $totalCycleDays) * 100;
    }

    return floor($subscriptionProgress);
}


function getCycleFirstLetter($cycle, $i18n)
{
    switch ($cycle) {
        case 1:
            return mb_substr(translate('Daily', $i18n), 0, 1, 'UTF-8');
        case 2:
            return mb_substr(translate('Weekly', $i18n), 0, 1, 'UTF-8');
        case 3:
            return mb_substr(translate('Monthly', $i18n), 0, 1, 'UTF-8');
        case 4:
            return mb_substr(translate('Yearly', $i18n), 0, 1, 'UTF-8');
    }
}

function getCycleShortNotation($cycle, $frequency, $i18n)
{
    $letter = getCycleFirstLetter($cycle, $i18n);
    return $frequency == 1 ? $letter : $frequency . $letter;
}


function getPriceConverted($price, $currency, $database)
{
    $query = "SELECT rate FROM currencies WHERE id = :currency";
    $stmt = $database->prepare($query);
    $stmt->bindParam(':currency', $currency, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $exchangeRate = $result->fetchArray(SQLITE3_ASSOC);
    if ($exchangeRate === false) {
        return $price;
    } else {
        $fromRate = $exchangeRate['rate'];
        return $price / $fromRate;
    }
}

function formatPrice($price, $currencyCode, $currencies)
{
    $formattedPrice = CurrencyFormatter::format($price, $currencyCode);
    if (strstr($formattedPrice, $currencyCode)) {
        $symbol = $currencyCode;
        
        foreach ($currencies as $currency) {

            if ($currency['code'] === $currencyCode) {
                if ($currency['symbol'] != "") {
                    $symbol = $currency['symbol'];
                }
                break;
            }
        }
        $formattedPrice = str_replace($currencyCode, $symbol, $formattedPrice);
    }

    return $formattedPrice;
}

function formatDate($date, $lang = 'en')
{
    $currentYear = date('Y');
    $dateYear = date('Y', strtotime($date));

    // Determine the date format based on whether the year matches the current year
    $dateFormat = ($currentYear == $dateYear) ? 'MMM d' : 'MMM yyyy';

    // Validate the locale and fallback to 'en' if unsupported
    if (!in_array($lang, ResourceBundle::getLocales(''))) {
        $lang = 'en'; // Fallback to English
    }

    // Create an IntlDateFormatter instance for the specified language
    $formatter = new IntlDateFormatter(
        $lang,
        IntlDateFormatter::SHORT,
        IntlDateFormatter::NONE,
        null,
        null,
        $dateFormat
    );

    // Format the date
    $formattedDate = $formatter->format(new DateTime($date));

    return $formattedDate;
}

function printSubscriptions($subscriptions, $sort, $categories, $members, $i18n, $colorTheme, $imagePath, $disabledToBottom, $mobileNavigation, $showSubscriptionProgress, $currencies, $lang)
{
    if ($sort === "price") {
        usort($subscriptions, function ($a, $b) {
            return $a['price'] < $b['price'] ? 1 : -1;
        });
        if ($disabledToBottom === 'true') {
            usort($subscriptions, function ($a, $b) {
                return $a['inactive'] - $b['inactive'];
            });
        }
    }

    $currentCategory = 0;
    $currentPayerUserId = 0;
    $currentPaymentMethodId = 0;
    foreach ($subscriptions as $subscription) {
        if ($sort == "category_id" && $subscription['category_id'] != $currentCategory) {
            ?>
            <div class="subscription-list-title">
                <?php
                if ($subscription['category_id'] == 1) {
                    echo translate('no_category', $i18n);
                } else {
                    echo $categories[$subscription['category_id']]['name'];
                }
                ?>
            </div>
            <?php
            $currentCategory = $subscription['category_id'];
        }
        if ($sort == "payer_user_id" && $subscription['payer_user_id'] != $currentPayerUserId) {
            ?>
            <div class="subscription-list-title">
                <?= $members[$subscription['payer_user_id']]['name'] ?>
            </div>
            <?php
            $currentPayerUserId = $subscription['payer_user_id'];
        }
        if ($sort == "payment_method_id" && $subscription['payment_method_id'] != $currentPaymentMethodId) {
            ?>
            <div class="subscription-list-title">
                <?= $subscription['payment_method_name'] ?>
            </div>
            <?php
            $currentPaymentMethodId = $subscription['payment_method_id'];
        }
        ?>
        <div class="subscription-container">
            <?php
            if ($mobileNavigation === 'true') {
                ?>
                <div class="mobile-actions" data-id="<?= $subscription['id'] ?>">
                    <button class="mobile-action-clone"></button>
                    <button class="mobile-action-clone" onClick="cloneSubscription(event, <?= $subscription['id'] ?>)">
                        <?php include $imagePath . "images/siteicons/svg/mobile-menu/clone.php"; ?>
                        Clone
                    </button>
                    <button class="mobile-action-delete" onClick="deleteSubscription(event, <?= $subscription['id'] ?>)">
                        <?php include $imagePath . "images/siteicons/svg/mobile-menu/delete.php"; ?>
                        Delete
                    </button>
                    <?php
                    if ($subscription['auto_renew'] != 1) {
                        ?>
                        <button class="mobile-action-renew" onClick="renewSubscription(event, <?= $subscription['id'] ?>)">
                            <?php include $imagePath . "images/siteicons/svg/mobile-menu/renew.php"; ?>
                            Renew
                        </button>
                        <?php
                    }
                    ?>
                    <button class="mobile-action-edit" onClick="openEditSubscription(event, <?= $subscription['id'] ?>)">
                        <?php include $imagePath . "images/siteicons/svg/mobile-menu/edit.php"; ?>
                        Edit
                    </button>
                </div>
                <?php
            }

            $subscriptionExtraClasses = "";
            if ($subscription['inactive']) {
                $subscriptionExtraClasses .= " inactive";
            }
            if ($subscription['auto_renew'] != 1) {
                $subscriptionExtraClasses .= " manual";
            }

            $hasLogo = false;
            if ($subscription['logo'] != "") {
                $hasLogo = true;
            }

            ?>

            <div class="subscription<?= $subscriptionExtraClasses ?>"
                onClick="toggleOpenSubscription(<?= $subscription['id'] ?>)" data-id="<?= $subscription['id'] ?>"
                data-name="<?= $subscription['name'] ?>">
                <div class="subscription-main">
                    <span class="logo <?= !$hasLogo ? 'hideOnMobile' : '' ?>">
                        <?php
                        if ($hasLogo) {
                            ?>
                            <img src="<?= $subscription['logo'] ?>">
                            <?php
                        } else {
                            include $imagePath . "images/siteicons/svg/logo.php";
                        }
                        ?>
                    </span>
                    <span class="name <?= $hasLogo ? 'hideOnMobile' : '' ?>"><?= $subscription['name'] ?></span>
                    <span class="cycle"
                        title="<?= $subscription['auto_renew'] ? translate("automatically_renews", $i18n) : translate("manual_renewal", $i18n) ?>">
                        <?php
                        // Show original price with cycle when different from selected period
                        if (isset($subscription['original_price']) && isset($subscription['display_period'])) {
                            $originalCycleNotation = getCycleShortNotation($subscription['original_cycle'], $subscription['original_frequency'], $i18n);
                            // Convert display_period to localized first letter
                            $selectedPeriodShort = '';
                            switch ($subscription['display_period']) {
                                case 'week':
                                    $selectedPeriodShort = getCycleFirstLetter(2, $i18n);
                                    break;
                                case 'month':
                                    $selectedPeriodShort = getCycleFirstLetter(3, $i18n);
                                    break;
                                case 'year':
                                    $selectedPeriodShort = getCycleFirstLetter(4, $i18n);
                                    break;
                            }
                            
                            // Show original price and cycle if currency differs OR billing cycle differs from selected period
                            if ($subscription['original_currency_code'] != $subscription['currency_code'] || 
                                $originalCycleNotation != $selectedPeriodShort) {
                                // Show the auto-renew icon first
                                if ($subscription['auto_renew']) {
                                    include $imagePath . "images/siteicons/svg/automatic.php";
                                } else {
                                    include $imagePath . "images/siteicons/svg/manual.php";
                                }
                                echo ' ' . strtoupper($originalCycleNotation) . ' · ' . formatPrice($subscription['original_price'], $subscription['original_currency_code'], $currencies);
                            } else {
                                // Normal billing cycle display
                                if ($subscription['auto_renew']) {
                                    include $imagePath . "images/siteicons/svg/automatic.php";
                                } else {
                                    include $imagePath . "images/siteicons/svg/manual.php";
                                }
                                echo ' ' . $subscription['billing_cycle'];
                            }
                        } else {
                            // Fallback to normal display
                            if ($subscription['auto_renew']) {
                                include $imagePath . "images/siteicons/svg/automatic.php";
                            } else {
                                include $imagePath . "images/siteicons/svg/manual.php";
                            }
                            echo ' ' . $subscription['billing_cycle'];
                        }
                        ?>
                    </span>
                    <span class="next"><?= formatDate($subscription['next_payment'], $lang) ?></span>
                    <span class="payment_method">
                        <img src="<?= $subscription['payment_method_icon'] ?>"
                            title="<?= translate('payment_method', $i18n) ?>: <?= $subscription['payment_method_name'] ?>" />
                    </span>
                    <span class="price">
                        <span class="value">
                            <?php
                            // Round to nearest whole number for yearly view, show cents for others
                            $decimals = (isset($subscription['display_period']) && $subscription['display_period'] === 'year') ? DECIMALS_WHOLE : DECIMALS_CURRENCY;
                            echo number_format($subscription['price'], $decimals);
                            ?>
                        </span>

                    </span>
                    <?php
                    $desktopMenuButtonClass = "";
                    if ($mobileNavigation === "true") {
                        $desktopMenuButtonClass = "mobileNavigationHideOnMobile";
                    }
                    ?>
                    <button type="button" class="actions-expand <?= $desktopMenuButtonClass ?>"
                        onClick="expandActions(event, <?= $subscription['id'] ?>)">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="actions">
                        <li class="edit" title="<?= translate('edit_subscription', $i18n) ?>"
                            onClick="openEditSubscription(event, <?= $subscription['id'] ?>)">
                            <?php include $imagePath . "images/siteicons/svg/edit.php"; ?>
                            <?= translate('edit_subscription', $i18n) ?>
                        </li>
                        <li class="delete" title="<?= translate('delete', $i18n) ?>"
                            onClick="deleteSubscription(event, <?= $subscription['id'] ?>)">
                            <?php include $imagePath . "images/siteicons/svg/delete.php"; ?>
                            <?= translate('delete', $i18n) ?>
                        </li>
                        <li class="clone" title="<?= translate('clone', $i18n) ?>"
                            onClick="cloneSubscription(event, <?= $subscription['id'] ?>)">
                            <?php include $imagePath . "images/siteicons/svg/clone.php"; ?>
                            <?= translate('clone', $i18n) ?>
                        </li>
                        <?php
                        if ($subscription['auto_renew'] != 1) {
                            ?>
                            <li class="renew" title="<?= translate('renew', $i18n) ?>"
                                onClick="renewSubscription(event, <?= $subscription['id'] ?>)">
                                <?php include $imagePath . "images/siteicons/svg/renew.php"; ?>
                                <?= translate('renew', $i18n) ?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="subscription-secondary">
                    <span
                        class="name"><?php include $imagePath . "images/siteicons/svg/subscription.php"; ?><?= $subscription['name'] ?></span>
                    <span class="payer_user"
                        title="<?= translate('paid_by', $i18n) ?>"><?php include $imagePath . "images/siteicons/svg/payment.php"; ?><?= $members[$subscription['payer_user_id']]['name'] ?></span>
                    <span class="category"
                        title="<?= translate('category', $i18n) ?>"><?php include $imagePath . "images/siteicons/svg/category.php"; ?><?= $categories[$subscription['category_id']]['name'] ?></span>
                    <?php
                    if ($subscription['url'] != "") {
                        $url = $subscription['url'];
                        if (!preg_match('/^https?:\/\//', $url)) {
                            $url = "https://" . $url;
                        }
                        ?>
                        <span class="url" title="<?= translate('external_url', $i18n) ?>"><a href="<?= $url ?>" target="_blank"
                                rel="noreferrer"><?php include $imagePath . "images/siteicons/svg/web.php"; ?></a></span>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if ($subscription['notes'] != "") {
                    ?>
                    <div class="subscription-notes">
                        <span class="notes">
                            <?php include $imagePath . "images/siteicons/svg/notes.php"; ?>
                            <?= nl2br(htmlspecialchars($subscription['notes'])) ?>
                        </span>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            if ($showSubscriptionProgress === 'true') {
                $progress = $subscription['progress'] > 100 ? 100 : $subscription['progress'];
                ?>
                <div class="subscription-progress-container">
                    <span class="subscription-progress" style="width: <?= $progress ?>%;"></span>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
}


?>