<?php
/**
 * partials_header.php
 * ส่วนหัว/เมนู ใช้ร่วมกันทุกหน้า — Mobile-first
 * Mobile   : top app-bar (โลโก้เท่านั้น) + bottom tab bar แบบ fixed
 * Desktop (md+) : top app-bar พร้อมเมนูแนวนอน, ไม่แสดง bottom tab bar
 * ต้องกำหนด $activePage ก่อน include ('products' | 'orders' | 'edit')
 */
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle ?? 'VietFashion Thailane') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand:       '#8A5A5A',
          brandDeep:   '#5C4545',
          brandLight:  '#DDA7A5',
          brandTint:   '#FFF0EF',
          brandWash:   '#FDF8F7',
          surface:     '#FAF5F5',
          surface2:    '#F9ECEB',
          ink:         '#1A1A1A',
          ink2:        '#4A3B32',
          muted:       '#9A8A8A',
          rule:        '#F2DBDB',
          border:      '#E8C5C5',
          success:     '#059669',
          warning:     '#D97706',
          danger:      '#DC2626',
          info:        '#2563EB',
          darkMenu:    '#1A1A1A',
          darkLabel:   '#F3D5D5',
        },
        fontFamily: {
          display: ['"Playfair Display"', 'Georgia', 'serif'],
          body:    ['Inter', 'system-ui', 'sans-serif'],
          mono:    ['"JetBrains Mono"', 'monospace'],
        },
        borderRadius: {
          xl2: '20px',
        },
        boxShadow: {
          brandsm: '0 1px 3px rgba(138,90,90,.08), 0 1px 2px rgba(138,90,90,.04)',
          brandmd: '0 4px 12px rgba(138,90,90,.10), 0 2px 4px rgba(138,90,90,.06)',
          brandlg: '0 8px 24px rgba(138,90,90,.12), 0 4px 8px rgba(138,90,90,.08)',
        }
      }
    }
  }
</script>
<style>
  body{font-family:'Inter',system-ui,sans-serif;}
  /* กันเนื้อหาชนกับ bottom tab bar บนมือถือ */
  @media (max-width: 767px){ #app-main{ padding-bottom: 84px; } }
</style>
</head>
<body class="bg-brandWash text-ink min-h-screen">

<!-- ═══ TOP APP BAR ═══ -->
<header class="bg-white border-b border-border shadow-brandsm sticky top-0 z-20">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between">
    <div class="flex items-center gap-2.5 sm:gap-3">
      <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-brand flex flex-col items-center justify-center flex-shrink-0">
        <span class="font-display text-white text-sm leading-none">V</span>
      </div>
      <div>
        <h1 class="font-display text-base sm:text-lg text-brandDeep leading-tight">VietFashion Thailand</h1>
        <p class="text-[10px] sm:text-[11px] text-muted -mt-0.5 hidden xs:block">Vietnam &rarr; Thailand Admin</p>
      </div>
    </div>

    <!-- เมนูแนวนอน: แสดงเฉพาะจอ md ขึ้นไป -->
    <nav class="hidden md:flex gap-1">
      <a href="products.php"
         class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $activePage === 'products' ? 'bg-surface2 text-brand' : 'text-ink2 hover:bg-surface' ?>">
        สินค้า
      </a>
      <a href="orders.php"
         class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $activePage === 'orders' ? 'bg-surface2 text-brand' : 'text-ink2 hover:bg-surface' ?>">
        คำสั่งซื้อ
      </a>
      <a href="product_edit.php"
         class="px-4 py-2 rounded-lg text-sm font-medium transition <?= $activePage === 'edit' ? 'bg-surface2 text-brand' : 'text-ink2 hover:bg-surface' ?>">
        แก้ไขสินค้า
      </a>
    </nav>
  </div>
</header>

<main id="app-main" class="max-w-6xl mx-auto px-4 sm:px-6 py-5 sm:py-8">
