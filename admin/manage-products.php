<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';

require_staff();

$error = '';

if (is_post()) {
    verify_csrf();

    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'add' || $action === 'update') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = positive_int($_POST['price'] ?? null);
        $category = (string) ($_POST['category'] ?? '');
        $image = trim((string) ($_POST['image'] ?? ''));

        $productId = positive_int($_POST['product_id'] ?? null);

        if ($name === '' || mb_strlen($name) > 100 || $price === null || !array_key_exists($category, PRODUCT_CATEGORIES)) {
            $error = 'Nama, harga, dan kategori wajib diisi dengan benar.';
        } elseif ($action === 'update' && $productId !== null) {
            $stmt = $pdo->prepare('UPDATE products SET name = :name, price = :price, category = :category, image = :image WHERE id = :id');
            $stmt->execute([':name' => $name, ':price' => $price, ':category' => $category, ':image' => $image, ':id' => $productId]);
            flash_set('success', 'Produk dan gambar berhasil diperbarui.');
            redirect('admin/manage-products.php');
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (name, price, category, image) VALUES (:name, :price, :category, :image)');
            $stmt->execute([':name' => $name, ':price' => $price, ':category' => $category, ':image' => $image]);
            flash_set('success', 'Produk berhasil ditambahkan.');
            redirect('admin/manage-products.php');
        }
    } elseif ($action === 'delete') {
        $productId = positive_int($_POST['product_id'] ?? null);

        if ($productId !== null) {
            $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
            $stmt->execute([':id' => $productId]);
            flash_set('success', 'Produk berhasil dihapus.');
        }

        redirect('admin/manage-products.php');
    }
}

$products = $pdo->query('SELECT * FROM products ORDER BY category, name')->fetchAll();

page_start('Kelola F&B · Bioskopie', ['admin' => true]);
?>
<header class="page-head">
    <p class="eyebrow">Admin</p>
    <h1>Kelola F&amp;B</h1>
</header>

<?= flash_render() ?>
<?= alert('error', $error) ?>

<details class="panel disclosure">
    <summary>Tambah produk baru</summary>
    <form method="POST" class="admin-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <div class="form-group"><label for="name">Nama produk</label><input type="text" id="name" name="name" maxlength="100" required></div>
            <div class="form-group"><label for="price">Harga (Rp)</label><input type="number" id="price" name="price" step="500" min="500" required></div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="category">Kategori</label>
                <select id="category" name="category" required>
                    <?php foreach (PRODUCT_CATEGORIES as $value => $label): ?><option value="<?= e($value) ?>"><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label for="image">URL gambar dari internet</label><input type="text" id="image" name="image" placeholder="https://contoh.com/popcorn.jpg"></div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan produk</button>
    </form>
</details>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Nama</th><th>Harga</th><th>Kategori</th><th>Gambar</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= e($product['name']) ?></td>
                    <td><?= e(rupiah($product['price'])) ?></td>
                    <td><?= e(PRODUCT_CATEGORIES[$product['category']] ?? $product['category']) ?></td>
                    <td>
                        <?= product_visual($product) ?>
                        <small class="cell-sub"><?= e($product['image'] !== '' ? $product['image'] : '-') ?></small>
                    </td>
                    <td class="cell-actions">
                        <details>
                            <summary class="btn btn-ghost btn-small">Ubah</summary>
                            <form method="POST" class="admin-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <div class="form-group"><label>Nama</label><input type="text" name="name" value="<?= e($product['name']) ?>" maxlength="100" required></div>
                                <div class="form-row">
                                    <div class="form-group"><label>Harga</label><input type="number" name="price" value="<?= (int) $product['price'] ?>" min="500" step="500" required></div>
                                    <div class="form-group"><label>Kategori</label><select name="category"><?php foreach (PRODUCT_CATEGORIES as $value => $label): ?><option value="<?= e($value) ?>"<?= $product['category'] === $value ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
                                </div>
                                <div class="form-group"><label>URL gambar dari internet</label><input type="text" name="image" value="<?= e($product['image']) ?>"></div>
                                <button type="submit" class="btn btn-primary btn-small">Simpan perubahan</button>
                            </form>
                        </details>
                        <form method="POST" data-confirm="Hapus produk ini?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-small">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php page_end(['script.js']); ?>
