# 06 — Deeplinks en domein-instellingen

## Web-app subdomein

Voorgestelde URL-structuur:

| URL | Wat | Hosting |
|---|---|---|
| `liftiqsupplements.nl` | Hoofdsite + shop | bestaande site |
| `app.liftiqsupplements.nl` | Training OS web-app | Vercel |
| (alternatief: `app.liftiq.nl` als je een nieuw merk-domein wilt) | | |

### DNS-instellingen voor het subdomein

In je DNS-provider (bijv. Cloudflare/TransIP/etc.):

```
Type:  CNAME
Name:  app
Value: cname.vercel-dns.com
TTL:   automatisch
```

Of als je hosting het wenst:
```
Type:  A
Name:  app
Value: 76.76.21.21
```

Vervolgens in Vercel: **Project → Settings → Domains → Add** →
`app.liftiqsupplements.nl`. Vercel regelt SSL automatisch.

## Mobiele app deeplinks

De mobiele app heeft een custom URL scheme `liftiq://` geconfigureerd in
`mobile/app.config.ts`. Hiermee kun je vanaf de site naar specifieke
schermen in de app linken (als de app geïnstalleerd is).

| Link | Opent in app |
|---|---|
| `liftiq://` | Vandaag-tab |
| `liftiq:///programs` | Schema's-tab |
| `liftiq:///exercises` | Oefeningen-tab |
| `liftiq:///exercise/bench-press` | Detail oefening |
| `liftiq:///program/<id>` | Detail schema |

### Universal Links (iOS) — later, vereist Apple Dev account

Voor een echt naadloze ervaring kan een link op `app.liftiqsupplements.nl/...`
automatisch de geïnstalleerde app openen. Dit vereist:

1. Apple Developer account (Team ID nodig).
2. Een `apple-app-site-association` bestand op de site:
   ```json
   {
     "applinks": {
       "apps": [],
       "details": [{
         "appID": "TEAMID.nl.liftiq.trainingos",
         "paths": ["/programs/*", "/exercises/*"]
       }]
     }
   }
   ```
   Plaats op: `https://app.liftiqsupplements.nl/.well-known/apple-app-site-association`
   (zonder `.json` extensie, served als `application/json`).

3. In `mobile/app.config.ts` toevoegen:
   ```ts
   ios: {
     associatedDomains: ['applinks:app.liftiqsupplements.nl'],
   },
   ```

4. App opnieuw builden met `eas build --profile production --platform ios`.

### Android App Links — later

Vergelijkbaar met iOS, vereist een `assetlinks.json` op de site.
Voor MVP niet nodig — de `liftiq://` scheme volstaat tijdens TestFlight-fase.

## CORS-instellingen in Supabase

Standaard staat Supabase open voor alle origins. Voor extra zekerheid in
productie kun je in **Authentication → URL Configuration** de toegestane
redirect-URLs vastpinnen:

```
Site URL: https://app.liftiqsupplements.nl
Additional Redirect URLs:
  https://liftiqsupplements.nl/**
  https://app.liftiqsupplements.nl/**
  liftiq://**
```

Zo werken zowel web-redirects als deeplinks vanuit de mobiele app na login.
