<?php
declare(strict_types=1);

function nav_link(string $file, string $label): string
{
    $active = str_ends_with((string) ($_SERVER['SCRIPT_NAME'] ?? ''), '/' . $file) ? ' aria-current="page"' : '';

    return '<a href="' . e(url($file)) . '"' . $active . '>' . e($label) . '</a>';
}

function brand_mark(string $href): string
{
    return '<a class="brand" href="' . e($href) . '"><span class="brand-bulbs" aria-hidden="true"><i></i><i></i><i></i></span>'
        . '<span class="brand-name">Bioskop<em>ie</em></span></a>';
}

function page_start(string $title, array $options = []): void
{
    $bodyClass = (string) ($options['body'] ?? '');
    $bare = (bool) ($options['bare'] ?? false);
    $admin = (bool) ($options['admin'] ?? false);

    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
        . '<meta name="theme-color" content="#0f0b1a">'
        . '<title>' . e($title) . '</title>'
        . '<link rel="preconnect" href="https://fonts.googleapis.com">'
        . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
        . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,700;1,9..144,500;1,9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap">'
        . '<link rel="stylesheet" href="' . e(url('assets/css/style.css')) . '">'
        . '<link rel="stylesheet" href="' . e(url('assets/css/print.css')) . '" media="print">'
        . '</head><body class="' . e($bodyClass) . '"><a class="skip-link" href="#main">Lewati ke konten</a>';

    if (!$bare) {
        echo '<header class="navbar"><div class="container navbar-inner">' . brand_mark(url($admin ? 'admin/dashboard.php' : 'index.php'));

        echo '<nav class="nav-links" aria-label="Navigasi utama">';

        if ($admin) {
            echo nav_link('admin/dashboard.php', 'Dasbor'), nav_link('admin/manage-movies.php', 'Film'),
                nav_link('admin/manage-schedules.php', 'Jadwal'), nav_link('admin/manage-products.php', 'F&B'),
                nav_link('admin/manage-bookings.php', 'Pesanan'), nav_link('admin/reports.php', 'Laporan'),
                '<a href="' . e(url('index.php')) . '">Situs</a>';
        } else {
            echo nav_link('index.php', 'Beranda'), nav_link('movies.php', 'Film'), nav_link('schedule.php', 'Jadwal');

            if (is_staff()) {
                echo '<a href="' . e(url('admin/dashboard.php')) . '">Admin</a>';
            }
        }

        echo '</nav><div class="nav-actions">';

        if (is_logged_in()) {
            echo '<span class="user-chip" title="' . e($_SESSION['user_name']) . '">' . e(mb_substr((string) $_SESSION['user_name'], 0, 1)) . '</span>'
                . '<a class="btn btn-ghost btn-small" href="' . e(url('logout.php')) . '">Keluar</a>';
        } else {
            echo '<a class="btn btn-ghost btn-small" href="' . e(url('login.php')) . '">Masuk</a>'
                . '<a class="btn btn-primary btn-small" href="' . e(url('register.php')) . '">Daftar</a>';
        }

        echo '</div></div></header>';
    }

    echo '<main id="main" class="container main">';
}

function page_end(array $scripts = [], bool $bare = false): void
{
    echo '</main>';

    if (!$bare) {
        echo '<footer class="site-footer"><div class="container footer-inner">'
            . '<span class="brand-name">Bioskop<em>ie</em></span>'
            . '<p>Pesan kursi, tambah camilan, tunjukkan e-ticket. Selesai.</p>'
            . '<small>&copy; ' . date('Y') . ' Bioskopie</small></div></footer>';
    }

    // Image handling is global because movie/product images appear on many pages.
    $scripts = array_values(array_unique(array_merge(['script.js'], $scripts)));

    foreach ($scripts as $script) {
        echo '<script src="' . e(url('assets/js/' . $script)) . '" defer></script>';
    }

    echo '</body></html>';
}

function booking_steps(int $active): string
{
    $labels = ['Kursi', 'Camilan', 'Ringkasan', 'Bayar'];
    $html = '<ol class="steps" aria-label="Tahapan pemesanan">';

    foreach ($labels as $index => $label) {
        $number = $index + 1;
        $state = $number < $active ? 'is-done' : ($number === $active ? 'is-active' : '');
        $current = $number === $active ? ' aria-current="step"' : '';
        $html .= '<li class="' . $state . '"' . $current . '><span>' . $number . '</span>' . e($label) . '</li>';
    }

    return $html . '</ol>';
}

function movie_card(array $movie): string
{
    $showing = (int) ($movie['showtimes'] ?? 0) > 0;
    $badge = $showing ? '<span class="badge badge-live">Tayang</span>' : '';

    return '<a class="movie-card" href="' . e(url('movie-detail.php?id=' . (int) $movie['id'])) . '">'
        . $badge . poster($movie)
        . '<div class="movie-card-body"><h3>' . e($movie['title']) . '</h3>'
        . '<p><span>' . e($movie['genre']) . '</span><span>' . (int) $movie['duration'] . ' mnt</span><span>' . e(date('Y', strtotime($movie['release_date']))) . '</span></p></div></a>';
}
