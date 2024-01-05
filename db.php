<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$port=3306;
$dbname = "signature";
// 创建连接
$conn = new mysqli($servername, $username, $password, 
$dbname,$port);
// 检测连接
if ($conn->connect_error) {
die("连接失败: " . $conn->connect_error);
 }
?>