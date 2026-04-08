<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . "/../config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = $_POST["login"];
    $email = $_POST["email"];
    $pass1 = $_POST["password"];
    $pass2 = $_POST["password2"];

    if ($pass1 !== $pass2) {
        $message = "Hasła nie są takie same!";
    } else {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
            );
            $stmt->execute([$login, $email, $hash]);

            $message = "Konto utworzone! Możesz się zalogować.";
        } catch (PDOException $e) {
            $message = "Login lub email już istnieje!";
        }
    }
}
?>
