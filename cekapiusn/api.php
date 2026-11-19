<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/src/ApiGames.php';

use Aditdev\ApiGames;

$api = new ApiGames();
$startTime = microtime(true);

// Parse input from GET or POST (JSON body or form data)
$input = $_GET;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) {
        $input = array_merge($input, $json);
    } else {
        $input = array_merge($input, $_POST);
    }
}

// Action: List supported games
$action = strtolower(trim((string) ($input['action'] ?? '')));
if ($action === 'games' || $action === 'list') {
    $games = $api->getGameList();
    echo json_encode([
        'success' => true,
        'status'  => 200,
        'count'   => count($games),
        'games'   => $games,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Extract parameters
$game    = trim((string) ($input['game'] ?? $input['game_code'] ?? ''));
$userId  = trim((string) ($input['user_id'] ?? $input['id'] ?? $input['userId'] ?? ''));
$zoneId  = trim((string) ($input['zone_id'] ?? $input['other_id'] ?? $input['server_id'] ?? $input['zone'] ?? $input['zoneId'] ?? ''));

if ($game === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'status'  => 400,
        'message' => 'Parameter "game" atau "game_code" wajib diisi.',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($userId === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'status'  => 400,
        'game'    => $game,
        'message' => 'Parameter "user_id" atau "id" wajib diisi.',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$api->supports($game)) {
    http_response_code(404);
    echo json_encode([
        'success'         => false,
        'status'          => 404,
        'game'            => $game,
        'message'         => "Game \"{$game}\" tidak didukung.",
        'supported_games' => $api->games(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$gameConfig = $api->getGame($game);
$result = $api->check($game, $userId, $zoneId);
$executionTimeMs = round((microtime(true) - $startTime) * 1000, 2);

$isSuccess = ($result['status'] ?? 0) === 200;

http_response_code($result['status'] ?? ($isSuccess ? 200 : 400));

echo json_encode([
    'success'           => $isSuccess,
    'status'            => $result['status'] ?? ($isSuccess ? 200 : 400),
    'game'              => strtoupper($game),
    'game_name'         => $gameConfig['name'] ?? $game,
    'user_id'           => $userId,
    'zone_id'           => $zoneId !== '' ? $zoneId : null,
    'nickname'          => $result['nickname'] ?? null,
    'message'           => $result['msg'] ?? ($isSuccess ? 'Valid User ID' : 'Gagal memeriksa nickname'),
    'provider'          => $gameConfig['provider'] ?? 'unknown',
    'execution_time_ms' => $executionTimeMs,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
