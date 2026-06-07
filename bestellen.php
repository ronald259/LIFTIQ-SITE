<?php
require_once __DIR__ . '/config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam     = trim($_POST['naam'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefoon = trim($_POST['telefoon'] ?? '');
    $levering = $_POST['levering'] ?? 'verzenden';
    $adres    = trim($_POST['adres'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    $stad     = trim($_POST['stad'] ?? '');

    if (empty($naam))  $errors[] = 'Vul je naam in.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Vul een geldig e-mailadres in.';
    if ($levering === 'verzenden') {
        if (empty($adres))    $errors[] = 'Vul je adres in.';
        if (empty($postcode)) $errors[] = 'Vul je postcode in.';
        if (empty($stad))     $errors[] = 'Vul je woonplaats in.';
    }

    $producten = [
        'preworkout_blue' => ['naam' => 'Pre-Workout Blueberry',                          'prijs' => 34.95],
        'preworkout_red'  => ['naam' => 'Pre-Workout Strawberry Kiwi',                    'prijs' => 34.95],
        'preworkout_gold' => ['naam' => 'Pre-Workout Tropical (cafeïnevrij)',             'prijs' => 34.95],
        'straps'          => ['naam' => 'Lifting Straps PRO',                             'prijs' => 24.95],
        'sleeves_s'       => ['naam' => 'Knee Sleeves COMP X — S/M',                      'prijs' => 39.95],
        'sleeves_l'       => ['naam' => 'Knee Sleeves COMP X — L/XL',                     'prijs' => 39.95],
        'bundle_1'        => ['naam' => 'Starter Bundle (Pre-Workout + Straps)',          'prijs' => 54.95],
    ];

    $bestelling = $_POST['bestelling'] ?? [];
    $totaal = 0;
    $regels = [];
    foreach ($bestelling as $id => $aantal) {
        $aantal = (int)$aantal;
        if ($aantal > 0 && isset($producten[$id])) {
            $totaal += $producten[$id]['prijs'] * $aantal;
            $regels[] = $aantal . 'x ' . $producten[$id]['naam'];
        }
    }

    $verzendkosten = 5.95;
    if ($levering === 'verzenden' && $totaal < 50.00) {
        $totaal += $verzendkosten;
        $regels[] = 'Verzendkosten €5,95';
    }

    if (empty($regels)) $errors[] = 'Kies minimaal één product.';

    if (empty($errors)) {
        $levering_info = $levering === 'afhalen'
            ? 'Afhalen op locatie'
            : "Verzenden naar: $adres, $postcode $stad";
        $data = [
            'amount'      => ['currency' => 'EUR', 'value' => number_format($totaal, 2, '.', '')],
            'description' => 'LIFT IQ — ' . implode(', ', $regels),
            'redirectUrl' => 'https://liftiq-supplement.nl/bedankt.html',
            'webhookUrl'  => 'https://liftiq-supplement.nl/webhook.php',
            'metadata'    => [
                'naam'      => $naam,
                'email'     => $email,
                'telefoon'  => $telefoon,
                'levering'  => $levering_info,
                'bestelling'=> implode(', ', $regels),
            ]
        ];
        $ch = curl_init('https://api.mollie.com/v2/payments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . MOLLIE_API_KEY
        ]);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($response, true);
        if ($code === 201 && isset($result['_links']['checkout']['href'])) {
            header('Location: ' . $result['_links']['checkout']['href']);
            exit;
        } else {
            $errors[] = 'Betaling kon niet worden aangemaakt. Probeer opnieuw of mail info@liftiq-supplement.nl';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop — LIFT IQ</title>
<meta name="description" content="Bestel LIFT IQ supplementen en lifting gear. Snelle levering, gratis verzending v.a. €50.">
<link rel="canonical" href="https://liftiq-supplement.nl/bestellen.php">
<link rel="stylesheet" href="style.css">
<style>
.page-hero {
  padding: 110px 2rem 3rem;
  background: var(--dark);
  border-bottom: 1px solid var(--border);
  text-align: center;
}
.page-hero h1 {
  font-family: Impact, sans-serif;
  font-size: clamp(32px, 5vw, 56px);
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--white);
  margin-bottom: 0.4rem;
}
.page-hero p { font-size: 13px; color: var(--text-muted); letter-spacing: 0.06em; }

.steps-bar { background: var(--dark-2); border-bottom: 1px solid var(--border); padding: 0 2rem; }
.steps { display: flex; max-width: 860px; margin: 0 auto; height: 52px; }
.step { display: flex; align-items: center; gap: 10px; padding: 0 1.5rem; flex: 1; border-right: 1px solid var(--border); opacity: 0.35; transition: opacity var(--transition); }
.step:last-child { border-right: none; }
.step.active { opacity: 1; }
.step.done { opacity: 0.6; }
.step-num { width: 22px; height: 22px; border-radius: 50%; background: var(--dark-3); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.4); flex-shrink: 0; transition: all var(--transition); }
.step.active .step-num { background: var(--blue); border-color: var(--blue); color: var(--dark); }
.step.done .step-num { background: transparent; border-color: var(--blue); color: var(--blue); }
.step-label { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.6); }
.step.active .step-label { color: var(--blue); }

.bestel-layout { display: grid; grid-template-columns: 1fr 340px; gap: 2rem; max-width: 1100px; margin: 0 auto; padding: 2.5rem 2rem 5rem; align-items: start; }
.bestel-sidebar { position: sticky; top: 80px; background: var(--dark-2); border: 1px solid var(--border); padding: 2rem; }

.form-section { margin-bottom: 2.5rem; }
.form-section-title { font-family: Impact, sans-serif; font-size: 20px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--white); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }

.product-lijst { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem; }
.product-item { background: var(--dark-3); border: 1px solid var(--border); padding: 1.25rem; display: flex; gap: 1rem; align-items: center; transition: border-color var(--transition); }
.product-item:hover { border-color: var(--blue); }
.product-item-info { flex: 1; }
.product-item-name { font-family: Impact, sans-serif; font-size: 16px; letter-spacing: 0.06em; text-transform: uppercase; color: var(--white); margin-bottom: 3px; }
.product-item-price { font-size: 12px; color: var(--blue); font-weight: 700; letter-spacing: 0.1em; }
.product-item-qty select { background: var(--dark); border: 1px solid var(--border); color: var(--white); padding: 8px 12px; font-size: 14px; outline: none; cursor: pointer; min-width: 70px; }
.product-item-qty select:focus { border-color: var(--blue); }

.levering-opties { display: flex; gap: 1rem; margin-bottom: 2rem; }
.levering-optie { flex: 1; background: var(--dark-3); border: 2px solid var(--border); padding: 1.25rem; cursor: pointer; transition: border-color var(--transition); }
.levering-optie:has(input:checked) { border-color: var(--blue); }
.levering-optie input { display: none; }
.levering-optie-naam { font-size: 13px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--white); margin-bottom: 4px; }
.levering-optie-info { font-size: 12px; color: var(--text-muted); }

.form-group { margin-bottom: 1.25rem; }
.form-label { display: block; font-size: 10px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 6px; }
.form-input { width: 100%; background: var(--dark-3); border: 1px solid var(--border); color: var(--white); padding: 12px 14px; font-size: 14px; outline: none; transition: border-color var(--transition); font-family: inherit; }
.form-input:focus { border-color: var(--blue); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.sidebar-title { font-family: Impact, sans-serif; font-size: 18px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--white); margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }
.sidebar-totaal { background: var(--dark-3); border: 1px solid var(--border-strong); padding: 1.5rem; margin: 1.5rem 0; }
.sidebar-totaal-regel { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; }
.sidebar-totaal-final { display: flex; justify-content: space-between; font-family: Impact, sans-serif; font-size: 22px; letter-spacing: 0.05em; color: var(--blue); border-top: 1px solid var(--border); padding-top: 12px; margin-top: 8px; }
.sidebar-submit { width: 100%; background: var(--blue); color: var(--dark); font-size: 13px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; padding: 17px; border: none; cursor: pointer; transition: background var(--transition); }
.sidebar-submit:hover { background: var(--blue-dark); color: #fff; }
.sidebar-trust { display: flex; flex-direction: column; gap: 8px; margin-top: 1.25rem; }
.trust-item { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; }
.trust-item span { color: var(--blue); }

.errors { background: rgba(255,50,50,0.1); border: 1px solid rgba(255,50,50,0.3); padding: 1rem 1.25rem; margin-bottom: 1.5rem; }
.errors li { font-size: 13px; color: #ff6b6b; margin-left: 1rem; margin-bottom: 4px; }

#adres-velden { display: none; }

@media (max-width: 860px) {
  .bestel-layout { grid-template-columns: 1fr; }
  .bestel-sidebar { position: static; }
  .form-row { grid-template-columns: 1fr; }
  .levering-opties { flex-direction: column; }
  .steps { display: none; }
}
</style>
</head>
<body>

<nav class="nav scrolled" id="nav">
  <a href="/" class="nav-logo">
    <img src="images/liftiq-logo-white.png" class="nav-logo-img" alt="LIFTIQ — Lift Smarter Every Day">
  </a>
  <ul class="nav-links">
    <li><a href="/">Home</a></li>
    <li><a href="gear.html">Gear</a></li>
    <li><a href="bestellen.php" class="nav-cta active">Shop Nu</a></li>
  </ul>
  <button class="nav-hamburger" id="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>
<div class="nav-mobile" id="nav-mobile">
  <a href="/">Home</a>
  <a href="gear.html">Gear</a>
  <a href="bestellen.php">Shop Nu</a>
</div>

<div class="page-hero">
  <div class="section-eyebrow">Webshop</div>
  <h1>SHOP NU</h1>
  <p>Gratis verzending v.a. €50 · Betaal veilig met iDEAL</p>
</div>

<div class="steps-bar">
  <div class="steps">
    <div class="step active" id="step-1"><div class="step-num">1</div><span class="step-label">Producten</span></div>
    <div class="step" id="step-2"><div class="step-num">2</div><span class="step-label">Levering</span></div>
    <div class="step" id="step-3"><div class="step-num">3</div><span class="step-label">Gegevens</span></div>
    <div class="step" id="step-4"><div class="step-num">4</div><span class="step-label">Betalen</span></div>
  </div>
</div>

<form method="POST" action="bestellen.php" id="bestelform">
<div class="bestel-layout">
  <div class="bestel-main">

    <?php if (!empty($errors)): ?>
    <ul class="errors">
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <!-- STAP 1: PRODUCTEN -->
    <div class="form-section">
      <div class="form-section-title">1 — Kies je producten</div>
      <div class="product-lijst">
        <div class="product-item">
          <div class="product-item-info">
            <div class="product-item-name">Pre-Workout Blueberry</div>
            <div class="product-item-price">€34,95</div>
          </div>
          <div class="product-item-qty">
            <select name="bestelling[preworkout_blue]" class="qty-select" data-price="34.95">
              <?php for($i=0;$i<=5;$i++) echo "<option value='$i'" . (($i==0)?'selected':'') . ">$i</option>"; ?>
            </select>
          </div>
        </div>
        <div class="product-item">
          <div class="product-item-info">
            <div class="product-item-name">Pre-Workout Strawberry Kiwi</div>
            <div class="product-item-price">€34,95</div>
          </div>
          <div class="product-item-qty">
            <select name="bestelling[preworkout_red]" class="qty-select" data-price="34.95">
              <?php for($i=0;$i<=5;$i++) echo "<option value='$i'" . (($i==0)?'selected':'') . ">$i</option>"; ?>
            </select>
          </div>
        </div>
        <div class="product-item">
          <div class="product-item-info">
            <div class="product-item-name">Pre-Workout Tropical (cafeïnevrij)</div>
            <div class="product-item-price">€34,95 <span style="font-size:10px;color:var(--text-muted)">cafeïnevrij</span></div>
          </div>
          <div class="product-item-qty">
            <select name="bestelling[preworkout_gold]" class="qty-select" data-price="34.95">
              <?php for($i=0;$i<=5;$i++) echo "<option value='$i'" . (($i==0)?'selected':'') . ">$i</option>"; ?>
            </select>
          </div>
        </div>
        <div class="product-item">
          <div class="product-item-info">
            <div class="product-item-name">Lifting Straps PRO</div>
            <div class="product-item-price">€24,95</div>
          </div>
          <div class="product-item-qty">
            <select name="bestelling[straps]" class="qty-select" data-price="24.95">
              <?php for($i=0;$i<=5;$i++) echo "<option value='$i'" . (($i==0)?'selected':'') . ">$i</option>"; ?>
            </select>
          </div>
        </div>
        <div class="product-item">
          <div class="product-item-info">
            <div class="product-item-name">Knee Sleeves COMP X — S/M</div>
            <div class="product-item-price">€39,95</div>
          </div>
          <div class="product-item-qty">
            <select name="bestelling[sleeves_s]" class="qty-select" data-price="39.95">
              <?php for($i=0;$i<=5;$i++) echo "<option value='$i'" . (($i==0)?'selected':'') . ">$i</option>"; ?>
            </select>
          </div>
        </div>
        <div class="product-item">
          <div class="product-item-info">
            <div class="product-item-name">Knee Sleeves COMP X — L/XL</div>
            <div class="product-item-price">€39,95</div>
          </div>
          <div class="product-item-qty">
            <select name="bestelling[sleeves_l]" class="qty-select" data-price="39.95">
              <?php for($i=0;$i<=5;$i++) echo "<option value='$i'" . (($i==0)?'selected':'') . ">$i</option>"; ?>
            </select>
          </div>
        </div>
        <div class="product-item">
          <div class="product-item-info">
            <div class="product-item-name">Starter Bundle — Pre-Workout + Straps</div>
            <div class="product-item-price">€54,95 <span style="font-size:10px;color:var(--text-muted)">(bespaar €4,95)</span></div>
          </div>
          <div class="product-item-qty">
            <select name="bestelling[bundle_1]" class="qty-select" data-price="54.95">
              <?php for($i=0;$i<=5;$i++) echo "<option value='$i'" . (($i==0)?'selected':'') . ">$i</option>"; ?>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- STAP 2: LEVERING -->
    <div class="form-section">
      <div class="form-section-title">2 — Levering</div>
      <div class="levering-opties">
        <label class="levering-optie">
          <input type="radio" name="levering" value="verzenden" checked onchange="toggleAdres(this)">
          <div class="levering-optie-naam">📦 Verzenden</div>
          <div class="levering-optie-info">€5,95 · gratis v.a. €50</div>
        </label>
        <label class="levering-optie">
          <input type="radio" name="levering" value="afhalen" onchange="toggleAdres(this)">
          <div class="levering-optie-naam">📍 Afhalen</div>
          <div class="levering-optie-info">Op afspraak · gratis</div>
        </label>
      </div>
      <div id="adres-velden" style="display:block;">
        <div class="form-group">
          <label class="form-label">Straat + huisnummer</label>
          <input type="text" name="adres" class="form-input" placeholder="Kerkstraat 1" value="<?= htmlspecialchars($_POST['adres'] ?? '') ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Postcode</label>
            <input type="text" name="postcode" class="form-input" placeholder="1234 AB" value="<?= htmlspecialchars($_POST['postcode'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Woonplaats</label>
            <input type="text" name="stad" class="form-input" placeholder="Amsterdam" value="<?= htmlspecialchars($_POST['stad'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>

    <!-- STAP 3: GEGEVENS -->
    <div class="form-section">
      <div class="form-section-title">3 — Jouw gegevens</div>
      <div class="form-group">
        <label class="form-label">Naam</label>
        <input type="text" name="naam" class="form-input" placeholder="Jan de Vries" value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">E-mailadres</label>
          <input type="email" name="email" class="form-input" placeholder="jan@email.nl" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Telefoon (optioneel)</label>
          <input type="tel" name="telefoon" class="form-input" placeholder="06-12345678" value="<?= htmlspecialchars($_POST['telefoon'] ?? '') ?>">
        </div>
      </div>
    </div>

  </div>

  <!-- SIDEBAR -->
  <div class="bestel-sidebar">
    <div class="sidebar-title">Bestelling</div>
    <div id="sidebar-items" style="font-size:13px;color:var(--text-muted);margin-bottom:1rem;">Geen producten geselecteerd.</div>
    <div class="sidebar-totaal">
      <div class="sidebar-totaal-regel"><span>Subtotaal</span><span id="subtotaal">€0,00</span></div>
      <div class="sidebar-totaal-regel"><span>Verzending</span><span id="verzend">€5,95</span></div>
      <div class="sidebar-totaal-final"><span>Totaal</span><span id="totaal">€5,95</span></div>
    </div>
    <button type="submit" class="sidebar-submit">Betaal veilig met iDEAL →</button>
    <div class="sidebar-trust">
      <div class="trust-item"><span>🔒</span> Beveiligde betaling via Mollie</div>
      <div class="trust-item"><span>🚚</span> Verzending binnen 24 uur</div>
      <div class="trust-item"><span>↩️</span> 30 dagen retourrecht</div>
    </div>
  </div>
</div>
</form>

<!-- FOOTER -->
<footer class="footer">
  <div>
    <img src="images/liftiq-logo-white.png" class="footer-logo-img" alt="LIFTIQ" style="height:38px;width:auto;display:block;margin-bottom:10px;">
    <div class="footer-tagline">Lift Smarter Every Day</div>
  </div>
  <div>
    <div class="footer-col-title">Navigatie</div>
    <ul class="footer-links">
      <li><a href="/">Home</a></li>
      <li><a href="gear.html">Gear</a></li>
      <li><a href="bestellen.php">Shop Nu</a></li>
    </ul>
  </div>
  <div>
    <div class="footer-col-title">Contact</div>
    <div class="footer-info">
      <a href="mailto:info@liftiq-supplement.nl">info@liftiq-supplement.nl</a>
    </div>
  </div>
</footer>
<div class="footer-bottom">
  <span>© 2026 LIFT IQ · liftiq-supplement.nl</span>
  <span>Lift Smarter Every Day.</span>
</div>

<script>
const hamburger = document.getElementById('hamburger');
const navMobile = document.getElementById('nav-mobile');
hamburger.addEventListener('click', () => navMobile.classList.toggle('open'));

function toggleAdres(el) {
  document.getElementById('adres-velden').style.display = el.value === 'verzenden' ? 'block' : 'none';
  updateTotaal();
}

function updateTotaal() {
  const selects = document.querySelectorAll('.qty-select');
  let sub = 0;
  const items = [];
  selects.forEach(s => {
    const qty = parseInt(s.value);
    const price = parseFloat(s.dataset.price);
    if (qty > 0) {
      sub += qty * price;
      items.push(qty + 'x ' + s.closest('.product-item').querySelector('.product-item-name').textContent);
    }
  });

  const isSend = document.querySelector('input[name="levering"]:checked')?.value === 'verzenden';
  const verzend = isSend && sub < 50 ? 5.95 : 0;
  const totaal = sub + verzend;

  document.getElementById('subtotaal').textContent = '€' + sub.toFixed(2).replace('.', ',');
  document.getElementById('verzend').textContent = verzend > 0 ? '€5,95' : 'Gratis';
  document.getElementById('totaal').textContent = '€' + totaal.toFixed(2).replace('.', ',');
  document.getElementById('sidebar-items').innerHTML = items.length
    ? items.map(i => `<div style="padding:4px 0;border-bottom:1px solid var(--border)">${i}</div>`).join('')
    : '<span>Geen producten geselecteerd.</span>';

  const steps = [document.getElementById('step-1'), document.getElementById('step-2'), document.getElementById('step-3'), document.getElementById('step-4')];
  steps.forEach((s, i) => { s.classList.remove('active','done'); if (i === 0) { if (items.length) { s.classList.add('done'); steps[1]?.classList.add('active'); } else s.classList.add('active'); } });
}

document.querySelectorAll('.qty-select').forEach(s => s.addEventListener('change', updateTotaal));
document.querySelectorAll('input[name="levering"]').forEach(r => r.addEventListener('change', updateTotaal));
updateTotaal();
</script>
</body>
</html>
