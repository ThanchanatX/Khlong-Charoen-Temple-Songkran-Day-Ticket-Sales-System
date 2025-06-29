<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ticket"; // ชื่อฐานข้อมูลที่ใช้

$conn = new mysqli($host, $user, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
