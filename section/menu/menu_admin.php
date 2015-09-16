<?php
/**
 * Navigation Dynamisch Verwalten
 *
 * @author Manuel Bochröder
 * @since 1.0.0
 */
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Menüverwaltung</title>');

/**
 * Funktion um die Reihenfolge der Menueeintraege anpassen zu koennen.
 * @param int $id Die ID des Elements welches geschoben werden soll.
 * @param int $ordering Die Position von der geschoben werden soll.
 * @param int $offset Die Verschiebung.
 * @return Der Status ob verschoben wurde.
 * @since 1.0.0
 */
function updateOrdering($id, $ordering, $offset) {

    //Globale Variable der Datenbank einbinden.
    global $PICVID;

    //SQL-Befehl erzeugen (Andere Elemente verschieben).
    $sql = "UPDATE `#__menu` SET `OrderNum` = `OrderNum` + ".$offset * (-1) ." WHERE `OrderNum` = ".((int) $ordering + $offset);

    //SQL-Befehl setzen.
    $PICVID['DATABASE']->setQuery($sql);

    //SQL-Befehl ausfuehren und pruefen.
    if($PICVID['DATABASE']->query() === true) {

        //SQL-Befehl setzen (neue Position des Elements speichern).
        $PICVID['DATABASE']->setQuery("UPDATE `#__menu` SET `OrderNum` = ".((int) $ordering + $offset)." WHERE `id` = ".(int) $id);

        //SQL-Befehl ausfuehren und Status zurueckgeben.
        return $PICVID['DATABASE']->query();
    }

    //Status zurueckgeben.
    return false;
}

//Pruefen welcher Befehl ausgefuehrt werden soll.
if(isset($_REQUEST['updateOrdNum'])) {

    //Reihenfolge aktualisieren.
    if(updateOrdering($PICVID['CORE']->getParameter($_REQUEST, 'id', 0), $PICVID['CORE']->getParameter($_REQUEST, 'ordering', 0), $PICVID['CORE']->getParameter($_REQUEST, 'offset', 0)) === true) {

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement wurde erfolgreich verschoben.', 'success');
        exit;
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement konnte nicht verschoben werden.', 'error');
        exit;
    }
} elseif(isset($_REQUEST['add'])) {

    //Variablen fuer das Formular setzen.
    $headline = 'Menüelement erstellen';
    $action = 'addtodb';
    $url = 'index.php?section=dashboard';
    $id = 0;

    //Sections aus der Datenbank erhalten.
    $sections = $PICVID['SECTION']->getFromDatabase(0, array('`id`', '`name`'));

    //Optionen fuer die Sections erzeugen.
    $section_options = array();

    //Pruefen ob Sections vorhanden sind.
    if((is_array($sections) === true) && (count($section) > 0)) {

        //Durchlaufen aller Sections.
        foreach($sections as $section) {

            //Pruefen ob es einen Frontendbereich gibt.
            if(file_exists($PICVID['CORE']->getValue('absolute_path').'/section/'.$section->name.'/'.$section->name.'.php') === true) {

                //Pruefen ob die Datei zur Section existiert.
                $section_options[] = HTML::option(array('text' => $section->name, 'value' => $section->id));
            }
        }
    }

    //Select-List fuer die Sections erzeugen.
    $section_select = HTML::select(array('class' => 'span3', 'name' => 'database_sectionid', 'id' => 'database_sectionid'), $section_options);

    //Optionen fuer den Typ erzeugen.
    $typ_options = array();
    $typ_options[] = HTML::option(array('text' => 'Bereich', 'value' => 0));
    $typ_options[] = HTML::option(array('text' => 'URL', 'value' => 1));

    //Select-List fuer die Typen erzeugen.
    $type_select = HTML::select(array('class' => 'span3', 'name' => 'database_type', 'id' => 'database_type', 'onchange' => 'changeType()', 'onkeyup' => 'changeType()'), $typ_options);

    //Formular laden.
	require_once('form.php');
} elseif(isset($_POST['addtodb'])) {

    //SQL-Befehl setzen (Letzte Ordering).
    $PICVID['DATABASE']->setQuery('SELECT MAX(`OrderNum`) FROM `#__menu`');

    //Typ abrufen.
    $type = $PICVID['CORE']->getParameter($_POST, 'database_type', 0);

    //SQL-Befehl erzeugen (Menueeintrag erstellen).
    $sql = "INSERT INTO `#__menu` (`SectionID`, `OrderNum`, `ShowName`, `URL`, `Type`) VALUES (".(($type == 0) ? $PICVID['CORE']->getParameter($_POST, 'database_sectionid', 0) : 0).", ";
    $sql .= ((int) $PICVID['DATABASE']->getResult() + 1).", '".$PICVID['CORE']->getParameter($_POST, 'database_showname', '')."', '".(($type == 1) ? $PICVID['CORE']->getParameter($_POST, 'database_url', '') : '')."', ";
    $sql .= (int) $type.")";

    //SQL-Befehl setzen (Menueeintrag erstellen).
    $PICVID['DATABASE']->setQuery($sql);

    //SQL-Befehl ausfuehren.
    if($PICVID['DATABASE']->query() === true) {

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement wurde erfolgreich erstellt.', 'success');
        exit;
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement konnte nicht erstellt werden.', 'error');
        exit;
    }
} elseif(isset($_REQUEST['edit'])) {

    //Variablen fuer das Formular setzen.
    $headline = 'Menüelement bearbeiten';
    $action = 'edittodb';
    $id = $PICVID['CORE']->getParameter($_REQUEST, 'id', 0);

    //Menueelement abrufen.
    $PICVID['DATABASE']->setQuery('SELECT * FROM `#__menu` WHERE `id` = '.(int) $id);
    $menuElement = $PICVID['DATABASE']->getObjectArray();

    //Menueelement umkopieren.
    $menuElement = $menuElement[0];

    //Sections aus der Datenbank erhalten.
    $sections = $PICVID['SECTION']->getFromDatabase(0, array('`id`', '`name`'));

    //Optionen fuer die Sections erzeugen.
    $section_options = array();

    //Pruefen ob Sections vorhanden sind.
    if((is_array($sections) === true) && (count($section) > 0)) {

        //Durchlaufen aller Sections.
        foreach($sections as $section) {

            //Pruefen ob es einen Frontendbereich gibt.
            if(file_exists($PICVID['CORE']->getValue('absolute_path').'/section/'.$section->name.'/'.$section->name.'.php') === true) {

                //Pruefen ob die Datei zur Section existiert.
                $section_options[] = HTML::option(array('text' => $section->name, 'value' => $section->id));
            }
        }
    }

    //Select-List fuer die Sections erzeugen.
    $section_select = HTML::select(array('class' => 'span3', 'name' => 'database_sectionid', 'id' => 'database_sectionid', 'selected' => $menuElement->SectionID), $section_options);

    //URL abrufen.
    if(trim($menuElement->URL) !== '') {
        $url = $menuElement->URL;
    } else {
        $url = 'index.php?section=dashboard';
    }

    //Optionen fuer den Typ erzeugen.
    $typ_options = array();
    $typ_options[] = HTML::option(array('text' => 'Bereich', 'value' => 0));
    $typ_options[] = HTML::option(array('text' => 'URL', 'value' => 1));

    //Select-List fuer die Typen erzeugen.
    $type_select = HTML::select(array('class' => 'span3', 'name' => 'database_type', 'id' => 'database_type', 'selected' => $menuElement->Type, 'onchange' => 'changeType()', 'onkeyup' => 'changeType()'), $typ_options);

    //Anzeigename setzen.
    $editmenuShowName = $menuElement->ShowName;

    //Formular laden.
    require_once('form.php');
} elseif(isset($_POST['edittodb'])) {

    //ID ermitteln.
    $id = $PICVID['CORE']->getParameter($_POST, 'editid', 0);

    //Typ abrufen.
    $type = $PICVID['CORE']->getParameter($_POST, 'database_type', 0);

    //SQL-Befehl erzeugen (Menueelement aktualsieren).
    $sql = "UPDATE `#__menu` SET `SectionID` = ".(($type == 0) ? $PICVID['CORE']->getParameter($_POST, 'database_sectionid', 0) : 0).", ";
    $sql .= "`ShowName` = '".$PICVID['CORE']->getParameter($_POST, 'database_showname', '')."', `URL` = '".(($type == 1) ? $PICVID['CORE']->getParameter($_POST, 'database_url', '') : '')."', ";
    $sql .= "`Type` = ".(int) $type." WHERE `id` = ".(int) $id;

    //SQL-Befehl setzen (Menueelement aktualsieren).
    $PICVID['DATABASE']->setQuery($sql);

    //SQL-Befehl ausfuehren.
    if($PICVID['DATABASE']->query() === true) {

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement wurde erfolgreich aktualsiert.', 'success');
        exit;
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement konnte nicht aktualsiert werden.', 'error');
        exit;
    }
} elseif(isset($_REQUEST['delete'])) {

    //ID des Elements ermitteln welches gelöscht werden soll.
    $id = $PICVID['CORE']->getParameter($_REQUEST, 'id', 0);

    //SQL-befehl setzen (Ordering abrufen).
    $PICVID['DATABASE']->setQuery("SELECT `OrderNum` FROM `#__menu` WHERE `id` = ".(int) $id);
    $ordering = $PICVID['DATABASE']->getResult();

    //SQL-Befehl setzen (Eintrag loeschen).
    $PICVID['DATABASE']->setQuery("DELETE FROM `#__menu` WHERE `id` = ".(int) $id);

    //SQL-Befehl ausfuehren (Eintrag loeschen).
	if($PICVID['DATABASE']->query() === true) {

        //SQL-Befehl setzen und ausfuehren (Elemente verschieben).
        $PICVID['DATABASE']->setQuery("UPDATE `#__menu` SET `OrderNum` = `OrderNum` - 1 WHERE `OrderNum` > ".(int) $ordering);
        $PICVID['DATABASE']->query();

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement wurde erfolgreich gelöscht!', 'success');
        exit;
	} else {

        //Weiterleiten mit Statusmeldung.
        $PICVID['CORE']->redirect('index.php?section=menu&admin=1', 'Menüelement konnte nicht gelöscht werden!', 'error');
        exit;
	}
} else {
?>
    <!-- Warnmeldung setzen um ggf. anzuzeigen. -->
    <div class="modal hide" id="warning">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3><font style="color:#f00;">Achtung!</font></h3>
        </div>
        <div class="modal-body">
           <p>Möchten Sie das ausgewählte Element wirklich löschen?</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
            <a id="delete-link" href="#" class="btn btn-danger">Löschen</a>
        </div>
    </div>

    <!-- Tabelle mit allen Menuepunkten ausgeben. -->
    <h3>Menüverwaltung</h3>
    <hr/>
    <div class="container">
        <table class="table list-table table-striped table-bordered table-condensed">
            <thead>
                <tr>
                    <th width="7%">#</th>
                    <th>Anzeigetitel</th>
                    <th>Bereich / URL</th>
                    <th width="12%">Position</th>
                    <th width="12%"></th>
                </tr>
            </thead>
            <tbody>
            <?php
            //Vorhandene Menüeinträge auslesen
            $PICVID['DATABASE']->setQuery("SELECT `id`, `ShowName`, `SectionID`, `OrderNum`, `URL`, `Type` FROM `#__menu` ORDER BY `OrderNum`");

            //Alle Eintraege ermitteln.
            $menu = $PICVID['DATABASE']->getObjectArray();

            //Pruefen ob Eintraege vorhanden sind.
            if((is_array($menu) === true) && (count($menu) > 0)) {

                //Zaehler zuruecksetzen.
                $line_number = 1;

                //Durchlaufen der Menueeintraege.
                foreach($menu as $row) {

                    //Instanz einer Section erzeugen.
                    $Section = new Section($PICVID['DATABASE']);

                    //Laden der Section aus der Datenbank.
                    $Section->loadFromDatabase($row->SectionID);

                    //Pruefen ob eine Section geladen werden konnte.
                    if(($Section->_id > 0) && ($Section->_state == 1)) {
                    ?>
                    <tr>
                        <td><?php echo $line_number++; ?></td>
                        <td><?php echo $row->ShowName; ?></td>
                        <td><?php echo ($row->Type == 0) ? $Section->_name : $row->URL; ?></td>
                        <td>
                            <?php if(($row->OrderNum > 1) && ($row->OrderNum <= count($menu))) { ?>
                            <a href="index.php?section=menu&admin=1&id=<?php echo $row->id; ?>&offset=-1&ordering=<?php echo $row->OrderNum; ?>&updateOrdNum=1" class="btn"><i class="icon-arrow-up"></i></a>
                            <?php } ?>
                            <?php if($row->OrderNum < count($menu)) { ?>
                            <a href="index.php?section=menu&admin=1&id=<?php echo $row->id; ?>&offset=1&ordering=<?php echo $row->OrderNum; ?>&updateOrdNum=1" class="btn"><i class="icon-arrow-down"></i></a>
                            <?php } ?>
                        </td>
                        <td>
                            <a class="btn btn-success" href="index.php?section=menu&admin=1&id=<?php echo $row->id; ?>&edit=1"><i class="icon-pencil"></i></a>
                            <a class="btn btn-danger" data-toggle="modal" data-target="#warning" href="#" onclick="document.getElementById('delete-link').href='index.php?section=menu&admin=1&id=<?php echo $row->id; ?>&delete=1'"><i class="icon-trash"></i></a>
                        </td>
                    </tr>
                    <?php
                    }
                }
            } else {
            ?>
                <tr>
                    <td colspan="5">Kein Eintrag vorhanden!</td>
                </tr>
            <?php
            }
            ?>
                <tr>
                    <td colspan="4"></td>
                    <td><a href="index.php?section=menu&admin=1&add=1" class="btn btn-success"><i class="icon-plus"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php
}
?>