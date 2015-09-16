<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Benutzerverwaltung</title>');

//Script um den Kalender verwenden zu koennen einbinden.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD",'<script src="'.$PICVID['CORE']->getValue('site_url').'/includes/js/dateTimePicker.min.js"></script>');

//Werte fuer die Section ermitteln.
$user_id = $PICVID['CORE']->getParameter($_REQUEST, 'user_id', 0);

//Pruefen welcher Task ausgefuehrt wird.
switch($task) {
    case 'user_add':
        user_managementPHP::edit_user(0);
        break;
    case 'user_delete':
        user_managementPHP::delete_user($user_id);
        break;
    case 'user_edit':
        user_managementPHP::edit_user($user_id);
        break;
    case 'user_save':
        user_managementPHP::save_user($user_id);
        break;
    case 'user_cancel':
        user_managementPHP::cancel_user();
        break;
    case 'show_user':
    default:
        user_managementPHP::show_user();
        break;
}

/**
 * Klasse um die HTML-Formulare der Benutzerverwaltung verwalten zu koennen.
 * @since 1.0.0
 */
class user_managementHTML {
    /**
     * Methode um einen Benutzer bearbeiten zu koennen.
     * @param array $user Ein Array mit einem Objekt des Benutzer.
     * @param array $html Ein Array mit allen HTML-Elementen (PHP).
     * @since 1.0.0
     */
    public static function edit_user($user, $html) {

        //Pruefen ob ein neuer Benutzer erstellt werden soll.
        if($user->_id > 0) {
            $header = 'Benutzerverwaltung - Bearbeiten ('.$user->_username.')';
        } else {
            $header = 'Benutzerverwaltung - Erstellen';
        }
    ?>
    <form action="index.php?section=user_management&admin=1" class="form-horizontal" method="post">
        <h3><?php echo $header; ?></h3>
        <hr/>
        <div class="row">
            <div class="span5">
                <div class="control-group">
                    <label class="control-label" for="user_id">ID</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" name="user_id" id="user_id" placeholder="ID" value="<?php echo $user->_id; ?>" readonly>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_name">Name</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" name="user_name" id="user_name" placeholder="Name" value="<?php echo $user->_name; ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_email">E-Mail</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" name="user_email" id="user_email" placeholder="E-Mail" value="<?php echo $user->_email; ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_username">Benutzername</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" name="user_username" id="user_username" placeholder="Benutzername" value="<?php echo $user->_username; ?>" <?php echo ($user->_id > 0) ? 'readonly' : ''; ?>>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_password">Passwort</label>
                    <div class="controls">
                        <input type="password" class="input-xlarge" name="user_password" id="user_password" placeholder="Passwort" value="">
                    </div>
                </div>
            </div>
            <div class="span5">
                <div class="control-group">
                    <label class="control-label" for="user_state">Status</label>
                    <div class="controls">
                        <?php echo $html['user_state']; ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_group">Gruppe</label>
                    <div class="controls">
                        <?php echo $html['user_group']; ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_publish_start_time">Veröffentlichungsbeginn</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" name="user_publish_start_time" id="user_publish_start_time" placeholder="Veröffentlichungsbeginn" value="<?php echo $user->_publish_start_time; ?>" onfocus="newCalendar('user_publish_start_time','yyyyMMdd','dropdown',true,'24',true)" onclick="newCalendar('section_publish_start_time','yyyyMMdd','dropdown',true,'24',true)">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_publish_end_time">Veröffentlichungsende</label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" name="user_publish_end_time" id="user_publish_end_time" value="<?php echo $user->_publish_end_time; ?>" onfocus="newCalendar('user_publish_end_time','yyyyMMdd','dropdown',true,'24',true)" onclick="newCalendar('section_publish_end_time','yyyyMMdd','dropdown',true,'24',true)">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="user_description">Beschreibung</label>
                    <div class="controls">
                        <textarea style="height:100px;" class="input-xlarge" name="user_description" id="user_description"><?php echo $user->_description; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button name="task" class="btn btn-success" value="user_save"><i class="icon-ok"></i> <font style="color:#000;">Speichern</font></button>
            <button name="task" class="btn btn-warning" value="user_cancel"><i class="icon-arrow-left"></i> <font style="color:#000;">Abbrechen</font></button>
            <?php if($user->_id > 0) { ?>
                <a class="btn" href="index.php?section=profile&id=<?php echo $user->_id; ?>" target="_blank"><i class="icon-user"></i> Profil anzeigen</a>
            <?php } ?>
        </div>
    </form>
    <?php
    }

    /**
     * Methode um eine Liste aller Benutzer ausgeben zu koennen.
     * @param array Ein Array mit Benutzerobjekten welche die Informationen darstellen.
     * @since 1.0.0
     */
    public static function show_user($users) {

        //Globale PicVid Variable in die Methode binden.
        global $PICVID;
    ?>
    <div class="modal hide" id="warning">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3><font style="color:#f00;">Achtung!</font></h3>
        </div>
        <div class="modal-body">
           <p>Möchten Sie den ausgewählten Benutzer wirklich löschen?</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
            <a id="delete-link" href="#" class="btn btn-danger">Löschen</a>
        </div>
    </div>

    <h3>Benutzerverwaltung</h3>
    <hr/>
    <table class="table list-table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th width="7%">#</th>
                <th>Name</th>
                <th>Benutzername</th>
                <th>Status</th>
                <th>Bestätigt</th>
                <th>Gruppe</th>
                <th width="12%"></th>
            </tr>
        </thead>
        <tbody>
        <?php
        //Pruefen ob Elemente vorhanden sind.
        if((is_array($users) === true) && (count($users) > 0)) {

            //Benutzerzaehler zuruecksetzen.
            $user_count = 0;

            //Durchlaufen aller Benutzer.
            foreach($users as $user) {
        ?>
        <tr>
            <td><?php echo ++$user_count; ?></td>
            <td><?php echo $user->name; ?></td>
            <td><?php echo $user->username; ?></td>
            <td><?php echo ($user->state > 0) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>'; ?></td>
            <td><?php echo ($user->activation === '') ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>'; ?></td>
            <td><?php echo ($user->group == 0) ? 'Besucher' : $PICVID['USER_GROUP']->getName($user->group); ?></td>
            <td>
                <a class="btn btn-success" href="index.php?section=user_management&admin=1&user_id=<?php echo $user->id;?>&task=user_edit"><i class="icon-pencil"></i></a>
                <a class="btn btn-danger" data-toggle="modal" data-target="#warning" href="#" onclick="document.getElementById('delete-link').href='index.php?section=user_management&admin=1&user_id=<?php echo $user->id;?>&task=user_delete'"><i class="icon-trash"></i></a>
            </td>
        </tr>
        <?php
            }
        }
        ?>
        <tr>
            <td colspan="6"></td>
            <td>
                <a class="btn btn-success" href="index.php?section=user_management&admin=1&task=user_add"><i class="icon-plus"></i></a>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
    }
}

/**
 * Klasse um die PHP-Funktionen der Benutzerverwaltung verwalten zu koennen.
 * @since 1.0.0
 */
class user_managementPHP {
    public static function cancel_user() {
        global $PICVID;
        $PICVID['CORE']->redirect('index.php?section=user_management&admin=1', 'Vorgang abgebrochen.', 'success');
    }
    /**
     * Methode um einen Benutzer loeschen zu koennen.
     * @param int $id Die ID des Benutzers welcher geloescht werden soll.
     * @since 1.0.0
     */
    public static function delete_user($id) {

        //Einbinden der globalen PicVid-Variable.
        global $PICVID;

        //Neuen Benutzer laden.
        $User = new User($PICVID['DATABASE']);

        //Benutzer aus der Datenbank laden.
        $User->loadFromDatabase($id);

        //Pruefen ob der aktuelle Benutzer berechtigt ist den Benutzer zu loeschen.
        if(($PICVID['ACT_USER']->_group >= $User->_group) && ($id != $PICVID['ACT_USER']->_id)) {

            //Loeschen und pruefen des Benutzers.
            if($User->delete($User->_id) === true) {
                $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Benutzer wurde erfolgreich gelöscht.', 'success');
            } else {
                $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Benutzer konnte nicht gelöscht werden.', 'error');
            }
        } else {
            $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Sie sind nicht berechtigt diesen Benutzer zu löschen.', 'error');
        }
    }

    /**
     * Methode um einen Benutzer bearbeiten zu koennen.
     * @param int $id Die ID des Benutzers welcher bearbeitet werden soll.
     * @since 1.0.0
     */
    public static function edit_user($id) {

        //Einbinden der globalen PicVid-Variable.
        global $PICVID;

        //Neuen Benutzer laden.
        $User = new User($PICVID['DATABASE']);

        //Pruefen ob eine gueltige ID vorhanden ist.
        if($id > 0) {
            $User->loadFromDatabase($id);

            //Pruefen ob der Benutzer berechtigt ist.
            if(($PICVID['ACT_USER']->_group < $User->_group) || ($id == 1 && $PICVID['ACT_USER']->_id > $id)) {

                //Session mit Meldungen setzen.
                $PICVID['CORE']->redirect('index.php?section=user_management&admin=1', 'Sie sind nicht berechtigt diesen Benutzer zu bearbeiten.', 'error');
            }
        }

        //Status und Ablaufstatus
        $user_state_options = array();
        $user_state_options[] = HTML::option(array('text' => 'aktiv', 'value' => 1));
        $user_state_options[] = HTML::option(array('text' => 'nicht aktiv', 'value' => 0));
        $html['user_state']= HTML::select(array('class' => 'input-xlarge', 'id' => 'user_state', 'name' => 'user_state', 'selected' => $User->_state), $user_state_options);

        //Benutzer- und Admin-Gruppe
        $user_group_options = array();
        $user_group_options[] = HTML::option(array('text' => 'Besucher', 'value' => 0));
        $user_group_options[] = HTML::option(array('text' => 'Registriert', 'value' => 1));
        $user_group_options[] = HTML::option(array('text' => 'Moderator', 'value' => 2));
        $user_group_options[] = HTML::option(array('text' => 'Administrator', 'value' => 3));
        $html['user_group']= HTML::select(array('class' => 'input-xlarge', 'id' => 'user_group', 'name' => 'user_group', 'selected' => $User->_group), $user_group_options);

        //Ausgeben des Formulars.
        user_managementHTML::edit_user($User, $html);
    }

    /**
     * Methode um einen Benutzer speichern zu koennen.
     * @since 1.0.0
     */
    public static function save_user() {

        //Einbinden der globalen PicVid-Variable.
        global $PICVID;

        //Neuen Benutzer laden.
        $User = new User($PICVID['DATABASE']);

        //Benutzer aus dem Array laden.
        $User->loadFromArray($_REQUEST);

        //Pruefen ob ein neuer Benutzer erstellt werden soll.
        if($User->_id < 1) {

            //Pruefen ob der Benutzer erstellt werden konnte.
            if($User->create() === true) {
                $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Benutzer wurde erfolgreich erstellt.', 'success');
            } else {
                $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Benutzer konnte nicht erstellt werden.', 'error');
            }
        } else {

            //Pruefen ob der aktuelle Benutzer berechtigt ist den Benutzer zu bearbeiten.
            if($PICVID['ACT_USER']->_group >= $User->_group) {

                //Pruefen ob der Benutzer aktualsiert werden konnte.
                if($User->update($User->_id) === true) {
                    $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Benutzer wurde erfolgreich gespeichert.', 'success');
                } else {
                    $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Benutzer konnte nicht gespeichert werden.', 'error');
                }
            } else {
                $PICVID['CORE']->redirect('index.php?section=user_management&task=show_user&admin=1', 'Sie sind nicht berechtigt diesen Benutzer zu bearbeiten.', 'error');
            }
        }
    }

    /**
     * Methode um die Liste aller Benutzer ausgeben zu koennen.
     * @since 1.0.0
     */
    public static function show_user() {

        //Einbinden der globalen PicVid-Variable.
        global $PICVID;

        //Abrufen aller Benutzer aus der Datenbank.
        $users = $PICVID['USER']->getFromDatabase();

        //Ausgeben der Liste aller Benutzer.
        user_managementHTML::show_user($users);
    }
}
?>