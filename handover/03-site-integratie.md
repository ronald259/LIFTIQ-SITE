# 03 — Hoofdsite koppelen (3 scenario's)

Hieronder drie manieren om liftiqsupplements.nl met Training OS te verbinden,
gerangschikt van eenvoudig naar diep geïntegreerd.

## Scenario A — Losse CTA-knoppen (snelste start, 1 uur)

De hoofdsite blijft helemaal los van Training OS. Op de site staan CTA's die
linken naar de Training OS web-app (op een eigen URL of subdomein).

**Stappen:**

1. Deploy de web-app naar Vercel (zie hoofd-README) op bijv. `app.liftiq.nl`.
2. Voeg op de site CTA-blokken toe die linken naar:
   - `/register` — nieuwe gebruikers
   - `/login` — bestaande gebruikers
   - `/muscle-groups` — publieke oefeningenbibliotheek (geen login)

**Voorbeeld HTML voor een WordPress/static block** — zie `07-snippets/wordpress-cta.html`.

**Voordeel:** geen technische integratie nodig.
**Nadeel:** gebruiker moet apart inloggen op de site én op Training OS.

---

## Scenario B — Gedeelde Supabase + SSO (volledige integratie, 1-2 dagen)

De hoofdsite gebruikt **dezelfde Supabase** voor auth. Eén account werkt
overal. Wanneer iemand inlogt op de site is hij/zij automatisch ingelogd op
Training OS (en vice versa), via gedeelde cookies/session.

**Vereisten:**
- Site en Training OS draaien op subdomeinen van hetzelfde root-domein
  (`liftiq.nl` en `app.liftiq.nl`, of `liftiqsupplements.nl` en `app.liftiqsupplements.nl`).
- Site moet eigen login/register UI hebben die via Supabase SDK werkt.

**Stappen:**

1. Installeer in de site-codebase:
   ```bash
   npm install @supabase/supabase-js @supabase/ssr
   ```

2. Voeg env-vars toe (dezelfde als de app):
   ```env
   NEXT_PUBLIC_SUPABASE_URL=https://....supabase.co
   NEXT_PUBLIC_SUPABASE_ANON_KEY=...
   ```

3. Maak een gedeelde Supabase client — zie `07-snippets/nextjs-shared-auth.ts`.

4. Configureer in Supabase **Settings → Auth → Cookies**:
   - **Cookie domain**: `.liftiq.nl` (met punt vooraan voor subdomein-deling)
   - **Site URL**: `https://www.liftiq.nl`
   - **Redirect URLs**: voeg `https://app.liftiq.nl/auth/callback` toe

5. Gebruik op de site `supabase.auth.signInWithPassword()` voor login.
   Na succes wordt de sessie-cookie gezet voor `.liftiq.nl` en gedeeld met de app.

**Voordeel:** seamless ervaring, één account voor alles.
**Nadeel:** site moet zelf auth-UI bouwen + correcte cookies/CSRF.

---

## Scenario C — Embed via subdomein (1 dag)

Training OS draait op een subdomein van de hoofdsite zelf
(`app.liftiqsupplements.nl`), met identieke huisstijl. Gebruikers gaan vanaf
de site direct door naar de app-omgeving die voelt als één geheel.

**Stappen:**

1. Deploy de web-app naar Vercel.
2. Voeg in Vercel onder **Settings → Domains** het domein
   `app.liftiqsupplements.nl` toe.
3. Maak in de DNS van liftiqsupplements.nl een **CNAME** aan:
   ```
   app   →   cname.vercel-dns.com
   ```
4. Pas de huisstijl van Training OS aan zodat hij naadloos overgaat in de
   hoofdsite (lettertype, logo, navigatiebalk identiek).
5. Voeg in de header van de hoofdsite een link toe naar `app.liftiqsupplements.nl`.

**Voordeel:** voelt als één merk, geen aparte codebase nodig.
**Nadeel:** gebruiker moet wel apart inloggen tenzij je dit met scenario B combineert.

---

## Beste combinatie voor LiftIQ

**Aanbevolen pad voor productie:**

1. **Nu (MVP):** scenario C — subdomein + CTA's. Snelste manier om gebruikers
   van site naar app te krijgen. Inloggen is een aparte stap maar nog
   acceptabel in een testfase.
2. **Bij groei:** voeg scenario B toe — gedeelde Supabase auth zodat het
   één account wordt. Vereist iets meer werk maar geeft de premium ervaring.
3. **Mobiele app:** verwijst naar dezelfde Supabase. Gebruikers loggen op
   hun telefoon in met hetzelfde account dat ze op de site hebben aangemaakt.

## Stripe-abonnementen op de site

Als de hoofdsite het abonnement verkoopt, gebruik dan een webhook om de
`subscription_status` in Supabase te updaten. Zie `07-snippets/webhook-payment.ts`.

Flow:
```
Klant koopt op liftiqsupplements.nl
       ↓
Stripe maakt subscription aan
       ↓
Stripe → webhook naar je site/server
       ↓
Server update profiles.subscription_status = 'active' in Supabase
       ↓
Klant ziet onmiddellijk premium-inhoud in web-app en mobiele app
```
