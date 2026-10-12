<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

require_staff();

$summary = $pdo->query('
    SELECT
        COALESCE(SUM(CASE WHEN payment_status = \'paid\' THEN grand_total END), 0) AS revenue,
        COUNT(*) AS bookings,
        SUM(payment_status = \'paid\') AS paid,
        SUM(payment_status = \'pending\') AS pending,
        SUM(payment_status = \'cancelled\') AS cancelled
    FROM bookings
')->fetch();

$ticketsSold = (int) $pdo->query('
    SELECT COUNT(*) FROM booking_seats bs JOIN bookings b ON bs.booking_id = b.id WHERE b.payment_status = \'paid\'
')->fetchColumn();

$movieRevenue = $pdo->query('
    SELECT m.title, COUNT(b.id) AS total_bookings, SUM(b.grand_total) AS total
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    JOIN movies m ON s.movie_id = m.id
    WHERE b.payment_status = \'paid\'
    GROUP BY m.id, m.title
    ORDER BY total DESC
')->fetchAll();

$paymentMethods = $pdo->query('
    SELECT payment_method, COUNT(*) AS total_bookings, SUM(grand_total) AS total
    FROM bookings
    WHERE payment_status = \'paid\'
    GROUP BY payment_method
    ORDER BY total DESC
')->fetchAll();

$maxMovie = $movieRevenue ? max(array_column($movieRevenue, 'total')) : 0;

page_start('Laporan · Bioskopie', ['admin' => true]);
?>
<header class="page-head">
    <p class="eyebrow">Admin</p>
    <h1>Laporan penjualan</h1>
    <p class="muted">Pendapatan hanya menghitung pesanan berstatus lunas.</p>
</header>

<div class="stats-grid">
    <div class="stat-card"><span>Pendapatan</span><strong><?= e(rupiah($summary['revenue'])) ?></strong></div>
    <div class="stat-card"><span>Tiket terjual</span><strong><?= $ticketsSold ?></strong></div>
    <div class="stat-card"><span>Total pesanan</span><strong><?= (int) $summary['bookings'] ?></strong></div>
    <div class="stat-card"><span>Menunggu</span><strong><?= (int) $summary['pending'] ?></strong></div>
    <div class="stat-card"><span>Dibatalkan</span><strong><?= (int) $summary['cancelled'] ?></strong></div>
</div>

<section class="section">
    <h2>Pendapatan per film</h2>
    <?php if ($movieRevenue): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Film</th><th>Pesanan</th><th>Pendapatan</th></tr></thead>
                <tbody>
                    <?php foreach ($movieRevenue as $row): ?>
                        <tr>
                            <td>
                                <?= e($row['title']) ?>
                                <span class="bar"><i style="width: <?= $maxMovie > 0 ? round(((float) $row['total'] / (float) $maxMovie) * 100) : 0 ?>%"></i></span>
                            </td>
                            <td><?= (int) $row['total_bookings'] ?></td>
                            <td><?= e(rupiah($row['total'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="empty-inline">Belum ada pesanan lunas.</p>
    <?php endif; ?>
</section>

<section class="section">
    <h2>Metode pembayaran</h2>
    <?php if ($paymentMethods): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Metode</th><th>Pesanan</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($paymentMethods as $row): ?>
                        <tr>
                            <td><?= e(PAYMENT_METHODS[$row['payment_method']] ?? '-') ?></td>
                            <td><?= (int) $row['total_bookings'] ?></td>
                            <td><?= e(rupiah($row['total'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="empty-inline">Belum ada data pembayaran.</p>
    <?php endif; ?>
</section>
<?php page_end(); ?>
