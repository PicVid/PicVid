<?php
/**
 * Klasse um Benutzer verwalten zu koennen.
 *
 * @author Sebastian Brosch
 * @since 1.0.0
 */
class User {
    /**
     * Eigenschaft um den Aktivierungsschluessel speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_activation = '';

    /**
     * Eigenschaft um den Zeitpunkt der Erstellung speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_create_time = '0000-00-00 00:00:00';

    /**
     * Eigenschaft um eine Datenbankinstanz speichern zu koennen.
     * @since 1.0.0
     * @var object
     */
    private $_database = null;

    /**
     * Eigenschaft um die Beschreibung speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_description = '';

    /**
     * Eigenschaft um eine E-Mail-Adresse speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_email = '';

    /**
     * Eigenschaft um einen Error-Code speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    private $_error_code = '';

    /**
     * Eigenschaft um die Gruppe speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_group = 0;

    /**
     * Eigenschaft um die ID speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_id = 0;

    /**
     * Eigenschaft um den Zeitpunkt des letzten Besuchs speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_lastvisit_time = '0000-00-00 00:00:00';

    /**
     * Eigenschaft um den Namen speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_name = '';

    /**
     * Eigenschaft um das Passwort speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_password = '';

    /**
     * Eigenschaft um das Veroeffentlichungsende speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_publish_end_time = '0000-00-00 00:00:00';

    /**
     * Eigenschaft um den Veroeffentlichungsbeginn speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_publish_start_time = '0000-00-00 00:00:00';

    /**
     * EIgenschaft um den Status speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_state = 1;

    /**
     * Eigenschaft um den Benutzernamen speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_username = '';

    /**
     * Konstruktor der Klasse.
     * @param object $database Ein Datenbankobjekt.
     * @since 1.0.0
     */
    public function __construct($database) {
        $this->_database = (is_object($database) === true) ? $database : null;
    }

    /**
     * Methode um einen Benutzer aktivieren zu koennen.
     * @param string $activation_key Der Schluessel mit welchem der Benutzer aktiviert werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
    public function activate($activation_key) {

        //SQL-Befehl setzen (Benutzer aktivieren).
        $this->_database->setQuery("UPDATE `#__user` SET `activation` = '', `state` = 1 WHERE `activation` = '".$this->_database->getEscaped(trim($activation_key))."'");

        //SQL-Befehl ausfuehren.
        return $this->_database->query();
    }

    /**
     * Methode um einen Benutzer anmelden zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function authenticate() {

        //Globale Variable des CMS in die Methode binden.
        global $PICVID;

        //Formulardaten des Logins einlesen.
        $username = trim($PICVID['CORE']->getParameter($_POST, 'username', ''));
        $password = md5(trim($PICVID['CORE']->getParameter($_POST, 'password')));

        //SQL-Befehl setzen (Benutzer abrufen).
        $this->_database->setQuery("SELECT `id` FROM `#__user` WHERE `username` = '".$this->_database->getEscaped($username)."' AND `password` = '".$password."' AND `activation` = '' AND `state` = 1 AND `id` NOT IN (SELECT `user_id` FROM `#__user_session`)");

        //ID des Benutzers erhalten.
        $user_id = $this->_database->getResult();

        //Pruefen ob eine ID ermittelt wurde.
        if($user_id > 0) {

            //Instanz eines Benutzers erzeugen.
            $User = new User($this->_database);

            //Benutzer aus der Datenbank laden.
            $User->loadFromDatabase($user_id);

            //Anmeldezeitpunkt in die Benutzerklasse schreiben.
            $User->_lastvisit_time = date('Y-m-d H:i:s');

            //Erzeugen und pruefen ob die Session fuer den aktuellen Benutzer erzeugt werden konnte.
            if($PICVID['USER_SESSION']->create($User) === true) {

                //SQL-Befehl setzen.
                $this->_database->setQuery("UPDATE `#__user` SET `lastvisit_time` = '".$this->_database->getEscaped($User->_lastvisit_time)."' WHERE `id` = ".(int) $User->_id);

                //SQL-befehl ausfuehren.
                if($this->_database->query() === true) {

                    //Weiterleiten um auf das Backend zu gelangen.
                    if((isset($_SERVER['HTTP_REFERER']) === true) && (strpos($_SERVER['HTTP_REFERER'], $PICVID['CORE']->getValue('site_url')) !== false)) {
                        $PICVID['CORE']->redirect($_SERVER['HTTP_REFERER']);
                        exit;
                    } else {
                        $PICVID['CORE']->redirect('index.php');
                        exit;
                    }
                }
            }
        }

        //Status zurueckgeben.
        return false;
    }

    /**
     * Methode um alle Eigenschaften der Klasse pruefen zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function checkProperties() {

        //Pruefen der Eigenschaften.
        if(preg_match('/^[0-9]{4}(-[0-9]{2}){2} [0-9]{2}(:[0-9]{2}){2}$/', $this->_create_time) == 0) {
            $this->_error_code = 'CREATE_TIME_ERROR';
            return false;
        }
        if(preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_\-]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $this->_email) == 0) {
            $this->_error_code = 'EMAIL_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]+$/', $this->_group) == 0) {
            $this->_error_code = 'GROUP_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]+$/', $this->_id) == 0) {
            $this->_error_code = 'ID_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]{4}(-[0-9]{2}){2} [0-9]{2}(:[0-9]{2}){2}$/', $this->_lastvisit_time) == 0) {
            $this->_error_code = 'LASTVISIT_TIME_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]{4}(-[0-9]{2}){2} [0-9]{2}(:[0-9]{2}){2}$/', $this->_publish_end_time) == 0) {
            $this->_error_code = 'PUBLISH_END_TIME_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]{4}(-[0-9]{2}){2} [0-9]{2}(:[0-9]{2}){2}$/', $this->_publish_start_time) == 0) {
            $this->_error_code = 'PUBLISH_START_TIME_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]+$/', $this->_state) == 0) {
            $this->_error_code = 'STATE_ERROR';
            return false;
        }

        //Status zurueckgeben.
        return true;
    }

    /**
     * Methode um einen Benutzer erstellen zu koennen.
     * @param bool $activate Optional. Der Status ob eine E-Mail-Aktivierung verwendet werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
    public function create($activate = false) {

        //Globale Variable von PICVID einbinden.
        global $PICVID;

        //Fehlercode zuruecksetzen.
        $this->_error_code = '';

        //Eigenschaften pruefen.
        if(($this->checkProperties() === false) || (trim($this->_username) === '') || (trim($this->_name) === '') || (trim($this->_password) === '')) {

            //Fehlercode setzen.
            $this->_error_code = (trim($this->_error_code) === '') ? 'PROPERTIES_ERROR' : $this->_error_code;

            //Status zurueckgeben.
            return false;
        }

        //SQL-Befehl setzen (Benutzer pruefen).
        $this->_database->setQuery("SELECT COUNT(`id`) FROM `#__user` WHERE `username` = '".$this->_database->getEscaped(trim($this->_username))."' OR `email` = '".$this->_database->getEscaped(trim($this->_email))."'");

        //Pruefen ob der Benutzer bereits existiert.
        if($this->_database->getResult() > 0) {
            $this->_error_code = 'EXISTS_ERROR';
            return false;
        } else {

            //Pruefen welche Benutzergruppe verwendet wird.
            if(($this->_group < 2) && ($activate === true)) {

                //E-Mail an den Benutzer senden.
                $this->deactivate();
            }

            //SQL-Befehl erzeugen (Benutzer erstellen).
            $sql = "INSERT INTO `#__user` (`activation`, `create_time`, `description`, `email`, `group`, `name`, `password`, `publish_end_time`, `publish_start_time`, `state`, `username`) ";
            $sql .= "VALUES ('".$this->_database->getEscaped(trim($this->_activation))."', NOW(), '".$this->_database->getEscaped(trim($this->_description))."', ";
            $sql .= "'".$this->_database->getEscaped(trim($this->_email))."', ".(int) $this->_group.", '".$this->_database->getEscaped(trim($this->_name))."', ";
            $sql .= "'".$this->_database->getEscaped(md5(trim($this->_password)))."', '".$this->_database->getEscaped(trim($this->_publish_end_time))."', ";
            $sql .= "'".$this->_database->getEscaped(trim($this->_publish_start_time))."', ".(int) $this->_state.", '".$this->_database->getEscaped(trim($this->_username))."')";

            //SQL-Befehl setzen.
            $this->_database->setQuery($sql);

            //SQL-Befehl ausfuehren und Status zurueckgeben.
            return $this->_database->query();
        }
    }

    /**
     * Methode um einen Aktivierungslink zu versenden.
     * @return Der Status.
     * @since 1.0.0
     */
    public function deactivate() {

        //Globale Variable von PICVID einbinden.
        global $PICVID;

        //Activation-Code setzen.
        $this->_activation = md5($PICVID['ENCRYPTION']->encrypt($this->_username.$this->_email));

        //Informationen der E-Mail setzen.
        $subject = 'Activate your Account for PicVid';
        $message = 'Dear '.trim($this->_name)."\n\n".'Please verify your email on: '.trim($PICVID['CORE']->getValue('site_url')).'/index.php?task=activation&key='.trim($this->_activation);

        //Email versenden.
        mail($this->_email, $subject, $message);
    }

    /**
     * Methode um einen Benutzer loeschen zu koennen.
     * @param int $id Die ID des Benutzers welcher geloescht werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
    public function delete($id) {

        //SQL-Befehl setzen (Benutzer loeschen).
        $this->_database->setQuery("DELETE FROM `#__user` WHERE `id` = ".(int) $id);

        //SQL-Befehl ausfuehren und Status zurueckgeben.
        return $this->_database->query();
    }

    /**
     * Methode um den Fehlercode aus der Klasse ermitteln zu koennen.
     * @return Der Fehlercode aus der Klasse.
     * @since 1.0.0
     */
    public function getErrorCode() {
        return $this->_error_code;
    }

    /**
     * Methode um einen oder mehrere Benutzer aus der Datenbank zu erhalten.
     * @param int $id Optional. Die ID des Benutzer welcher geladen werden soll.
     * @param array $fields Optional. Ein Array mit Feldern des Benutzers welche mit zurueckgegeben werden sollen.
     * @param string $filter Optional. Ein gueltiger SQL-Befehl um die Daten filtern zu koennen.
     * @return Ein Array mit Objekten welcher jeweils einen Benutzer darstellen.
     * @since 1.0.0
     */
    public function getFromDatabase($id = 0, $fields = 0, $filter = '') {

        //Pruefen ob ein bestimmtes Element geladen werden soll.
        if($id > 0) {

            //Pruefen ob ein Filter vorhanden ist.
            if(trim($filter) === '') {

                //Filter erweitern.
                $filter .= ' WHERE `id` = '.(int) $id;
            } else {

                //Filter erweitern.
                $filter = ' WHERE '.$filter.' AND `id` = '.(int) $id;
            }
        } else {

            //Pruefen ob ein Filter vorhanden ist.
            if(trim($filter) !== '') {

                //Filter erweitern.
                $filter = ' WHERE '.$filter;
            }
        }

        //Pruefen ob Felder uebergeben wurden.
        if((is_array($fields) === true) && (count($fields) > 0)) {

            //SQL-Befehl setzen (Benutzer ermitteln).
            $this->_database->setQuery("SELECT ".implode(', ', $fields)." FROM `#__user`".$filter);
        } else {

            //SQL-Befehl setzen (Benutzer ermitteln).
            $this->_database->setQuery("SELECT * FROM `#__user`".$filter);
        }

        //SQL-Befehl ausfuehren und Array mit Objekten zurueckgeben.
        return $this->_database->getObjectArray();
    }

    /**
     * Methode um einen Benutzernamen anhand der ID ermitteln zu koennen.
     * @param int $id Die ID des Benutzers.
     * @return Der Benutzername.
     * @since 1.0.0
     */
    public function getUsernameFromID($id) {

        //Benutzername ermitteln.
        $this->_database->setQuery("SELECT `username` FROM `#__user` WHERE `id` = ".(int) $id);

        //Zurueckgeben des Benutzernamens.
        return $this->_database->getResult();
    }

    /**
     * Methode um ein Array in diese Klasse laden zu koennen.
     * @param array $array Der Array welcher in diese Klasse geladen werden soll.
     * @since 1.0.0
     */
    public function loadFromArray($array) {

        //Alle Klasseneigensschaften dieser Klasse ermitteln.
        $properties = get_class_vars(get_class($this));

        //Prueen ob ein gueltiger Array uebergeben wurde.
        if((is_array($array) === true) && (count($array) > 0) && (count($properties) > 0)) {

            //Durchlaufen aller Klasseneigenschaften.
            foreach($properties as $property => $value) {

                //Array-Eigenschaft erzeugen.
                $array_property = 'user'.$property;

                //Pruefen ob der Index vorhanden ist.
                if(isset($array[$array_property]) === true) {

                    //Wert in die Klasse schreiben.
                    $this->$property = $array[$array_property];
                }
            }
        }
    }

    /**
     * Methode um Werte aus der Datenbank in diese Klasse zu laden.
     * @param int $id Die ID des Benutzers welcher geladen werden soll.
     * @since 1.0.0
     */
    public function loadFromDatabase($id) {

        //Informationen des Elements aus der Datenbank lesen.
        $object = $this->getFromDatabase($id);

        //Alle Klasseneigensschaften dieser Klasse ermitteln.
        $properties = get_class_vars(get_class($this));

        //Pruefen ob Eigenschaften vorhanden sind und ein Element aus der Datenbank geladen werden konnte.
        if((count($properties) > 0) && (isset($object[0]) === true)) {

            //Durchlaufen aller Klasseneigenschaften.
            foreach($properties as $property => $value) {

                //Neue Klasseneigenschaft erzeugen.
                $class_property = substr($property, 1);

                //Pruefen ob die Eigenschaft in der Datenbank existiert.
                if(isset($object[0]->$class_property) === true) {

                    //Wert der Datenbank in diese Klasse schreiben.
                    $this->$property = $object[0]->$class_property;
                }
            }
        }
    }

    /**
     * Methode um einen Benutzer aus der Session laden zu koennen.
     * @since 1.0.0
     */
    public function loadFromSession() {

        //Globale Variablen in die Methode binden.
        global $PICVID;

        //Werte aus der Session laden.
        $this->_activation = $PICVID['CORE']->getParameter($_SESSION, 'user_activation', '1');
        $this->_create_time = $PICVID['CORE']->getParameter($_SESSION, 'user_create_time', '0000-00-00 00:00:00');
        $this->_description = $PICVID['CORE']->getParameter($_SESSION, 'user_description', '');
        $this->_email = $PICVID['CORE']->getParameter($_SESSION, 'user_email', '');
        $this->_group = $PICVID['CORE']->getParameter($_SESSION, 'user_group', 0);
        $this->_id = $PICVID['CORE']->getParameter($_SESSION, 'user_id', 0);
        $this->_lastvisit_time = date('Y-m-d H:i:s', (int) $PICVID['CORE']->getParameter($_SESSION, 'user_lastvisit_time', 0));
        $this->_name = $PICVID['CORE']->getParameter($_SESSION, 'user_name', '');
        $this->_publish_end_time = $PICVID['CORE']->getParameter($_SESSION, 'user_publish_end_time', '0000-00-00 00:00:00');
        $this->_publish_start_time = $PICVID['CORE']->getParameter($_SESSION, 'user_publish_start_time', '0000-00-00 00:00:00');
        $this->_state = $PICVID['CORE']->getParameter($_SESSION, 'user_state', 0);
        $this->_username = $PICVID['CORE']->getParameter($_SESSION, 'user_username', '');
    }

    /**
     * Methode um einen Benutzer aktualisieren zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function update() {

        //Fehlercods zuruecksetzen.
        $this->_error_code = '';

        //Eigenschaften pruefen.
        if(($this->checkProperties() === false) || (trim($this->_username) === '') || (trim($this->_name) === '')) {

            //Fehlercode setzen.
            $this->_error_code = (trim($this->_error_code) === '') ? 'PROPERTIES_ERROR' : $this->_error_code;

            //Status zurueckgeben.
            return false;
        }

        //SQL-Befehl setzen (Benutzer pruefen).
        $this->_database->setQuery("SELECT COUNT(`id`) FROM `#__user` WHERE `email` = '".$this->_database->getEscaped(trim($this->_email))."' AND `id` <> ".(int) $this->_id);

        //Pruefen ob der Benutzer bereits existiert.
        if($this->_database->getResult() > 0) {
            $this->_error_code = 'EXISTS_ERROR';
            return false;
        }

        //Pruefen ob das Password geaendert werden soll.
        if(trim($this->_password) !== '') {
            $password = " `password` = '".$this->_database->getEscaped(md5(trim($this->_password)))."',";
        } else {
            $password = '';
        }

        //SQL-Befehl erzeugen (Benutzer aktualisieren).
        $sql = "UPDATE `#__user` SET `activation` = '".$this->_database->getEscaped(trim($this->_activation))."', `description` = '".$this->_database->getEscaped(trim($this->_description))."', ";
        $sql .= "`email` = '".$this->_database->getEscaped(trim($this->_email))."', `group` = ".(int) $this->_group.", `name` = '".$this->_database->getEscaped(trim($this->_name))."',".$password." ";
        $sql .= "`publish_end_time` = '".$this->_database->getEscaped(trim($this->_publish_end_time))."', `publish_start_time` = '".$this->_database->getEscaped(trim($this->_publish_start_time))."', ";
        $sql .= "`state` = ".(int) $this->_state." WHERE `id` = ".(int) $this->_id;

        //SQL-Befehl setzen.
        $this->_database->setQuery($sql);

        //SQL-Befehl ausfuehren und Status zurueckgeben.
        return $this->_database->query();
    }

    /**
     * Methode um die automatischen Werte des Benutzers aktualisieren zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function updateAutoValues() {

        //Globale Variable des CMS einbinden.
        global $PICVID;

        //Alle Elemente aus der Datenbank ermitteln.
        $users = $this->getFromDatabase(0, array("`id`"), "`publish_end_time` <> '0000-00-00 00:00:00' OR `publish_start_time` <> '0000-00-00 00:00:00'");

        //Array fuer alle SQL-Befehle erzeugen.
        $sql = array();

        //Pruefen ob Elemente ermittelt werden konnten.
        if((is_array($users) === true) && (count($users) > 0)) {

            //Instanz fuer einen Benutzer erzeugen.
            $User = new User($PICVID['DATABASE']);

            //Durchlaufen aller Elemente.
            foreach($users as $user) {

                //Laden des Benutzers.
                $User->loadFromDatabase($user->id);

                //Zeitstempel des Veroeffentlichungsende ermitteln.
                $publish_end_timestamp = strtotime($User->_publish_end_time);

                //Pruefen ob der Zeitstempel ermittelt werden konnte.
                if(($publish_end_timestamp !== false) && ($publish_end_timestamp != -1) && ($publish_end_timestamp < time())) {

                    //Eigenschaften setzen.
                    $User->_publish_start_time = '0000-00-00 00:00:00';
                    $User->_publish_end_time = '0000-00-00 00:00:00';
                    $User->_state = 0;
                }

                //Zeitstempel des Veroefentlichungsbeginn ermitteln.
                $publish_start_timestamp = strtotime($User->_publish_start_time);

                //Pruefen ob der Zeitstempel ermittelt werden konnte.
                if(($publish_start_timestamp !== false) && ($publish_start_timestamp != -1) && ($publish_start_timestamp < time())) {

                    //Eigenschaften setzen.
                    $User->_publish_start_time = '0000-00-00 00:00:00';
                    $User->_state = 1;
                }

                //Benutzer aktualisieren.
                $User->update();
            }
        }
    }
}

/**
 * Klasse um Benutzergruppen verwalten zu koennen.
 *
 * @author Sebastian Brosch
 * @since 1.0.0
 */
class UserGroup {
    /**
     * Eigenschaft um die ID der Benutzergruppe speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_id = 0;

    /**
     * Eigenschaft um den Namen der Gruppe speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_name = '';

    /**
     * Eigenschaft um ein Datenbankobjekt speichern zu koennen.
     * @since 1.0.0
     * @var object
     */
    private $_database = null;

    /**
     * Konstruktor der Klasse
     * @param object $database Ein Datenbankobjekt.
     * @since 1.0.0
     */
    public function __construct($database) {
        $this->_database = (is_object($database) === true) ? $database : null;
    }

    /**
     * Methode um einen oder mehrere Gruppen aus der Datenbank zu erhalten.
     * @param int $id Optional. Die ID der Gruppe welcher geladen werden soll.
     * @param array $fields Optional. Ein Array mit Feldern des Benutzers welche mit zurueckgegeben werden sollen.
     * @param string $filter Optional. Ein gueltiger SQL-Befehl um die Daten filtern zu koennen.
     * @return Ein Array mit Objekten welcher jeweils einen Benutzer darstellen.
     * @since 1.0.0
     */
    public function getFromDatabase($id = 0, $fields = 0, $filter = '') {

        //Pruefen ob ein bestimmtes Element geladen werden soll.
        if($id > 0) {

            //Pruefen ob ein Filter vorhanden ist.
            if(trim($filter) === '') {

                //Filter erweitern.
                $filter .= ' WHERE `id` = '.(int) $id;
            } else {

                //Filter erweitern.
                $filter = ' WHERE '.$filter.' AND `id` = '.(int) $id;
            }
        } else {

            //Pruefen ob ein Filter vorhanden ist.
            if(trim($filter) !== '') {

                //Filter erweitern.
                $filter = ' WHERE '.$filter;
            }
        }

        //Pruefen ob Felder uebergeben wurden.
        if((is_array($fields) === true) && (count($fields) > 0)) {

            //SQL-Befehl setzen (Benutzer ermitteln).
            $this->_database->setQuery("SELECT ".implode(', ', $fields)." FROM `#__user_group`".$filter);
        } else {

            //SQL-Befehl setzen (Benutzer ermitteln).
            $this->_database->setQuery("SELECT * FROM `#__user_group`".$filter);
        }

        //SQL-Befehl ausfuehren und Array mit Objekten zurueckgeben.
        return $this->_database->getObjectArray();
    }

    /**
     * Methode um mit Hilfe eines Gruppennamen die Gruppen-ID zu erhalten.
     * @param string $name Der Gruppenname.
     * @return Die ID der Gruppe.
     * @since 1.0.0
     */
    public function getGroupID($name) {

        //SQL-Befehl setzen (Gruppen-ID erhalten).
        $this->_database->setQuery("SELECT `id` FROM `#__user_group` WHERE `name` = '".$this->_database->getEscaped(trim($name))."'");

        //Zurueckgeben der Gruppen-ID.
        return $this->_database->getResult();
    }

    /**
     * Methode um mit Hilfe der Gruppen-ID den Gruppennamen zu erhalten.
     * @param int $id Die ID der Gruppe.
     * @return Der Gruppenname.
     * @since 1.0.0
     */
    public function getName($id) {

        //SQL-Befehl setzen (Gruppennamen erhalten).
        $this->_database->setQuery("SELECT `name` FROM `#__user_group` WHERE `id` = ".(int) $id);

        //Zureuckgeben des Gruppennamens.
        return $this->_database->getResult();
    }

    /**
     * Methode um ein Array in diese Klasse laden zu koennen.
     * @param array $array Der Array welcher in diese Klasse geladen werden soll.
     * @since 1.0.0
     */
    public function loadFromArray($array) {

        //Alle Klasseneigensschaften dieser Klasse ermitteln.
        $properties = get_class_vars(get_class($this));

        //Prueen ob ein gueltiger Array uebergeben wurde.
        if((is_array($array) === true) && (count($array) > 0) && (count($properties) > 0)) {

            //Durchlaufen aller Klasseneigenschaften.
            foreach($properties as $property => $value) {

                //Array-Eigenschaft erzeugen.
                $array_property = 'user_group'.$property;

                //Pruefen ob der Index vorhanden ist.
                if(isset($array[$array_property]) === true) {

                    //Wert in die Klasse schreiben.
                    $this->$property = $array[$array_property];
                }
            }
        }
    }

    /**
     * Methode um Werte aus der Datenbank in diese Klasse zu laden.
     * @param int $id Die ID des Benutzers welcher geladen werden soll.
     * @since 1.0.0
     */
    public function loadFromDatabase($id) {

        //Informationen des Elements aus der Datenbank lesen.
        $object = $this->getFromDatabase($id);

        //Alle Klasseneigensschaften dieser Klasse ermitteln.
        $properties = get_class_vars(get_class($this));

        //Pruefen ob Eigenschaften vorhanden sind und ein Element aus der Datenbank geladen werden konnte.
        if((count($properties) > 0) && (isset($object[0]) === true)) {

            //Durchlaufen aller Klasseneigenschaften.
            foreach($properties as $property => $value) {

                //Neue Klasseneigenschaft erzeugen.
                $class_property = substr($property, 1);

                //Pruefen ob die Eigenschaft in der Datenbank existiert.
                if(isset($object[0]->$class_property) === true) {

                    //Wert der Datenbank in diese Klasse schreiben.
                    $this->$property = $object[0]->$class_property;
                }
            }
        }
    }
}

/**
 * Klasse um Benutzersession verwalten zu koennen.
 *
 * @author Sebastian Brosch
 * @since 1.0.0
 */
class UserSession {
    /**
     * Eigenschaft um eine Datenbankinstanz speichern zu koennen.
     * @since 1.0.0
     * @var object
     */
     private $_database = null;

    /**
     * Konstruktor der Klasse
     * @param object $database Ein Datenbankobjekt.
     * @since 1.0.0
     */
    public function __construct($database) {
        $this->_database = (is_object($database) === true) ? $database : null;
    }

    /**
     * Methode um eine Session erzeugen zu koennen.
     * @param object $user Ein Benutzerobjekt mit den Informationen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function create($user) {

        //Anmeldezeitpunkt ermitteln.
        $login_time = time();

        //Erzeugen der Session-ID.
        $session_id = md5($user->_id.$user->_username.$user->_name.$login_time);

        //SQL-Befehl erzeugen (Session erstellen).
        $sql = "INSERT INTO `#__user_session` SET `id` = '".trim($session_id)."', `user_id` = ".(int) $user->_id.", ";
        $sql .= "`username` = '".$this->_database->getEscaped(trim($user->_username))."', `time` = '".trim($login_time)."'";

        //SQL-Befehl setzen.
        $this->_database->setQuery($sql);

        //SQL-Befehl ausfuehren.
        if($this->_database->query() === true) {

            //Session erzeugen.
            $_SESSION['id'] = $session_id;
            $_SESSION['user_activation'] = $user->_activation;
            $_SESSION['user_create_time'] = $user->_create_time;
            $_SESSION['user_description'] = $user->_description;
            $_SESSION['user_email'] = $user->_email;
            $_SESSION['user_group'] = $user->_group;
            $_SESSION['user_id'] = $user->_id;
            $_SESSION['user_lastvisit_time'] = $login_time;
            $_SESSION['user_name'] = $user->_name;
            $_SESSION['user_publish_end_time'] = $user->_publish_end_time;
            $_SESSION['user_publish_start_time'] = $user->_publish_start_time;
            $_SESSION['user_state'] = $user->_state;
            $_SESSION['user_username'] = $user->_username;

            //Status zurueckgeben.
            return true;
        } else {

            //Session zuruecksetzen und zerstoeren.
            unset($_SESSION);
            session_destroy();

            //Status zurueckgeben.
            return false;
        }
    }

    /**
     * Methode um eine Session loeschen zu koennen.
     * @param int $id Optional. Doe Benutzer-ID der Session welche geloescht werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
    public function delete($id = 0) {

        //Globale Variable des CMS einbinden.
        global $PICVID;

        //Pruefen ob ein bestimmter Benutzer ausgeloggt werden soll.
        if($id === 0) {

            //Session-ID aus der Session holen.
            $session_id = $PICVID['CORE']->getParameter($_SESSION, 'id', '');

            //Pruefen ob eine Session-ID vorhanden ist.
            if(trim($session_id) === '') {

                //Status zurueckgeben.
                return false;
            } else {

                //SQL-Befehl setzen (Session loeschen).
                $this->_database->setQuery("DELETE FROM `#__user_session` WHERE `id` = '".$this->_database->getEscaped(trim($session_id))."' AND `id` NOT IN (SELECT `id` FROM `#__user` WHERE `state` = 1 AND `activation` = '')");
            }
        } else {

            //SQL-Befehl setzen (Session loeschen).
            $this->_database->setQuery("DELETE FROM `#__user_session` WHERE `user_id` = ".(int) $id." AND `user_id` NOT IN (SELECT `id` FROM `#__user` WHERE `state` = 1 AND `activation` = '')");
        }

        //Status zurueckgeben.
        return $this->_database->query();
    }

    /**
     * Methode um die Benutzersession aktualisieren zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function update() {

        //SQL-Befehl setzen (Session aktualisieren).
        $this->_database->setQuery("DELETE FROM `#__user_session` WHERE `time` < ".(int) (time() - 900));

        //SQL-Befehl ausfuehren.
        return $this->_database->query();
    }

    /**
     * Methode um die Session validieren zu koennen.
     * @param object $user Ein Benutzerobjekt mit den Informationen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function validate($user) {

        //Globale Variablen in die Methode binden.
        global $PICVID;

        //Auslesen der Session-ID.
        $session_id = $PICVID['CORE']->getParameter($_SESSION, 'id', '');

        //Zeitpunkt des letzten Besuchs auslesen.
        $user_lastvisit_time = $PICVID['CORE']->getParameter($_SESSION, 'user_lastvisit_time', '');

        //Pruefen ob die Session-ID gueltig ist.
        if($session_id === trim(md5($user->_id.$user->_username.$user->_name.$user_lastvisit_time))) {

            //SQL-Befehl setzen (Anzahl der Sessions ermitteln).
            $this->_database->setQuery("SELECT COUNT(`id`) FROM `#__user_session` WHERE `user_id` = ".(int) $user->_id);

            //Pruefen ob eine Session verfuegbar ist.
            if($this->_database->getResult() > 0) {

                //SQL-Befehl erzeugen (Session validieren).
                $sql = "UPDATE `#__user_session` SET `time` = '".time()."' WHERE `id` = '".$this->_database->getEscaped(trim($session_id))."' ";
                $sql .= "AND `username` = '".$this->_database->getEscaped(trim($user->_username))."' AND `user_id` = ".(int) $user->_id;

                //SQL-Befehl setzen.
                $this->_database->setQuery($sql);

                //SQL-Befehl ausfuehren.
                if($this->_database->query() === true) {

                    //SQL-Befehl erzeugen (Session auslesen).
                    $sql = "SELECT `time` FROM `#__user_session` WHERE `id` = '".$this->_database->getEscaped($session_id)."' ";
                    $sql .= "AND `username` = '".$this->_database->getEscaped(trim($user->_username))."' AND `user_id` = ".(int) $user->_id;

                    //SQL-Befehl setzen.
                    $this->_database->setQuery($sql);

                    //Status zurueckgeben.
                    return $this->_database->getResult();
                }
            }
        }

        //Status zurueckgeben.
        return false;
    }
}
?>