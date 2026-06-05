<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
$adminName = $_SESSION['admin_name'] ?? 'Admin';
