<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Bereiche</title>');

//Script um den Kalender verwenden zu koennen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD",'<script src="'.$PICVID['CORE']->getValue('site_url').'/includes/js/dateTimePicker.min.js"></script>');

//Werte des Bereichs ermitteln.
$id = $PICVID['CORE']->getParameter($_REQUEST, 'id', 0);
$task = $PICVID['CORE']->getParameter($_REQUEST, 'task', 'show');

//Pruefen ob die Bearbeitung abgebrochen werden soll.
if($task === 'cancel') {

    //Weiterleiten mit Statusmeldung.
    $PICVID['CORE']->redirect('index.php?section=section_management&admin=1', 'Vorgang abgebrochen.', 'success');
}

//Pruefen ob ein Bereich geloescht werden soll.
if($task === 'delete') {

    //Loeschen des gewaehlten Bereichs.
    if($PICVID['SECTION']->delete($id) === true) {
        $PICVID['CORE']->redirect('index.php?section=section_management&admin=1', 'Bereich wurde erfolgreich gelöscht.', 'success');
    } else {
        $PICVID['CORE']->redirect('index.php?section=section_management&admin=1', 'Bereich konnte nicht gelöscht werden.', 'error');
    }
}

//Pruefen ob ein Bereich gespeichert werden soll.
if($task === 'save') {

    //Instanz eines Bereichs erstellen.
    $Section = new Section($PICVID['DATABASE']);

    //Laden eines Bereichs.
    $Section->loadFromArray($_POST);

    //Bereich aktualisieren.
    if($Section->update() === true) {
        $PICVID['CORE']->redirect('index.php?section=section_management&admin=1', 'Bereich wurde erfolgreich aktualisiert.', 'success');
    } else {
        $PICVID['CORE']->redirect('index.php?section=section_management&admin=1', 'Bereich konnte nicht aktualisiert werden.', 'error');
    }
}

//Pruefen ob alle Bereiche angezeigt werden sollen.
if(($task !== 'delete') && ($task !== 'edit') && ($task !== 'save') && ($task !== 'cancel')) {

    //Alle Bereiche ermitteln.
    $sections = $PICVID['SECTION']->getFromDatabase(0, array('`id`', '`name`', '`menu_title`', '`state`', '`version`'));
?>
    <div class="container">

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

        <!-- Tabelle mit allen Berechen ausgeben. -->
        <h3>Bereiche</h3>
        <hr/>
        <table class="table list-table table-striped table-bordered table-condensed">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Anzeigetitel</th>
                    <th>Status</th>
                    <th>Version</th>
                    <th width="12%"></th>
                </tr>
            </thead>
            <tbody>
            <?php
            //Pruefen ob Bereiche vorhanden sind.
            if((is_array($sections) === true) && (count($sections) > 0)) {

                //Zeilennummer zuruecksetzen.
                $line_number = 1;

                //Durchlaufen aller Bereiche.
                foreach($sections as $section) {
                ?>
                <tr>
                    <td><?php echo $line_number++; ?></td>
                    <td><?php echo $section->name; ?></td>
                    <td><?php echo $section->menu_title; ?></td>
                    <td><?php echo ($section->state == 1) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>'; ?></td>
                    <td><?php echo $section->version; ?></td>
                    <td>
                        <a class="btn btn-success" href="index.php?section=section_management&admin=1&id=<?php echo $section->id;?>&task=edit"><i class="icon-pencil"></i></a>
                        <a class="btn btn-danger" data-toggle="modal" data-target="#warning" href="#" onclick="document.getElementById('delete-link').href='index.php?section=section_management&admin=1&id=<?php echo $section->id;?>&task=delete'"><i class="icon-trash"></i></a>
                    </td>
                </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
<?php
}

//Pruefen ob ein Bereich bearbeitet werden soll.
if(($task === 'edit') && ($id > 0)) {

    //Instanz eines Bereich erzeugen.
    $Section = new Section($PICVID['DATABASE']);

    //Laden des Bereichs aus der Datenbank.
    $Section->loadFromDatabase($id);

    //Optionen fuer die Status erzeugen.
	$section_state_options = array();
	$section_state_options[] = HTML::option(array('text' => 'Aktiviert', 'value' => 1));
	$section_state_options[] = HTML::option(array('text' => 'Deaktiviert', 'value' => 0));

    //Select-List fuer den Status erzeugen.
    $html['section_state']= HTML::select(array('class' => 'input-xlarge', 'id' => 'section_state', 'name' => 'section_state', 'selected' => $Section->_state), $section_state_options);

    //Select-List fuer den Ablauf-Status erzeguen.
    $html['section_expiry_state']= HTML::select(array('class' => 'input-xlarge', 'id' => 'section_expiry_state', 'name' => 'section_expiry_state', 'selected' => $Section->_expiry_state), $section_state_options);

    //Optionen fuer die Kategorien erzeugen.
    $section_category_options = array();
    $section_category_options[] = HTML::option(array('text' => '-- Kategorie wählen --', 'value' => 0));
    $section_category_options[] = HTML::option(array('text' => 'Hauptmenü', 'value' => -1));
    $section_category_options[] = HTML::option(array('text' => 'PicVid', 'value' => 1));
    $section_category_options[] = HTML::option(array('text' => 'Verwaltung', 'value' => 2));
    $section_category_options[] = HTML::option(array('text' => 'Extensions', 'value' => 3));

    //Select-List fuer die Kategorien erzeugen.
    $html['section_category_id'] = HTML::select(array('id' => 'section_category_id', 'name' => 'section_category_id', 'selected' => $Section->_category_id, 'size' => 1), $section_category_options);

    //Optionen fuer die Gruppen erzeugen.
    $section_group_options = array();
	$section_group_options[] = HTML::option(array('text' => 'Besucher', 'value' => 0));
	$section_group_options[] = HTML::option(array('text' => 'Registriert', 'value' => 1));
	$section_group_options[] = HTML::option(array('text' => 'Moderator', 'value' => 2));
	$section_group_options[] = HTML::option(array('text' => 'Administrator', 'value' => 3));

    //Select-List fuer die Gruppen der Administration erzeugen.
    $html['section_admin_group']= HTML::select(array('class' => 'input-xlarge', 'id' => 'section_admin_group', 'name' => 'section_admin_group', 'selected' => $Section->_admin_group), $section_group_options);

    //Select-List fuer die Gruppen des Frontends erzeugen.
    $html['section_user_group']= HTML::select(array('class' => 'input-xlarge', 'id' => 'section_user_group', 'name' => 'section_user_group', 'selected' => $Section->_user_group), $section_group_options);
?>
<div class="container">
    <form action="index.php?section=section_management&admin=1" class="form-horizontal" method="post">
        <h3>Bereich "<?php echo $Section->_name; ?>" bearbeiten</h3>
		<hr />
		<div class="control-group">
            <label class="control-label" for="section_id">ID</label>
		    <div class="controls">
		    	<input type="text" class="input-xlarge" name="section_id" id="section_id" value="<?php echo $Section->_id; ?>" readonly>
			</div>
	    </div>
        <div class="control-group">
            <label class="control-label" for="section_expiry_state">Ablaufstatus</label>
		    <div class="controls">
                <?php echo $html['section_expiry_state']; ?>
            </div>
        </div>
        <div class="control-group">
		    <label class="control-label" for="section_expiry_time">Ablaufzeitpunkt</label>
		    <div class="controls">
		    	<input type="text" class="input-xlarge" name="section_expiry_time" id="section_expiry_time" value="<?php echo $Section->_expiry_time; ?>" onclick="newCalendar('section_expiry_time','yyyyMMdd','dropdown',true,'24',true)">
			</div>
	    </div>
        <div class="control-group">
		    <label class="control-label" for="section_name">Name</label>
		    <div class="controls">
		    	<input type="text" class="input-xlarge" name="section_name" id="section_name" value="<?php echo $Section->_name; ?>">
			</div>
	    </div>
    	<div class="control-group">
		    <label class="control-label" for="section_publish_start_time">Veröffentlichungsbeginn</label>
		    <div class="controls">
		    	<input type="text" class="input-xlarge" name="section_publish_start_time" id="section_publish_start_time" value="<?php echo $Section->_publish_start_time; ?>" onclick="newCalendar('section_publish_start_time','yyyyMMdd','dropdown',true,'24',true)">
			</div>
	    </div>
    	<div class="control-group">
		    <label class="control-label" for="section_publish_end_time">Veröffentlichungsende</label>
		    <div class="controls">
		    	<input type="text" class="input-xlarge" name="section_publish_end_time" id="section_publish_end_time" value="<?php echo $Section->_publish_end_time; ?>" onclick="newCalendar('section_publish_end_time','yyyyMMdd','dropdown',true,'24',true)">
			</div>
	    </div>
	   <div class="control-group">
		    <label class="control-label" for="section_state">Status</label>
		    <div class="controls">
		    	<?php echo $html['section_state']; ?>
			</div>
	    </div>
        <div class="control-group">
		    <label class="control-label" for="section_user_group">Benutzergruppe</label>
		    <div class="controls">
		    	<?php echo $html['section_user_group']; ?>
			</div>
	    </div>
        <div class="control-group">
		    <label class="control-label" for="section_admin_group">Administratorgruppe</label>
		    <div class="controls">
		    	<?php echo $html['section_admin_group']; ?>
			</div>
	    </div>
	   <div class="control-group">
            <label class="control-label" for="section_category_id">Kategorie</label>
            <div class="controls">
                <?php echo $html['section_category_id']; ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="section_menu_title">Anzeigetitel</label>
            <div class="controls">
                <input type="text" class="input-xlarge" name="section_menu_title" id="section_menu_title" value="<?php echo $Section->_menu_title; ?>"/>
            </div>
        </div>
        <div class="control-group">
		    <label class="control-label" for="section_version">Version</label>
		    <div class="controls">
		    	<input type="text" class="input-xlarge" name="section_version" id="section_version" value="<?php echo $Section->_version; ?>" readonly>
			</div>
	    </div>
	    <div class="form-actions">
            <button name="task" class="btn btn-success" value="save"><i class="icon-ok"></i> <font style="color:#000;">Speichern</font></button>
            <button name="task" class="btn btn-warning" value="cancel"><i class="icon-arrow-left"></i> <font style="color:#000;">Abbrechen</font></button>
            <?php
            if(file_exists($PICVID['CORE']->getValue('absolute_path').'/section/'.$Section->_name.'/'.$Section->_name.'_admin.php') === true) {
            ?>
            <a class="btn" href="index.php?section=<?php echo $Section->_name; ?>&admin=1" target="_blank"><i class="icon-wrench"></i> Administration</a>
            <?php
            }
            if(file_exists($PICVID['CORE']->getValue('absolute_path').'/section/'.$Section->_name.'/'.$Section->_name.'.php') === true) {
            ?>
            <a class="btn" href="index.php?section=<?php echo $Section->_name; ?>" target="_blank"><i class="icon-home"></i> Frontend</a>
            <?php
            }
            if($Section->_name === 'videoshow') {
            ?>
            <a class="btn" href="index.php?section=detailed&location=video" target="_blank"><i class="icon-film"></i> Videos (Details)</a>
            <?php
            }
            if($Section->_name === 'slideshow') {
            ?>
            <a class="btn" href="index.php?section=detailed&location=image" target="_blank"><i class="icon-picture"></i> Bilder (Details)</a>
            <?php
            }
            ?>
        </div>
    </form>
</div>
<?php } ?>