<?php
/**
 * product_edit.php — หน้าแก้ไขข้อมูลสินค้า (UPDATE)
 * ตรงกับ UC-03: การจัดการสินค้าโดย Admin (SRS)
 */
require_once __DIR__ . '/config.php';

$pageTitle = 'แก้ไขสินค้า — VietFashion Karoline';
$activePage = 'edit';

$categories = ['เดรส','เสื้อ','กางเกง','กระโปรง','ชุดเซ็ต','outerwear','ชุดชั้นใน','ชุดบีช'];

$errors = [];
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

// ----------------------------------------------------------------
// รับค่า product id ที่จะแก้ไข (จาก query string หรือ dropdown)
// ----------------------------------------------------------------
$productId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

// ----------------------------------------------------------------
// บันทึกการแก้ไข (POST)
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $basePrice   = $_POST['base_price'] ?? '';
    $weightG     = $_POST['weight_g'] ?? '';
    $isActive    = isset($_POST['is_active']) ? 1 : 0;

    if ($productId <= 0) {
        $errors[] = 'ไม่พบรหัสสินค้าที่ต้องการแก้ไข';
    }
    if ($name === '') {
        $errors[] = 'กรุณากรอกชื่อสินค้า';
    }
    if (!in_array($category, $categories, true)) {
        $errors[] = 'กรุณาเลือกหมวดหมู่ที่ถูกต้อง';
    }
    if (!is_numeric($basePrice) || (float)$basePrice <= 0) {
        $errors[] = 'ราคาต้องเป็นตัวเลขและมากกว่า 0';
    }
    if (!is_numeric($weightG) || (int)$weightG <= 0) {
        $errors[] = 'น้ำหนักต้องเป็นตัวเลขและมากกว่า 0';
    }

    if (empty($errors)) {
        $update = $pdo->prepare("
            UPDATE products
            SET name = :name,
                category = :category,
                description = :description,
                base_price = :base_price,
                weight_g = :weight_g,
                is_active = :is_active
            WHERE id = :id
        ");
        $update->execute([
            ':name'        => $name,
            ':category'    => $category,
            ':description' => $description,
            ':base_price'  => (float)$basePrice,
            ':weight_g'    => (int)$weightG,
            ':is_active'   => $isActive,
            ':id'          => $productId,
        ]);

        $_SESSION['flash_success'] = 'บันทึกข้อมูลสินค้า "' . $name . '" เรียบร้อยแล้ว';
        header('Location: product_edit.php?id=' . $productId);
        exit;
    }
}

// ----------------------------------------------------------------
// ดึงรายชื่อสินค้าทั้งหมดสำหรับ dropdown เลือกสินค้า
// ----------------------------------------------------------------
$productList = $pdo->query("SELECT id, name FROM products ORDER BY name")->fetchAll();

// ----------------------------------------------------------------
// ดึงข้อมูลสินค้าปัจจุบันที่จะแก้ไข (สำหรับ pre-fill ฟอร์ม)
// ----------------------------------------------------------------
$product = null;
if ($productId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();
    if (!$product) {
        $errors[] = 'ไม่พบสินค้ารหัส #' . $productId;
        $productId = 0;
    }
}

require_once __DIR__ . '/partials_header.php';
?>

<div class="mb-5 sm:mb-6">
  <h2 class="font-display text-xl sm:text-2xl text-brandDeep">แก้ไขข้อมูลสินค้า</h2>
  <p class="text-xs sm:text-sm text-muted mt-1">เลือกสินค้าที่ต้องการแก้ไขจากรายการด้านล่าง</p>
</div>

<?php if ($flashSuccess): ?>
  <div class="mb-5 sm:mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-success text-sm flex items-center gap-2">
    <span>✓</span> <?= h($flashSuccess) ?>
  </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="mb-5 sm:mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-danger text-sm">
    <ul class="list-disc list-inside space-y-1">
      <?php foreach ($errors as $err): ?>
        <li><?= h($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<!-- เลือกสินค้าที่จะแก้ไข -->
<form method="get" class="bg-white border border-border rounded-xl2 p-4 mb-5 sm:mb-6 shadow-brandsm
                           flex flex-col sm:flex-row gap-3 sm:items-end">
  <div class="flex-1 min-w-0 sm:min-w-[240px]">
    <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">เลือกสินค้า</label>
    <select name="id" onchange="this.form.submit()"
            class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
      <option value="">-- เลือกสินค้า --</option>
      <?php foreach ($productList as $item): ?>
        <option value="<?= (int)$item['id'] ?>" <?= $productId === (int)$item['id'] ? 'selected' : '' ?>>
          <?= h($item['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <noscript><button type="submit" class="w-full sm:w-auto px-5 py-2.5 sm:py-2 rounded-lg bg-brand text-white text-sm font-semibold uppercase tracking-wide">เลือก</button></noscript>
</form>

<?php if ($product): ?>
<!-- ฟอร์มแก้ไขสินค้า — 1 คอลัมน์บนมือถือ, 2 คอลัมน์บนจอ md ขึ้นไป -->
<form method="post" class="bg-white border border-border rounded-xl2 p-4 sm:p-6 shadow-brandsm max-w-2xl">
  <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
    <div class="md:col-span-2">
      <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">ชื่อสินค้า</label>
      <input type="text" name="name" required value="<?= h($_POST['name'] ?? $product['name']) ?>"
             class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
    </div>

    <div>
      <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">หมวดหมู่</label>
      <select name="category" required
              class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
        <?php $curCat = $_POST['category'] ?? $product['category']; ?>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= h($cat) ?>" <?= $curCat === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">ราคา (THB)</label>
      <input type="number" step="0.01" min="0.01" name="base_price" required inputmode="decimal"
             value="<?= h((string)($_POST['base_price'] ?? $product['base_price'])) ?>"
             class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
    </div>

    <div>
      <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">น้ำหนัก (กรัม)</label>
      <input type="number" min="1" name="weight_g" required inputmode="numeric"
             value="<?= h((string)($_POST['weight_g'] ?? $product['weight_g'])) ?>"
             class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
    </div>

    <div class="flex items-center gap-2 sm:mt-6">
      <input type="checkbox" id="is_active" name="is_active" value="1"
             <?= (isset($_POST['is_active']) || (!isset($_POST['name']) && $product['is_active'])) ? 'checked' : '' ?>
             class="w-4 h-4 rounded border-border text-brand focus:ring-brandLight">
      <label for="is_active" class="text-sm text-ink2">เปิดขายสินค้านี้</label>
    </div>

    <div class="md:col-span-2">
      <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">รายละเอียดสินค้า</label>
      <textarea name="description" rows="3"
                class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight"><?= h($_POST['description'] ?? $product['description']) ?></textarea>
    </div>
  </div>

  <!-- ปุ่มกว้างเต็มจอบนมือถือ, auto บนจอใหญ่ -->
  <div class="flex flex-col sm:flex-row gap-3 mt-6">
    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-lg bg-brand text-white text-sm font-semibold uppercase tracking-wide hover:bg-brandDeep transition">
      บันทึกการแก้ไข
    </button>
    <a href="products.php" class="w-full sm:w-auto text-center px-6 py-2.5 rounded-lg border border-rule text-ink2 text-sm font-medium hover:bg-surface transition">
      ยกเลิก / กลับไปดูรายการสินค้า
    </a>
  </div>
</form>
<?php elseif ($productId === 0 && empty($errors)): ?>
  <div class="bg-white border border-border rounded-xl2 p-8 text-center text-muted shadow-brandsm">
    กรุณาเลือกสินค้าจาก dropdown ด้านบนเพื่อเริ่มแก้ไข
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/partials_footer.php'; ?>
