<?php
header('Content-Type: application/json');
require_once 'db_connector.php';  // Your class file

// DB config
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'leeds';

// Create DB instance
$db = new PlayerDB($host, $user, $pass, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $jersey_number = intval($_POST['jersey_number'] ?? 0);
    $nationality = trim($_POST['nationality'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    // Validate required fields
    if (!$name || !$position || $jersey_number <= 0 || !$nationality || !$birthdate) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields correctly.'
        ]);
        $db->close();
        exit;
    }

    // Add player
    $msg = $db->addPlayer($name, $position, $jersey_number, $nationality, $birthdate);

    // Detect success by checking if message contains inserted ID
    if (preg_match('/ID: (\d+)/', $msg, $matches)) {
        $player_id = (int)$matches[1];

        echo json_encode([
            'success' => true,
            'player' => [
                'id' => $player_id,
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
    exit;
}

// If request method not POST
echo json_encode([
    'success' => false,
    'message' => 'Invalid request method'
]);
$db->close();
?>
