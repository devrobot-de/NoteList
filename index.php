<?php
// Datenbank-Klasse einbinden
require_once 'database.php';
require_once 'controller.php';

// User-Anfrage verarbeiten
Controller::handleRequest();

// Nachdem etwaige User-Anfragen bearbeitet wurden werden die Listen-Einträge aus der Datenbank geholt.
$note_list = Database::getList();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>NoteList PHP</title>
</head>

<body>
    <div class="wrapper">
        <div id="header">
            <div id="page_title" class="rounded_box">
                <h1>NoteList PHP</h1>
            </div>

            <div id="note_input" class="rounded_box column">
                <form method="post" action="index.php">
                    <h2>Notiz hinzufügen</h2>
                    <div class="row">
                        <div class="section section_main">
                            <input type="text" name="value" placeholder="Deine Notiz">
                        </div>
                        <div class="section">
                            <input class="button" type="submit" name="add_entry" value="Hinzufügen">
                        </div>
                    </div>
                </form>
<?php
// Nachrichten-Ausgabe (Erfolg oder Fehler)
if (Controller::$message != ""):
    $message = Controller::$message;
?>
                <div id="messenger" class="row">
                    <div class="section section_main">
                        <?php echo '<p>' . $message . '</p>'; ?>
                    </div>
                    <div class="section">
                        <form action="index.php" method="post">
                            <input type="submit" class="button" name="delete_message" value="X">
                        </form>
                    </div>
                </div>
                <!-- /messenger -->
<?php
endif;
?>
            </div>
            <!-- /note_input -->
        </div>
        <!-- /header -->

        <div id="notes_list" class="column">
            <div class="list_header">
                <h2>Notizen</h2>
            </div>
            <div class="list_content">


<?php
// Ausgabe der einzelnen Listeneinträge
foreach ($note_list as $item):
    $id = htmlspecialchars($item["id"]);
    $status = htmlspecialchars($item["status"]);
    $value = htmlspecialchars($item["value"]);
    ?>

		            <div class="list_item row <?php echo $status == 1 ? 'checked' : 'unchecked' ?>">
		                <form method="post" action="index.php" class="row">
		                    <input type="hidden" name="id" value="<?php echo $id ?>">

		                    <div class="section row">
		                        <input type="submit" class="button" name="order_up" value="&#8679;">
		                        <input type="submit" class="button" name="order_down" value="&#8681;">
		                    </div>
		                    <div class="section section_main row">
		                        <input type="text" name="value" value="<?php echo $value ?>">
		                    </div>
		                    <div class="section row">
		                        <input type="submit" class="button" name="update_value" value="&#128190;">
		                        <input type="submit" class="button" name="delete_item" value="&#10060;">
		                        <input type="submit" class="button" name="switch_state" value="&#10004;">
		                    </div>
		                </form>
		            </div>
	                <!-- /list_item -->
	<?php
endforeach;
?>

                </div>
                <!-- /list_content -->
            <div class="list_footer">
                <!-- empty -->
            </div>
        </div>
        <!-- /notes_list -->
    </div>
    <!-- /wrapper -->
</body>

</html>