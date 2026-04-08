<?php
require __DIR__ . '/../../../auth/guard.php';
require __DIR__ . '/../../../config/db.php';

$userId = $_SESSION['user'];
$month  = (int)($_GET['m'] ?? date('n'));
$year   = (int)($_GET['y'] ?? date('Y'));
$error  = '';
$success = '';
$stmt = $pdo->prepare("SELECT pv_install_year FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user_install_year = $stmt->fetchColumn() ?: null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day       = $_POST['day'] ?? '';
    $generated = (float)($_POST['generated'] ?? 0);
    $consumed  = (float)($_POST['consumed'] ?? 0);

    if (!$day) {
        $error = 'Wybierz datę.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO energy_daily (user_id, day, generated, consumed)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE generated=VALUES(generated), consumed=VALUES(consumed)
            ");
            $stmt->execute([$userId, $day, $generated, $consumed]);
            $success = 'Zapisano!';
        } catch (Exception $e) {
            $error = 'Błąd zapisu.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head><meta charset="UTF-8"><title>Dodaj dzień</title>
<link rel="stylesheet" href="/public/static/css/style.css">
<link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico">
<style>
.forms-container {
    display: flex;          
    gap: 20px;             
    justify-content: center;
    align-items: stretch; 
    flex-wrap: wrap;
}

.card-form {
    flex: 1;               
    min-width: 300px;
    max-width: 500px;
}
.success {
background: rgba(0, 255, 0, 0.2);
 color: #00ff00;
  padding: 10px;
   border-radius: 8px;
    text-align: center; 
    border: 1px solid #00ff00;
}
.error{
background: rgba(255, 0, 0, 0.2);
 color: #ff4444; 
 padding: 10px; 
 border-radius: 8px; 
 text-align: center;
  border: 1px solid #ff4444;
}
</style>
</head>
<body>
    <div class="title-panel">
    <div class="panel-logo">
        <a  style="color: white; text-decoration: none;" href="/public/html/home.html"><img src="/images/dark_logo.png" class="logo" alt="NexControl"></a>
    </div>

    <div class="panel">
        <h1 class="panel_h1"><a  style="color: white; text-decoration: none;" href="/public/html/home.html">NexControl</a></h1>
    </div>

    <div class="panel-buttons">
        <a class="nav-btn" href="month.php?m=<?= $month ?>&y=<?= $year ?>"> ← Powrót</a>
        <a href="/auth/logout.php" class="nav-btn logout">Wyloguj</a>
    </div>
</div>

<h2>Dodaj dane dnia</h2>

<?php if ($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green"><?= $success ?></p><?php endif; ?>
<div class="forms-container">
<div class="newgenerated, card-form">
      <h3>Wprowadź dane dotyczące prądu wygenerowanego</h3>
      <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'ok'): ?>
            <p  class="success">
                ✅ Dane zapisane pomyślnie!
            </p>
        <?php elseif ($_GET['msg'] === 'err'): ?>
            <p class="error">
                ❌ Błąd zapisu danych.
            </p>
        <?php endif; ?>
    <?php endif; ?>
      <form action="/public/php/form/newgenerated.php" method="post" id="newgen">
        <label for="day">Wprowadź datę</label>
        <input type="date" name="day" id="day" required /><br />
        <label for="generated">Wprowadź ilość prądu wygenerowanego (kWh)</label>
        <input type="number" name="generated" id="generated" step="0.01" required/><br/>
        <label for="weather">Opisz jaka była pogoda</label>
        <textarea name="weather" id="weather" placeholder="słonecznie/deszczowo/pochmurno/..." style="resize: none" cols="35px" required></textarea><br/>
        <label for="storage">Wprowadź stan magazynu z rozliczenia</label>
        <input type="number" name="storage" id="storage" step="0.01"/><br />
        <button type="submit">Utwórz wpis</button>
      </form>
    </div>
<div class="newconsumption, card-form">
      <h3>Wprowadź dane dotyczące prądu pobranego</h3>
      <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'ok_cons'): ?>
            <p  class="success">
                ✅ Dane zapisane pomyślnie!
            </p>
        <?php elseif ($_GET['msg'] === 'err_cons'): ?>
            <p class="error">
                ❌ Błąd zapisu danych.
            </p>
        <?php endif; ?>
    <?php endif; ?>
      <form action="/public/php/form/newconsumption.php" method="post" id="newgen">
        <label for="day">Wprowadź datę początkową</label>
        <input type="date" name="day" id="day" required /><br />
        <label for="counter_reading">Wprowadź stan licznika</label>
        <input type="number" name="counter_reading" id="counter_reading" step="0.001" required/><br/>
        <?php if (!$user_install_year): ?>
    <label for="created">W którym roku założyłeś panele fotowoltaiczne?</label>
    <input type="number" name="created" id="created" min="2000" max="<?= date('Y') ?>" value="2020" required>
    <button type="submit" name="save_year">Zapisz rok</button>
<?php else: ?>
    <p>System rozliczeń dla roku: <strong><?= $user_install_year ?></strong></p>
<?php endif; ?>
        <button type="submit">Utwórz wpis</button>
</form>
    </div>
</div>
<script>
    setTimeout(() => {
        const alerts = document.querySelectorAll('.card-form p');
        alerts.forEach(alert => {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
</script>
</body>
</html>