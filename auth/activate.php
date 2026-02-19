<?php
require "../config/db.php";

$token = $_GET["token"] ?? '';

$stmt = $pdo->prepare("UPDATE users SET is_active=1, token=NULL WHERE token=?");
$stmt->execute([$token]);

if ($stmt->rowCount()) {
    echo "Konto aktywowane 🎉";
} else {
    echo "Nieprawidłowy token lub konto już aktywowane.";
}
