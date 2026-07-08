<?php
/**
 * orders.php — หน้าแสดงข้อมูลคำสั่งซื้อทั้งหมด (READ) — Mobile-first
 * Mobile  : การ์ดคำสั่งซื้อเรียงต่อกัน พร้อม status-badge
 * Desktop : ตารางข้อมูลเต็มรูปแบบ
 */
require_once __DIR__ . '/config.php';

$pageTitle = 'คำสั่งซื้อ — VietFashion Karoline';
$activePage = 'orders';

$statusFilter = trim($_GET['status'] ?? '');

$sql = "
    SELECT
        o.id,
        o.status,
        o.shipping_name,
        o.shipping_phone,
        o.total_product_thb,
        o.shipping_fee_thb,
        o.total_thb,
        o.payment_method,
        o.paid_at,
        o.created_at,
        u.name  AS customer_name,
        u.email AS customer_email,
        COUNT(oi.id) AS item_count,
        SUM(oi.quantity) AS total_qty
    FROM orders o
    JOIN users u ON u.id = o.user_id
    LEFT JOIN order_items oi ON oi.order_id = o.id
    WHERE 1=1
";

$params = [];
if ($statusFilter !== '') {
    $sql .= " AND o.status = :status";
    $params[':status'] = $statusFilter;
}
$sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statuses = ['รอชำระเงิน','ชำระแล้ว','กำลังจัดเตรียม','จัดส่งจากเวียดนาม','ถึงไทย','จัดส่งในไทย','สำเร็จ','ยกเลิก'];

// map สีของแต่ละสถานะ ให้ตรงกับ .status-badge ใน Design System
function statusBadgeClass(string $status): string
{
    return match ($status) {
        'สำเร็จ'              => 'bg-green-50 text-[#065F46]',
        'ถึงไทย'              => 'bg-green-50 text-[#065F46]',
        'ยกเลิก'              => 'bg-red-50 text-[#991B1B]',
        'รอชำระเงิน'          => 'bg-amber-50 text-[#92400E]',
        'ชำระแล้ว'            => 'bg-blue-50 text-[#1E40AF]',
        'จัดส่งจากเวียดนาม',
        'จัดส่งในไทย'         => 'bg-violet-50 text-[#5B21B6]',
        default                => 'bg-surface2 text-brand',
    };
}

require_once __DIR__ . '/partials_header.php';
?>

<div class="mb-5 sm:mb-6">
  <h2 class="font-display text-xl sm:text-2xl text-brandDeep">คำสั่งซื้อทั้งหมด</h2>
  <p class="text-xs sm:text-sm text-muted mt-1">ทั้งหมด <?= count($orders) ?> รายการ</p>
</div>

<!-- ฟอร์มกรองสถานะ -->
<form method="get" class="bg-white border border-border rounded-xl2 p-4 mb-5 sm:mb-6 shadow-brandsm
                           flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end">
  <div class="flex-1 min-w-0 sm:min-w-[220px]">
    <label class="block text-[11px] font-mono text-muted mb-1 uppercase tracking-wide">กรองตามสถานะ</label>
    <select name="status" class="w-full px-3 py-2.5 sm:py-2 rounded-lg border border-rule bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-brandLight">
      <option value="">ทั้งหมด</option>
      <?php foreach ($statuses as $st): ?>
        <option value="<?= h($st) ?>" <?= $statusFilter === $st ? 'selected' : '' ?>><?= h($st) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="flex gap-2">
    <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 sm:py-2 rounded-lg bg-brand text-white text-sm font-semibold uppercase tracking-wide hover:bg-brandDeep transition">
      กรอง
    </button>
    <a href="orders.php" class="flex-1 sm:flex-none text-center px-5 py-2.5 sm:py-2 rounded-lg border border-rule text-ink2 text-sm font-medium hover:bg-surface transition">
      ล้าง
    </a>
  </div>
</form>

<?php if (empty($orders)): ?>
  <div class="bg-white border border-border rounded-xl2 p-10 text-center text-muted shadow-brandsm">
    ไม่พบคำสั่งซื้อที่ตรงกับเงื่อนไข
  </div>
<?php else: ?>

  <!-- ═══ MOBILE: การ์ดคำสั่งซื้อเรียงต่อกัน (ซ่อนบนจอ md ขึ้นไป) ═══ -->
  <div class="flex flex-col gap-3 md:hidden">
    <?php foreach ($orders as $o): ?>
      <div class="bg-white border border-rule rounded-xl2 shadow-brandsm p-4">
        <div class="flex items-start justify-between gap-2">
          <div>
            <div class="font-mono text-xs text-muted">#VF-<?= str_pad((string)$o['id'], 4, '0', STR_PAD_LEFT) ?></div>
            <div class="font-semibold text-sm text-ink mt-0.5"><?= h($o['shipping_name']) ?></div>
            <div class="text-[11px] text-muted"><?= h($o['customer_email']) ?></div>
          </div>
          <span class="flex-shrink-0 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wide <?= statusBadgeClass($o['status']) ?>">
            <?= h($o['status']) ?>
          </span>
        </div>

        <div class="flex items-center justify-between mt-3 pt-3 border-t border-dashed border-rule text-xs text-ink2">
          <span><?= (int)$o['item_count'] ?> รายการ / <?= (int)($o['total_qty'] ?? 0) ?> ชิ้น</span>
          <span class="text-[11px] text-muted"><?= h($o['payment_method'] ?? 'ยังไม่ชำระ') ?></span>
        </div>

        <div class="flex items-end justify-between mt-2">
          <div class="text-[11px] text-muted">
            <?= h(date('d M Y H:i', strtotime($o['created_at']))) ?>
          </div>
          <div class="text-right">
            <div class="text-[10px] text-muted">ยอดรวม</div>
            <div class="font-display text-lg font-bold text-brand leading-none"><?= thb((float)$o['total_thb']) ?></div>
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
          <th class="px-4 py-3 font-semibold">เลขที่</th>
          <th class="px-4 py-3 font-semibold">ลูกค้า</th>
          <th class="px-4 py-3 font-semibold">รายการสินค้า</th>
          <th class="px-4 py-3 font-semibold text-right">ยอดสินค้า</th>
          <th class="px-4 py-3 font-semibold text-right">ค่าจัดส่ง</th>
          <th class="px-4 py-3 font-semibold text-right">ยอดรวม</th>
          <th class="px-4 py-3 font-semibold">ชำระเงิน</th>
          <th class="px-4 py-3 font-semibold text-center">สถานะ</th>
          <th class="px-4 py-3 font-semibold">วันที่สั่ง</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-rule">
        <?php foreach ($orders as $o): ?>
          <tr class="hover:bg-brandTint/50 transition">
            <td class="px-4 py-3 font-mono text-ink2">#VF-<?= str_pad((string)$o['id'], 4, '0', STR_PAD_LEFT) ?></td>
            <td class="px-4 py-3">
              <div class="font-medium text-ink"><?= h($o['shipping_name']) ?></div>
              <div class="text-xs text-muted"><?= h($o['customer_email']) ?></div>
            </td>
            <td class="px-4 py-3 text-ink2"><?= (int)$o['item_count'] ?> รายการ / <?= (int)($o['total_qty'] ?? 0) ?> ชิ้น</td>
            <td class="px-4 py-3 text-right font-mono"><?= thb((float)$o['total_product_thb']) ?></td>
            <td class="px-4 py-3 text-right font-mono"><?= thb((float)$o['shipping_fee_thb']) ?></td>
            <td class="px-4 py-3 text-right font-mono text-brand font-semibold"><?= thb((float)$o['total_thb']) ?></td>
            <td class="px-4 py-3 text-ink2 text-xs"><?= h($o['payment_method'] ?? '—') ?></td>
            <td class="px-4 py-3 text-center">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= statusBadgeClass($o['status']) ?>">
                <?= h($o['status']) ?>
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-muted"><?= h(date('d M Y H:i', strtotime($o['created_at']))) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php require_once __DIR__ . '/partials_footer.php'; ?>
