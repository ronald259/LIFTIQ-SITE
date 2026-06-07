# 08 — Checklist go-live

Werkbare to-do lijst voor de site-bouwer + Dustin om van prototype naar
publieke testfase te gaan.

## Fase 1 — Backend (1-2 uur)

- [ ] Supabase project aangemaakt (regio EU)
- [ ] `supabase/schema.sql` uitgevoerd
- [ ] `mobile/supabase/schema.sql` uitgevoerd (voegt personal_records toe)
- [ ] `supabase/seed.sql` uitgevoerd
- [ ] Dustin's account aangemaakt en `role` op `admin` gezet
- [ ] Dustin's `subscription_status` op `active` gezet
- [ ] Storage buckets `exercise-media` en `avatars` aangemaakt (optioneel)
- [ ] API-keys gekopieerd naar veilige opslag (1Password/Bitwarden)

## Fase 2 — Web-app (1 uur)

- [ ] Code gepusht naar GitHub
- [ ] Vercel-project verbonden aan repo
- [ ] Environment variables ingesteld in Vercel:
  - [ ] `NEXT_PUBLIC_SUPABASE_URL`
  - [ ] `NEXT_PUBLIC_SUPABASE_ANON_KEY`
- [ ] Build geslaagd in Vercel
- [ ] Custom domein gekoppeld (bijv. `app.liftiqsupplements.nl`)
- [ ] DNS CNAME aangemaakt
- [ ] SSL-certificaat actief (automatisch via Vercel)
- [ ] Login + register getest met Dustin's account
- [ ] Admin-panel getest: schema/oefening toevoegen werkt

## Fase 3 — Hoofdsite koppeling (½-2 dagen, afhankelijk van scenario)

### Scenario A (CTA's):
- [ ] CTA-blokken uit `07-snippets/wordpress-cta.html` geplaatst
- [ ] Header van site heeft "Inloggen op Training OS" link
- [ ] Footer of menu verwijst naar `app.liftiqsupplements.nl`

### Scenario B (gedeelde auth):
- [ ] `@supabase/ssr` geïnstalleerd op de site
- [ ] Cookie-domein in Supabase Auth ingesteld op `.liftiqsupplements.nl`
- [ ] Login-form op site gebouwd met `nextjs-shared-auth.ts` snippet
- [ ] Sessie-deling getest: login op site → automatisch in app
- [ ] Logout op één plek logt overal uit

### Scenario C (subdomein):
- [ ] `app.liftiqsupplements.nl` werkt
- [ ] Huisstijl tussen site en app komt overeen
- [ ] Hoofdsite heeft prominente link in menu naar de app

## Fase 4 — Stripe (optioneel, alleen als je betaalde toegang gaat live zetten)

- [ ] Stripe account aangemaakt en geactiveerd
- [ ] Product + abonnement aangemaakt in Stripe Dashboard
- [ ] Checkout-flow op site geïntegreerd
- [ ] Bij checkout `supabase_user_id` als metadata meegegeven
- [ ] Webhook endpoint gedeployed (`07-snippets/webhook-payment.ts`)
- [ ] Stripe webhook secret ingesteld als env-var op de server
- [ ] Test: testbetaling → `subscription_status` wordt `active`

## Fase 5 — Mobiele app testfase

- [ ] `npm install` in `/mobile` succesvol
- [ ] `.env` met Supabase-keys in `/mobile/.env`
- [ ] `npx expo start` werkt lokaal
- [ ] Getest op iPhone via **Expo Go** (Dustin scant QR-code)
- [ ] Login werkt met dezelfde Supabase-account als de web-app
- [ ] PR toevoegen werkt, verschijnt ook in Supabase
- [ ] Admin-modus verschijnt bij Dustin's account

### Later (vóór App Store):
- [ ] Apple Developer-account ($99/jaar)
- [ ] `eas init` gedaan → projectId in `app.config.ts`
- [ ] EAS secrets ingesteld: `EXPO_PUBLIC_SUPABASE_URL` en `_ANON_KEY`
- [ ] `eas build --profile production --platform ios` geslaagd
- [ ] Build geüpload naar TestFlight
- [ ] Eerste 5-10 testers uitgenodigd

## Fase 6 — Communicatie

- [ ] Welkom-e-mail in Supabase aangepast naar LiftIQ huisstijl
- [ ] Privacyverklaring + algemene voorwaarden geüpdate (Supabase = gebruikersdata)
- [ ] Cookie-banner op site noemt Supabase auth-cookies
- [ ] Disclaimer "geen medische claims" zichtbaar in de app

## Fase 7 — Monitoring

- [ ] Supabase Dashboard → Logs: dagelijks even checken
- [ ] Vercel Analytics aangezet (gratis)
- [ ] Sentry/Logflare voor errors (optioneel)
- [ ] Wekelijkse backup van Supabase database (handmatig `pg_dump` of Pro-tier)

## Veelvoorkomende problemen

| Probleem | Oplossing |
|---|---|
| Build faalt op Vercel met "No Output Directory" | Framework Preset → Next.js (niet Other) |
| TypeScript build error over `mobile/` | Zorg dat `mobile/` in `tsconfig.json` excludes staat |
| Auth-cookie wordt niet gedeeld tussen site en app | Cookie-domein in Supabase Auth + `.liftiq.nl` met punt vooraan |
| `subscription_status` blijft `free` na betaling | Check Stripe webhook → `supabase_user_id` als metadata meegestuurd? |
| Admin-modus zichtbaar voor verkeerde user | Check `profiles.role` in Supabase Table Editor |

---

Met deze checklist afgewerkt ben je klaar voor de testfase. 🚀
