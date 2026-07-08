# VietFashion Karoline — Admin Panel (PHP + PDO + Tailwind)

โปรเจกต์ตัวอย่าง 3 หน้า อ้างอิง `config.php` เดียวกัน ตามที่ขอ:

| หน้า | ไฟล์ | หน้าที่ |
|---|---|---|
| แสดงข้อมูล 1 | `products.php` | รายการสินค้าทั้งหมด (join stores, รวมสต็อกจาก variants, ค้นหา/กรองหมวดหมู่) |
| แสดงข้อมูล 2 | `orders.php` | รายการคำสั่งซื้อทั้งหมด (join users, นับจำนวนสินค้า, กรองตามสถานะ) |
| แก้ไขข้อมูล | `product_edit.php` | เลือกสินค้าจาก dropdown แล้วแก้ไข ชื่อ/หมวดหมู่/ราคา/น้ำหนัก/สถานะ/รายละเอียด |

## โครงสร้างไฟล์ (ทุกไฟล์อยู่โฟลเดอร์เดียวกัน)
```
vietfashion-app/
├── config.php            ← เชื่อมต่อ DB (PDO) ใช้ร่วมกันทุกหน้า
├── partials_header.php   ← ส่วนหัว/เมนู ใช้ร่วมกัน (Tailwind + สีตาม Design System)
├── partials_footer.php   ← ส่วนท้าย ใช้ร่วมกัน
├── index.php             ← redirect ไป products.php
├── products.php          ← แสดงข้อมูล (หน้าที่ 1)
├── orders.php            ← แสดงข้อมูล (หน้าที่ 2)
└── product_edit.php      ← แก้ไขข้อมูล (หน้าที่ 3)
```

## วิธีติดตั้งและรัน

1. ติดตั้ง MySQL/MariaDB แล้วรันไฟล์ `vietnam_clothing_full__2_.sql` ที่แนบมา เพื่อสร้างฐานข้อมูล `vietnam_clothing_db` พร้อมข้อมูลตัวอย่าง
   ```
   mysql -u root -p < vietnam_clothing_full__2_.sql
   ```
2. ตรวจสอบว่า MySQL ใช้ user `root` / password `root` ตรงกับที่ตั้งไว้ใน `config.php`
   (ถ้าฐานข้อมูลของคุณใช้ user/password อื่น แก้ที่ตัวแปร `$DB_USER`, `$DB_PASS` ใน `config.php` จุดเดียว ทุกหน้าจะใช้ค่าที่แก้ตามกันทันที)
3. วางไฟล์ทั้งหมดในโฟลเดอร์เดียวกันภายใต้ webroot ของ PHP เช่น (XAMPP) `htdocs/vietfashion-app/`
4. เปิดเบราว์เซอร์ไปที่ `http://localhost/vietfashion-app/`

ต้องการ PHP 8.0+ ที่เปิดใช้งาน PDO MySQL extension (`pdo_mysql`)

## Responsive / Mobile-first
- ทุกหน้าออกแบบแบบ **mobile-first** ตาม Design System ที่แนบมา (สี Mauve Rose, ฟอนต์ Playfair Display / Inter / JetBrains Mono)
- จอมือถือ (< md / 768px): เมนูล่างแบบ **bottom tab bar** fixed, ตารางข้อมูลถูกแปลงเป็น **การ์ดเรียงต่อกัน** (product-card / order-card) อ่านง่ายด้วยนิ้วโป้ง, ปุ่มฟอร์มกว้างเต็มจอ (full-width), ฟอร์มค้นหา/กรองเรียงต่อกันแนวตั้ง
- จอ md ขึ้นไป: เมนูบนแนวนอนที่ app-bar, ตารางข้อมูลเต็มรูปแบบ (data-table), ฟอร์มเรียงแนวนอน 2 คอลัมน์
- ใช้ Tailwind breakpoint `md:` (768px) เป็นจุดสลับ mobile ↔ desktop ทุกหน้า

## หมายเหตุด้านความปลอดภัย (ตามข้อกำหนด SRS ข้อ 9)
- ทุก query ใช้ PDO **prepared statements** ป้องกัน SQL Injection
- ทุกจุดที่แสดงข้อมูลผู้ใช้ ใช้ฟังก์ชัน `h()` (htmlspecialchars) ป้องกัน XSS
- ฟอร์มแก้ไขมีการ validate ฝั่ง server (ราคา/น้ำหนักต้อง > 0, หมวดหมู่ต้องอยู่ใน ENUM ที่กำหนด) ตรงกับ UC-03 ใน SRS
