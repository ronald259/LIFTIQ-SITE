# LIFTIQ — Design System

De gedeelde merkidentiteit voor alle LIFTIQ-producten: de webshop (`LIFTIQ-SITE`)
én de training-app (`LiftIQ-APP`). Doel: één herkenbare look & feel, ongeacht de branche.

> **Karakter:** donker, high-contrast, "hardcore gym". Eén elektrische cyaan-accentkleur
> op diepzwart, vette condensed kapitalen, strakke rechte hoeken, subtiele beweging
> (parallax, grain, fade-in). Premium maar rauw — geen ronde hoeken, geen pastel.

---

## 1. Kleuren

| Token | Hex / waarde | Gebruik |
|-------|--------------|---------|
| `blue` | `#00CFFF` | **Primaire accent.** Knoppen, links, accenten, iconen, actieve states |
| `blue-dark` | `#0099CC` | Hover op accent, gradient-diepte |
| `dark` | `#0a0a0a` | Paginakleur / app-achtergrond |
| `dark-2` | `#111111` | Secties, kaarten, footer, panelen |
| `dark-3` | `#1a1a1a` | Inputs, dropdowns, genest oppervlak |
| `dark-4` | `#222222` | Randen/hover op donkere vlakken |
| `white` | `#ffffff` | Koppen, primaire tekst |
| `grey` | `#888888` | Gedempte tekst |
| `grey-light` | `#cccccc` | Secundaire tekst |
| `text-muted` | `rgba(255,255,255,0.5)` | Bijschriften, hints, omschrijvingen |
| `border` | `rgba(0,207,255,0.15)` | Standaard rand (subtiele cyaan gloed) |
| `border-strong` | `rgba(0,207,255,0.4)` | Nadruk-rand, focus |

**Regels**
- Eén accentkleur. Cyaan `#00CFFF` is de enige "kleur"; al het andere is zwart/wit/grijs.
- Randen zijn nooit grijs maar **transparant cyaan** (`border`) — dat geeft de subtiele gloed.
- Tekst op donker: wit voor koppen, `0.7` opacity voor body, `0.5` voor muted.
- Accent-knoppen hebben **donkere tekst** (`dark`) op cyaan — niet wit.

**Signature gradients & gloed**
- Hero/overlay: `linear-gradient(105deg, rgba(10,10,10,0.85) 0%, rgba(10,10,10,0.5) 60%, rgba(0,207,255,0.05) 100%)`
- Sectie-gloed: `radial-gradient(ellipse, rgba(0,207,255,0.06–0.08) 0%, transparent 65–70%)`
- Hover-glow op kaarten: `box-shadow: 0 0 40px rgba(0,207,255,0.15), 0 20px 60px rgba(0,0,0,0.5)`

---

## 2. Typografie

| Rol | Font | Stijl |
|-----|------|-------|
| Display / koppen | `Impact, 'Arial Narrow', sans-serif` | UPPERCASE, `letter-spacing: 0.04–0.08em`, `line-height: 0.95–1` |
| Body / UI | `'Helvetica Neue', Arial, sans-serif` | normaal, `line-height: 1.7–1.9` |
| Eyebrow / label | body | 9–11px, `700`, `letter-spacing: 0.25–0.4em`, UPPERCASE, kleur `blue` |

**Schaal (responsive via `clamp`)**
- Hero-kop: `clamp(52px, 8vw, 96px)`
- Sectiekop (H2): `clamp(36px, 5vw, 64px)`
- Kaart-titel: `22px` Impact
- Body: `15–16px` · Klein/muted: `12–13px` · Eyebrow: `10px`

**Patroon:** elke sectie opent met een cyaan **eyebrow** (klein, gespatieerd, uppercase),
daaronder een grote Impact-kop, vaak met één woord in `color: blue` als highlight,
gevolgd door een **blue-line** (48×2px cyaan streepje) als scheiding.

---

## 3. Spacing, vorm & layout

- **Rechte hoeken overal** — `border-radius: 0`. Dit is een kernkenmerk.
- **Border:** standaard `1px solid var(--border)`.
- **Sectiepadding:** royaal — `6–8rem` verticaal op desktop, `4–5rem` mobiel.
- **Containers:** `max-width` 1100–1200px, gecentreerd; content-pagina's 820px.
- **Grid:** kaarten in `repeat(3, 1fr)` desktop → `1fr` mobiel; USP `repeat(4,1fr)` → `repeat(2,1fr)`.
- **Spacing-ritme:** veelvouden van `0.5rem` (8px). Gaps 1–2rem.

---

## 4. Componenten

### Navigatie
Fixed, 68px hoog, `rgba(10,10,10,0.92)` + `backdrop-filter: blur(16px)`, onderrand `border`.
Bij scrollen (`> 60px`): donkerder + schaduw (`.scrolled`). Logo links, links rechts in
uppercase 12px. CTA-link = gevulde cyaan knop. Mobiel: hamburger → uitklapmenu.

### Knoppen
- **Primair** (`.btn-blue`): cyaan vlak, donkere tekst, uppercase 12px/700, padding `15×36px`,
  hover → `blue-dark` + `translateY(-1px)`.
- **Outline** (`.btn-outline`): transparant, cyaan rand + tekst, hover → vult cyaan.
- Geen ronde hoeken.

### Kaart
`dark-2` achtergrond, `border`. Hover: rand → cyaan + glow + lichte **3D-tilt**
(`perspective(800px) rotateY/rotateX ~8deg`). Afbeelding `aspect-ratio: 3/4`, `object-fit: cover`,
hover-zoom `scale(1.04)`. Badge linksboven (cyaan vlak, of outline-variant voor "Coming Soon").

### Inputs
`dark-3` achtergrond, `border`, witte tekst, focus → `border` wordt cyaan. Rechte hoeken.

### Accordion (FAQ)
Items gescheiden door onderrand; +/- icoon in cyaan dat naar – animeert bij openen;
`max-height`-transitie voor het uitklappen.

### Marquee / proof-strip
Oneindig horizontaal scrollende balk (`@keyframes marquee` translateX 0→-50%, dubbele track),
items gescheiden door verticale randen, cyaan icoon + uppercase label.

---

## 5. Motion

- **Transitie-standaard:** `0.3s ease` (token `transition`).
- **Scroll-reveal:** elementen starten `opacity:0; translateY(28px)` → `.visible` bij in-view
  (IntersectionObserver), met staggered delays `0.1 / 0.2 / 0.3s`.
- **Parallax:** achtergrondlagen verschuiven `~0.2–0.3×` de scroll.
- **Grain:** subtiele ruis-overlay (`opacity 0.025`) met micro-animatie voor textuur.
- **Scroll-indicator:** pulserend cyaan lijntje.

Houd beweging subtiel en snel; nooit bouncy of speels.

---

## 6. Vertaling naar de training-app (andere branche)

De **foundations** (kleur, type, spacing, motion, vorm) zijn 1-op-1 overdraagbaar.
Branche-specifieke secties van de webshop (producten, checkout) laat je vallen; behoud:

- Donkere `dark`-achtergrond + cyaan `blue` accent als enige kleur.
- Impact-koppen in uppercase met cyaan highlight-woord + blue-line.
- Kaarten/panelen in `dark-2` met cyaan-transparante randen en hover-glow.
- Rechte hoeken, royale spacing, eyebrow-labels.
- Scroll-/state-transities van `0.3s ease`, subtiele reveals.

**Aanbevolen UI-mapping voor een training-app**
- Workout-/oefeningkaarten → de `Kaart`-component (3/4 of 16/9 beeld).
- Voortgang/stats → cyaan accent op donker (progress bars `blue` op `dark-3`).
- Primaire actie ("Start workout") → `.btn-blue`.
- Tab-bar/nav actief item → cyaan, inactief → `text-muted`.
- Lege states/sectiekoppen → eyebrow + Impact-kop patroon.

---

## 7. Bestanden in deze map

| Bestand | Voor |
|---------|------|
| `DESIGN-SYSTEM.md` | Deze spec (mens-leesbaar) |
| `liftiq-theme.css` | Drop-in CSS custom properties + basis-utilities (elk web/HTML project) |
| `liftiq-tokens.ts` | TypeScript-tokens (voor de React/TS training-app) |

Importeer `liftiq-theme.css` globaal, of gebruik `liftiq-tokens.ts` in je
styled-components/Tailwind/inline styles. Beide bevatten exact dezelfde waarden.
