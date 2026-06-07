# LiftIQ Training OS — Overdrachtspakket

Dit pakket bevat alles wat nodig is om **liftiqsupplements.nl** (of een andere
website/landingpage) te koppelen aan het LiftIQ Training OS platform.

Het platform bestaat uit drie onderdelen die allemaal dezelfde Supabase-backend
delen:

| Onderdeel | Locatie in deze repo | Stack |
|-----------|----------------------|-------|
| **Web-app** (Training OS) | `/src` (Next.js 15) | Next.js · TypeScript · Tailwind · Supabase |
| **Mobiele app** | `/mobile` | Expo · React Native · TypeScript · Supabase |
| **Gedeelde backend** | Supabase project | PostgreSQL · Auth · Storage |

## Inhoud van dit pakket

```
handover/
├── README.md                     # dit bestand
├── 01-overzicht.md               # architectuur en hoe alles samenwerkt
├── 02-supabase-setup.md          # backend opzetten (volledige uitleg)
├── 03-site-integratie.md         # de hoofdsite koppelen (3 scenario's)
├── 04-environment-vars.md        # alle env-variabelen op één plek
├── 05-rollen-en-rechten.md       # admin/member rollen, RLS, abonnementen
├── 06-deeplinks-en-domein.md     # subdomein, deeplinks naar de app
├── 07-snippets/                  # kant-en-klare code-snippets
│   ├── nextjs-shared-auth.ts     # gedeelde auth tussen sites
│   ├── wordpress-cta.html        # CTA-blok voor WordPress/static sites
│   ├── login-redirect.html       # SSO-style redirect-pagina
│   └── webhook-payment.ts        # Stripe → subscription_status koppeling
└── 08-checklist-go-live.md       # stappenplan voor live-gang
```

## Snel starten

1. Lees `01-overzicht.md` voor de big picture.
2. Volg `02-supabase-setup.md` om de backend te delen tussen site en app.
3. Kies in `03-site-integratie.md` het scenario dat past bij liftiqsupplements.nl.
4. Werk de checklist in `08-checklist-go-live.md` af.

## Contact

Vragen tijdens overdracht? De volledige codebase staat in deze GitHub repo:
[ronald259/LiftIQ-APP](https://github.com/ronald259/LiftIQ-APP).
