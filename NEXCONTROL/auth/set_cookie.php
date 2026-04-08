<?php
if (isset($_GET['cookie_action'])) {
    $value = $_GET['cookie_action'] === 'accept' ? 'accepted' : 'rejected';
    $expires = time() + 60 * 60 * 24 * 365; 
    setcookie('cookies_consent', $value, [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => true,      
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $url);
    exit;
}