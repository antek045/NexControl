<?php
session_start();
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$userId = $_SESSION['user'] ?? null;
if (!$userId) die("Błąd: Użytkownik nie jest zalogowany.");

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Połączenie nieudane: " . $e->getMessage());
}

$stmt_year = $conn->prepare("SELECT pv_install_year FROM users WHERE id = ?");
$stmt_year->execute([$userId]);
$user_install_year = $stmt_year->fetchColumn() ?: null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['save_year']) && !$user_install_year) {
        $year_to_save = (int)$_POST['created'];
        $stmt_update = $conn->prepare("UPDATE users SET pv_install_year = ? WHERE id = ?");
        $stmt_update->execute([$year_to_save, $userId]);
        $user_install_year = $year_to_save;
    }


    if (isset($_POST['counter_reading'])) {
        $current_day = $_POST['day'];
        $current_reading = (float)$_POST['counter_reading'];


        $sql_last = "SELECT counter_reading FROM energy_daily 
                     WHERE user_id = ? AND day < ? AND counter_reading > 0 
                     ORDER BY day DESC LIMIT 1";
        $stmt_last = $conn->prepare($sql_last);
        $stmt_last->execute([$userId, $current_day]);
        $last_row = $stmt_last->fetch(PDO::FETCH_ASSOC);

        $consumption_amount = 0;
        if ($last_row) {
            $previous_reading = (float)$last_row['counter_reading'];
            if ($current_reading >= $previous_reading) {
                $consumption_amount = $current_reading - $previous_reading;
            }
        }

 $sql_insert = "INSERT INTO energy_daily (user_id, day, consumed, counter_reading) 
                       VALUES (:uid, :day, :consumed, :reading)
                       ON DUPLICATE KEY UPDATE 
                       consumed = :consumed_upd,
                       counter_reading = :reading_upd";

        $stmt = $conn->prepare($sql_insert);
        
        $params = [
            ':uid' => $userId, 
            ':day' => $current_day, 
            ':consumed' => $consumption_amount,
            ':reading' => $current_reading,
            ':consumed_upd' => $consumption_amount,
            ':reading_upd' => $current_reading
        ];

        if ($stmt->execute($params)) {
            header("Location: /public/php/summary/add_day.php?msg=ok_cons");
            exit;
        } else {
            header("Location: /public/php/summary/add_day.php?msg=err_cons");
            exit;
        }
    }
}
?>
