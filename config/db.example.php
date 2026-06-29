<?php
$host = 'localhost';
$dbname = 'tinviet';
$user = 'root';
$pass = ''; // XAMPP mặc định để trống
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}