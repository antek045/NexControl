<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ../public/index.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<body>

<h2>Formularz zgłoszenia</h2>

<form method="POST" action="send_report.php">
    <textarea name="message" required></textarea>
    <button>Wyślij</button>
</form>

<a href="../auth/logout.php">Wyloguj</a>

</body>
</html>
