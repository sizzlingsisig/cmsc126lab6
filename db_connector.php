<?php
class PlayerDB {
    private $conn;

    public function __construct($host, $username, $password, $dbname = "leeds") {
        $this->conn = new mysqli($host, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Add a new player
    public function addPlayer($name, $position, $jersey_number, $nationality = null, $birthdate = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO leeds_players (name, position, jersey_number, nationality, birthdate) VALUES (?, ?, ?, ?, ?)"
        );
        if (!$stmt) {
            return "Prepare failed: " . $this->conn->error;
        }

        $nationality = $nationality ?: null;
        $birthdate = $birthdate ?: null;

        $stmt->bind_param("ssiss", $name, $position, $jersey_number, $nationality, $birthdate);

        if ($stmt->execute()) {
            $msg = "New player added with ID: " . $stmt->insert_id;
        } else {
            $msg = "Error adding player: " . $stmt->error;
        }

        $stmt->close();
        return $msg;
    }

    // Delete player by id
    public function deletePlayer($id) {
        $stmt = $this->conn->prepare("DELETE FROM leeds_players WHERE id = ?");
        if (!$stmt) {
            return "Prepare failed: " . $this->conn->error;
        }

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $msg = "Player with ID $id deleted.";
            } else {
                $msg = "No player found with ID $id.";
            }
        } else {
            $msg = "Error deleting player: " . $stmt->error;
        }

        $stmt->close();
        return $msg;
    }

    // Edit player by id
    public function editPlayer($id, $name, $position, $jersey_number, $nationality = null, $birthdate = null) {
        $stmt = $this->conn->prepare(
            "UPDATE leeds_players SET name = ?, position = ?, jersey_number = ?, nationality = ?, birthdate = ? WHERE id = ?"
        );
        if (!$stmt) {
            return "Prepare failed: " . $this->conn->error;
        }

        $nationality = $nationality ?: null;
        $birthdate = $birthdate ?: null;

        $stmt->bind_param("ssissi", $name, $position, $jersey_number, $nationality, $birthdate, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $msg = "Player with ID $id updated.";
            } else {
                $msg = "No changes made or player with ID $id not found.";
            }
        } else {
            $msg = "Error updating player: " . $stmt->error;
        }

        $stmt->close();
        return $msg;
    }
        public function getPlayers() {
        $players = [];
        $sql = "SELECT * FROM leeds_players";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $players[] = $row;
            }
        }

        return $players;
    }

    public function close() {
        $this->conn->close();
    }
}
?>
