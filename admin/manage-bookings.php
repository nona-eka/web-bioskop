<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

require_staff();

if (is_post()) {
    verify_csrf();

    $bookingId = positive_int($_POST['booking_id'] ?? null);
    $status = (string) ($_POST['status'] ?? '');

    if ($bookingId !== null && array_key_exists($status, PAYMENT_STATUSES)) {
        $stmt = $pdo->prepare('UPDATE bookings SET payment_status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $bookingId]);
        flash_set('success', 'Status pesanan diperbarui.');
    }

    redirect('admin/manage-bookings.php' . (isset($_GET['status']) ? '?status=' . urlencode((string) $_GET['status']) : ''));
}

$filter = (string) ($_GET['status'] ?? '');
$params = [];
$where = '';

if (array_key_exists($filter, PAYMENT_STATUSES)) {
    $where = 'WHERE b.payment_status = :status';
    $params[':status'] = $filter;
} else {
    $filter = '';
}

$stmt = $pdo->prepare("
    SELECT b.*, u.name AS user_name, m.title, s.show_date, s.show_time,
           (SELECT GROUP_CONCAT(seat_number ORDER BY seat_number SEPARATOR ', ') FROM booking_seats WHERE booking_id = b.id) AS seats
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN schedules s ON b.schedule_id = s.id
    JOIN movies m ON s.movie_id = m.id
    $where
    ORDER BY b.booking_date DESC
    LIMIT 200
");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

page_start('Kelola Pesanan · Bioskopie', ['admin' => true]);
?>
<header class="page-head">
    <p class="eyebrow">Admin</p>
    <h1>Kelola pesanan</h1>
</header>

<?= flash_render() ?>

<nav class="chips" aria-label="Filter status">
    <a class="chip<?= $filter === '' ? ' is-active' : '' ?>" href="<?= e(url('admin/manage-bookings.php')) ?>">Semua</a>
    <?php foreach (PAYMENT_STATUSES as $value => $label): ?>
        <a class="chip<?= $filter === $value ? ' is-active' : '' ?>" href="<?= e(url('admin/manage-bookings.php?status=' . $value)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($bookings): ?>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Kode</th><th>Pemesan</th><th>Film</th><th>Kursi</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>BSK-<?= e(str_pad((string) $booking['id'], 6, '0', STR_PAD_LEFT)) ?></td>
                        <td><?= e($booking['user_name']) ?></td>
                        <td><?= e($booking['title']) ?><small class="cell-sub"><?= e(date_id($booking['show_date'], false)) ?> · <?= e(time_short($booking['show_time'])) ?></small></td>
                        <td><?= e($booking['seats']) ?></td>
                        <td><?= e(rupiah($booking['grand_total'])) ?></td>
                        <td><span class="status-badge status-<?= e($booking['payment_status']) ?>"><?= e(PAYMENT_STATUSES[$booking['payment_status']] ?? $booking['payment_status']) ?></span></td>
                        <td class="cell-actions">
                            <form method="POST" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                <select name="status" aria-label="Ubah status">
                                    <?php foreach (PAYMENT_STATUSES as $value => $label): ?>
                                        <option value="<?= e($value) ?>"<?= $booking['payment_status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-ghost btn-small">Simpan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="empty-inline">Belum ada pesanan.</p>
<?php endif; ?>
<?php page_end(); ?>
