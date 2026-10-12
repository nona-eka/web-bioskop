<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

require_staff();

$error = '';

if (is_post()) {
    verify_csrf();

    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'add') {
        $movieId = positive_int($_POST['movie_id'] ?? null);
        $studioId = positive_int($_POST['studio_id'] ?? null);
        $showDate = (string) ($_POST['show_date'] ?? '');
        $showTime = (string) ($_POST['show_time'] ?? '');
        $price = positive_int($_POST['price'] ?? null);

        $validDate = DateTime::createFromFormat('Y-m-d', $showDate);
        $validTime = DateTime::createFromFormat('H:i', $showTime);

        if ($movieId === null || $studioId === null || $price === null
            || !$validDate || $validDate->format('Y-m-d') !== $showDate
            || !$validTime || $validTime->format('H:i') !== $showTime) {
            $error = 'Semua field wajib diisi dengan benar.';
        } else {
            $clash = $pdo->prepare('SELECT COUNT(*) FROM schedules WHERE studio_id = :studio_id AND show_date = :show_date AND show_time = :show_time');
            $clash->execute([':studio_id' => $studioId, ':show_date' => $showDate, ':show_time' => $showTime . ':00']);

            if ((int) $clash->fetchColumn() > 0) {
                $error = 'Studio tersebut sudah memiliki jadwal pada tanggal dan jam yang sama.';
            } else {
                try {
                    $stmt = $pdo->prepare('INSERT INTO schedules (movie_id, studio_id, show_date, show_time, price) VALUES (:movie_id, :studio_id, :show_date, :show_time, :price)');
                    $stmt->execute([':movie_id' => $movieId, ':studio_id' => $studioId, ':show_date' => $showDate, ':show_time' => $showTime . ':00', ':price' => $price]);
                    flash_set('success', 'Jadwal berhasil ditambahkan.');
                    redirect('admin/manage-schedules.php');
                } catch (PDOException $exception) {
                    error_log('Add schedule failed: ' . $exception->getMessage());
                    $error = 'Jadwal gagal disimpan. Pastikan film dan studio valid.';
                }
            }
        }
    } elseif ($action === 'delete') {
        $scheduleId = positive_int($_POST['schedule_id'] ?? null);

        if ($scheduleId !== null) {
            $stmt = $pdo->prepare('DELETE FROM schedules WHERE id = :id');
            $stmt->execute([':id' => $scheduleId]);
            flash_set('success', 'Jadwal berhasil dihapus.');
        }

        redirect('admin/manage-schedules.php');
    }
}

$schedules = $pdo->query('
    SELECT s.*, m.title, c.name AS cinema_name, st.name AS studio_name
    FROM schedules s
    JOIN movies m ON s.movie_id = m.id
    JOIN studios st ON s.studio_id = st.id
    JOIN cinemas c ON st.cinema_id = c.id
    ORDER BY s.show_date, s.show_time
')->fetchAll();

$movies = $pdo->query('SELECT id, title FROM movies ORDER BY title')->fetchAll();
$studios = $pdo->query('SELECT st.id, st.name, c.name AS cinema_name FROM studios st JOIN cinemas c ON st.cinema_id = c.id ORDER BY c.name, st.name')->fetchAll();

page_start('Kelola Jadwal · Bioskopie', ['admin' => true]);
?>
<header class="page-head">
    <p class="eyebrow">Admin</p>
    <h1>Kelola jadwal</h1>
</header>

<?= flash_render() ?>
<?= alert('error', $error) ?>

<details class="panel disclosure"<?= $error !== '' ? ' open' : '' ?>>
    <summary>Tambah jadwal baru · 1 minggu ke depan</summary>
    <form method="POST" class="admin-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <div class="form-group">
                <label for="movie_id">Film</label>
                <select id="movie_id" name="movie_id" required>
                    <option value="">Pilih film</option>
                    <?php foreach ($movies as $movie): ?><option value="<?= (int) $movie['id'] ?>"><?= e($movie['title']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="studio_id">Studio</label>
                <select id="studio_id" name="studio_id" required>
                    <option value="">Pilih studio</option>
                    <?php foreach ($studios as $studio): ?><option value="<?= (int) $studio['id'] ?>"><?= e($studio['cinema_name']) ?> · <?= e($studio['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row form-row-3">
            <div class="form-group"><label for="show_date">Tanggal</label><input type="date" id="show_date" name="show_date" min="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>" max="<?= e(date('Y-m-d', strtotime('+30 days'))) ?>" required></div>
            <div class="form-group"><label for="show_time">Jam</label><input type="time" id="show_time" name="show_time" required></div>
            <div class="form-group"><label for="price">Harga tiket (Rp)</label><input type="number" id="price" name="price" step="1000" min="1000" required></div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan jadwal</button>
    </form>
</details>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Film</th><th>Bioskop · Studio</th><th>Tanggal</th><th>Jam</th><th>Harga</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($schedules as $schedule): ?>
                <tr>
                    <td><?= e($schedule['title']) ?></td>
                    <td><?= e($schedule['cinema_name']) ?> · <?= e($schedule['studio_name']) ?></td>
                    <td><?= e(date_id($schedule['show_date'], false)) ?></td>
                    <td><?= e(time_short($schedule['show_time'])) ?></td>
                    <td><?= e(rupiah($schedule['price'])) ?></td>
                    <td class="cell-actions">
                        <form method="POST" data-confirm="Hapus jadwal ini beserta pesanan yang terkait?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="schedule_id" value="<?= (int) $schedule['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-small">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php page_end(['script.js']); ?>
