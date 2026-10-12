<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

$movieId = positive_int($_GET['id'] ?? null);

if ($movieId === null) {
    abort_page(404, 'Film tidak ditemukan.');
}

$stmt = $pdo->prepare('SELECT * FROM movies WHERE id = :id');
$stmt->execute([':id' => $movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    abort_page(404, 'Film tidak ditemukan.');
}

$stmt = $pdo->prepare('
    SELECT s.*, c.name AS cinema_name, st.name AS studio_name
    FROM schedules s
    JOIN studios st ON s.studio_id = st.id
    JOIN cinemas c ON st.cinema_id = c.id
    WHERE s.movie_id = :movie_id AND s.show_date >= CURDATE()
    ORDER BY s.show_date, s.show_time
');
$stmt->execute([':movie_id' => $movieId]);

$byDate = [];
foreach ($stmt->fetchAll() as $schedule) {
    $byDate[$schedule['show_date']][] = $schedule;
}

page_start($movie['title'] . ' · Bioskopie');
?>
<section class="movie-hero">
    <?= poster($movie, 'poster-large') ?>
    <div class="movie-hero-body">
        <a class="link-back" href="<?= e(url('movies.php')) ?>">Semua film</a>
        <p class="eyebrow"><?= e($movie['genre']) ?></p>
        <h1><?= e($movie['title']) ?></h1>
        <ul class="meta-list">
            <li><?= (int) $movie['duration'] ?> menit</li>
            <li>Rilis <?= e(date('Y', strtotime($movie['release_date']))) ?></li>
        </ul>
        <p class="synopsis"><?= e($movie['synopsis']) ?></p>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Jadwal tayang</p>
            <h2>Pilih jam nontonmu</h2>
        </div>
    </div>

    <?php if ($byDate): ?>
        <?php foreach ($byDate as $date => $schedules): ?>
            <h3 class="date-heading"><?= e(date_id($date)) ?></h3>
            <div class="ticket-list">
                <?php foreach ($schedules as $schedule): ?>
                    <article class="ticket">
                        <div class="ticket-main">
                            <strong class="ticket-time"><?= e(time_short($schedule['show_time'])) ?></strong>
                            <div>
                                <p class="ticket-title"><?= e($schedule['cinema_name']) ?></p>
                                <p class="ticket-sub"><?= e($schedule['studio_name']) ?> · <?= e(rupiah($schedule['price'])) ?></p>
                            </div>
                        </div>
                        <div class="ticket-stub">
                            <a class="btn btn-primary btn-small" href="<?= e(url('seat-selection.php?schedule_id=' . (int) $schedule['id'])) ?>">Pilih kursi</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-inline">Belum ada jadwal tayang untuk film ini.</p>
    <?php endif; ?>
</section>
<?php page_end(); ?>
