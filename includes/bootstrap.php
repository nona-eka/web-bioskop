<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

require_once __DIR__ . '/../database/connection.php';

$scriptDirectory = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if (str_ends_with($scriptDirectory, '/admin')) {
    $scriptDirectory = substr($scriptDirectory, 0, -6);
}

define('BASE_URL', $scriptDirectory);
define('ROOT_PATH', dirname(__DIR__));

const SEATS_PER_ROW = 10;
const MAX_SEATS_PER_BOOKING = 8;
const MAX_QUANTITY_PER_PRODUCT = 20;
const PAYMENT_METHODS = [
    'transfer_bank' => 'Transfer Bank',
    'e_wallet' => 'E-Wallet',
    'cash' => 'Tunai di Loket',
];
const PRODUCT_CATEGORIES = [
    'food' => 'Makanan',
    'beverage' => 'Minuman',
    'combo' => 'Combo',
];
const PAYMENT_STATUSES = [
    'pending' => 'Menunggu',
    'paid' => 'Lunas',
    'cancelled' => 'Dibatalkan',
];

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/booking.php';
require_once __DIR__ . '/layout.php';
