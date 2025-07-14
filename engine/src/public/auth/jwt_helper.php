<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/setting.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Generate JWT
 */
function generate_jwt($payload, $expiration = 3600) {
    $key = JWT_SECRET;
    $issuedAt = time();
    $payload['iat'] = $issuedAt;
    $payload['exp'] = $issuedAt + $expiration;

    return JWT::encode($payload, $key, 'HS256');
}

define('JWT_SECRET', 'secret123');

/**
 * Verify JWT
 */
function verify_jwt($token) {
    $key = JWT_SECRET;
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Validate JWT or return 401/403 and exit
 */
function validate_jwt_or_die($requiredRole = null) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "No token provided"]);
        exit;
    }

    $token = $matches[1];
    $payload = verify_jwt($token);

    if (!$payload) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
        exit;
    }

    if ($requiredRole && ($payload['role'] ?? '') !== $requiredRole) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Access denied. Requires role: $requiredRole"]);
        exit;
    }

    return $payload;
}
