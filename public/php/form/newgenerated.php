<?php
session_start();

$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user'] ?? null;

    if (!$userId) {
        die("Błąd: Użytkownik nie jest zalogowany.");
    }
    $day       = $_POST['day'];
    $generated = (float)($_POST['generated'] ?? 0);
    $weather   = $_POST['weather'] ?? '';
    $storage   = (float)($_POST['storage'] ?? 0);

$generated = (float)($_POST['generated'] ?? 0);

if ($generated < 0) {
    $generated = 0;
}


    try {
        $stmt = $pdo->prepare("
    INSERT INTO `energy_daily` 
    (`user_id`, `day`, `generated`, `weather`, `storage_kwh`, `consumed`) 
    VALUES (?, ?, ?, ?, ?, 0)
    ON DUPLICATE KEY UPDATE 
        `generated` = VALUES(`generated`),
        `weather`   = VALUES(`weather`),
        `storage_kwh` = VALUES(`storage_kwh`)
");

        if ($stmt->execute([$userId, $day, $generated, $weather, $storage])) {
            header("Location: /public/php/summary/add_day.php?msg=ok");
        } else {
            header("Location: /public/php/summary/add_day.php?msg=err");
        }
        exit;

    } catch (PDOException $e) {
        die("Błąd zapisu: " . $e->getMessage());
    }
}
?>
