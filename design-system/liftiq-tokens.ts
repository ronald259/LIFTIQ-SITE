/**
 * LIFTIQ — Design System tokens (TypeScript)
 * Gedeeld tussen LIFTIQ-SITE en LiftIQ-APP (training-app).
 * Gebruik in styled-components, inline styles, of als bron voor een
 * Tailwind/Theme-config. Zie DESIGN-SYSTEM.md voor de richtlijnen.
 */

export const colors = {
  blue: '#00CFFF', // primaire accent
  blueDark: '#0099CC',
  dark: '#0a0a0a', // achtergrond
  dark2: '#111111', // secties / kaarten
  dark3: '#1a1a1a', // inputs / genest
  dark4: '#222222',
  white: '#ffffff',
  grey: '#888888',
  greyLight: '#cccccc',
  textMuted: 'rgba(255,255,255,0.5)',
  border: 'rgba(0,207,255,0.15)',
  borderStrong: 'rgba(0,207,255,0.4)',
} as const;

export const fonts = {
  display: "Impact, 'Arial Narrow', sans-serif",
  body: "'Helvetica Neue', Arial, sans-serif",
} as const;

/** Responsive font-sizes (CSS clamp strings). */
export const fontSizes = {
  hero: 'clamp(52px, 8vw, 96px)',
  h2: 'clamp(36px, 5vw, 64px)',
  cardTitle: '22px',
  body: '16px',
  small: '13px',
  eyebrow: '10px',
} as const;

export const fontWeights = {
  regular: 400,
  semibold: 600,
  bold: 700,
} as const;

export const letterSpacing = {
  display: '0.05em',
  eyebrow: '0.35em',
  button: '0.15em',
} as const;

/** Spacing-ritme op basis van 8px. */
export const space = {
  xs: '0.5rem',
  sm: '1rem',
  md: '1.5rem',
  lg: '2.5rem',
  xl: '4rem',
  section: '7rem',
} as const;

export const radius = {
  none: '0', // rechte hoeken = merkkenmerk
} as const;

export const transition = {
  base: '0.3s ease',
  reveal: '0.7s ease',
} as const;

export const effects = {
  heroOverlay:
    'linear-gradient(105deg, rgba(10,10,10,0.85) 0%, rgba(10,10,10,0.5) 60%, rgba(0,207,255,0.05) 100%)',
  glowBlue: '0 0 40px rgba(0,207,255,0.15), 0 20px 60px rgba(0,0,0,0.5)',
  radialGlow: 'radial-gradient(ellipse, rgba(0,207,255,0.07) 0%, transparent 68%)',
} as const;

export const breakpoints = {
  mobile: '768px',
  tablet: '900px',
} as const;

/** Eén thema-object voor ThemeProvider e.d. */
export const liftiqTheme = {
  colors,
  fonts,
  fontSizes,
  fontWeights,
  letterSpacing,
  space,
  radius,
  transition,
  effects,
  breakpoints,
} as const;

export type LiftiqTheme = typeof liftiqTheme;

export default liftiqTheme;
