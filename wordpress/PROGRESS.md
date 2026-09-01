# LIFTIQ webshop — voortgang & overzicht (WordPress/WooCommerce)

Back-up en documentatie van de live webshop op **https://liftiqgear.nl**.
Laatst bijgewerkt: 1 september 2026.

---

## Hosting & domeinen
- **Host:** STRATO Hosting Advanced (contract 9967964) — PHP + MySQL.
- **Domeinen:** `liftiqgear.nl` (hoofddomein) + `liftiqgear.com`.
- **Database:** MariaDB 11.8 (`dbs16021672` / gebruiker `dbu808270`, host `rdbms.strato.de`).
- **WordPress** geïnstalleerd in webmap `/liftiqgear/` op het hoofddomein.
- **SSL:** Let's Encrypt actief + HTTPS geforceerd. WordPress-adres/Site-adres staan op `https://`.

## Platform
- **WordPress** + **WooCommerce** (vervangt de oude custom PHP-site, die staat nog in deze repo als origineel).
- **Thema:** LIFTIQ (child-thema van **Storefront**) — zie `wordpress/liftiq-theme/`.

## Actieve plugins (kern)
| Plugin | Doel |
|--------|------|
| WooCommerce | webshop |
| Mollie Payments for WooCommerce | betalingen (**iDEAL** live + creditcard/PayPal) |
| Sendcloud Shipping | verzendlabels (gekoppeld aan bestaand SendCloud-account) |
| Complianz GDPR/CCPA | AVG-conforme cookiebanner (LIFTIQ-stijl, zie `complianz-banner.css`) |
| Storefront (ouder-thema) | basis voor het LIFTIQ-thema |

**Gedeactiveerd/op te ruimen:** WF Cookie Consent (vervangen door Complianz), Novalnet, WooPayments (dubbele kassa's).

## Producten
- Geïmporteerd via `wordpress/woo-import/liftiq-producten.csv` (foto's staan in `/images/`).
- **Gepubliceerd:** 3× Pre Lift 1 (Blueberry, Strawberry Kiwi, Tropical) — €34,95.
- **Gear** (Straps €19,95 · Wraps €14,95 · Knee Sleeves €39,95) — prijzen zijn **placeholders**, bevestigen indien nodig.
- Winkelvolgorde handmatig gesorteerd (gear-rij / pre-workout-rij) via aangepaste sortering.

## Het LIFTIQ-thema (`wordpress/liftiq-theme/`)
- **v1.1.0** — donkere look, cyaan accent, Impact/Anton-koppen.
- Bevat alle checkout-/winkelwagen-styling (besteltabel, betaalmethoden, privacyblok, zijbalk verbergen) — vást in `style.css`, dus geen Extra CSS meer nodig voor de checkout.
- **Updaten:** Weergave → Thema's → Nieuw thema toevoegen → Thema uploaden → "Vervang huidige met geüploade". Logo/menu/instellingen blijven behouden (staan in de database).

## Checkout
- **Klassieke** WooCommerce-checkout (shortcode `[woocommerce_checkout]`) op de Afrekenpagina (ID 9) — betrouwbaarder donker te stylen dan het blok.
- Betaalmethoden via Mollie: **iDEAL** (belangrijkste voor NL) + creditcard + PayPal.

## Beheer voor Dustin (mobiel)
- Gratis **WooCommerce mobiele app** (iOS/Android) → orders, prijzen, voorraad, pushmelding bij nieuwe bestelling.
- **SendCloud-app** → verzendlabels printen.

## Nog te doen / optioneel
- [ ] 1 complete testbetaling (iDEAL) → order + SendCloud + mail bevestigen → refunden in Mollie.
- [ ] Echte **gear-prijzen** bevestigen en gear-producten definitief publiceren.
- [ ] Afgebroken testorders opruimen (WooCommerce → Bestellingen).
- [ ] "Hello world"-bericht + Sample Page verwijderen.

---

*Geheime sleutels (Mollie/SendCloud/DB-wachtwoord) staan NIET in deze repo — die zitten in de WordPress-/plugin-instellingen op de server.*
