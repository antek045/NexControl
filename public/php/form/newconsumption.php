<?php
//
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auth_system"; // Upewnij się, że nazwa bazy jest poprawna

// 2. Połączenie z bazą danych
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

// 3. Sprawdź, czy formularz został wysłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $current_day = $_POST['day'];
    $current_reading = $_POST['counter_reading'];
    $table_name = "consumption"; // Nazwa tabeli do zużycia

    // --- LOGIKA OBLICZANIA RÓŻNICY ---

    // A. Pobierz ostatni stan licznika z bazy
    $sql_last = "SELECT `day`, `counter_reading` FROM `$table_name` ORDER BY `day` DESC LIMIT 1";
    $result_last = $conn->query($sql_last);

    $previous_reading = 0;
    $days_difference = 0;
    $consumption_amount = 0;
    $message = "";

    if ($result_last->num_rows > 0) {
        $last_row = $result_last->fetch_assoc();
        $previous_reading = $last_row['counter_reading'];
        $previous_day = $last_row['day'];

        // Oblicz różnicę w zużyciu
        $consumption_amount = $current_reading - $previous_reading;
        
        // Oblicz różnicę w dniach
        $datetime1 = new DateTime($previous_day);
        $datetime2 = new DateTime($current_day);
        $interval = $datetime1->diff($datetime2);
        $days_difference = $interval->days;

        $message = "Utworzono wpis. Wzrost prądu pobranego o **" . number_format($consumption_amount, 3) . " kWh** w ciągu **" . $days_difference . "** dni.";

    } else {
        $message = "To jest pierwszy wpis w bazie danych. Nie można obliczyć różnicy.";
    }

    // --- ZAPISZ NOWY WPIS DO BAZY ---

    $sql_insert = "INSERT INTO `$table_name` (`day`, `counter_reading`) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("sd", $current_day, $current_reading); // s=string(date), d=double/decimal

    if ($stmt->execute()) {
        echo "<h2>✅ Sukces!</h2><p>$message</p>";
    } else {
        echo "<h2>❌ Błąd podczas zapisu: </h2>" . $stmt->error;
    }

    $stmt->close();

} else {
    echo "<p>Formularz nie został wysłany metodą POST.</p>";
}

$conn->close();
?>
