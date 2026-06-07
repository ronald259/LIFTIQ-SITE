# 01 — Architectuur overzicht

## De drie lagen

```
┌─────────────────────────────────────────────────────────────────┐
│                    liftiqsupplements.nl                         │
│                  (bestaande hoofdsite / shop)                   │
│                                                                 │
│   • Marketing, supplementen-shop, brand                         │
│   • Bevat CTA's en links naar Training OS                       │
│   • Stripe/checkout voor abonnementen                           │
└──────────────────────┬──────────────────────────────────────────┘
                       │
                       │  link / SSO / iframe / subdomein
                       ▼
┌─────────────────────────────────────────────────────────────────┐
│              LiftIQ Training OS (deze repo)                     │
│                                                                 │
│   ┌────────────────────┐         ┌────────────────────┐         │
│   │   Web-app          │         │   Mobiele app      │         │
│   │   (Next.js)        │         │   (Expo / iOS)     │         │
│   │   trainingos.      │         │   App Store        │         │
│   │   liftiq.nl        │         │   (later)          │         │
│   └─────────┬──────────┘         └─────────┬──────────┘         │
│             │                              │                    │
│             └──────────────┬───────────────┘                    │
└────────────────────────────┼────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Supabase (gedeeld)                           │
│                                                                 │
│   • Auth (e-mail/wachtwoord, social login uitbreidbaar)         │
│   • PostgreSQL met tabellen + RLS                               │
│   • Storage voor afbeeldingen/video's                           │
│   • Edge Functions (optioneel: Stripe webhooks, e-mails)        │
└─────────────────────────────────────────────────────────────────┘
```

## Wie deelt wat?

| | Web-app | Mobiele app | Hoofdsite |
|---|---|---|---|
| **Gebruikersaccounts** | ✓ | ✓ | optioneel (SSO) |
| **Trainingsschema's** | ✓ | ✓ | preview/CTA |
| **Eetschema's** | ✓ | ✓ | preview/CTA |
| **Oefeningen** | ✓ | ✓ | demo blokken |
| **PR's / voortgang** | ✓ | ✓ | — |
| **Admin (Dustin)** | ✓ | ✓ | — |
| **Stripe-abonnement** | leesbaar | leesbaar | **beheer** |

De hoofdsite kan zelf de Stripe-checkout regelen en via een webhook de
`subscription_status` in Supabase bijwerken. Daarna ziet de gebruiker
automatisch z'n premium-inhoud in zowel web-app als mobiele app.

## Drie integratie-scenario's

Welke past bij liftiqsupplements.nl? Zie `03-site-integratie.md` voor details.

| Scenario | Voor wie | Effort |
|---|---|---|
| **A. Losse CTA-knoppen** | Snelste start. Site linkt naar Training OS, account wordt daar aangemaakt. | 1 uur |
| **B. Gedeelde Supabase + SSO** | Eén account werkt op site én app. Sessie loopt door. | 1-2 dagen |
| **C. Embed via subdomein** | Training OS op `app.liftiqsupplements.nl`, cookie-deling, één huisstijl. | 1 dag |

Aanbevolen voor MVP: **A** of **C**. Voor lange termijn: **B**.

## Belangrijke domein-keuzes

Maak vóór go-live een keuze:

- **Subdomein voor de web-app** — bijvoorbeeld `app.liftiq.nl` of `trainingos.liftiqsupplements.nl`
- **Custom URL scheme voor de mobiele app** — nu `liftiq://`, voor deeplinks
- **Universal Links / App Links** — als je vanaf de site naar de app wilt openen op iOS

Zie `06-deeplinks-en-domein.md` voor de instellingen.
