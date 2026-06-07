# 05 — Rollen, rechten en abonnementen

## Rollen

In `profiles.role`:

| Rol | Mag wat |
|---|---|
| `member` | Eigen profiel + eigen schema's, PR's, voortgang zien |
| `admin` | Alles + alle gebruikers + content beheren |

**Iemand admin maken:** Supabase → Table Editor → profiles → zoek user →
zet `role` op `admin`. Werkt direct in zowel web als mobiele app.

## Abonnementsstatussen

In `profiles.subscription_status`:

| Status | Betekenis | Wat ziet gebruiker |
|---|---|---|
| `free` | Geen actief abonnement | Preview + CTA om premium te activeren |
| `active` | Betalende klant | Volledig schema, eetschema, voortgang |
| `paused` | Tijdelijk gepauzeerd | Beperkte toegang, CTA om te hervatten |
| `cancelled` | Opgezegd | Read-only naar oude data |

De web-app en mobiele app checken al op `subscription_status === 'active'`
om premium-inhoud te tonen. Dustin kan handmatig wijzigen, of de site
zet het automatisch via een Stripe-webhook (zie `07-snippets/webhook-payment.ts`).

## Row Level Security (RLS) — kort overzicht

| Tabel | Wie mag lezen | Wie mag schrijven |
|---|---|---|
| `profiles` | Eigenaar + admin | Eigenaar + admin |
| `muscle_groups` | Iedereen | Admin |
| `exercises` | Iedereen | Admin |
| `training_programs` | Iedereen | Admin |
| `program_days` | Iedereen | Admin |
| `program_exercises` | Iedereen | Admin |
| `nutrition_plans` | Iedereen | Admin |
| `user_programs` | Eigenaar + admin | Admin |
| `progress_logs` | Eigenaar + admin | Eigenaar |
| `personal_records` | Eigenaar + admin | Eigenaar |
| `user_questions` | Eigenaar + admin | Eigenaar (insert) + admin (update) |

Dit betekent: de `anon` key in de client-code is veilig. Een gewone gebruiker
kan via die key NOOIT andermans PR's of voortgang opvragen.

## Premium-content gating in code

Voorbeeld (web-app, server component):

```tsx
const { data: profile } = await supabase
  .from('profiles')
  .select('subscription_status')
  .eq('id', user.id)
  .single();

const isPremium = profile?.subscription_status === 'active';

return isPremium ? <VolledigSchema /> : <PreviewMetCTA />;
```

Voorbeeld (mobiele app):

```tsx
const { profile } = useAuth();
const isPremium = profile?.subscription_status === 'active';
```

## Hoe Dustin een schema aan iemand koppelt

1. Open de admin-modus (web: `/admin/users` of mobile: `/admin/users`).
2. Klik op de gebruiker.
3. "Schema toewijzen" → kies een `training_program` en optioneel een `nutrition_plan`.
4. Optioneel: voeg een `personal_notes` toe voor die gebruiker.
5. Dat maakt een rij aan in `user_programs` met `active = true`.

De gebruiker ziet de koppeling direct in zijn dashboard.

## Veelgestelde vragen

**Wat als iemand zelf admin probeert te worden?**
De RLS staat alleen toe dat admins andere profielen kunnen aanpassen. Een
gewone user kan zijn eigen `role` NIET op `admin` zetten (de RLS-policy filtert
op `role` in de check).

**Kan een member andermans PR's zien?**
Nee. De `own prs` policy filtert op `auth.uid() = user_id`.

**Wat als ik later meer rollen wil (zoals `coach`)?**
Update de `role` check-constraint in `profiles` en pas de RLS-policies aan.
