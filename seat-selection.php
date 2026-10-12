<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

$scheduleId = positive_int($_GET['schedule_id'] ?? null);
$schedule = $scheduleId !== null ? fetch_schedule($pdo, $scheduleId) : null;

if ($schedule === null) {
    abort_page(404, 'Jadwal tidak ditemukan.');
}

$booked = booked_seats($pdo, $scheduleId);
$layout = seat_layout((int) $schedule['capacity']);
$draft = get_draft();
$preselected = ($draft && (int) $draft['schedule_id'] === $scheduleId) ? $draft['seats'] : [];

page_start('Pilih Kursi · Bioskopie');
?>
<?= booking_steps(1) ?>

<header class="page-head">
    <p class="eyebrow"><?= e($schedule['cinema_name']) ?> · <?= e($schedule['studio_name']) ?></p>
    <h1><?= e($schedule['title']) ?></h1>
    <p class="muted"><?= e(date_id($schedule['show_date'])) ?> · <?= e(time_short($schedule['show_time'])) ?> · <?= e(rupiah($schedule['price'])) ?> / kursi</p>
</header>

<?= flash_render() ?>

<form method="POST" action="<?= e(url('fnv-upsell.php')) ?>" id="seat-form" data-price="<?= (int) $schedule['price'] ?>" data-max="<?= MAX_SEATS_PER_BOOKING ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="schedule_id" value="<?= $scheduleId ?>">

    <div class="cinema-seats">
        <div class="screen" aria-hidden="true"><span>LAYAR</span></div>

        <div class="seat-map" role="group" aria-label="Denah kursi">
            <?php foreach ($layout as $rowLetter => $seats): ?>
                <div class="seat-row">
                    <span class="seat-row-label" aria-hidden="true"><?= e($rowLetter) ?></span>
                    <?php foreach ($seats as $index => $seatId): ?>
                        <?php $aisle = ($index + 1) === (int) (SEATS_PER_ROW / 2) ? ' seat-aisle' : ''; ?>
                        <?php if (in_array($seatId, $booked, true)): ?>
                            <span class="seat is-booked<?= $aisle ?>" title="<?= e($seatId) ?> sudah terpesan" aria-label="Kursi <?= e($seatId) ?> sudah terpesan"><?= e(substr($seatId, 1)) ?></span>
                        <?php else: ?>
                            <label class="seat<?= $aisle ?>" title="Kursi <?= e($seatId) ?>">
                                <input type="checkbox" name="seats[]" value="<?= e($seatId) ?>"<?= in_array($seatId, $preselected, true) ? ' checked' : '' ?>>
                                <span><?= e(substr($seatId, 1)) ?></span>
                            </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <ul class="legend">
            <li><i class="swatch swatch-available"></i>Tersedia</li>
            <li><i class="swatch swatch-selected"></i>Dipilih</li>
            <li><i class="swatch swatch-booked"></i>Terpesan</li>
        </ul>
    </div>

    <div class="action-bar">
        <div class="action-bar-info">
            <p><span class="muted">Kursi</span> <strong id="selected-seats">-</strong></p>
            <p><span class="muted">Total tiket</span> <strong id="total-price">Rp 0</strong></p>
        </div>
        <button type="submit" class="btn btn-primary btn-large" id="continue-btn" disabled>Lanjut ke camilan</button>
    </div>
</form>
<?php page_end(['seat.js']); ?>
