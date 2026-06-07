# 04 — Environment variables (alles op één plek)

## Web-app (`/`) — Next.js → Vercel

```env
# Supabase
NEXT_PUBLIC_SUPABASE_URL=https://xxxxxxxxxxxx.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJhbGciOi...

# Optioneel — alleen voor server-side acties (Stripe webhook)
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOi...   # NIET in client-code
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

**Instellen in Vercel:** Settings → Environment Variables → toevoegen voor
Production, Preview, Development.

## Mobiele app (`/mobile`) — Expo

```env
# Supabase — moeten met EXPO_PUBLIC_ prefix beginnen
EXPO_PUBLIC_SUPABASE_URL=https://xxxxxxxxxxxx.supabase.co
EXPO_PUBLIC_SUPABASE_ANON_KEY=eyJhbGciOi...
```

**Instellen voor lokaal ontwikkelen:** maak een `.env` bestand in `/mobile`.
**Instellen voor EAS builds:**

```bash
cd mobile
eas secret:create --name EXPO_PUBLIC_SUPABASE_URL --value "https://xxxxxxxxxxxx.supabase.co"
eas secret:create --name EXPO_PUBLIC_SUPABASE_ANON_KEY --value "eyJhbGciOi..."
```

## Hoofdsite (liftiqsupplements.nl)

Hangt af van wat je gekozen scenario is:

| Scenario | Welke vars? |
|---|---|
| A. Losse CTA's | geen — alleen URLs naar de app |
| B. Gedeelde auth | `NEXT_PUBLIC_SUPABASE_URL`, `NEXT_PUBLIC_SUPABASE_ANON_KEY` |
| C. Subdomein | geen — alleen DNS-aanpassingen |

Bij scenario B ook server-side voor de Stripe-webhook:
```env
SUPABASE_SERVICE_ROLE_KEY=...   # om subscription_status te updaten
STRIPE_SECRET_KEY=...
STRIPE_WEBHOOK_SECRET=...
```

## Veiligheid — wat WEL en NIET in de client

| Var | Browser? | Mobile app? | Server only? |
|---|---|---|---|
| `*_SUPABASE_URL` | ✅ | ✅ | ✅ |
| `*_SUPABASE_ANON_KEY` | ✅ | ✅ | ✅ |
| `SUPABASE_SERVICE_ROLE_KEY` | ❌ NOOIT | ❌ NOOIT | ✅ |
| `STRIPE_SECRET_KEY` | ❌ NOOIT | ❌ NOOIT | ✅ |
| `STRIPE_WEBHOOK_SECRET` | ❌ NOOIT | ❌ NOOIT | ✅ |

De `anon` key is veilig in client-code — RLS-policies in Supabase beperken
wat een ingelogde gebruiker mag zien/doen. De `service_role` key omzeilt RLS
en mag absoluut nooit in de browser of mobiele app komen.
