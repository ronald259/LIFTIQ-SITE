<?php
/**
 * Mollie webhook → SendCloud.
 *
 * Mollie POST't alleen een betaling-id. We halen de échte status op bij Mollie
 * (nooit de POST-body vertrouwen). Bij status 'paid' + verzenden melden we het
 * pakket aan bij SendCloud, zodat Dustin het label vanaf zijn telefoon kan
 * printen. Afhaalorders slaan we over.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/_lib.php';

header('Content-Type: text/plain; charset=utf-8');

$payment_id = isset($_POST['id']) ? trim($_POST['id']) : '';
if ($payment_id === '') {
    http_response_code(400);
    echo 'missing id';
    exit;
}

// 1) Betrouwbare status ophalen bij Mollie
$res = mollie_get_payment($payment_id);
$payment = $res['data'];
if ($res['code'] !== 200 || !is_array($payment)) {
    liftiq_log('webhook.log', "Mollie ophalen mislukt voor $payment_id (http {$res['code']})");
    http_response_code(200); // 200 → Mollie blijft niet eindeloos retryen op onze fout
    echo 'ok';
    exit;
}

$status = isset($payment['status']) ? $payment['status'] : '';
$meta   = isset($payment['metadata']) ? (array) $payment['metadata'] : array();
liftiq_log('orders.log', "$payment_id status=$status " . json_encode($meta, JSON_UNESCAPED_UNICODE));

// 2) Alleen doorgaan bij betaald
if ($status !== 'paid') {
    echo 'ok'; // open / failed / expired / canceled → niets te doen
    exit;
}

// 3) Dubbele verwerking voorkomen (Mollie kan de webhook meermaals aanroepen)
if (liftiq_already_processed($payment_id)) {
    echo 'ok (al verwerkt)';
    exit;
}

// 4) Afhalen? Dan geen verzendlabel.
$levering = isset($meta['levering']) ? $meta['levering'] : 'verzenden';
if ($levering !== 'verzenden') {
    liftiq_log('webhook.log', "$payment_id is afhalen — geen SendCloud-pakket aangemaakt");
    echo 'ok (afhalen)';
    exit;
}

// 5) Pakket aanmelden bij SendCloud
$adres  = liftiq_split_adres(isset($meta['adres']) ? $meta['adres'] : '');
$parcel = array(
    'name'          => isset($meta['naam'])     ? $meta['naam']     : '',
    'address'       => $adres['street'],
    'house_number'  => $adres['house'],
    'city'          => isset($meta['stad'])     ? $meta['stad']     : '',
    'postal_code'   => isset($meta['postcode']) ? $meta['postcode'] : '',
    'country'       => isset($meta['land'])     ? $meta['land']     : 'NL',
    'email'         => isset($meta['email'])    ? $meta['email']    : '',
    'telephone'     => isset($meta['telefoon']) ? $meta['telefoon'] : '',
    'order_number'  => $payment_id,
    'weight'        => (isset($meta['gewicht']) && $meta['gewicht'] !== '') ? $meta['gewicht'] : '0.500',
    'request_label' => false,
);

$sc = sendcloud_create_parcel($parcel);
if ($sc['code'] === 200 || $sc['code'] === 201) {
    $pid = isset($sc['data']['parcel']['id']) ? $sc['data']['parcel']['id'] : '?';
    liftiq_log('webhook.log', "$payment_id → SendCloud pakket #$pid aangemaakt");
} else {
    liftiq_log('webhook.log', "$payment_id → SendCloud FOUT (http {$sc['code']}): "
        . substr(json_encode($sc['data']), 0, 500));
}

echo 'ok';
