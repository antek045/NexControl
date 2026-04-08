<?php
$consent = $_COOKIE['cookies_consent'] ?? null;
?>

<?php if (!$consent): ?>
<div id="cookie-banner" style="
  position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%);
  width: min(600px, calc(100% - 2rem));
  background: #fff; border: 1px solid #ddd; border-radius: 12px;
  padding: 1.25rem 1.5rem; box-shadow: 0 4px 24px rgba(0,0,0,0.1);
  z-index: 9999; font-family: sans-serif;">

  <p style="margin:0 0 6px; font-size:15px; font-weight:600;">Ta strona używa plików cookie</p>
  <p style="margin:0 0 1rem; font-size:13px; color:#555; line-height:1.6;">
    Używamy niezbędnych plików cookie do działania strony. Za Twoją zgodą stosujemy 
    też analityczne i marketingowe. <a href="/polityka-prywatnosci" style="color:#333;">Dowiedz się więcej</a>
  </p>

  <div style="display:flex; gap:8px; flex-wrap:wrap;">
    <a href="?cookie_action=accept" style="
      padding:8px 18px; font-size:13px; font-weight:500;
      background:#111; color:#fff; border-radius:8px; text-decoration:none;">
      Akceptuj wszystkie
    </a>
    <a href="?cookie_action=reject" style="
      padding:8px 18px; font-size:13px; font-weight:500;
      background:transparent; color:#111; border:1px solid #ccc;
      border-radius:8px; text-decoration:none;">
      Tylko niezbędne
    </a>
  </div>
</div>
<?php endif; ?>