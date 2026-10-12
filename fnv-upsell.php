<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

if (is_post() && isset($_POST['schedule_id'])) {
    verify_csrf();

    $scheduleId = positive_int($_POST['schedule_id']);
    $schedule = $scheduleId !== null ? fetch_schedule($pdo, $scheduleId) : null;

    if ($schedule === null) {
        abort_page(404, 'Jadwal tidak ditemukan.');
    }

    $seats = sanitize_seats($_POST['seats'] ?? [], (int) $schedule['capacity']);
    $previous = get_draft();
    $keepQuantities = $previous && (int) $previous['schedule_id'] === $scheduleId ? sanitize_quantities($previous['quantities']) : [];

    set_draft($scheduleId, $seats, $keepQuantities);
}

[$schedule, $seats, $quantities] = load_valid_draft($pdo);

$products = $pdo->query('SELECT * FROM products ORDER BY FIELD(category, \'combo\', \'food\', \'beverage\'), name')->fetchAll();
$grouped = [];
foreach ($products as $product) {
    $grouped[$product['category']][] = $product;
}

page_start('Camilan · Bioskopie');
?>
<?= booking_steps(2) ?>

<header class="page-head">
    <p class="eyebrow">Opsional</p>
    <h1>Nonton lebih seru dengan camilan</h1>
    <p class="muted"><?= e($schedule['title']) ?> · Kursi <?= e(implode(', ', $seats)) ?></p>
</header>

<form method="POST" action="<?= e(url('checkout.php')) ?>" id="fnv-form">
    <?= csrf_field() ?>

    <?php if ($products): ?>
        <?php foreach ($grouped as $category => $items): ?>
            <h2 class="date-heading"><?= e(PRODUCT_CATEGORIES[$category] ?? ucfirst($category)) ?></h2>
            <div class="products-grid">
                <?php foreach ($items as $product): ?>
                    <article class="product-card" data-price="<?= (int) $product['price'] ?>">
                        <?= product_visual($product) ?>
                        <h3><?= e($product['name']) ?></h3>
                        <p class="price"><?= e(rupiah($product['price'])) ?></p>
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn" data-action="decrease" aria-label="Kurangi <?= e($product['name']) ?>">−</button>
                            <input type="number" class="qty-input" name="qty[<?= (int) $product['id'] ?>]" value="<?= (int) ($quantities[(int) $product['id']] ?? 0) ?>" min="0" max="<?= MAX_QUANTITY_PER_PRODUCT ?>" inputmode="numeric" aria-label="Jumlah <?= e($product['name']) ?>">
                            <button type="button" class="qty-btn" data-action="increase" aria-label="Tambah <?= e($product['name']) ?>">+</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-inline">Menu camilan belum tersedia. Kamu bisa lanjut ke ringkasan pesanan.</p>
    <?php endif; ?>

    <div class="action-bar">
        <div class="action-bar-info">
            <p><span class="muted">Total F&amp;B</span> <strong id="fnv-total">Rp 0</strong></p>
        </div>
        <div class="action-bar-buttons">
            <a class="btn btn-ghost" href="<?= e(url('seat-selection.php?schedule_id=' . (int) $schedule['id'])) ?>">Ubah kursi</a>
            <button type="submit" class="btn btn-primary btn-large">Lanjut ke ringkasan</button>
        </div>
    </div>
</form>
<?php page_end(['script.js']); ?>
