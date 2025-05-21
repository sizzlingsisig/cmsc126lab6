<?php
header('Content-Type: application/json');
require_once 'db_connector.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'leeds';

$db = new PlayerDB($host, $user, $pass, $dbname);

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id'] ?? 0);
$name = trim($data['name'] ?? '');
$position = trim($data['position'] ?? '');
$jersey_number = intval($data['jersey_number'] ?? 0);
$nationality = trim($data['nationality'] ?? '');
$birthdate = trim($data['birthdate'] ?? '');

if ($id <= 0 || !$name || !$position || $jersey_number <= 0 || !$nationality || !$birthdate) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields correctly including a valid player ID.'
    ]);
    $db->close();
    exit;
}

$msg = $db->editPlayer($id, $name, $position, $jersey_number, $nationality, $birthdate);

if (strpos($msg, 'updated') !== false) {
    echo json_encode([
        'success' => true,
        'message' => $msg,
        'player' => [
            'id' => $id,
            'name' => $name,
            'position' => $position,
            'jersey_number' => $jersey_number,
            'nationality' => $nationality,
            'birthdate' => $birthdate
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $msg
    ]);
}

$db->close();
?>