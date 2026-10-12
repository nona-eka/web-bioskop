<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function rupiah(float|int|string $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';

    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        abort_page(419, 'Sesi formulir sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function is_staff(): bool
{
    return in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'], true);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_staff(): void
{
    require_login();

    if (!is_staff()) {
        abort_page(403, 'Halaman ini hanya untuk admin dan staf.');
    }
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function flash_render(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return '<div class="alert alert-' . e($flash['type']) . '" role="alert">'
        . e($flash['message'])
        . '</div>';
}

function alert(string $type, string $message): string
{
    if ($message === '') {
        return '';
    }

    return '<div class="alert alert-' . e($type) . '" role="alert">'
        . e($message)
        . '</div>';
}

function positive_int(mixed $value): ?int
{
    $number = filter_var($value, FILTER_VALIDATE_INT);

    return ($number !== false && $number > 0) ? $number : null;
}

function date_id(string $date, bool $withDay = true): string
{
    $days = [
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    ];

    $months = [
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $timestamp = strtotime($date);

    $text = (int) date('j', $timestamp)
        . ' '
        . $months[(int) date('n', $timestamp)]
        . ' '
        . date('Y', $timestamp);

    return $withDay
        ? $days[(int) date('w', $timestamp)] . ', ' . $text
        : $text;
}

function time_short(string $time): string
{
    return substr($time, 0, 5);
}

function genre_hue(string $genre): int
{
    return [
        'Fantasy' => 268,
        'Horror' => 350,
        'Action' => 28,
        'Animation' => 172
    ][$genre] ?? 210;
}

function image_source(string $value): string
{
    $value = trim($value);

    if ($value === '') {
        return '';
    }

    // The database stores internet image URLs. Route remote URLs through our
    // local PHP proxy so hosts such as Amazon cannot break the <img> request
    // because of hotlink/referrer restrictions. No image is stored locally.
    if (str_starts_with($value, '//')) {
        $value = 'https:' . $value;
    }

    if (filter_var($value, FILTER_VALIDATE_URL)) {
        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

        if (in_array($scheme, ['http', 'https'], true)) {
            return url('image-proxy.php?url=' . rawurlencode($value));
        }
    }

    // Keep support for existing TMDB path values if a future record uses one.
    if (str_starts_with($value, '/')) {
        $remote = 'https://image.tmdb.org/t/p/w500' . $value;
        return url('image-proxy.php?url=' . rawurlencode($remote));
    }

    if (str_contains($value, 'image.tmdb.org') || str_contains($value, 'tmdb.org')) {
        return url('image-proxy.php?url=' . rawurlencode($value));
    }

    return '';
}

function poster(array $movie, string $class = ''): string
{
    $title = (string) ($movie['title'] ?? 'Film');
    $poster = (string) ($movie['poster'] ?? '');
    $source = image_source($poster);

    if ($source === '') {
        return '<div class="poster ' . e($class) . '">'
            . '<div class="poster-empty"><span>Poster tidak tersedia</span></div>'
            . '</div>';
    }

    return '<div class="poster ' . e($class) . '">'
        . '<img src="' . e($source) . '"'
        . ' alt="Poster ' . e($title) . '"'
        . ' loading="lazy"'
        . ' data-poster-title="' . e($title) . '"'
        . ' data-original-image="' . e($poster) . '"'
        . ' onerror="window.imageFallback && window.imageFallback(this)">'
        . '</div>';
}

function product_visual(array $product): string
{
    $name = (string) ($product['name'] ?? 'Produk');
    $image = (string) ($product['image'] ?? '');
    $source = image_source($image);

    if ($source === '') {
        return '<div class="product-visual product-visual-empty">'
            . '<span>Gambar tidak tersedia</span>'
            . '</div>';
    }

    return '<div class="product-visual">'
        . '<img src="' . e($source) . '"'
        . ' alt="' . e($name) . '"'
        . ' loading="lazy"'
        . ' data-product-title="' . e($name) . '"'
        . ' data-original-image="' . e($image) . '"'
        . ' onerror="window.imageFallback && window.imageFallback(this)">'
        . '</div>';
}

function abort_page(int $code, string $message): never
{
    http_response_code($code);

    page_start('Oops · Bioskopie');

    echo '<section class="empty-state">'
        . '<p class="eyebrow">Error ' . $code . '</p>'
        . '<h1>Layar mendadak gelap</h1>'
        . '<p>' . e($message) . '</p>'
        . '<a class="btn btn-primary" href="' . e(url('index.php')) . '">Kembali ke beranda</a>'
        . '</section>';

    page_end();

    exit;
}