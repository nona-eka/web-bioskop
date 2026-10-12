<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$featured = $pdo->query('
    SELECT m.*, COUNT(s.id) AS showtimes
    FROM movies m
    JOIN schedules s ON s.movie_id = m.id AND s.show_date >= CURDATE()
    GROUP BY m.id
    ORDER BY m.release_date DESC
    LIMIT 6
')->fetchAll();

$totals = $pdo->query('
    SELECT
        (SELECT COUNT(*) FROM movies) AS movies,
        (SELECT COUNT(*) FROM cinemas) AS cinemas,
        (SELECT COUNT(*) FROM schedules WHERE show_date >= CURDATE()) AS showtimes
')->fetch();

$genres = $pdo->query('SELECT genre, COUNT(*) AS total FROM movies GROUP BY genre ORDER BY genre')->fetchAll();

page_start('Bioskopie · Pesan Tiket Bioskop Online');
?>
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">Tiket bioskop, tanpa antre</p>
        <h1>Satu layar,<br><em>seribu malam</em> yang layak diingat.</h1>
        <p class="lead">Pilih film, pilih kursi favoritmu, tambahkan popcorn, lalu tunjukkan e-ticket di pintu studio. Semudah itu.</p>
        <div class="hero-actions">
            <?php if (is_logged_in()): ?>
                <a class="btn btn-primary btn-large" href="<?= e(url('movies.php')) ?>">Lihat film tayang</a>
                <a class="btn btn-ghost btn-large" href="<?= e(url('schedule.php')) ?>">Jadwal hari ini</a>
            <?php else: ?>
                <a class="btn btn-primary btn-large" href="<?= e(url('register.php')) ?>">Mulai pesan tiket</a>
                <a class="btn btn-ghost btn-large" href="<?= e(url('login.php')) ?>">Saya sudah punya akun</a>
            <?php endif; ?>
        </div>
        <dl class="hero-stats">
            <div><dt><?= (int) $totals['movies'] ?></dt><dd>Film</dd></div>
            <div><dt><?= (int) $totals['cinemas'] ?></dt><dd>Bioskop</dd></div>
            <div><dt><?= (int) $totals['showtimes'] ?></dt><dd>Jam tayang</dd></div>
        </dl>
    </div>
    <div class="hero-visual" aria-hidden="true">
        <div class="stub stub-a"><span>ADMIT ONE</span><strong>A · 07</strong><small>STUDIO 1 · 19:00</small></div>
        <div class="stub stub-b"><span>ADMIT ONE</span><strong>F · 05</strong><small>PREMIERE · 20:30</small></div>
    </div>
    <div class="bulbs" aria-hidden="true"></div>
</section>

<section class="section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Sedang tayang</p>
            <h2>Malam ini di layar</h2>
        </div>
        <a class="link-arrow" href="<?= e(url('movies.php')) ?>">Semua film</a>
    </div>

    <?php if ($featured): ?>
        <div class="movie-grid">
            <?php foreach ($featured as $movie): ?>
                <?= movie_card($movie) ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">Belum ada film dengan jadwal tayang mendatang.</p>
    <?php endif; ?>
</section>

<section class="section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Jelajahi</p>
            <h2>Pilih suasana hatimu</h2>
        </div>
    </div>
    <div class="genre-tiles">
        <?php foreach ($genres as $genre): ?>
            <a class="genre-tile" style="--hue: <?= genre_hue($genre['genre']) ?>" href="<?= e(url('movies.php?genre=' . urlencode($genre['genre']))) ?>">
                <strong><?= e($genre['genre']) ?></strong>
                <span><?= (int) $genre['total'] ?> film</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php page_end(); ?>
