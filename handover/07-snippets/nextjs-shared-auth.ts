// Gedeelde Supabase auth tussen liftiqsupplements.nl en de Training OS app.
// Plaats dit in de site-codebase (Next.js voorbeeld).
//
// Voor scenario B uit 03-site-integratie.md: één account werkt op site + app.
// Vereist dat beide domeinen subdomeinen zijn van hetzelfde root-domein,
// zodat ze de auth-cookie kunnen delen via cookie domain '.liftiq.nl'.

import { createBrowserClient } from '@supabase/ssr';

export function createSiteSupabaseClient() {
  return createBrowserClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY!,
    {
      cookieOptions: {
        // Cruciaal voor cross-subdomein sessie-deling.
        // Stel ook in Supabase Auth Settings hetzelfde domein in.
        domain: '.liftiq.nl', // PAS AAN naar je echte root-domein
        sameSite: 'lax',
        secure: true,
      },
    }
  );
}

// Voorbeeld: login-formulier op de site
export async function loginOnSite(email: string, password: string) {
  const supabase = createSiteSupabaseClient();
  const { data, error } = await supabase.auth.signInWithPassword({
    email,
    password,
  });

  if (error) return { error: error.message };

  // Na succes is de gebruiker ook automatisch ingelogd op
  // https://app.liftiq.nl (zelfde cookie-domein).
  return { user: data.user };
}

// Voorbeeld: registratie op de site, met automatische redirect naar de app
export async function registerOnSite(email: string, password: string, fullName: string) {
  const supabase = createSiteSupabaseClient();
  const { data, error } = await supabase.auth.signUp({
    email,
    password,
    options: {
      data: { full_name: fullName },
      emailRedirectTo: 'https://app.liftiq.nl/auth/callback',
    },
  });
  return { user: data?.user, error: error?.message ?? null };
}

// Voorbeeld: check abonnementsstatus op de site (voor "Mijn account" pagina)
export async function getUserSubscription(userId: string) {
  const supabase = createSiteSupabaseClient();
  const { data } = await supabase
    .from('profiles')
    .select('subscription_status, full_name, role')
    .eq('id', userId)
    .single();
  return data;
}
