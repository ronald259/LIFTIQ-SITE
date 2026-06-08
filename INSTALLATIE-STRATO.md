# LIFTIQ webshop — installeren op Strato

Korte handleiding om de webshop live te zetten op **liftiqgear.nl** (Strato-hosting).

## 1. Sleutels invullen
1. Hernoem `config.php.example` naar **`config.php`**.
2. Open `config.php` en vul in:
   - **MOLLIE_API_KEY** — Mollie-dashboard → Developers → API-keys (live key).
   - **SENDCLOUD_PUBLIC_KEY** en **SENDCLOUD_SECRET_KEY** — SendCloud-paneel →
     Instellingen → Integraties → *Sendcloud API* (Public + Secret key).
   - **SITE_URL** — staat al goed op `https://liftiqgear.nl`.

> `config.php` bevat je geheime sleutels. Upload 'm wél naar de server, maar deel
> 'm nooit en zet 'm niet in Git (staat al in `.gitignore`).

## 2. Uploaden naar Strato
Upload **alle** bestanden uit deze map naar de webroot van liftiqgear.nl
(bij Strato meestal de map die aan het domein gekoppeld is, bijv. de
hoofdmap of een submap). Gebruik FTP (FileZilla) of Strato Bestandsbeheer.

**Let op — neem de verborgen bestanden mee:**
- `.htaccess` (in de hoofdmap) — beschermt sleutels en logs.
- `logs/.htaccess` — houdt de logmap privé.

## 3. Serverinstellingen (Strato-paneel)
- **PHP-versie:** zet op **8.0 of hoger** (PHP-instellingen in het Strato-paneel).
- **Map `logs/` schrijfbaar:** PHP schrijft hierin order- en webhook-logs.
  Meestal automatisch goed; lukt het niet, zet de rechten op `755`.

## 4. E-mail
Maak de mailbox **info@liftiqgear.nl** aan in het Strato-paneel (anders komen
klant-/contactmails niet aan).

## 5. Domein / DNS
Zorg dat **liftiqgear.nl** naar je Strato-webhosting wijst en als (hoofd)domein
gekoppeld is. Dit is nodig zodat Mollie de webhook
`https://liftiqgear.nl/webhook.php` publiek kan bereiken.

## 6. Testen
1. Open `https://liftiqgear.nl` → site laadt.
2. Plaats een testbestelling via `bestellen.php`.
   - Tip: gebruik tijdelijk een **Mollie test-API-key** om zonder echte betaling
     de flow te testen.
3. Na "betaald": de order verschijnt automatisch in **SendCloud** → label printen
   in de SendCloud-app.
4. Controleer bij twijfel de logs in de map `logs/` (`orders.log`, `webhook.log`).

## Hoe het werkt
```
Klant bestelt  →  betaalt via Mollie (iDEAL)
   →  Mollie roept webhook.php aan
   →  webhook.php checkt bij Mollie of het écht betaald is
   →  order wordt aangemeld bij SendCloud
   →  Dustin print het label vanaf zijn telefoon
```

## Let op: ontbrekende pagina
De menubalk bevat een link **"Gear" → `gear.html`**, maar dat bestand bestaat nog
niet (geeft een 404). Laat `gear.html` maken, of laat de Gear-link verwijderen/
omleiden.
