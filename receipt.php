<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

$bookingId = positive_int($_GET['id'] ?? null) ?? positive_int($_SESSION['booking_id'] ?? null);

if ($bookingId === null) {
    redirect('movies.php');
}

$stmt = $pdo->prepare('
    SELECT b.*, s.show_date, s.show_time, s.price, m.title, st.name AS studio_name, c.name AS cinema_name
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    JOIN movies m ON s.movie_id = m.id
    JOIN studios st ON s.studio_id = st.id
    JOIN cinemas c ON st.cinema_id = c.id
    WHERE b.id = :booking_id AND b.user_id = :user_id
');
$stmt->execute([':booking_id' => $bookingId, ':user_id' => $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    abort_page(404, 'Pesanan tidak ditemukan.');
}

$stmt = $pdo->prepare('SELECT seat_number FROM booking_seats WHERE booking_id = :id');
$stmt->execute([':id' => $bookingId]);
$seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
usort($seats, 'strnatcmp');

$stmt = $pdo->prepare('
    SELECT bp.quantity, bp.subtotal, p.name
    FROM booking_products bp
    JOIN products p ON bp.product_id = p.id
    WHERE bp.booking_id = :id
');
$stmt->execute([':id' => $bookingId]);
$products = $stmt->fetchAll();

$code = 'BSK-' . str_pad((string) $booking['id'], 6, '0', STR_PAD_LEFT);
$statusLabel = PAYMENT_STATUSES[$booking['payment_status']] ?? $booking['payment_status'];

page_start('E-Ticket ' . $code . ' · Bioskopie', ['body' => 'page-receipt']);
?>
<header class="page-head no-print">
    <p class="eyebrow">Pemesanan berhasil</p>
    <h1>E-ticket kamu sudah siap</h1>
</header>

<article class="receipt">
    <div class="receipt-main">
        <p class="eyebrow"><?= e($booking['cinema_name']) ?> · <?= e($booking['studio_name']) ?></p>
        <h2><?= e($booking['title']) ?></h2>

        <dl class="receipt-grid">
            <div><dt>Tanggal</dt><dd><?= e(date_id($booking['show_date'], false)) ?></dd></div>
            <div><dt>Jam</dt><dd><?= e(time_short($booking['show_time'])) ?></dd></div>
            <div><dt>Kursi</dt><dd class="seat-list"><?= e(implode(', ', $seats)) ?></dd></div>
            <div><dt>Pemesan</dt><dd><?= e($_SESSION['user_name']) ?></dd></div>
        </dl>

        <table class="summary-table">
            <tbody>
                <tr><td><?= count($seats) ?> tiket × <?= e(rupiah($booking['price'])) ?></td><td class="amount"><?= e(rupiah($booking['total_ticket_price'])) ?></td></tr>
                <?php foreach ($products as $product): ?>
                    <tr><td><?= e($product['name']) ?> × <?= (int) $product['quantity'] ?></td><td class="amount"><?= e(rupiah($product['subtotal'])) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><th>Total</th><th class="amount amount-total"><?= e(rupiah($booking['grand_total'])) ?></th></tr>
            </tfoot>
        </table>

        <p class="receipt-meta">
            <?= e(PAYMENT_METHODS[$booking['payment_method']] ?? '-') ?>
            <span class="status-badge status-<?= e($booking['payment_status']) ?>"><?= e($statusLabel) ?></span>
        </p>
    </div>

    <div class="receipt-stub">
        <p class="eyebrow">Kode pesanan</p>
        <strong class="receipt-code"><?= e($code) ?></strong>
        <div class="barcode" aria-hidden="true"></div>
        <small>Tunjukkan e-ticket ini di pintu studio.</small>
    </div>
</article>

<div class="receipt-actions no-print">
    <button type="button" class="btn btn-primary" onclick="window.print()">Cetak e-ticket</button>
    <a class="btn btn-ghost" href="<?= e(url('index.php')) ?>">Kembali ke beranda</a>
</div>
<?php page_end(); ?>
