# LIFTIQ webshop — live zetten op STRATO

Voor jouw setup: **STRATO Hosting Advanced** (contract 9967964) met de domeinen
**liftiqgear.nl** (hoofddomein) en **liftiqgear.com** (doorverwijzing).

---

## 1. Domein instellen (STRATO-paneel)
1. **Mijn account → Pakketten → STRATO Hosting Advanced → Domeinen.**
2. Zet **`liftiqgear.nl`** als **hoofddomein** dat naar de webruimte-hoofdmap wijst
   (bij Strato meestal de map die aan het pakket hangt).
3. Zet **`liftiqgear.com`** op **doorsturen (redirect) naar `https://liftiqgear.nl`**
   — of laat 'm naar dezelfde map wijzen als je beide wilt laten werken.

## 2. PHP-versie
- **PHP-instellingen** → zet op **PHP 8.0 of hoger**.

## 3. SSL (https)
- **SSL** → activeer het gratis **Let's Encrypt**-certificaat voor beide domeinen.
  De hele webshop draait op `https://` (nodig voor Mollie/SendCloud).

## 4. Bestanden uploaden
Upload **alle** bestanden uit deze map naar de **webroot** van liftiqgear.nl
(FTP met FileZilla, of STRATO Bestandsbeheer).

**Neem de verborgen bestanden mee:**
- `.htaccess` (hoofdmap) — beschermt sleutels en logs.
- `logs/.htaccess` — houdt de logmap privé.

FTP-gegevens vind je in het paneel onder **Toegang / FTP**.

## 5. Sleutels invullen
1. Hernoem `config.php.example` → **`config.php`**.
2. Vul in:
   - **MOLLIE_API_KEY** — Mollie-dashboard → Developers → API-keys (live key).
   - **SENDCLOUD_PUBLIC_KEY** + **SENDCLOUD_SECRET_KEY** — SendCloud-paneel →
     Instellingen → Integraties → *Sendcloud API*.
   - **SITE_URL** — staat al goed op `https://liftiqgear.nl`.

> `config.php` bevat je geheime sleutels — upload 'm wél, maar deel 'm nooit
> (staat in `.gitignore`, komt niet in Git).

## 6. E-mail
- Maak de mailbox **info@liftiqgear.nl** aan (STRATO-paneel → E-mail).

## 7. Logmap schrijfbaar
- De map **`logs/`** moet schrijfbaar zijn (order-/webhook-logs). Meestal
  automatisch goed; lukt het niet, zet de rechten op `755`.

## 8. Testen
1. Open `https://liftiqgear.nl` → site laadt met slotje (SSL).
2. Homepage → **In winkelmand** → **Ga door naar betalen** → op `bestellen.php`
   staan de aantallen ingevuld + de upsell "Bestel dit erbij".
3. Reken af — gebruik tijdelijk een **Mollie test-API-key** om zonder echte
   betaling te testen. Na "betaald" verschijnt de order in **SendCloud**.
4. Bij twijfel: check `logs/orders.log` en `logs/webhook.log`.

## Hoe het werkt
```
Klant bestelt  →  betaalt via Mollie (iDEAL)
   →  Mollie roept webhook.php aan
   →  webhook.php checkt bij Mollie of het écht betaald is
   →  order wordt aangemeld bij SendCloud
   →  Dustin print het label vanaf zijn telefoon
```

## Let op
- **Gear-prijzen** in `bestellen.php` zijn voorlopige placeholders
  (straps €19,95 · wraps €14,95 · sleeves €39,95) — bevestig vóór go-live.
- De menubalk-link **"Gear"** in `bestellen.php` verwijst nog naar `gear.html`
  (bestaat niet). Laat 'm weghalen of maken indien nodig.
