<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

require_staff();

$stats = $pdo->query('
    SELECT
        (SELECT COUNT(*) FROM movies) AS movies,
        (SELECT COUNT(*) FROM schedules WHERE show_date >= CURDATE()) AS schedules,
        (SELECT COUNT(*) FROM bookings) AS bookings,
        (SELECT COALESCE(SUM(grand_total), 0) FROM bookings WHERE payment_status = \'paid\') AS revenue,
        (SELECT COUNT(*) FROM bookings WHERE payment_status = \'pending\') AS pending
')->fetch();

$recentSales = $pdo->query('
    SELECT b.id, b.booking_date, b.grand_total, b.payment_status, u.name AS user_name, m.title
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN schedules s ON b.schedule_id = s.id
    JOIN movies m ON s.movie_id = m.id
    ORDER BY b.booking_date DESC
    LIMIT 8
')->fetchAll();

$shortcuts = [
    ['admin/manage-movies.php', 'Kelola film', 'Tambah atau hapus judul di katalog.'],
    ['admin/manage-schedules.php', 'Kelola jadwal', 'Atur jam tayang, studio, dan harga.'],
    ['admin/manage-products.php', 'Kelola F&B', 'Menu camilan dan minuman.'],
    ['admin/manage-bookings.php', 'Kelola pesanan', 'Konfirmasi pembayaran atau batalkan.'],
    ['admin/reports.php', 'Laporan', 'Pendapatan per film dan metode bayar.'],
];

page_start('Dasbor Admin · Bioskopie', ['admin' => true]);
?>
<header class="page-head">
    <p class="eyebrow">Halo, <?= e($_SESSION['user_name']) ?></p>
    <h1>Dasbor</h1>
</header>

<div class="stats-grid">
    <div class="stat-card"><span>Pendapatan</span><strong><?= e(rupiah($stats['revenue'])) ?></strong></div>
    <div class="stat-card"><span>Total pesanan</span><strong><?= (int) $stats['bookings'] ?></strong></div>
    <div class="stat-card"><span>Menunggu bayar</span><strong><?= (int) $stats['pending'] ?></strong></div>
    <div class="stat-card"><span>Jadwal mendatang</span><strong><?= (int) $stats['schedules'] ?></strong></div>
    <div class="stat-card"><span>Film</span><strong><?= (int) $stats['movies'] ?></strong></div>
</div>

<div class="shortcut-grid">
    <?php foreach ($shortcuts as [$path, $title, $text]): ?>
        <a class="shortcut" href="<?= e(url($path)) ?>">
            <strong><?= e($title) ?></strong>
            <span><?= e($text) ?></span>
        </a>
    <?php endforeach; ?>
</div>

<section class="section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Penjualan</p>
            <h2>Transaksi terbaru</h2>
        </div>
        <a class="btn btn-ghost btn-small" href="<?= e(url('admin/reports.php')) ?>">Lihat laporan lengkap</a>
    </div>
    <?php if ($recentSales): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Kode</th><th>Pelanggan</th><th>Film</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
                <tbody>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td>BSK-<?= e(str_pad((string) $sale['id'], 6, '0', STR_PAD_LEFT)) ?></td>
                            <td><?= e($sale['user_name']) ?></td>
                            <td><?= e($sale['title']) ?></td>
                            <td><?= e(rupiah($sale['grand_total'])) ?></td>
                            <td><span class="status-badge status-<?= e($sale['payment_status']) ?>"><?= e(PAYMENT_STATUSES[$sale['payment_status']] ?? $sale['payment_status']) ?></span></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($sale['booking_date']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="empty-inline">Belum ada transaksi.</p>
    <?php endif; ?>
</section>

<?php page_end(); ?>