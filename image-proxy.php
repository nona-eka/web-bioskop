<?php
declare(strict_types=1);

// Remote image proxy. It does not save the image to disk; it streams the
// internet image to the browser. This is used for URLs stored in the database.
$url = trim((string) ($_GET['url'] ?? ''));

if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit('Invalid image URL');
}

$scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
if (!in_array($scheme, ['http', 'https'], true)) {
    http_response_code(400);
    exit('Unsupported image URL');
}

function stream_remote_image(string $target): array
{
    $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/154 Safari/537.36',
        'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($target);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        return [$status, $type, is_string($body) ? $body : ''];
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 20,
            'follow_location' => 1,
            'max_redirects' => 5,
        ],
    ]);
    $body = @file_get_contents($target, false, $context);
    $type = '';
    foreach (($http_response_header ?? []) as $header) {
        if (stripos($header, 'Content-Type:') === 0) {
            $type = trim(substr($header, 13));
        }
    }
    return [$body === false ? 0 : 200, $type, $body === false ? '' : $body];
}

[$status, $type, $body] = stream_remote_image($url);

// Amazon/IMDb image hosts can reject direct hotlinking. If the direct request
// fails, retry through wsrv.nl while keeping the original database URL intact.
if ($body === '' || $status < 200 || $status >= 400 || !str_starts_with(strtolower($type), 'image/')) {
    $proxyUrl = 'https://wsrv.nl/?url=' . rawurlencode($url);
    [$proxyStatus, $proxyType, $proxyBody] = stream_remote_image($proxyUrl);
    if ($proxyBody !== '' && $proxyStatus >= 200 && $proxyStatus < 400 && str_starts_with(strtolower($proxyType), 'image/')) {
        $status = $proxyStatus;
        $type = $proxyType;
        $body = $proxyBody;
    }
}

if ($body === '' || $status < 200 || $status >= 400 || !str_starts_with(strtolower($type), 'image/')) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Image could not be loaded');
}

header('Content-Type: ' . trim(explode(';', $type)[0]));
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
echo $body;
