<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/PollManager.php';

$config = require __DIR__ . '/../config/config.php';
$pollManager = new PollManager($config);

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_results':
        $results = $pollManager->getResults();
        echo json_encode($results);
        break;

    case 'get_stats':
        $stats = $pollManager->getVotingStats();
        echo json_encode($stats);
        break;

    case 'vote':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $optionIndex = (int)($_POST['vote'] ?? -1);

        if ($optionIndex < 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid option']);
            exit;
        }

        $result = $pollManager->castVote($ip, $optionIndex);
        echo json_encode($result);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
