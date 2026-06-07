/**
 * LIFTIQ — Tailwind preset
 * Voor de Training OS web-app (Next.js + Tailwind) en mobiel (NativeWind).
 *
 * Gebruik in tailwind.config.{js,ts}:
 *   module.exports = {
 *     presets: [require('./liftiq-tailwind-preset.js')],
 *     content: [...],
 *   };
 *
 * Daarna beschikbaar als utilities, o.a.:
 *   bg-liftiq-dark  text-liftiq-blue  border-liftiq  font-display
 *   shadow-liftiq-glow  bg-liftiq-hero  tracking-eyebrow  rounded-none
 *
 * Zie APP-INTEGRATION.md voor de volledige stappen.
 */
module.exports = {
  theme: {
    extend: {
      colors: {
        liftiq: {
          blue: '#00CFFF',
          'blue-dark': '#0099CC',
          dark: '#0a0a0a',
          'dark-2': '#111111',
          'dark-3': '#1a1a1a',
          'dark-4': '#222222',
          white: '#ffffff',
          grey: '#888888',
          'grey-light': '#cccccc',
        },
      },
      fontFamily: {
        display: ['Impact', "'Arial Narrow'", 'sans-serif'],
        sans: ["'Helvetica Neue'", 'Arial', 'sans-serif'],
      },
      letterSpacing: {
        eyebrow: '0.35em',
        display: '0.05em',
        button: '0.15em',
      },
      borderColor: {
        liftiq: 'rgba(0,207,255,0.15)',
        'liftiq-strong': 'rgba(0,207,255,0.4)',
      },
      boxShadow: {
        'liftiq-glow': '0 0 40px rgba(0,207,255,0.15), 0 20px 60px rgba(0,0,0,0.5)',
      },
      backgroundImage: {
        'liftiq-hero':
          'linear-gradient(105deg, rgba(10,10,10,0.85) 0%, rgba(10,10,10,0.5) 60%, rgba(0,207,255,0.05) 100%)',
        'liftiq-glow':
          'radial-gradient(ellipse, rgba(0,207,255,0.07) 0%, transparent 68%)',
      },
      fontSize: {
        'liftiq-hero': ['clamp(52px, 8vw, 96px)', { lineHeight: '0.95' }],
        'liftiq-h2': ['clamp(36px, 5vw, 64px)', { lineHeight: '1' }],
        eyebrow: ['10px', { letterSpacing: '0.35em' }],
      },
      borderRadius: {
        // Merkkenmerk: rechte hoeken. Gebruik standaard `rounded-none`.
        liftiq: '0',
      },
      transitionDuration: {
        liftiq: '300ms',
      },
    },
  },
};
