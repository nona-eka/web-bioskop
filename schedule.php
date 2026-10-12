<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

$movieFilter = positive_int($_GET['movie_id'] ?? null);
$cinemaFilter = positive_int($_GET['cinema_id'] ?? null);

$movies = $pdo->query('
    SELECT DISTINCT m.id, m.title
    FROM schedules s JOIN movies m ON s.movie_id = m.id
    WHERE s.show_date >= CURDATE()
    ORDER BY m.title
')->fetchAll();

$cinemas = $pdo->query('
    SELECT DISTINCT c.id, c.name
    FROM schedules s
    JOIN studios st ON s.studio_id = st.id
    JOIN cinemas c ON st.cinema_id = c.id
    WHERE s.show_date >= CURDATE()
    ORDER BY c.name
')->fetchAll();

$conditions = ['s.show_date >= CURDATE()'];
$params = [];

if ($movieFilter !== null) {
    $conditions[] = 's.movie_id = :movie_id';
    $params[':movie_id'] = $movieFilter;
}

if ($cinemaFilter !== null) {
    $conditions[] = 'c.id = :cinema_id';
    $params[':cinema_id'] = $cinemaFilter;
}

$stmt = $pdo->prepare('
    SELECT s.*, m.title, m.genre, c.name AS cinema_name, st.name AS studio_name
    FROM schedules s
    JOIN movies m ON s.movie_id = m.id
    JOIN studios st ON s.studio_id = st.id
    JOIN cinemas c ON st.cinema_id = c.id
    WHERE ' . implode(' AND ', $conditions) . '
    ORDER BY s.show_date, s.show_time
');
$stmt->execute($params);

$byDate = [];
foreach ($stmt->fetchAll() as $schedule) {
    $byDate[$schedule['show_date']][] = $schedule;
}

page_start('Jadwal Tayang · Bioskopie');
?>
<header class="page-head">
    <p class="eyebrow">Jadwal</p>
    <h1>Jam tayang mendatang</h1>
</header>

<form class="toolbar toolbar-filters" method="GET">
    <div class="form-group">
        <label for="movie_id">Film</label>
        <select id="movie_id" name="movie_id">
            <option value="">Semua film</option>
            <?php foreach ($movies as $movie): ?>
                <option value="<?= (int) $movie['id'] ?>"<?= $movieFilter === (int) $movie['id'] ? ' selected' : '' ?>><?= e($movie['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="cinema_id">Bioskop</label>
        <select id="cinema_id" name="cinema_id">
            <option value="">Semua bioskop</option>
            <?php foreach ($cinemas as $cinema): ?>
                <option value="<?= (int) $cinema['id'] ?>"<?= $cinemaFilter === (int) $cinema['id'] ? ' selected' : '' ?>><?= e($cinema['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Terapkan</button>
</form>

<?php if ($byDate): ?>
    <?php foreach ($byDate as $date => $schedules): ?>
        <h2 class="date-heading"><?= e(date_id($date)) ?></h2>
        <div class="ticket-list">
            <?php foreach ($schedules as $schedule): ?>
                <article class="ticket">
                    <div class="ticket-main">
                        <strong class="ticket-time"><?= e(time_short($schedule['show_time'])) ?></strong>
                        <div>
                            <p class="ticket-title"><?= e($schedule['title']) ?></p>
                            <p class="ticket-sub"><?= e($schedule['cinema_name']) ?> · <?= e($schedule['studio_name']) ?> · <?= e($schedule['genre']) ?></p>
                        </div>
                    </div>
                    <div class="ticket-stub">
                        <span class="ticket-price"><?= e(rupiah($schedule['price'])) ?></span>
                        <a class="btn btn-primary btn-small" href="<?= e(url('seat-selection.php?schedule_id=' . (int) $schedule['id'])) ?>">Pilih kursi</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="empty-inline">Tidak ada jadwal yang sesuai dengan filter kamu.</p>
<?php endif; ?>
<?php page_end(); ?>
