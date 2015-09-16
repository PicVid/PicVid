<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - News</title>');

/**
 * News Dynamisch Verwalten
 *
 * @author Manuel Bochröder
 * @since 1.0.0
 */

//Pruefen welcher Befehl ausgefuehrt werden soll.
if($PICVID['CORE']->getParameter($_REQUEST, 'add', 0) == 1) {

    //Variable fuer das Formular setzen.
    $headline = 'Artikel erstellen';
	$action = 'addtodb';
	$id = 0;

    //Formular laden.
    require_once('form.php');
} elseif($PICVID['CORE']->getParameter($_REQUEST, 'addtodb', 0) == 1) {

    //SQL-Befehl erzeugen (News erzeugen).
    $sql = "INSERT INTO `#__news` (`NewsTitle`, `NewsDate`, `NewsContent`, `NewsUserID`) VALUES (";
    $sql .= "'".$PICVID['CORE']->getParameter($_POST, 'database_title', '')."', '".date('Y-m-d H:i:s')."', ";
    $sql .= "'".$PICVID['CORE']->getParameter($_POST, 'database_text', '')."', ".(int) $PICVID['ACT_USER']->_id.")";

    //SQL-Befehl setzen.
    $PICVID['DATABASE']->setQuery($sql);

    //SQL-Befehl asufuehren.
    if($PICVID['DATABASE']->query() === true) {

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=news&admin=1', 'Neuer Artikel wurde erfolgreich erstellt.', 'success');
        exit;
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=news&admin=1', 'Artikel konnte nicht erstellt werden.', 'error');
        exit;
    }
} elseif($PICVID['CORE']->getParameter($_REQUEST, 'edit', 0) == 1) {

    //Variable fuer das Formular setzen.
    $headline = 'Artikel bearbeiten';
    $action = 'edittodb';
    $id = $PICVID['CORE']->getParameter($_REQUEST, 'id', 0);

    //SQL-Befehl setzen (Titel und Text ermitteln).
    $PICVID['DATABASE']->setQuery("SELECT `NewsTitle`, `NewsContent` FROM `#__news` WHERE `NewsID` = ".(int) $id);

    //Element aus der Datenbank abrufen.
    $newsElement = $PICVID['DATABASE']->getObjectArray();

    //Weitere Werte fuer das Formular setzen.
    $editnewsTitle = $newsElement[0]->NewsTitle;
	$editnewsText = $newsElement[0]->NewsContent;

	//Formular laden.
    require_once('form.php');
} elseif($PICVID['CORE']->getParameter($_REQUEST, 'edittodb', 0) == 1) {

    //SQL-Befehl erzeugen (News aktualsieren).
    $sql = "UPDATE `#__news` SET `NewsTitle` = '".$PICVID['CORE']->getParameter($_POST, 'database_title', '')."', ";
    $sql .= "`NewsContent` = '".$PICVID['CORE']->getParameter($_POST, 'database_text', '')."' ";
    $sql .= "WHERE `NewsID` = ".(int) $PICVID['CORE']->getParameter($_POST, 'editid', 0);

    //SQL-Befehl setzen.
    $PICVID['DATABASE']->setQuery($sql);

    //Pruefen ob der Artikel aktualsiert wurde.
    if($PICVID['DATABASE']->query() === true) {

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=news&admin=1', 'Artikel wurde erfolgreich aktualisiert.', 'success');
        exit;
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=news&admin=1', 'Artikel konnte nicht aktualisiert werden.', 'error');
        exit;
    }
} elseif($PICVID['CORE']->getParameter($_REQUEST, 'delete', 0) == 1) {

    //SQL-Befehl setzen (Artikel loeschen).
    $PICVID['DATABASE']->setQuery("DELETE FROM `#__news` WHERE `NewsID` = ".(int) $PICVID['CORE']->getParameter($_REQUEST, 'id', 0));

    //SQL-Befehl ausfuehren.
    if($PICVID['DATABASE']->query() === true) {

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=news&admin=1', 'Artikel wurde erfolgreich gelöscht.', 'success');
        exit;
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=news&admin=1', 'Artikel konnte nicht gelöscht werden.', 'error');
        exit;
    }
} else {
?>
    <div class="modal hide" id="warning">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3><font style="color:#f00;">Achtung!</font></h3>
        </div>
        <div class="modal-body">
           <p>Möchten Sie den ausgewählten Artikel wirklich löschen?</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
            <a id="delete-link" href="#" class="btn btn-danger">Löschen</a>
        </div>
    </div>

    <h3>News / Artikel</h3>
    <hr/>
    <table class="table list-table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th width="7%">#</th>
                <th>Titel</th>
                <th>Erstellt am</th>
                <th>Autor</th>
                <th width="16%"></th>
            </tr>
        </thead>
        <tbody>
        <?php
        //Alle News und Artikel auslesen.
        $PICVID['DATABASE']->setQuery('SELECT `NewsID`, `NewsTitle`, `NewsDate`, `NewsUserID` FROM `#__news` ORDER BY `NewsID`');
        $articles = $PICVID['DATABASE']->getObjectArray();

        //Pruefen ob News vorhanden sind.
        if((is_array($articles) === true) && (count($articles) > 0)) {

            //Zaehler der Tabelle zuruecksetzen.
            $row_count = 1;

            //Durchlaufen aller Artikel.
            foreach($articles as $article) {
            ?>
            <tr>
                <td><?php echo $row_count++; ?></td>
                <td><?php echo $article->NewsTitle; ?></td>
                <td><?php echo $article->NewsDate; ?></td>
                <td><?php echo $PICVID['USER']->getUsernameFromID($article->NewsUserID); ?></td>
                <td>
                    <a href="index.php?section=news&admin=1&edit=1&id=<?php echo $article->NewsID; ?>" class="btn btn-success"><i class="icon-pencil"></i></a>
                    <a class="btn btn-danger" data-toggle="modal" data-target="#warning" href="#" onclick="document.getElementById('delete-link').href='index.php?section=news&admin=1&delete=1&id=<?php echo $article->NewsID; ?>'"><i class="icon-trash"></i></a>
                    <a href="index.php?section=news&id=<?php echo $article->NewsID; ?>" class="btn"><i class="icon-eye-open"></i></a>
                </td>
            </tr>
            <?php
            }
        } else {
        ?>
            <tr>
                <td colspan="5">Kein Artikel vorhanden!</td>
            </tr>
        <?php
        }
        ?>
            <tr>
                <td colspan="4"></td>
                <td><a href="index.php?section=news&admin=1&add=1" class="btn btn-success"><i class="icon-plus"></i></a></td>
            </tr>
        </tbody>
    </table>
<?php } ?>