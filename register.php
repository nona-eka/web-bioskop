<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = false;
$name = '';
$email = '';

if (is_post()) {
    verify_csrf();

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $error = 'Semua field harus diisi.';
    } elseif (mb_strlen($name) > 100 || mb_strlen($email) > 100) {
        $error = 'Nama dan email maksimal 100 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, \'client\')');
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $success = true;
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1062) {
                $error = 'Email sudah terdaftar.';
            } else {
                error_log('Register failed: ' . $exception->getMessage());
                $error = 'Pendaftaran gagal diproses. Silakan coba lagi.';
            }
        }
    }
}

page_start('Daftar · Bioskopie', ['bare' => true, 'body' => 'page-auth']);
?>
<div class="auth-shell">
    <aside class="auth-aside">
        <?= brand_mark(url('index.php')) ?>
        <h1>Satu akun, <em>semua film</em>.</h1>
        <p>Daftar gratis dan pesan tiket favoritmu dalam hitungan menit.</p>
        <div class="stub stub-b" aria-hidden="true"><span>ADMIT ONE</span><strong>F · 05</strong><small>PREMIERE · 20:30</small></div>
    </aside>
    <section class="form-box">
        <h2>Buat akun</h2>
        <?= alert('error', $error) ?>
        <?php if ($success): ?>
            <?= alert('success', 'Pendaftaran berhasil! Silakan masuk dengan akun barumu.') ?>
            <a class="btn btn-primary btn-block btn-large" href="<?= e(url('login.php')) ?>">Masuk sekarang</a>
        <?php else: ?>
            <form method="POST" novalidate>
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="name">Nama lengkap</label>
                    <input type="text" id="name" name="name" value="<?= e($name) ?>" autocomplete="name" maxlength="100" required autofocus>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" maxlength="100" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Ulangi password</label>
                        <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-large">Daftar</button>
            </form>
        <?php endif; ?>
        <p class="form-foot">Sudah punya akun? <a href="<?= e(url('login.php')) ?>">Masuk di sini</a></p>
    </section>
</div>
<?php page_end([], true); ?>
