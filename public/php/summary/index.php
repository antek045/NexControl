<?php
require __DIR__ . '/../../../auth/guard.php';
require __DIR__ . '/../../../config/db.php';

$userId = $_SESSION['user'];
$year = (int)($_GET['y'] ?? date('Y'));

// Łączymy wszystko w jedno zapytanie - wydajniej i bez błędów o brakujące kolumny
$stmt = $pdo->prepare("
    SELECT 
        MONTH(day) AS month,
        COUNT(*) AS days_count,
        AVG(`generated`) AS avg_generated,
        AVG(consumed) AS avg_consumed,
        SUM(`generated` - consumed) AS net,
        MAX(storage_kwh) AS last_storage -- Pobieramy najwyższy/ostatni stan magazynu w miesiącu
    FROM energy_daily
    WHERE user_id = ? AND YEAR(day) = ?
    GROUP BY MONTH(day)
");
$stmt->execute([$userId, $year]);
$dailyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mapujemy dane na miesiące
$summary = [];
foreach ($dailyData as $row) {
    $summary[$row['month']] = $row;
}

$months = [
    1=>'Styczeń', 2=>'Luty', 3=>'Marzec', 4=>'Kwiecień',
    5=>'Maj', 6=>'Czerwiec', 7=>'Lipiec', 8=>'Sierpień',
    9=>'Wrzesień', 10=>'Październik', 11=>'Listopad', 12=>'Grudzień'
];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Power System Management - Podsumowanie <?= $year ?></title>
    <link rel="stylesheet" href="/public/static/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico">
    <style>
        .months-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }
        .card { padding: 15px; border-radius: 8px; background: rgba(255,255,255,0.1); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.15); }
        .net-value { font-weight: bold; }
        @media (max-width: 900px) { .months-grid { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 560px) { .months-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="title-panel">
<a  style="color: white; text-decoration: none;" href="/public/html/home.html"><img src="/images/dark_logo.png" class="logo" alt="NexControl"></a>
<h1 class="panel_h1"><a  style="color: white; text-decoration: none;" href="/public/html/home.html">NexControl</a></h1>
<a href="/public/php/summary/add_day.php" class="nav-btn">+ Dodaj dzień</a>
<a href="/auth/logout.php" class="nav-btn logout" style="margin-left: 5px;">Wyloguj</a>
</div>
<div class="title-panel">
    <h1 class="panel_h1">Podsumowanie <?= $year ?></h1>
    <div class="panel-buttons">
        <a href="?y=<?= $year-1 ?>" class="nav-btn">← <?= $year-1 ?></a>
        <a href="?y=<?= $year+1 ?>" class="nav-btn"><?= $year+1 ?> →</a>
    </div>
</div>

<div class="months-grid">
    <?php foreach ($months as $nr => $name): ?>
        <?php
            $data = $summary[$nr] ?? null;
            $hasData = !empty($data);
            $net = $hasData ? (float)$data['net'] : 0;
        ?>
        <a href="month.php?m=<?= $nr ?>&y=<?= $year ?>" style="text-decoration:none; color: inherit;">
            <div class="card" style="<?= !$hasData ? 'opacity:0.5' : '' ?>">
                <h2><?= $name ?></h2>
                <?php if ($hasData): ?>
                    <p>
                        <strong>Dni z danymi:</strong> <?= $data['days_count'] ?><br>
                        <strong>Śr. wygenerowano:</strong> <?= number_format($data['avg_generated'], 2) ?> kWh<br>
                        <strong>Śr. zużycie:</strong> <?= number_format($data['avg_consumed'], 2) ?> kWh<br>
                        <strong>Bilans netto:</strong> 
                        <span class="net-value" style="color:<?= $net >= 0 ? '#4ade80' : '#f87171' ?>">
                            <?= number_format($net, 2) ?> kWh
                        </span><br>
                        <strong>Magazyn:</strong> <?= number_format($data['last_storage'], 2) ?> kWh
                    </p>
                <?php else: ?>
                    <p>Brak danych — kliknij, aby dodać</p>
                <?php endif; ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<footer class="footer-panel">
    <p>&copy; NexControl <?= $year ?></p>
</footer>

</body>
</html>
