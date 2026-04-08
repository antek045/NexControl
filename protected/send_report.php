<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION["user"])) {
    die("Brak dostępu");
}

$message = $_POST["message"];
$userId = $_SESSION["user"];

$stmt = $pdo->prepare(
    "INSERT INTO reports (user_id, message) VALUES (?, ?)"
);
$stmt->execute([$userId, $message]);

echo "Zgłoszenie wysłane ✔";
