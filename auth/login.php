<?php
session_start();
$userId = $_SESSION['user'] ?? null;
session_write_close();

require __DIR__ . '/../config/db.php';

$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'login') {

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($login === '' || $password === '') {
            $message = 'Uzupełnij wszystkie pola.';
            $type = 'error';
        } else {

            $stmt = $pdo->prepare(
                'SELECT id, password 
                 FROM users 
                 WHERE username = ? OR email = ?
                 LIMIT 1'
            );
            $stmt->execute([$login, $login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user'] = $user['id'];
                session_write_close();

                header('Location: /nexcontrol/public/html/home.html');
                exit;
            } else {
                $message = 'Nieprawidłowy login lub hasło.';
                $type = 'error';
            }
        }
    }

    if ($_POST['action'] === 'register') {

        $login = trim($_POST['login'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass1 = $_POST['password'] ?? '';
        $pass2 = $_POST['password2'] ?? '';

        if ($login === '' || $email === '' || $pass1 === '' || $pass2 === '') {
            $message = 'Wypełnij wszystkie pola.';
            $type = 'error';
        } elseif ($pass1 !== $pass2) {
            $message = 'Hasła nie są takie same.';
            $type = 'error';
        } else {
            try {
                $hash = password_hash($pass1, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    'INSERT INTO users (username, email, password)
                     VALUES (?, ?, ?)'
                );
                $stmt->execute([$login, $email, $hash]);

                $message = 'Konto utworzone! Możesz się zalogować.';
                $type = 'success';

            } catch (PDOException $e) {
                $message = 'Login lub e-mail już istnieje.';
                $type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Panel logowania</title>
    <link rel="stylesheet" href="../public/static/css/style.css">
    <link rel="stylesheet" href="../public/static/css/login.css">
</head>

<body>
    <?php if ($message !== ''): ?>
        <div class="alert <?= htmlspecialchars($type) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="title-panel">
        <div class="title-left">
            <img src="logo.png" alt="Logo" class="logo">
        </div>

        <div class="title-right">
            <div class="title-text">
                <h1>Panel logowania do strony<br>Power System Management</h1>
            </div>

            <nav class="navigation">
                <button class="nav-btn" onclick="location.href='../public/newgenerated.html'">
                    Nowe zgłoszenie (Generowanie)
                </button>
                <button class="nav-btn">Nowe zgłoszenie (Rozliczenie)</button>
                <button class="nav-btn">Podsumowanie zgłoszeń</button>
                <button class="nav-btn logout">Wyloguj</button>
            </nav>
        </div>
    </div>

    <main class="page-content">
        <div class="form-center">
            <div class="container">

                <div class="switch">
                    <button id="loginBtn" class="active">Logowanie</button>
                    <button id="registerBtn">Rejestracja</button>
                </div>

                <!-- LOGOWANIE -->
                <form id="loginForm" method="POST" class="form active">
                    <input type="hidden" name="action" value="login">
                    <input type="text" name="login" placeholder="Login lub e-mail" required>
                    <input type="password" name="password" placeholder="Hasło" required>
                    <button type="submit">Zaloguj się</button>
                </form>

                <!-- REJESTRACJA -->
                <form id="registerForm" method="POST" class="form">
                    <input type="hidden" name="action" value="register">
                    <input type="text" name="login" placeholder="Login" required>
                    <input type="email" name="email" placeholder="E-mail" required>
                    <input type="password" name="password" placeholder="Hasło" required>
                    <input type="password" name="password2" placeholder="Powtórz hasło" required>
                    <button type="submit">Zarejestruj się</button>
                </form>

            </div>
        </div>
    </main>

    <footer>
        <p>© 2025 Feedback System - CNR ISTC</p>
    </footer>

    <script src="../public/script.js"></script>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.style.display = 'none';
        }, 3000);
    </script>

</body>

</html>