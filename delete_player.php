<?php
header('Content-Type: application/json');
require_once 'db_connector.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'leeds';

$db = new PlayerDB($host, $user, $pass, $dbname);

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON input instead of $_POST
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or missing player ID.'
        ]);
        $db->close();
        exit;
    }

    $msg = $db->deletePlayer($id);

    if (strpos($msg, 'deleted') !== false) {
        echo json_encode([
            'success' => true,
            'message' => $msg,
            'player_id' => $id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $msg
        ]);
    }

    $db->close();
    exit;
}

// If not POST
echo json_encode([
    'success' => false,
    'message' => 'Invalid request method'
]);

$db->close();
?>
