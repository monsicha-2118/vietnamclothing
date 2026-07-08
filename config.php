<?php
/**
 * config.php
 * ไฟล์ config กลางสำหรับเชื่อมต่อฐานข้อมูล (ใช้ร่วมกันทุกหน้า)
 * VietFashion Karoline — Vietnam-to-Thailand Cross-Border Clothing Platform
 */

declare(strict_types=1);

// ----------------------------------------------------------------
// การตั้งค่าฐานข้อมูล
// ----------------------------------------------------------------
$DB_HOST = '127.0.0.1';
$DB_NAME = 'vietnam_clothing_db';
$DB_USER = 'root';
$DB_PASS = 'root';
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // ไม่แสดงรายละเอียด error connection string ออกสู่ผู้ใช้งานจริง
    http_response_code(500);
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . htmlspecialchars($e->getMessage()));
}

// ตั้งค่า session สำหรับ flash message (ใช้ในหน้าแก้ไข)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * ฟังก์ชันช่วย escape ข้อมูลก่อนแสดงผล (ป้องกัน XSS)
 */
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * ฟังก์ชันจัดรูปแบบราคาเป็น THB
 */
function thb(float $amount): string
{
    return number_format($amount, 2) . ' ฿';
}
