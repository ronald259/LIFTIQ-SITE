// Stripe webhook → Supabase subscription_status koppeling.
// Plaats dit op je backend (bijv. liftiqsupplements.nl als Next.js API route,
// of als Supabase Edge Function, of als losse Node-server).
//
// Wat het doet:
// - Luistert naar Stripe webhook events
// - Update bij betaling profiles.subscription_status = 'active'
// - Zet status op 'paused'/'cancelled' bij respectievelijke events
//
// Vereist: SUPABASE_SERVICE_ROLE_KEY (NIET de anon key — die kan dit niet).

import Stripe from 'stripe';
import { createClient } from '@supabase/supabase-js';
import type { NextRequest } from 'next/server';

const stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
  apiVersion: '2024-12-18.acacia',
});

// Service-role client kan RLS omzeilen om alle profielen bij te werken.
const supabase = createClient(
  process.env.NEXT_PUBLIC_SUPABASE_URL!,
  process.env.SUPABASE_SERVICE_ROLE_KEY! // SECRET — alleen op server
);

export async function POST(request: NextRequest) {
  const sig = request.headers.get('stripe-signature')!;
  const body = await request.text();

  let event: Stripe.Event;
  try {
    event = stripe.webhooks.constructEvent(body, sig, process.env.STRIPE_WEBHOOK_SECRET!);
  } catch (err) {
    return new Response(`Webhook signature invalid: ${(err as Error).message}`, { status: 400 });
  }

  // Stripe customer/subscription mapping naar Supabase user_id.
  // Tip: sla bij checkout de Supabase user_id op als Stripe customer metadata.
  async function findUserId(customerId: string): Promise<string | null> {
    const customer = await stripe.customers.retrieve(customerId);
    if (customer.deleted) return null;
    return (customer.metadata?.supabase_user_id as string) ?? null;
  }

  async function setStatus(customerId: string, status: 'active' | 'paused' | 'cancelled' | 'free') {
    const userId = await findUserId(customerId);
    if (!userId) return;
    await supabase
      .from('profiles')
      .update({ subscription_status: status, updated_at: new Date().toISOString() })
      .eq('id', userId);
  }

  switch (event.type) {
    // Nieuwe betaling geslaagd → activeer.
    case 'checkout.session.completed': {
      const session = event.data.object as Stripe.Checkout.Session;
      if (session.customer) await setStatus(session.customer as string, 'active');
      break;
    }

    // Maandelijkse incasso geslaagd → blijft actief.
    case 'invoice.paid': {
      const invoice = event.data.object as Stripe.Invoice;
      if (invoice.customer) await setStatus(invoice.customer as string, 'active');
      break;
    }

    // Betaling mislukt → pauzeer.
    case 'invoice.payment_failed': {
      const invoice = event.data.object as Stripe.Invoice;
      if (invoice.customer) await setStatus(invoice.customer as string, 'paused');
      break;
    }

    // Klant zegt op.
    case 'customer.subscription.deleted': {
      const sub = event.data.object as Stripe.Subscription;
      if (sub.customer) await setStatus(sub.customer as string, 'cancelled');
      break;
    }
  }

  return new Response('ok', { status: 200 });
}

// Belangrijk om in te stellen in Stripe Dashboard:
// - Webhook endpoint: https://liftiqsupplements.nl/api/webhooks/stripe
// - Events: checkout.session.completed, invoice.paid, invoice.payment_failed,
//           customer.subscription.deleted
//
// Bij het aanmaken van een Stripe checkout-sessie:
//   await stripe.checkout.sessions.create({
//     customer_email: user.email,
//     metadata: { supabase_user_id: user.id }, // zo kunnen we terugkoppelen
//     ...
//   });
