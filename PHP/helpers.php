<?php
require_once 'config.php';

// JWT Secret Key
define('JWT_SECRET', 'your-secret-key-here'); // Change this in production

function base64url_encode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function base64url_decode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

function generate_jwt($user_id) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'user_id' => $user_id,
        'iat' => time(),
        'exp' => time() + 3600 // 1 hour
    ]);

    $header_encoded = base64url_encode($header);
    $payload_encoded = base64url_encode($payload);

    $signature = hash_hmac('sha256', $header_encoded . "." . $payload_encoded, JWT_SECRET, true);
    $signature_encoded = base64url_encode($signature);

    return $header_encoded . "." . $payload_encoded . "." . $signature_encoded;
}

function validate_jwt($token) {
    global $redis;

    // Check if token is blacklisted
    if ($redis->get('blacklist:' . $token)) {
        return false;
    }

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }

    $header = $parts[0];
    $payload = $parts[1];
    $signature = $parts[2];

    $expected_signature = hash_hmac('sha256', $header . "." . $payload, JWT_SECRET, true);
    $expected_signature_encoded = base64url_encode($expected_signature);

    if (!hash_equals($signature, $expected_signature_encoded)) {
        return false;
    }

    $payload_decoded = json_decode(base64url_decode($payload), true);
    if ($payload_decoded['exp'] < time()) {
        return false;
    }

    return $payload_decoded;
}

function get_user_id_from_token() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        return false;
    }

    $auth_header = $headers['Authorization'];
    if (!preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        return false;
    }

    $token = $matches[1];
    $payload = validate_jwt($token);
    return $payload ? $payload['user_id'] : false;
}

function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function json_error($message, $status_code = 400) {
    json_response(['message' => $message], $status_code);
}
?>