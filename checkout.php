<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

if (is_post() && isset($_POST['qty'])) {
    verify_csrf();

    $draft = get_draft();

    if ($draft === null) {
        redirect('movies.php');
    }

    set_draft((int) $draft['schedule_id'], $draft['seats'], sanitize_quantities($_POST['qty']));
}

[$schedule, $seats, $quantities] = load_valid_draft($pdo);

$items = resolve_products($pdo, $quantities);
$totals = order_totals($schedule, $seats, $items);

page_start('Ringkasan Pesanan · Bioskopie');
?>
<?= booking_steps(3) ?>

<header class="page-head">
    <p class="eyebrow">Hampir selesai</p>
    <h1>Ringkasan pesanan</h1>
</header>

<div class="checkout-layout">
    <section class="panel">
        <div class="summary-movie">
            <?= poster($schedule, 'poster-small') ?>
            <div>
                <h2><?= e($schedule['title']) ?></h2>
                <p class="muted"><?= e($schedule['cinema_name']) ?> · <?= e($schedule['studio_name']) ?></p>
                <p class="muted"><?= e(date_id($schedule['show_date'])) ?> · <?= e(time_short($schedule['show_time'])) ?></p>
            </div>
        </div>

        <table class="summary-table">
            <tbody>
                <tr>
                    <td>Kursi <strong><?= e(implode(', ', $seats)) ?></strong><small><?= count($seats) ?> × <?= e(rupiah($schedule['price'])) ?></small></td>
                    <td class="amount"><?= e(rupiah($totals['ticket'])) ?></td>
                </tr>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?><small><?= (int) $item['quantity'] ?> × <?= e(rupiah($item['price'])) ?></small></td>
                        <td class="amount"><?= e(rupiah($item['subtotal'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total pembayaran</th>
                    <th class="amount amount-total"><?= e(rupiah($totals['grand'])) ?></th>
                </tr>
            </tfoot>
        </table>
    </section>

    <aside class="panel panel-cta">
        <p class="eyebrow">Langkah terakhir</p>
        <p>Pilih metode pembayaran untuk menyelesaikan pesanan.</p>
        <a class="btn btn-primary btn-large btn-block" href="<?= e(url('payment.php')) ?>">Lanjut ke pembayaran</a>
        <a class="btn btn-ghost btn-block" href="<?= e(url('fnv-upsell.php')) ?>">Ubah camilan</a>
    </aside>
</div>
<?php page_end(); ?>
