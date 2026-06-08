<?php
/**
 * LIFTIQ webshop — gedeelde helpers (Mollie + SendCloud).
 *
 * Strato-proof: gebruikt cURL als dat beschikbaar is, en valt automatisch
 * terug op PHP-streams (allow_url_fopen) als cURL op de server uitstaat.
 * Dit bestand bevat GEEN geheime sleutels — die staan in config.php.
 */
if (!defined('LIFTIQ_LIB')) {
define('LIFTIQ_LIB', true);

if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://liftiqgear.nl');
}

/**
 * HTTP-request met JSON. Werkt met cURL of, als fallback, met streams.
 * @return array{code:int, body:string, error:?string}
 */
function liftiq_http_json($method, $url, $payload = null, $headers = array(), $basic_auth = null) {
    $json = ($payload === null) ? null : json_encode($payload);
    $base = array('Accept: application/json');
    if ($json !== null) { $base[] = 'Content-Type: application/json'; }
    $headers = array_merge($base, $headers);

    // --- cURL-pad (voorkeur) ---
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ($json !== null)        { curl_setopt($ch, CURLOPT_POSTFIELDS, $json); }
        if ($basic_auth !== null)  { curl_setopt($ch, CURLOPT_USERPWD, $basic_auth[0] . ':' . $basic_auth[1]); }
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        return array('code' => $code, 'body' => (string) $body, 'error' => ($err !== '' ? $err : null));
    }

    // --- stream-fallback (geen cURL op de server) ---
    if ($basic_auth !== null) {
        $headers[] = 'Authorization: Basic ' . base64_encode($basic_auth[0] . ':' . $basic_auth[1]);
    }
    $ctx = stream_context_create(array('http' => array(
        'method'        => $method,
        'header'        => implode("\r\n", $headers),
        'content'       => $json,
        'timeout'       => 30,
        'ignore_errors' => true,
    )));
    $body = @file_get_contents($url, false, $ctx);
    $code = 0;
    if (isset($http_response_header[0]) && preg_match('#HTTP/\S+\s+(\d+)#', $http_response_header[0], $m)) {
        $code = (int) $m[1];
    }
    return array('code' => $code, 'body' => (string) $body, 'error' => ($body === false ? 'request failed' : null));
}

/** Maak een Mollie-betaling aan. */
function mollie_create_payment($data) {
    $res = liftiq_http_json('POST', 'https://api.mollie.com/v2/payments', $data,
        array('Authorization: Bearer ' . MOLLIE_API_KEY));
    return array('code' => $res['code'], 'data' => json_decode($res['body'], true), 'raw' => $res);
}

/** Haal de actuele (betrouwbare) status van een Mollie-betaling op. */
function mollie_get_payment($id) {
    $res = liftiq_http_json('GET', 'https://api.mollie.com/v2/payments/' . rawurlencode($id), null,
        array('Authorization: Bearer ' . MOLLIE_API_KEY));
    return array('code' => $res['code'], 'data' => json_decode($res['body'], true), 'raw' => $res);
}

/**
 * Meld een pakket aan bij SendCloud (request_label = false).
 * Dustin kiest daarna de verzendmethode en print het label in de SendCloud-app.
 */
function sendcloud_create_parcel($parcel) {
    if (!defined('SENDCLOUD_PUBLIC_KEY') || !defined('SENDCLOUD_SECRET_KEY')
        || SENDCLOUD_PUBLIC_KEY === '' || strpos(SENDCLOUD_PUBLIC_KEY, 'VERVANG') !== false) {
        return array('code' => 0, 'data' => null, 'raw' => array('error' => 'SendCloud-keys niet ingesteld'));
    }
    $res = liftiq_http_json('POST', 'https://panel.sendcloud.sc/api/v2/parcels',
        array('parcel' => $parcel), array(),
        array(SENDCLOUD_PUBLIC_KEY, SENDCLOUD_SECRET_KEY));
    return array('code' => $res['code'], 'data' => json_decode($res['body'], true), 'raw' => $res);
}

/** Splits "Kerkstraat 12A" in straat + huisnummer (NL-adres). */
function liftiq_split_adres($adres) {
    $adres = trim($adres);
    if (preg_match('/^(.*?)\s+(\d+.*)$/u', $adres, $m)) {
        return array('street' => trim($m[1]), 'house' => trim($m[2]));
    }
    return array('street' => $adres, 'house' => '');
}

/** Simpele logging naar /logs (afgeschermd via .htaccess). */
function liftiq_log($file, $msg) {
    $dir = __DIR__ . '/logs';
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
    @file_put_contents($dir . '/' . $file,
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n", FILE_APPEND | LOCK_EX);
}

/** Idempotentie: al verwerkt? Zo niet, markeer en geef false terug. */
function liftiq_already_processed($payment_id) {
    $dir = __DIR__ . '/logs';
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
    $f = $dir . '/processed.txt';
    $done = is_file($f) ? file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : array();
    if (in_array($payment_id, $done, true)) { return true; }
    @file_put_contents($f, $payment_id . "\n", FILE_APPEND | LOCK_EX);
    return false;
}

} // LIFTIQ_LIB
