<?php
require __DIR__ . '/../../../auth/guard.php';
require __DIR__ . '/../../../config/db.php';

$userId = $_SESSION['user'];
$month  = (int)($_GET['m'] ?? date('n'));
$year   = (int)($_GET['y'] ?? date('Y'));

$stmt = $pdo->prepare("
    SELECT 
        day, 
        `generated`, 
        weather,
        storage_kwh,
        counter_reading,
        (counter_reading - LAG(counter_reading) OVER (ORDER BY day ASC)) AS consumed
    FROM energy_daily
    WHERE user_id = ? AND YEAR(day) = ? AND MONTH(day) = ?
    ORDER BY day ASC
");
$stmt->execute([$userId, $year, $month]);
$days = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("
    SELECT storage_kwh, day 
    FROM energy_daily 
    WHERE user_id = ? AND storage_kwh > 0
    ORDER BY day DESC 
    LIMIT 1
");
$stmt2->execute([$userId]);
$last_storage_row = $stmt2->fetch(PDO::FETCH_ASSOC);

$base_storage = $last_storage_row['storage_kwh'] ?? 0;
$base_date = $last_storage_row['day'] ?? '1970-01-01';

$monthNames = [
    1=>'Styczeń',2=>'Luty',3=>'Marzec',4=>'Kwiecień',
    5=>'Maj',6=>'Czerwiec',7=>'Lipiec',8=>'Sierpień',
    9=>'Wrzesień',10=>'Październik',11=>'Listopad',12=>'Grudzień'
];

$running_storage = $base_storage;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title><?= $monthNames[$month] ?> <?= $year ?></title>
<link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico">
<link rel="stylesheet" href="/public/static/css/style.css">
<style>
    table { border-collapse: collapse; width: 85%; margin: 20px auto; color: white; background: rgba(0,0,0,0.2); }
    th, td { border: 1px solid rgba(255,255,255,0.2); padding: 12px; text-align: center; }
    th { background: rgba(255,255,255,0.1); }
    .storage-col { background: rgba(74, 222, 128, 0.1); font-weight: bold; }
</style>
</head>
<body>
  <div class="title-panel">
<a  style="color: white; text-decoration: none;" href="/public/html/home.html"><img src="/images/dark_logo.png" class="logo" alt="NexControl"></a>
<h1 class="panel_h1"><a  style="color: white; text-decoration: none;" href="/public/html/home.html">NexControl</a></h1>
    <div class="panel-buttons">
        <a href="index.php?y=<?= $year ?>" class="nav-btn">← Powrót</a>
        <a href="add_day.php?m=<?= $month ?>&y=<?= $year ?>" class="nav-btn">+ Dodaj dzień</a>
        <a href="/auth/logout.php" class="nav-btn logout">Wyloguj</a>
    </div>
</div>

<h2>Podsumowanie: <?= $monthNames[$month] ?> <?= $year ?></h2>
<p style="text-align:center">Baza magazynu: <strong><?= number_format($base_storage, 2) ?> kWh</strong> (z dnia <?= $base_date ?>)</p>

<table>
<tr>
    <th>Dzień</th>
    <th>Wygenerowano (kWh)</th>
    <th>Zużyto (kWh)</th>
    <th>Bilans (kWh)</th>
    <th>Stan Magazynu</th>
    <th>Pogoda</th>
</tr>
<?php foreach ($days as $row): 
    $consumed = (float)($row['consumed'] ?? 0);
    $balance = $row['generated'] - $consumed;
    if ($row['day'] > $base_date) {
        $running_storage += $balance;
    } elseif ($row['day'] == $base_date) {
        $running_storage = $row['storage_kwh'];
    }
?>
<tr>
    <td><?= date('d.m.Y', strtotime($row['day'])) ?></td>
    <td><?= number_format($row['generated'], 2) ?></td>
    <td><?= number_format($consumed, 2) ?></td>
    <td style="color:<?= $balance >= 0 ? '#4ade80' : '#f87171' ?>">
        <?= ($balance >= 0 ? '+' : '') . number_format($balance, 2) ?>
    </td>
    <td class="storage-col"><?= number_format($running_storage, 2) ?> kWh</td>
    <td style="font-style: italic; color: #aaa;"><?= htmlspecialchars($row['weather'] ?? '-') ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
