<?php
require_once 'config.php';
require_once 'helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$user_id = get_user_id_from_token();
if (!$user_id) {
    json_error('Unauthorized', 401);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get username from MySQL
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        json_error('User not found', 404);
    }

    // Get profile from MongoDB
    $collection = $mongoDB->profiles;
    $profile = $collection->findOne(['user_id' => $user_id]);

    $response = [
        'username' => $user['username'],
        'bio' => $profile['bio'] ?? '',
        'email' => $profile['email'] ?? '',
        'updated_at' => $profile['updated_at'] ?? null
    ];

    json_response($response);

} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        json_error('Invalid JSON input');
    }

    $bio = trim($input['bio'] ?? '');
    $email = trim($input['email'] ?? '');

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_error('Invalid email format');
    }

    // Update or insert profile in MongoDB
    $collection = $mongoDB->profiles;
    $collection->updateOne(
        ['user_id' => $user_id],
        ['$set' => [
            'bio' => $bio,
            'email' => $email,
            'updated_at' => date('Y-m-d H:i:s')
        ]],
        ['upsert' => true]
    );

    json_response(['message' => 'Profile updated successfully']);

} else {
    json_error('Method not allowed', 405);
}
?>