# LIFTIQ design-system → LiftIQ-APP integratie

Stappen om de gedeelde LIFTIQ look & feel in de training-app toe te passen.
De app = **Next.js 15 + TypeScript + Tailwind** (web, `/src`) en **Expo / React
Native** (mobiel, `/mobile`). Beide delen dezelfde tokens.

Kopieer deze bestanden uit `design-system/` naar de app:

| Bestand | Bestemming (suggestie) |
|---------|------------------------|
| `liftiq-tailwind-preset.js` | repo-root van de web-app |
| `liftiq-tokens.ts` | `src/lib/theme.ts` (web) en/of `mobile/lib/theme.ts` |
| `liftiq-theme.css` | optioneel, voor losse CSS |

---

## A. Web-app (Next.js + Tailwind)

**1. Preset koppelen** in `tailwind.config.js`/`.ts`:

```js
module.exports = {
  presets: [require('./liftiq-tailwind-preset.js')],
  content: ['./src/**/*.{ts,tsx}'],
};
```

**2. Basis-stijl** in `src/app/globals.css` (of `globals.css`):

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

:root { color-scheme: dark; }

body {
  @apply bg-liftiq-dark text-liftiq-white font-sans antialiased;
}

/* Koppen in de merkstijl */
.h-display { @apply font-display uppercase tracking-display leading-none; }
.h-display .accent { @apply text-liftiq-blue; }
.eyebrow { @apply text-liftiq-blue text-eyebrow font-bold uppercase; }
```

> Impact is een systeemfont — geen `@font-face` nodig. Wil je 100% consistente
> rendering op alle apparaten, kies dan een vergelijkbare webfont (bijv.
> *Anton* of *Oswald* via `next/font`) en zet die op `fontFamily.display`.

**3. Gebruik in componenten:**

```tsx
// Knop (primair)
<button className="bg-liftiq-blue text-liftiq-dark font-bold uppercase tracking-button px-9 py-4 rounded-none transition-colors hover:bg-liftiq-blue-dark hover:text-white">
  Start workout
</button>

// Kaart
<div className="bg-liftiq-dark-2 border border-liftiq rounded-none transition hover:border-liftiq-blue hover:shadow-liftiq-glow">
  ...
</div>

// Sectiekop
<p className="eyebrow">Vandaag</p>
<h2 className="h-display text-liftiq-h2">Jouw <span className="accent">schema</span></h2>

// Progress bar (accent op donker)
<div className="h-1 bg-liftiq-dark-3">
  <div className="h-1 bg-liftiq-blue" style={{ width: '64%' }} />
</div>
```

**Mapping-richtlijn (training-app):**
- Primaire actie ("Start workout") → cyaan knop, donkere tekst.
- Tab-bar actief → `text-liftiq-blue`; inactief → `text-white/50`.
- Workout-/oefeningkaarten → `bg-liftiq-dark-2` + `border-liftiq` + hover-glow.
- Stats/voortgang → cyaan accent op `dark-3`.
- Alles met **rechte hoeken** (`rounded-none`) en `transition-colors`.

---

## B. Mobiele app (Expo / React Native)

React Native gebruikt geen CSS. Twee opties:

**Optie 1 — tokens-object** (`mobile/lib/theme.ts` = kopie van `liftiq-tokens.ts`):

```tsx
import { colors, fonts } from '../lib/theme';

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: colors.dark },
  title: { color: colors.white, fontFamily: 'Anton_400Regular', textTransform: 'uppercase' },
  button: { backgroundColor: colors.blue, borderRadius: 0, paddingVertical: 14 },
  buttonText: { color: colors.dark, fontWeight: '700', letterSpacing: 1.5, textTransform: 'uppercase' },
  card: { backgroundColor: colors.dark2, borderWidth: 1, borderColor: colors.border, borderRadius: 0 },
});
```

> Impact bestaat niet op iOS/Android. Laad een vervangende display-font via
> `expo-font` / `@expo-google-fonts/anton` (Anton lijkt sterk op Impact) en
> gebruik die als `fontFamily.display`.

**Optie 2 — NativeWind:** als de app NativeWind gebruikt, koppel dezelfde
`liftiq-tailwind-preset.js` in de NativeWind-`tailwind.config.js` en gebruik
exact dezelfde classNames als de web-app.

---

## C. Resultaat

Eén bron van waarheid voor kleur, type, vorm en effecten. De webshop
(`LIFTIQ-SITE`) en de training-app (`LiftIQ-APP`) ogen als één merk:
diepzwart, elektrische cyaan accent, vette uppercase koppen, rechte hoeken,
subtiele glow en transitions.

> **Wijziging in de huisstijl?** Pas alleen `liftiq-tokens.ts` /
> `liftiq-tailwind-preset.js` aan en kopieer naar beide repo's. Overweeg op
> termijn een gedeeld npm-package (`@liftiq/design-system`) zodat één update
> automatisch overal landt.
