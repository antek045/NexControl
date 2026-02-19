<?php
// Ten skrypt odbiera dane z formularza HTML wysłane metodą POST.

// 1. Ustawienia bazy danych (DANE LOGOWANIA DO ZMIANY!)
$servername = "localhost";
$username = "root"; // Domyślna nazwa użytkownika XAMPP
$password = "";     // Domyślne hasło XAMPP (puste)
$dbname = "auth_system"; // Nazwa Twojej bazy danych z phpMyAdmin

// 2. Połączenie z bazą danych
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdź połączenie
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

// 3. Sprawdź, czy formularz został wysłany (metodą POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Zabezpieczanie danych wejściowych i przypisywanie do zmiennych
    // Używamy funkcji real_escape_string jako dodatkowe zabezpieczenie, choć to prepared statements są kluczowe
    $day = $conn->real_escape_string($_POST['day']);
    $generated = $conn->real_escape_string($_POST['generated']);
    $weather = $conn->real_escape_string($_POST['weather']);

    // 4. Przygotowanie bezpiecznego zapytania SQL (Prepared Statements)
    // Zwróć uwagę na nazwy kolumn: `day`, `generated`, `weather`
    $sql = "INSERT INTO `newgenerated` (`day`, `generated`, `weather`) VALUES (?, ?, ?)";
    
    // Inicjalizacja prepared statement
    $stmt = $conn->prepare($sql);
    
    // Powiązanie parametrów (s=string, d=double/decimal, s=string)
    $stmt->bind_param("sds", $day, $generated, $weather);

    // 5. Wykonanie zapytania
    if ($stmt->execute()) {
        echo "<h2>✅ Dane zapisane pomyślnie w bazie danych!</h2>";
    } else {
        echo "<h2>❌ Błąd podczas zapisu danych: </h2>" . $stmt->error;
    }

    // Zamknięcie zapytania
    $stmt->close();
} else {
    // Jeśli ktoś próbuje wejść na ten plik bezpośrednio (nie przez formularz)
    echo "<p>Formularz nie został wysłany metodą POST.</p>";
}

// 6. Zamknięcie połączenia z bazą
$conn->close();
?>
