<?php
// Datenbank-Klasse
class Database {
    private static $db_host = 'localhost';
    private static $db_user = 'root';
    private static $db_password = '';
    private static $db_db = 'notelist';
    private static $db_table = 'list_items';

    // Verbindung zur Datenbank herstellen
    private static function connect() {
        $conn = new mysqli(self::$db_host, self::$db_user, self::$db_password, self::$db_db);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    // Verbindung zur Datenbank schließen
    private static function close($conn) {
        $conn->close();
    }

    // Methoden für die Datenbank-Abfragen:
    // - NoteList aus der Datenbank holen
    public static function getList() {
        $conn = self::connect();

        $stmt = $conn->prepare("SELECT * FROM " . self::$db_table . " ORDER BY position ASC");

        $stmt->execute();
        
        $result = $stmt->get_result();
        $list = array();
        
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        
        $stmt->close();

        self::close($conn);
        
        return $list;
    }

    // - Eintrag anhand der ID aus der Datenbank holen
    public static function getEntryById($id) {
        $conn = self::connect();

        $stmt = $conn->prepare("SELECT * FROM " . self::$db_table . " WHERE id = ?");
        $stmt->bind_param("i", $id);

        $stmt->execute();
        
        $result = $stmt->get_result();
        $entry = $result->fetch_assoc();

        $stmt->close();

        self::close($conn);
        return $entry;
    }

    // - Eintrag anhand der Listen-Position aus der Datenbank holen
    public static function getEntryByPosition($position) {
        $conn = self::connect();

        $stmt = $conn->prepare("SELECT * FROM " . self::$db_table . " WHERE position = ?");
        $stmt->bind_param("i", $position);

        $stmt->execute();
        
        $result = $stmt->get_result();
        $entry = $result->fetch_assoc();
        
        $stmt->close();

        self::close($conn);
        
        return $entry;
    }

    // - Neuen Eintrag in die Datenbank hinzufügen
    public static function addEntry($value) {
        $conn = self::connect();

        // Ermitteln der aktuell höchsten Position und erhöhen um 1
        $highest_value = self::getMaxPosition($conn) + 1;

        $stmt = $conn->prepare("INSERT INTO " . self::$db_table . " (value, position) VALUES (?, ?)");

        $stmt->bind_param("si", $value, $highest_value);
        
        $stmt->execute();

        $stmt->close();

        self::close($conn);
    }

    // - Vorhandenen Eintrag in der Datenbank aktualisieren
    public static function updateEntry($id, $position, $status, $value) {
        $conn = self::connect();

        $stmt = $conn->prepare("UPDATE " . self::$db_table . " SET position = ?, status = ?, value = ? WHERE id = ?");
        $stmt->bind_param("iisi", $position, $status, $value, $id);

        $stmt->execute();

        $stmt->close();

        self::close($conn);
    }

    // - Eintrag in der Datenbank löschen
    public static function deleteEntry($id) {
        $conn = self::connect();

        $stmt = $conn->prepare("DELETE FROM " . self::$db_table . " WHERE id = ?");
        $stmt->bind_param("i", $id);

        $stmt->execute();

        $stmt->close();

        self::close($conn);
    }

    // Alle Einträge in der Datenbank löschen
    public static function deleteAllEntries() {
        $conn = self::connect();

        $stmt = $conn->prepare("DELETE FROM " . self::$db_table . "");
        $stmt->execute();

        $stmt->close();

        self::close($conn);
    }

    // - Höchste Listen-Position aus der Datenbank holen
    public static function getMaxPosition($con = null) {
        if($con == null) {
            $conn = self::connect();
        }
        else {
            $conn = $con;
        }

        $stmt = $conn->prepare("SELECT MAX(position) AS max_value FROM " . self::$db_table . "");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $highest_value = $row['max_value'];

        $stmt->close();

        if($con == null) {
            self::close($conn);
        }

        return $highest_value;
    }

}
