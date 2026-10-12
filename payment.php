<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

[$schedule, $seats, $quantities] = load_valid_draft($pdo);

$items = resolve_products($pdo, $quantities);
$totals = order_totals($schedule, $seats, $items);
$error = '';

if (is_post() && isset($_POST['pay_now'])) {
    verify_csrf();

    $method = (string) ($_POST['payment_method'] ?? '');

    if (!array_key_exists($method, PAYMENT_METHODS)) {
        $error = 'Pilih metode pembayaran terlebih dahulu.';
    } else {
        try {
            $pdo->beginTransaction();

            $lock = $pdo->prepare('SELECT id FROM schedules WHERE id = :id FOR UPDATE');
            $lock->execute([':id' => $schedule['id']]);

            $conflict = array_intersect($seats, booked_seats($pdo, (int) $schedule['id']));

            if ($conflict) {
                $pdo->rollBack();
                flash_set('error', 'Kursi ' . implode(', ', $conflict) . ' baru saja dipesan orang lain. Silakan pilih kursi lain.');
                redirect('seat-selection.php?schedule_id=' . $schedule['id']);
            }

            $status = $method === 'cash' ? 'pending' : 'paid';

            $stmt = $pdo->prepare('
                INSERT INTO bookings (user_id, schedule_id, total_ticket_price, total_fnv_price, grand_total, payment_status, payment_method)
                VALUES (:user_id, :schedule_id, :ticket_price, :fnv_price, :grand_total, :status, :method)
            ');
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':schedule_id' => $schedule['id'],
                ':ticket_price' => $totals['ticket'],
                ':fnv_price' => $totals['fnv'],
                ':grand_total' => $totals['grand'],
                ':status' => $status,
                ':method' => $method,
            ]);

            $bookingId = (int) $pdo->lastInsertId();

            $seatStmt = $pdo->prepare('INSERT INTO booking_seats (booking_id, seat_number) VALUES (:booking_id, :seat)');
            foreach ($seats as $seat) {
                $seatStmt->execute([':booking_id' => $bookingId, ':seat' => $seat]);
            }

            $productStmt = $pdo->prepare('
                INSERT INTO booking_products (booking_id, product_id, quantity, subtotal)
                VALUES (:booking_id, :product_id, :quantity, :subtotal)
            ');
            foreach ($items as $item) {
                $productStmt->execute([
                    ':booking_id' => $bookingId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':subtotal' => $item['subtotal'],
                ]);
            }

            $pdo->commit();

            clear_draft();
            $_SESSION['booking_id'] = $bookingId;
            redirect('receipt.php');
        } catch (PDOException $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('Payment failed: ' . $exception->getMessage());
            $error = 'Pembayaran gagal diproses. Silakan coba lagi.';
        }
    }
}

$methodHints = [
    'transfer_bank' => 'Bayar via virtual account, langsung terkonfirmasi.',
    'e_wallet' => 'Bayar cepat lewat dompet digital favoritmu.',
    'cash' => 'Bayar di loket sebelum film dimulai. Status: menunggu.',
];

page_start('Pembayaran · Bioskopie');
?>
<?= booking_steps(4) ?>

<header class="page-head">
    <p class="eyebrow">Pembayaran</p>
    <h1>Pilih metode bayar</h1>
</header>

<?= alert('error', $error) ?>

<form method="POST" class="checkout-layout">
    <?= csrf_field() ?>

    <section class="panel">
        <div class="payment-methods" role="radiogroup" aria-label="Metode pembayaran">
            <?php foreach (PAYMENT_METHODS as $value => $label): ?>
                <label class="method">
                    <input type="radio" name="payment_method" value="<?= e($value) ?>" required>
                    <span class="method-body">
                        <strong><?= e($label) ?></strong>
                        <small><?= e($methodHints[$value]) ?></small>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="panel panel-cta">
        <p class="eyebrow">Total pembayaran</p>
        <p class="amount-hero"><?= e(rupiah($totals['grand'])) ?></p>
        <p class="muted"><?= e($schedule['title']) ?><br>Kursi <?= e(implode(', ', $seats)) ?></p>
        <button type="submit" name="pay_now" value="1" class="btn btn-primary btn-large btn-block">Konfirmasi pembayaran</button>
        <a class="btn btn-ghost btn-block" href="<?= e(url('checkout.php')) ?>">Kembali</a>
    </aside>
</form>
<?php page_end(); ?>
