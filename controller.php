<?php
class Controller {
    public static $message = "";
    public static $note_list = null;

    public static function handleRequest() {
        // Neuen Listeneintrag hinzufügen
        if(isset($_POST['add_entry'])) {
            if(isset($_POST['value'])) {
                self::addEntry($_POST['value']);
            }
        }
        else if(isset($_POST['delete_item'])) {
            if(isset($_POST['id'])) {
                self::deleteEntry(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
            }
        }
        else if(isset($_POST['update_value'])) {
            if(isset($_POST['id']) && isset($_POST['value'])) {
                self::updateValue(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), $_POST['value']);
            }
        }
        else if(isset($_POST['order_up'])) {
            if(isset($_POST['id'])) {
                self::reorderEntry(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), 'up');
            }
        }
        else if(isset($_POST['order_down'])) {
            if(isset($_POST['id'])) {
                self::reorderEntry(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), 'down');
            }
        }
        else if(isset($_POST['switch_state'])) {
            if(isset($_POST['id'])) {
                self::switchState(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT));
            }
        }
    }

    private static function orderList() {
        // Sortierung neu berechnen
        $entries = Database::getList();
        $position = 1;
        foreach($entries as $entry) {
            $entry['position'] = $position;
            Database::updateEntry($entry['id'], $entry['position'], $entry['status'], $entry['value']);
            $position++;
        }
    }

    private static function addEntry($value) {
        Database::addEntry($value);
        self::$message = "Eintrag erfolgreich hinzugefügt";
    }

    private static function deleteEntry($id) {
        Database::deleteEntry($id);
        self::$message = "Eintrag erfolgreich gelöscht";
        self::orderList();
    }

    private static function updateValue($id, $new_value) {
        $entry = Database::getEntryById($id);
        Database::updateEntry($id, $entry['position'], $entry['status'], $new_value);
        self::$message = "Eintrag erfolgreich aktualisiert";
    }

    private static function reorderEntry($entry_id, $direction) {
        // Eintrag der nach oben verschoben werden soll
        $entry = Database::getEntryById($entry_id);
        $old_position = $entry['position'];
        $new_position = 0;

        // Neue Position berechnen
        switch($direction) {
            case 'up':
                if($old_position == 1) {
                    return;
                }
                $new_position = $old_position - 1;
                break;
            case 'down':
                if($old_position == Database::getMaxPosition()) {
                    return;
                }
                $new_position = $old_position + 1;
                break;
        }
        
        // Eintrag der bisher an der neuen Position war holen
        $entry_at_new_position = Database::getEntryByPosition($new_position);

        // Bisherigen Eintrag an neuer Position speichern
        Database::updateEntry($entry_at_new_position['id'], $old_position, $entry_at_new_position['status'], $entry_at_new_position['value']);

        // Eintrag an neuer Position speichern
        Database::updateEntry($entry_id, $new_position, $entry['status'], $entry['value']);
    }

    private static function switchState($id) {
        $entry = Database::getEntryById($id);
        if($entry['status'] == 0) {
            $entry['status'] = 1;
        }
        else {
            $entry['status'] = 0;
        }
        Database::updateEntry($entry['id'], $entry['position'], $entry['status'], $entry['value']);
    }
}

