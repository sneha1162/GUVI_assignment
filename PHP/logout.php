<?php
require_once 'config.php';
require_once 'helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$user_id = get_user_id_from_token();
if (!$user_id) {
    json_error('Unauthorized', 401);
}

// Get token from header to blacklist it
$headers = getallheaders();
$auth_header = $headers['Authorization'];
preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches);
$token = $matches[1];

// Blacklist token in Redis with expiration (1 hour)
global $redis;
$redis->setex('blacklist:' . $token, 3600, '1');

json_response(['message' => 'Logged out successfully']);
?>