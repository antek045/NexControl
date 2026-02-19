<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=auth_system;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$m = (int)($_GET['m'] ?? 0);
if ($m < 1 || $m > 12) die("Błędny miesiąc");

$cons = $pdo->prepare("
    SELECT day, daily_consumption
    FROM daily_consumption
    WHERE MONTH(day) = ?
");
$cons->execute([$m]);

$consumption = [];
foreach ($cons as $r) {
    $consumption[$r['day']] = round($r['daily_consumption'],2);
}

$gen = $pdo->prepare("
    SELECT day, generated, weather
    FROM newgenerated
    WHERE MONTH(day) = ?
");
$gen->execute([$m]);

$generated = [];
foreach ($gen as $r) {
    $generated[$r['day']] = $r;
}

$months = [
    1=>'Styczeń',2=>'Luty',3=>'Marzec',4=>'Kwiecień',
    5=>'Maj',6=>'Czerwiec',7=>'Lipiec',8=>'Sierpień',
    9=>'Wrzesień',10=>'Październik',11=>'Listopad',12=>'Grudzień'
];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Power System Managment - <?= $months[$m] ?></title>
<link rel="stylesheet" href="../public/static/css/month.css">
<link rel="stylesheet" href="../public/static/css/style.css">
</head>
<body>
<div class="title-panel">
    <div class="title-left">
        <img src="logo.png" alt="Logo" class="logo">
    </div>
    <div class="title-right">
        <div class="title-text">
            <h1>Podsumowanie miesiąca - <?= $months[$m] ?></h1>
        </div>
        <nav class="navigation">
            <button class="nav-btn" onclick="location.href='newgenerated.html'">Nowe zgłoszenie (Generowanie)</button>
            <button class="nav-btn" onclick="location.href='newconsumption.html'">Nowe zgłoszenie (Rozliczenie)</button>
            <button class="btn" onclick="location.href='index.php'">Wróć</button>
            <button class="nav-btn logout" onclick="location.href='../auth/auth/login.php'">Wyloguj</button>
        </nav>
    </div>
</div>

<table>
<tr>
    <th>Dzień</th>
    <th>Zużycie</th>
    <th>Wygenerowano</th>
    <th>Pogoda</th>
    <th>Magazyn</th>
</tr>

<?php
$days = cal_days_in_month(CAL_GREGORIAN, $m, date('Y'));
for ($d=1; $d<=$days; $d++):
    $date = date("Y-m-d", strtotime(date('Y')."-$m-$d"));
?>
<tr>
    <td><?= $d ?></td>
    <td><?= $consumption[$date] ?? '-' ?></td>
    <td><?= $generated[$date]['generated'] ?? '-' ?></td>
    <td><?= $generated[$date]['weather'] ?? '-' ?></td>
    <td><?= $generated[$date]['storage'] ?? '-' ?></td>

</tr>
<?php endfor; ?>

</table>

</body>
</html>
