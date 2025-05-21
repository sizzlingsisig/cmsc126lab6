<?php
header('Content-Type: application/json');
require 'db_connector.php';  // make sure this is the correct path

$db = new PlayerDB('localhost', 'root', '', 'leeds');
$players = $db->getPlayers();
$db->close();

echo json_encode([
    'success' => true,
    'players' => $players
]);
?>
