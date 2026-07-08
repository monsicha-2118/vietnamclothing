</main>

<footer class="hidden md:block max-w-6xl mx-auto px-6 py-8 text-center text-[11px] text-muted">
  VietFashion Karoline &middot; Demo Admin Panel &middot; PHP + PDO + Tailwind CSS
</footer>

<!-- ═══ BOTTOM TAB BAR — เฉพาะมือถือ/แท็บเล็ต (ซ่อนเมื่อจอ md ขึ้นไป) ═══ -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-20 bg-white border-t border-rule shadow-brandlg flex pb-[env(safe-area-inset-bottom)]">
  <a href="products.php" class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[9px] font-semibold uppercase tracking-wider <?= $activePage === 'products' ? 'text-brand' : 'text-muted' ?>">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
    </svg>
    สินค้า
  </a>
  <a href="orders.php" class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[9px] font-semibold uppercase tracking-wider <?= $activePage === 'orders' ? 'text-brand' : 'text-muted' ?>">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    คำสั่งซื้อ
  </a>
  <a href="product_edit.php" class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[9px] font-semibold uppercase tracking-wider <?= $activePage === 'edit' ? 'text-brand' : 'text-muted' ?>">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
    แก้ไขสินค้า
  </a>
</nav>

</body>
</html>
