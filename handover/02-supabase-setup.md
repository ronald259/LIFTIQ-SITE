# 02 — Supabase backend opzetten

Supabase is de centrale backend voor zowel de web-app, de mobiele app als de
optionele integratie op liftiqsupplements.nl. Eén project = één bron van waarheid.

## 1. Project aanmaken

1. Maak een account op [supabase.com](https://supabase.com).
2. **New project** → kies een sterke database password en de regio dichtbij
   Nederland (`West EU - Frankfurt` / `eu-central-1`).
3. Wacht ~2 minuten tot het project klaar is.

## 2. Tabellen + RLS aanmaken

Open in Supabase de **SQL Editor** en voer onderstaande bestanden uit
deze repo uit, in deze volgorde:

```
1. supabase/schema.sql         (web-app schema — alle hoofdtabellen + RLS)
2. mobile/supabase/schema.sql  (voegt personal_records-tabel toe voor PR's)
3. supabase/seed.sql           (voorbeelddata: 12 spiergroepen, 20 oefeningen, schema's)
```

> De web-schema en mobile-schema hebben overlappende tabellen; ze gebruiken
> `create table if not exists` en `drop policy if exists` om idempotent te zijn.
> Voer in deze volgorde uit en het werkt.

## 3. API-keys ophalen

Ga naar **Settings → API**. Je hebt twee keys:

| Key | Waar gebruiken? |
|---|---|
| `URL` | overal als `*_SUPABASE_URL` |
| `anon` (public) | overal als `*_SUPABASE_ANON_KEY` — veilig in client-code |
| `service_role` (secret) | **alleen** server-side (webhooks, admin-scripts) — NOOIT in browser/app |

## 4. Email/auth instellen

Ga naar **Authentication → Providers** en check dat **Email** aanstaat.

Optioneel (aanbevolen voor productie):
- **Authentication → URL Configuration**: vul je site-URL in (bijv. `https://app.liftiq.nl`)
  en de redirect-URLs (de URLs waar Supabase na inloggen naar mag terugsturen).
- **Email templates**: pas welkom/reset-mails aan naar LiftIQ huisstijl.

## 5. Eerste admin aanmaken (Dustin)

1. Registreer Dustin's account via de web-app of mobiele app.
2. Ga in Supabase naar **Table Editor → profiles**.
3. Zoek zijn rij en zet:
   - `role` → `admin`
   - `subscription_status` → `active`

Vanaf dat moment ziet Dustin de admin-modus in beide apps.

## 6. Storage (optioneel, voor video's/foto's later)

Ga naar **Storage → New bucket** → maak twee buckets aan:
- `exercise-media` (public) — voor oefening-video's en thumbnails
- `avatars` (public) — voor profielfoto's

Voeg deze RLS-policies toe als je uploads via de app wilt toestaan:

```sql
-- Iedereen mag lezen (publieke media)
create policy "public read exercise-media" on storage.objects
  for select to anon, authenticated using (bucket_id = 'exercise-media');

-- Alleen admins mogen uploaden naar exercise-media
create policy "admin write exercise-media" on storage.objects
  for insert to authenticated with check (
    bucket_id = 'exercise-media' and
    exists (select 1 from public.profiles where id = auth.uid() and role = 'admin')
  );

-- Gebruikers mogen hun eigen avatar uploaden
create policy "own avatar" on storage.objects
  for all to authenticated using (
    bucket_id = 'avatars' and (storage.foldername(name))[1] = auth.uid()::text
  );
```

## 7. Database backups

Supabase doet automatisch dagelijkse backups op de Pro-tier ($25/maand).
Voor MVP volstaat de free-tier; zet een herinnering om handmatige exports te
doen via **Database → Backups** of `pg_dump`.

## Klaar

Je Supabase-project is nu klaar. Geef de site-bouwer twee dingen:

1. `NEXT_PUBLIC_SUPABASE_URL` (of `EXPO_PUBLIC_SUPABASE_URL` voor mobiel)
2. `NEXT_PUBLIC_SUPABASE_ANON_KEY` (of `EXPO_PUBLIC_SUPABASE_ANON_KEY`)

Voor server-side acties (Stripe-webhook) ook de `service_role` key — maar
nooit in client-side code.
