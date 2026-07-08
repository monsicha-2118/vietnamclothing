<?php
/**
 * products.php — หน้าแสดงข้อมูลสินค้าทั้งหมด (READ) — Mobile-first
 * Mobile  : การ์ดสินค้าเรียงต่อกัน (product-card style)
 * Desktop : ตารางข้อมูลเต็มรูปแบบ (data-table style)
 */
require_once __DIR__ . '/config.php';

$pageTitle = 'รายการสินค้า — VietFashion Karoline';
$activePage = 'products';

$search   = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = "
    SELECT
        p.id,
        p.name,
        p.category,
        p.base_price,
        p.weight_g,
        p.is_active,
        s.name AS store_name,
        s.city AS store_city,
        COALESCE(SUM(pv.stock_qty), 0) AS total_stock,
        COUNT(DISTINCT pv.id) AS variant_count,
        ROUND(AVG(r.rating), 1) AS avg_rating,
        COUNT(DISTINCT r.id) AS review_count
    FROM products p
    JOIN stores s ON s.id = p.store_id
    LEFT JOIN product_variants pv ON pv.product_id = p.id
    LEFT JOIN reviews r ON r.product_id = p.id
    WHERE 1=1
";

$params = [];
if ($search !== '') {
    $sql .= " AND p.name LIKE :search";
    $params[':search'] = '%' . $search . '%';
}
if ($category !== '') {
    $sql .= " AND p.category = :category";
    $params[':category'] = $category;
}
$sql .= " GROUP BY p.id ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category")
                  ->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/partials_header.php';
?>

<div class="mb-5 sm:mb-6">
  <h2 class="font-display text-xl sm:text-2xl text-brandDeep">สินค้าทั้งหมด</h2>
  <p class="text-xs sm:text-sm text-muted mt-1">ทั้งหมด <?= count($products) ?> รายการ</p>
</div>

<!-- ฟอร์มค้นหา / กรอง — stack แนวตั้งบนมือถือ, แนวนอนบนจอใหญ่ -->
<form method="get" class="bg-white border border-border rounded-xl2 p-4 mb-5 sm:mb-6 shadow-brandsm
                           flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end">
  <div class="flex-1 min-w-0 sm:min-w-[200px]">
    <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">ค้นหาชื่อสินค้า</label>
    <input type="text" name="q" value="<?= h($search) ?>" placeholder="เช่น เดรสลินิน"
           class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
  </div>
  <div class="sm:min-w-[160px]">
    <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">หมวดหมู่</label>
    <select name="category" class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
      <option value="">ทั้งหมด</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= h($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="flex gap-2">
    <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 sm:py-2 rounded-lg bg-brand text-white text-sm font-semibold uppercase tracking-wide hover:bg-brandDeep transition">
      ค้นหา
    </button>
    <a href="products.php" class="flex-1 sm:flex-none text-center px-5 py-2.5 sm:py-2 rounded-lg border border-rule text-ink2 text-sm font-medium hover:bg-surface transition">
      ล้าง
    </a>
  </div>
</form>

<?php if (empty($products)): ?>
  <div class="bg-white border border-border rounded-xl2 p-10 text-center text-muted shadow-brandsm">
    ไม่พบสินค้าที่ตรงกับเงื่อนไข
  </div>
<?php else: ?>

  <!-- ═══ MOBILE: การ์ดเรียงต่อกัน (ซ่อนบนจอ md ขึ้นไป) ═══ -->
  <div class="grid grid-cols-1 xs:grid-cols-2 gap-4 md:hidden">
    <?php foreach ($products as $p): ?>
      <div class="bg-white border border-rule rounded-xl2 shadow-brandsm overflow-hidden">
        <div class="h-24 bg-gradient-to-br from-brandLight to-surface2 flex items-center justify-center">
          <span class="font-display text-2xl text-brandDeep opacity-70"><?= h(mb_substr($p['name'], 0, 1)) ?></span>
        </div>
        <div class="p-3">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-sm font-semibold text-ink leading-snug"><?= h($p['name']) ?></h3>
            <?php if ($p['is_active']): ?>
              <span class="flex-shrink-0 px-2 py-0.5 rounded-full bg-green-100 text-success text-[9px] font-bold uppercase">เปิดขาย</span>
            <?php else: ?>
              <span class="flex-shrink-0 px-2 py-0.5 rounded-full bg-red-100 text-danger text-[9px] font-bold uppercase">ปิดขาย</span>
            <?php endif; ?>
          </div>
          <p class="text-[11px] text-muted mt-0.5"><?= h($p['store_name']) ?> · <?= h($p['store_city']) ?></p>

          <div class="flex items-center gap-2 mt-2">
            <span class="inline-block px-2 py-0.5 rounded-full bg-surface2 text-brand text-[10px] font-mono"><?= h($p['category']) ?></span>
            <?php if ($p['review_count'] > 0): ?>
              <span class="text-[10px] text-muted">⭐ <?= h((string)$p['avg_rating']) ?> (<?= (int)$p['review_count'] ?>)</span>
            <?php endif; ?>
          </div>

          <div class="flex items-end justify-between mt-3">
            <div>
              <div class="font-display text-lg font-bold text-brand leading-none"><?= thb((float)$p['base_price']) ?></div>
              <div class="text-[10px] text-muted mt-1"><?= (int)$p['weight_g'] ?> g · สต็อก <?= (int)$p['total_stock'] ?></div>
            </div>
            <a href="product_edit.php?id=<?= (int)$p['id'] ?>"
               class="px-3 py-1.5 rounded-lg bg-brand text-white text-[11px] font-semibold uppercase tracking-wide hover:bg-brandDeep transition">
              แก้ไข
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ═══ DESKTOP: ตารางข้อมูลเต็มรูปแบบ (ซ่อนบนมือถือ) ═══ -->
  <div class="hidden md:block bg-white border border-border rounded-xl2 shadow-brandsm overflow-hidden overflow-x-auto">
    <table class="w-full text-sm data-table">
      <thead>
        <tr class="bg-surface2 text-brandDeep text-left">
          <th class="px-4 py-3 font-semibold">สินค้า</th>
          <th class="px-4 py-3 font-semibold">ร้านค้า</th>
          <th class="px-4 py-3 font-semibold">หมวดหมู่</th>
          <th class="px-4 py-3 font-semibold text-right">ราคา</th>
          <th class="px-4 py-3 font-semibold text-right">น้ำหนัก</th>
          <th class="px-4 py-3 font-semibold text-right">สต็อกรวม</th>
          <th class="px-4 py-3 font-semibold text-center">รีวิว</th>
          <th class="px-4 py-3 font-semibold text-center">สถานะ</th>
          <th class="px-4 py-3 font-semibold text-center">จัดการ</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-rule">
        <?php foreach ($products as $p): ?>
          <tr class="hover:bg-brandTint/50 transition">
            <td class="px-4 py-3 font-medium text-ink"><?= h($p['name']) ?></td>
            <td class="px-4 py-3 text-ink2"><?= h($p['store_name']) ?> <span class="text-muted text-xs">(<?= h($p['store_city']) ?>)</span></td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-0.5 rounded-full bg-surface2 text-brand text-xs font-mono"><?= h($p['category']) ?></span>
            </td>
            <td class="px-4 py-3 text-right font-mono text-brand font-semibold"><?= thb((float)$p['base_price']) ?></td>
            <td class="px-4 py-3 text-right text-ink2"><?= (int)$p['weight_g'] ?> g</td>
            <td class="px-4 py-3 text-right">
              <?= (int)$p['total_stock'] ?>
              <span class="text-muted text-xs">(<?= (int)$p['variant_count'] ?> variant)</span>
            </td>
            <td class="px-4 py-3 text-center text-ink2">
              <?php if ($p['review_count'] > 0): ?>
                ⭐ <?= h((string)$p['avg_rating']) ?> <span class="text-muted text-xs">(<?= (int)$p['review_count'] ?>)</span>
              <?php else: ?>
                <span class="text-muted text-xs">ยังไม่มีรีวิว</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-center">
              <?php if ($p['is_active']): ?>
                <span class="px-2 py-0.5 rounded-full bg-green-100 text-success text-xs font-medium">เปิดขาย</span>
              <?php else: ?>
                <span class="px-2 py-0.5 rounded-full bg-red-100 text-danger text-xs font-medium">ปิดขาย</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-center">
              <a href="product_edit.php?id=<?= (int)$p['id'] ?>"
                 class="px-3 py-1.5 rounded-lg bg-brand text-white text-xs font-medium hover:bg-brandDeep transition">
                แก้ไข
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php require_once __DIR__ . '/partials_footer.php'; ?>
