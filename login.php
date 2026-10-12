<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$email = '';

if (is_post()) {
    verify_csrf();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Email dan password harus diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            redirect(in_array($user['role'], ['admin', 'staff'], true) ? 'admin/dashboard.php' : 'index.php');
        }

        $error = 'Email atau password salah.';
    }
}

page_start('Masuk · Bioskopie', ['bare' => true, 'body' => 'page-auth']);
?>
<div class="auth-shell">
    <aside class="auth-aside">
        <?= brand_mark(url('index.php')) ?>
        <h1>Selamat datang <em>kembali</em>.</h1>
        <p>Kursi favoritmu menunggu. Masuk untuk melanjutkan pemesanan.</p>
        <div class="stub stub-a" aria-hidden="true"><span>ADMIT ONE</span><strong>A · 07</strong><small>STUDIO 1 · 19:00</small></div>
    </aside>
    <section class="form-box">
        <h2>Masuk</h2>
        <?= alert('error', $error) ?>
        <form method="POST" novalidate>
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-large">Masuk</button>
        </form>
        <p class="form-foot">Belum punya akun? <a href="<?= e(url('register.php')) ?>">Daftar di sini</a></p>
    </section>
</div>
<?php page_end([], true); ?>
