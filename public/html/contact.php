<?php
//require __DIR__ . '/../../auth/guard.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$email || !$subject || !$message) {
        $error = 'Wypełnij wszystkie pola.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Nieprawidłowy adres e-mail.';
    } else {
        require __DIR__ . '/../../vendor/autoload.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nexcontrol0407@gmail.com';   
            $mail->Password   = 'jmbe tiom qxhr ujwt'; 
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('nexcontrol0407@gmail.com', 'NexControl - System');
            $mail->addAddress('antoni.gdula08@gmail.com');
            $mail->addAddress('nexcontrol0407@gmail.com');
            $mail->addReplyTo($email);

            $mail->isHTML(true);
            $mail->Subject = 'Nowe zgloszenie: ' . $subject;
            
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #4a0072; color: #ffffff; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Nowa wiadomość z NexControl</h2>
                </div>
                <div style='padding: 20px; color: #333; line-height: 1.6;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold; border-bottom: 1px solid #eee;'>Od:</td>
                            <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>$email</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-weight: bold; border-bottom: 1px solid #eee;'>Temat:</td>
                            <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>$subject</td>
                        </tr>
                    </table>
                    <div style='margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #4a0072;'>
                        <strong>Tresc wiadomosci:</strong><br>
                        " . nl2br(htmlspecialchars($message)) . "
                    </div>
                </div>
                <div style='background-color: #f4f4f4; color: #777; padding: 10px; text-align: center; font-size: 12px;'>
                    Wiadomosc wygenerowana automatycznie przez system NexControl.
                </div>
            </div>";

            $mail->AltBody = "Nowa wiadomosc od: $email\nTemat: $subject\n\nTresc:\n$message";

            $mail->send();
            $success = true;
        }  catch (Exception $e) {
    $error = 'Błąd: ' . $mail->ErrorInfo; // To pokaże co dokładnie nie działa
}}}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>NexControl - Kontakt</title>
    <link rel="stylesheet" href="/public/static/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico">
</head>
<body>

<div class="title-panel">
  <img src="/images/dark_logo.png" class="logo" alt="NexControl">
  <h1 class="panel_h1">
    <a href="/public/html/home.html" style="color:white; text-decoration:none;">NexControl</a>
  </h1>
  <div class="panel-buttons">
    <a href="/public/html/aboutus.html" class="nav-btn">O nas</a>
    <a href="/public/php/summary/index.php" class="nav-btn">Podsumowanie</a>
    <a href="/public/html/home.html" class="nav-btn">Strona główna</a>
    <a href="/auth/logout.php" class="nav-btn logout">Wyloguj</a>
  </div>
</div>

<div class="card-form" style="margin: 40px auto;">

  <h3>Kontakt</h3>

  <?php if ($success): ?>
    <p style="background:rgba(0,255,0,0.2); color:#00ff00; padding:10px; border-radius:8px; border:1px solid #00ff00; text-align:center;">
      ✅ Wiadomość wysłana!
    </p>
  <?php endif; ?>

  <?php if ($error): ?>
    <p style="background:rgba(255,0,0,0.2); color:#ff4444; padding:10px; border-radius:8px; border:1px solid #ff4444; text-align:center;">
      ❌ <?= htmlspecialchars($error) ?>
    </p>
  <?php endif; ?>

  <form method="POST">
    <label>Twój adres e-mail</label>
    <input type="email" name="email" placeholder="twoj@email.com" required>

    <label>Temat</label>
    <input type="text" name="subject" placeholder="Temat wiadomości" required>

    <label>Wiadomość</label>
    <textarea name="message" placeholder="Napisz wiadomość..." style="resize:none;" rows="6" required></textarea>

    <button type="submit">Wyślij wiadomość</button>
  </form>

</div>

<footer class="footer-panel">
  <p>&copy; NexControl</p>
</footer>

</body>
</html>