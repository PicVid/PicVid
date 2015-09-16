<?php
//PHP-Fehlerausgabe aktivieren.
error_reporting(E_ALL);

//Einbinden der benoetigten Klassen.
require_once(dirname(__FILE__).'/includes/core.class.php');
require_once(dirname(__FILE__).'/includes/crypt.class.php');
require_once(dirname(__FILE__).'/includes/cito.class.php');
require_once(dirname(__FILE__).'/includes/database.class.php');
require_once(dirname(__FILE__).'/includes/html.class.php');
require_once(dirname(__FILE__).'/includes/user.class.php');
require_once(dirname(__FILE__).'/includes/media.class.php');
require_once(dirname(__FILE__).'/includes/section.class.php');

//Erzeugen eines Arrays mit allen Objekten von PicVid.
$PICVID = array();
$PICVID['CORE'] = new Core();
$PICVID['ENCRYPTION'] = new Encryption($PICVID['CORE']->getValue('security_key'));
$PICVID['DATABASE'] = $PICVID['CORE']->getDatabaseObject();
$PICVID['MEDIA'] = new Media($PICVID['DATABASE']);
$PICVID['SECTION'] = new Section($PICVID['DATABASE']);
$PICVID['TEMPLATE_ENGINE'] = new TemplateEngine();
$PICVID['USER'] = new User($PICVID['DATABASE']);
$PICVID['USER_GROUP'] = new UserGroup($PICVID['DATABASE']);
$PICVID['USER_SESSION'] = new UserSession($PICVID['DATABASE']);
$PICVID['ACT_USER'] = new User($PICVID['DATABASE']);

//Automatische Felder aktualsieren.
$PICVID['SECTION']->updateAutoValues();
$PICVID['USER']->updateAutoValues();

//Parameter fuer Aktionen ermitteln.
$key = $PICVID['CORE']->getParameter($_REQUEST, 'key', '');
$task = $PICVID['CORE']->getParameter($_REQUEST, 'task', '');

//Pruefen ob ein Benutzer aktiviert werden soll.
if(($task === 'activation') && (trim($key) !== '')) {
    $PICVID['USER']->activate($PICVID['USER']->activate($key));
}

//Name der Session setzen.
session_name(md5($PICVID['CORE']->getValue('site_url')));

//Eine neue Session starten.
session_start();

//Pruefen ob der Benutzer abgemeldet werden soll.
if($task === 'user_logout') {

    //Benutzersession aus der Datenbank entfernen.
    $PICVID['USER_SESSION']->delete();

    //Weiterleiten mit Statusmeldung.
    if((isset($_SERVER['HTTP_REFERER']) === true) && (strpos($_SERVER['HTTP_REFERER'], $PICVID['CORE']->getValue('site_url')) !== false)) {
        $PICVID['CORE']->redirect($_SERVER['HTTP_REFERER'], 'Benutzer wurde erfolgreich abgemeldet.', 'success');
        exit;
    } else {
        $PICVID['CORE']->redirect('index.php', 'Benutzer wurde erfolgreich abgemeldet.', 'success');
        exit;
    }
} else {

    //Werte aus der Session in den Benutzer laden.
    $PICVID['ACT_USER']->loadFromSession();
}

//Benutzersession aktualisieren.
$PICVID['USER_SESSION']->update();

//Validieren der Benutzersession des aktuellen Benutzers.
if($PICVID['USER_SESSION']->validate($PICVID['ACT_USER']) === false) {

    //Benutzer zuruecksetzen.
    $PICVID['ACT_USER'] = null;

    //Pruefen ob ein Benutzer eingeloggt werden soll.
    if($task === 'user_login') {

        //Benutzer am System anmelden.
        $PICVID['ACT_USER'] = $PICVID['USER']->authenticate();

        //Fehlermeldung in der Session speichern.
        $_SESSION['message_text'] = 'Benutzer konnte nicht angemeldet werden.';
        $_SESSION['message_level'] = 'error';
    }

    //Pruefen ob ein Benutzer sich registrieren will.
    if($task === 'user_register') {

        //Benutzer auf die Registrierung weiterleiten.
        $PICVID['CORE']->redirect('index.php?section=register');
    }
}

//Pruefen ob keine Section ausgewaehlt wurde.
if($PICVID['CORE']->getParameter($_REQUEST, 'section', '') === '') {

    //Weiterleiten auf das Dashboard.
    $PICVID['CORE']->redirect('index.php?section=dashboard');
    exit;
}

//Aktuelles Template einbinden.
require_once($PICVID['CORE']->getValue('absolute_path').'/template/index.php');

//Template-Engine ausfuehren.
$PICVID['TEMPLATE_ENGINE']->execute();
?>