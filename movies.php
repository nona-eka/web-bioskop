<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

require_login();

$genre = trim((string) ($_GET['genre'] ?? ''));
$search = trim((string) ($_GET['q'] ?? ''));

$genres = $pdo->query('SELECT DISTINCT genre FROM movies ORDER BY genre')->fetchAll(PDO::FETCH_COLUMN);

$conditions = [];
$params = [];

if ($genre !== '' && in_array($genre, $genres, true)) {
    $conditions[] = 'm.genre = :genre';
    $params[':genre'] = $genre;
} else {
    $genre = '';
}

if ($search !== '') {
    $conditions[] = 'm.title LIKE :search';
    $params[':search'] = '%' . addcslashes($search, '%_\\') . '%';
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$stmt = $pdo->prepare("
    SELECT m.*, COUNT(s.id) AS showtimes
    FROM movies m
    LEFT JOIN schedules s ON s.movie_id = m.id AND s.show_date >= CURDATE()
    $where
    GROUP BY m.id
    ORDER BY (COUNT(s.id) > 0) DESC, m.release_date DESC
");
$stmt->execute($params);
$movies = $stmt->fetchAll();

function genre_filter_url(string $genre, string $search): string
{
    return url('movies.php?' . http_build_query(array_filter(['genre' => $genre, 'q' => $search], static fn (string $v): bool => $v !== '')));
}

page_start('Daftar Film · Bioskopie');
?>
<header class="page-head">
    <p class="eyebrow">Katalog</p>
    <h1>Semua film</h1>
</header>

<?= flash_render() ?>

<form class="toolbar" method="GET" role="search">
    <?php if ($genre !== ''): ?><input type="hidden" name="genre" value="<?= e($genre) ?>"><?php endif; ?>
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Cari judul film…" aria-label="Cari judul film">
    <button class="btn btn-primary" type="submit">Cari</button>
</form>

<nav class="chips" aria-label="Filter genre">
    <a class="chip<?= $genre === '' ? ' is-active' : '' ?>" href="<?= e(genre_filter_url('', $search)) ?>">Semua</a>
    <?php foreach ($genres as $item): ?>
        <a class="chip<?= $genre === $item ? ' is-active' : '' ?>" href="<?= e(genre_filter_url($item, $search)) ?>"><?= e($item) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($movies): ?>
    <div class="movie-grid">
        <?php foreach ($movies as $movie): ?>
            <?= movie_card($movie) ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="empty-inline">Tidak ada film yang cocok dengan pencarianmu.</p>
<?php endif; ?>
<?php page_end(); ?>
