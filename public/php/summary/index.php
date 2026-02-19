<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=auth_system;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$data = $pdo->query("SELECT * FROM monthly_full")
            ->fetchAll(PDO::FETCH_ASSOC);

$months = [
    1=>'Styczeń',2=>'Luty',3=>'Marzec',4=>'Kwiecień',
    5=>'Maj',6=>'Czerwiec',7=>'Lipiec',8=>'Sierpień',
    9=>'Wrzesień',10=>'Październik',11=>'Listopad',12=>'Grudzień'
];

$summary = [];
foreach ($data as $row) {
    $summary[$row['month']] = $row;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Power System Managment - Podsumowanie</title>
<link rel="stylesheet" href="../static/css/index.css">
<link rel="stylesheet" href="../static/css/style.css">
</head>
<body>
<div class="title-panel">
    <div class="title-left">
        <img src="logo.png" alt="Logo" class="logo">
    </div>
    <div class="title-right">
        <div class="title-text">
            <h1>Podsumowanie zgłoszeń</h1>
        </div>
        <nav class="navigation">
            <button class="nav-btn">Nowe zgłoszenie (Generowanie)</button>
            <button class="nav-btn">Nowe zgłoszenie (Rozliczenie)</button>
            <button class="nav-btn logout">Wyloguj</button>
        </nav>
    </div>
</div>

<table>
<tr>
    <th></th>
    <?php foreach ($months as $nr=>$name): ?>
        <th><a href="month.php?m=<?= $nr ?>"><?= $name ?></a></th>
    <?php endforeach; ?>
</tr>

<tr>
    <td><b>Średnie zużycie</b></td>
    <?php for ($m=1;$m<=12;$m++): ?>
        <td><?= $summary[$m]['avg_consumption'] ?? '-' ?></td>
    <?php endfor; ?>
</tr>

<tr>
    <td><b>Średnio wygenerowano</b></td>
    <?php for ($m=1;$m<=12;$m++): ?>
        <td><?= $summary[$m]['avg_generated'] ?? '-' ?></td>
    <?php endfor; ?>
</tr>
<tr>
    <td><b>W magazynie</b></td>
    <?php for ($m=1;$m<=12;$m++): ?>
        <td><?= $summary[$m]['storage'] ?? '-' ?></td>
    <?php endfor; ?>
</tr>

</table>

</body>
</html>
