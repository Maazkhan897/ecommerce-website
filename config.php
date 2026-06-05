<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function getCartCount(): int {
    global $conn;
    if (!isLoggedIn()) return 0;
    $uid = (int)$_SESSION['user_id'];
    $r = mysqli_query($conn, "SELECT COALESCE(SUM(quantity),0) AS t FROM cart WHERE user_id=$uid");
    $row = mysqli_fetch_assoc($r);
    return (int)($row['t'] ?? 0);
}

function sanitize(string $input): string {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}

function redirect(string $url): void {
    header("Location: $url");
    exit();
}

function renderStars(float $rating): string {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $rating ? '<span class="star filled">★</span>' : '<span class="star">☆</span>';
    }
    return $out;
}

function formatPrice(float $price): string {
    return '$' . number_format($price, 2);
}
?>
