<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

require_staff();

$error = '';

if (is_post()) {
    verify_csrf();

    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'add' || $action === 'update') {
        $title = trim((string) ($_POST['title'] ?? ''));
        $genre = trim((string) ($_POST['genre'] ?? ''));
        $duration = positive_int($_POST['duration'] ?? null);
        $synopsis = trim((string) ($_POST['synopsis'] ?? ''));
        $poster = trim((string) ($_POST['poster'] ?? ''));
        $releaseDate = (string) ($_POST['release_date'] ?? '');
        $validDate = DateTime::createFromFormat('Y-m-d', $releaseDate);

        $movieId = positive_int($_POST['movie_id'] ?? null);

        if ($title === '' || $genre === '' || $duration === null || $synopsis === '' || !$validDate || $validDate->format('Y-m-d') !== $releaseDate) {
            $error = 'Judul, genre, durasi, sinopsis, dan tanggal rilis wajib diisi dengan benar.';
        } elseif (mb_strlen($title) > 150 || mb_strlen($genre) > 100) {
            $error = 'Judul maksimal 150 karakter dan genre maksimal 100 karakter.';
        } elseif ($action === 'update' && $movieId !== null) {
            $stmt = $pdo->prepare('UPDATE movies SET title = :title, genre = :genre, duration = :duration, synopsis = :synopsis, poster = :poster, release_date = :release_date WHERE id = :id');
            $stmt->execute([
                ':title' => $title,
                ':genre' => $genre,
                ':duration' => $duration,
                ':synopsis' => $synopsis,
                ':poster' => $poster,
                ':release_date' => $releaseDate,
                ':id' => $movieId,
            ]);
            flash_set('success', 'Data film dan poster berhasil diperbarui.');
            redirect('admin/manage-movies.php');
        } elseif ($action === 'add') {
            $stmt = $pdo->prepare('INSERT INTO movies (title, genre, duration, synopsis, poster, release_date) VALUES (:title, :genre, :duration, :synopsis, :poster, :release_date)');
            $stmt->execute([
                ':title' => $title,
                ':genre' => $genre,
                ':duration' => $duration,
                ':synopsis' => $synopsis,
                ':poster' => $poster,
                ':release_date' => $releaseDate,
            ]);
            flash_set('success', 'Film berhasil ditambahkan.');
            redirect('admin/manage-movies.php');
        }
    } elseif ($action === 'delete') {
        $movieId = positive_int($_POST['movie_id'] ?? null);

        if ($movieId !== null) {
            $stmt = $pdo->prepare('DELETE FROM movies WHERE id = :id');
            $stmt->execute([':id' => $movieId]);
            flash_set('success', 'Film beserta jadwalnya berhasil dihapus.');
        }

        redirect('admin/manage-movies.php');
    }
}

$movies = $pdo->query('SELECT * FROM movies ORDER BY title')->fetchAll();
$genres = $pdo->query('SELECT DISTINCT genre FROM movies ORDER BY genre')->fetchAll(PDO::FETCH_COLUMN);

page_start('Kelola Film · Bioskopie', ['admin' => true]);
?>
<header class="page-head">
    <p class="eyebrow">Admin</p>
    <h1>Kelola film</h1>
</header>

<?= flash_render() ?>
<?= alert('error', $error) ?>

<details class="panel disclosure">
    <summary>Tambah film baru</summary>
    <form method="POST" class="admin-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <div class="form-group"><label for="title">Judul</label><input type="text" id="title" name="title" maxlength="150" required></div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" list="genre-options" maxlength="100" required>
                <datalist id="genre-options"><?php foreach ($genres as $genre): ?><option value="<?= e($genre) ?>"><?php endforeach; ?></datalist>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label for="duration">Durasi (menit)</label><input type="number" id="duration" name="duration" min="1" required></div>
            <div class="form-group"><label for="release_date">Tanggal rilis</label><input type="date" id="release_date" name="release_date" required></div>
        </div>
        <div class="form-group"><label for="poster">URL poster dari internet</label><input type="text" id="poster" name="poster" placeholder="https://contoh.com/poster.jpg"></div>
        <div class="form-group"><label for="synopsis">Sinopsis</label><textarea id="synopsis" name="synopsis" rows="3" required></textarea></div>
        <button type="submit" class="btn btn-primary">Simpan film</button>
    </form>
</details>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Poster</th><th>Judul</th><th>Genre</th><th>Durasi</th><th>Rilis</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($movies as $movie): ?>
                <tr>
                    <td style="width:90px"><?= poster($movie, 'poster-thumb') ?></td>
                    <td><?= e($movie['title']) ?></td>
                    <td><?= e($movie['genre']) ?></td>
                    <td><?= (int) $movie['duration'] ?> mnt</td>
                    <td><?= e(date_id($movie['release_date'], false)) ?></td>
                    <td class="cell-actions">
                        <details>
                            <summary class="btn btn-ghost btn-small">Ubah</summary>
                            <form method="POST" class="admin-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
                                <div class="form-group"><label>Judul</label><input type="text" name="title" value="<?= e($movie['title']) ?>" maxlength="150" required></div>
                                <div class="form-group"><label>Genre</label><input type="text" name="genre" value="<?= e($movie['genre']) ?>" maxlength="100" required></div>
                                <div class="form-row">
                                    <div class="form-group"><label>Durasi</label><input type="number" name="duration" value="<?= (int) $movie['duration'] ?>" min="1" required></div>
                                    <div class="form-group"><label>Tanggal rilis</label><input type="date" name="release_date" value="<?= e($movie['release_date']) ?>" required></div>
                                </div>
                                <div class="form-group"><label>URL poster dari internet</label><input type="text" name="poster" value="<?= e($movie['poster']) ?>"></div>
                                <div class="form-group"><label>Sinopsis</label><textarea name="synopsis" rows="3" required><?= e($movie['synopsis']) ?></textarea></div>
                                <button type="submit" class="btn btn-primary btn-small">Simpan perubahan</button>
                            </form>
                        </details>
                        <form method="POST" data-confirm="Hapus film ini beserta seluruh jadwal dan pesanannya?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="movie_id" value="<?= (int) $movie['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-small">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php page_end(['script.js']); ?>
