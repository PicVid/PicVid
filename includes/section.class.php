<?php
/**
 * Klasse um Bereiche verwalten zu koennen.
 *
 * @author Sebastian Brosch, Jasmin Kemmerich
 * @since 1.0.0
 */
class Section {
    /**
     * Eigenschaft um die Gruppen-ID des Admin-Bereichs speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_admin_group = 0;

    /**
     * Eigenschaft um die ID der Kategorie speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_category_id = 0;

    /**
	 * Eigenschaft um eine Datenbankinstanz speichern zu koennen.
	 * @since 1.0.0
	 * @var object
	 */
	private $_database = null;

    /**
	 * Eigenschaft um einen Fehlercode speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	private $_error_code = '';

    /**
	 * Eigenschaft um den Ablaufstatus speichern zu koennen.
	 * @since 1.0.0
	 * @var int
	 */
	public $_expiry_state = 0;

	/**
	 * Eigenschaft um den Ablaufzeitpunkt speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	public $_expiry_time = '0000-00-00 00:00:00';

    /**
	 * Eigenschaft um die ID speichern zu koennen.
	 * @since 1.0.0
	 * @var int
	 */
	public $_id = 0;

    /**
     * Eigenschaft um den Anzaigetext speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_menu_title = '';

	/**
	 * Eigenschaft um den Namen speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	public $_name = '';

	/**
	 * Eigenschaft um den Veroeffentlichungsbeginn speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	public $_publish_start_time = '0000-00-00 00:00:00';

	/**
	 * Eigenschaft um das Vereoffentlichungsende speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	public $_publish_end_time = '0000-00-00 00:00:00';

	/**
	 * Eigenschaft um den Status speichern zu koennen.
	 * @since 1.0.0
	 * @var int
	 */
	public $_state = 0;

    /**
     * Eigenschaft um die Gruppen-ID des Frontend speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_user_group = 0;

	/**
	 * Eigenschaft um die Version (numerisch) speichern zu koennen.
	 * @since 1.0.0
	 * @var int
	 */
	public $_version = 0;

	/**
     * Konstruktor der Klasse.
     * @param object $database Ein Datenbankobjekt.
     * @since 1.0.0
     */
    public function __construct($database) {
        $this->_database = (is_object($database) === true) ? $database : null;
    }

	/**
     * Methode um die Eigenschaften der Klasse ueberpruefen zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function checkProperties() {

        //Pruefen der Eigenschaften.
        if(preg_match('/^[0-9]+$/', $this->_admin_group) == 0) {
            $this->_error_code = 'ADMIN_GROUP_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]+$/', $this->_category_id) == 0) {
            $this->_error_code = 'CATEGORY_ID_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]+$/', $this->_expiry_state) == 0) {
            $this->_error_code = 'EXPIRY_STATE_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]{4}(-[0-9]{2}){2} [0-9]{2}(:[0-9]{2}){2}$/', $this->_expiry_time) == 0) {
            $this->_error_code = 'EXPIRY_TIME_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]+$/', $this->_id) == 0) {
            $this->_error_code = 'ID_ERROR';
            return false;
        }
        if(preg_match('/^[a-z_]+$/', $this->_name) == 0) {
            $this->_error_code = 'NAME_ERROR';
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
        if(preg_match('/^[0-9]+$/', $this->_user_group) == 0) {
            $this->_error_code = 'USER_GROUP_ERROR';
            return false;
        }
        if(preg_match('/^[0-9]+$/', $this->_version) == 0) {
            $this->_error_code = 'VERSION_ERROR';
            return false;
        }

        //Status zurueckgeben.
        return true;
    }

    /**
     * Methode um eine Section zu erstelllen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function create() {

        //Pruefen ob die Section bereits vorhanden ist.
        $this->_database->setQuery("SELECT COUNT(`id`) FROM `#__section` WHERE `name` = '".$this->_database->getEscaped(trim($this->_name))."'");

        //Pruefen ob das Element bereits vorhanden ist.
        if($this->_database->getResult() > 0) {

            //Fehlercode setzen.
            $this->_error_code = 'EXIST_ERROR';

            //Status zurueckgeben.
            return false;
        } else {

            //Einfuegen der neuen Section.
            $this->_database->setQuery("INSERT INTO `#__section` SET `name` = '".$this->_database->getEscaped(trim($this->_name))."'");

            //Befehl ausfuehren und Status zurueckgeben.
            return $this->_database->query();
        }
    }

    /**
     * Methode um eine Section loeschen zu koennen.
     * @param int $id Die ID der Section welche geloescht werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
	public function delete($id) {

        //Globale PicVid-Variable einbinden.
        global $PICVID;

        //Pruefen ob eine gueltige ID vorhanden ist.
        if($id > 0) {

            //SQL-Befehl setzen (Namen ermitteln).
            $this->_database->setQuery("SELECT `name` FROM `#__section` WHERE `id` = ".(int) $id);

            //Namen ermitteln.
            $section_name = $this->_database->getResult();

            //Pruefen ob der Name der Section gueltig ist.
            if(trim($section_name) !== '') {

                //SQL-Befehl setzen (Section loeschen).
                $this->_database->setQuery("DELETE FROM `#__section` WHERE `id` = ".(int) $id);

                //SQL-Befehl ausfuehren (Section loeschen).
                if($this->_database->query() === true) {

                    //Ermitteln aller Sections im Dateisystem.
                    $section_names = scandir($PICVID['CORE']->getValue('absolute_path').'\section');

                    //Pruefen ob die Section im Dateisystem vorhanden ist.
                    if((trim($section_name) !== '') && (array_search($section_name, $section_names) !== false)) {
                        return $this->deleteDirectory($PICVID['CORE']->getValue('absolute_path').'\section\\'.$section_name);
                    }

                    //Status zurueckgeben.
                    return true;
                }
            }
        }

        //Status zurueckgeben.
        return false;
    }

    /**
     * Methode um das Verzeichnis einer Section loeschen zu koennen.
     * @param string $path Der Pfad der Section welcher geloescht werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
    public function deleteDirectory($path) {

        //Alle Dateien und Verzeichnisse ermitteln.
        $folder_files = array_diff(scandir($path), array('.', '..'));

        //Pruefen ob Dateien zum loeschen vorhanden sind.
        if((is_array($folder_files) === true) && (count($folder_files) > 0)) {

            //Durchlaufen aller Verzeichniselemente.
            foreach($folder_files as $folder_file) {

                //Vollstaendigen Pfad erzeugen.
                $file = $path.'/'.$folder_file;

                //Pruefen ob die Datei ein Verzeichnis ist.
                if(is_dir($file) === true) {

                    //Diese Funktion rekursiv aufrufen.
                    $this->deleteDirectory($file);
                }  else {

                    //Datei loeschen.
                    unlink($file);
                }
            }
        }

        //Zurueckgeben des Status.
        return rmdir($path);
    }

    /**
	 * Methode um eine Section zu aktualisieren.
	 * @param string $name Der Mame der Section welche aktualisiert werden soll.
	 * @param int $version Die Version ab welcher aktualsiert werden soll.
	 * @return Der Status.
	 * @since 1.0.0
	 */
	public function executeUpdate($name, $version) {

        //Globale Variable von PicVid einbinden.
        global $PICVID;

        //Pfad zum Update-Verzeichnis der Section ermitteln.
        $path = $PICVID['CORE']->getValue('absolute_path').'/section/'.trim(strtolower($name)).'/sql';

        //Pruefen ob Updates vorhanden sind.
        if(file_exists($path) === false) {

            //Status zurueckgeben.
            return false;
        }

        //Ermitteln aller Dateien im Update-Verzeichnis.
        $files = scandir($path);

        //Fehlercode der Methode zuruecksetzen.
        $error = false;

        //Durchlaufen aller Dateien.
        foreach($files as $file) {

            //Pfad zur Update-Datei sowie der SQL-Befehl zuruecksetzen.
            $upload_path = '';

            //Pruefen ob es sich um eine SQL-Datei handelt.
            if(preg_match('/^[0-9]+.sql$/', $file) > 0) {

                //Dateiendung entfernen.
                $file = str_replace('.sql', '', $file);

                //Pruefen ob der reine Name der Datei eine Zahl ist.
                if(is_numeric($file) === true) {

                    //Name der Datei als Zahl setzen (Typecast).
                    $file = (int) $file;

                    //Pruefen ob es sich um ein neues Update handelt.
                    if($file > $version) {

                        //Pfad zur Update-Datei erzeugen.
                        $update_path = $path.'/'.$file.'.sql';

                        //Pruefen ob die Update-Datei existiert.
                        if(file_exists($update_path) === true){

                            //Update-Datei ausfuehren um Update zu installieren.
                            if($this->_database->splitQuery(file_get_contents($update_path), true, $PICVID['CORE']->getValue('db_prefix')) === false) {

                                //Update konnte nicht ausgefuehrt werden.
                                $error = true;
                            } else {

                                //Version der Section neu setzen.
                                $this->_database->setQuery("UPDATE `#__section` SET `version` = ".(int) $file." WHERE `name` = '".$this->_database->getEscaped(trim($name))."'");

                                //Prueen ob die Version neu gesetzt werden konnte.
                                if($this->_database->query() === false) {

                                    //Fehlercode setzen.
                                    $error = true;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

	/**
     * Methode um einen oder mehrere Sections aus der Datenbank zu erhalten.
     * @param int $id Optional. Die ID der Section welche ermittelt werden soll.
     * @param array $fields Optional. Ein Array mit Feldern der Sections welche mit zurueckgegeben werden sollen.
     * @param string $filter Optional. Ein gueltiger SQL-Befehl um die Daten filtern zu koennen.
     * @return Ein Array mit Objekten welcher jeweils eine Section darstellen.
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
            $this->_database->setQuery("SELECT ".implode(', ', $fields)." FROM `#__section`".$filter);
        } else {

            //SQL-Befehl setzen (Benutzer ermitteln).
            $this->_database->setQuery("SELECT * FROM `#__section`".$filter);
        }

        //SQL-Befehl ausfuehren und Array mit Objekten zurueckgeben.
        return $this->_database->getObjectArray();
    }

	/**
	 * Methode um die Version der Section ermitteln zu koennen.
	 * @param string $name Der Name der Section deren Version pruefen.
	 * @return Die Versionsnummer.
	 * @since 1.0.0
	 */
	public function getVersion($name) {

        //Pruefen ob die Section vorhanden ist.
        $this->_database->setQuery("SELECT COUNT(`id`) FROM `#__section` WHERE `name` = '".$this->_database->getEscaped(trim($name))."'");

        //Pruefen ob Elemente vorhanden sind.
        if($this->_database->getResult() > 0) {

            //SQL-Befehl setzen (Version ermitteln).
            $this->_database->setQuery("SELECT `version` FROM `#__section` WHERE `name` = '".$this->_database->getEscaped(trim($name))."'");

            //Version zrueckgeben.
            return $this->_database->getResult();
        } else {

            //Fehlercode setzen.
            $this->_error_code = 'EXIST_ERROR';

            //Standard zurueckgeben.
            return 0;
        }
    }

    /**
     * Methode um den Bereich anhand der URL ermitteln zu koennen.
     * @param string $default Optional. Der Name des Standard-Bereichs welcher als Alternative angezeigt werden soll.
     * @return Pfad zur Section.
     * @since 1.0.0
     */
    public function getSection($default = '') {

        //Globale Variable von PICVID einbinden.
        global $PICVID;

        //Pruefen ob ein Standard uebergeben wurde.
        $default = (trim($default) === '') ? 'dashboard' : $default;

        //Standard-Section erzeugen.
        $default = (file_exists($PICVID['CORE']->getValue('absolute_path').'/section/'.$default.'/'.$default.'.php') === true) ? $PICVID['CORE']->getValue('absolute_path').'/section/'.$default.'/'.$default.'.php' : '';

        //Bereich aus dem globalen Array ermitteln.
        $section_name = $PICVID['CORE']->getParameter($_REQUEST, 'section', $default);

        //Status des Amdins aus der URL ermitteln.
        $section_admin = $PICVID['CORE']->getParameter($_REQUEST, 'admin', 0);

        //Pruefen ob ein Benutzer angemeldet ist.
        $user_group = (is_object($PICVID['ACT_USER']) === true) ? $PICVID['ACT_USER']->_group : 0;

        //Pruefen ob die Administration einer Section geoeffnet werden soll.
        if($section_admin > 0) {

            //SQL-Befehl setzen (Rechte der Section pruefen).
            $this->_database->setQuery("SELECT `id` FROM `#__section` WHERE `name` = '".$this->_database->getEscaped(trim(strtolower($section_name)))."' AND `admin_group` <= ".(int) $user_group." AND `state` = 1");

            //Pruefen ob die Rechte vorhanden sind.
            if($this->_database->getResult() > 0) {

                //Pruefen ob die Datei des Adminbereichs existiert.
                if(file_exists($PICVID['CORE']->getValue('absolute_path').'/section/'.$section_name.'/'.$section_name.'_admin.php') === true) {

                    //Zurueckgeben des Verzeichnisses.
                    return $PICVID['CORE']->getValue('absolute_path').'/section/'.$section_name.'/'.$section_name.'_admin.php';
                }
            }
        } else {

            //SQL-Befehl setzen (Rechte der Section pruefen).
            $this->_database->setQuery("SELECT `id` FROM `#__section` WHERE `name` = '".$this->_database->getEscaped(trim(strtolower($section_name)))."' AND `user_group` <= ".(int) $user_group." AND `state` = 1");

            //Pruefen ob die Rechte vorhanden sind.
            if($this->_database->getResult() > 0) {

                //Pruefen ob die Datei des Adminbereichs existiert.
                if(file_exists($PICVID['CORE']->getValue('absolute_path').'/section/'.$section_name.'/'.$section_name.'.php') === true) {

                    //Zurueckgeben des Verzeichnisses.
                    return $PICVID['CORE']->getValue('absolute_path').'/section/'.$section_name.'/'.$section_name.'.php';
                }
            }
        }

        //Standard zurueckgeben.
        return $default;
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
                $array_property = 'section'.$property;

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
     * @param int $id Die ID der Section welcher geladen werden soll.
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

	public function update() {

        //SQL-Befehl erzeugen (Section aktualisieren).
		$sql = "UPDATE `#__section` SET `admin_group` = ".(int) $this->_admin_group.", `category_id` = ".(int) $this->_category_id.", `expiry_state` = ".(int) $this->_expiry_state.", ";
		$sql .= "`expiry_time` = '".$this->_database->getEscaped(trim($this->_expiry_time))."', `menu_title` = '".$this->_database->getEscaped(trim($this->_menu_title))."', `publish_start_time` = '".$this->_database->getEscaped(trim($this->_publish_start_time))."', ";
		$sql .= "`publish_end_time` = '".$this->_database->getEscaped(trim($this->_publish_end_time))."', `state` = ".(int) $this->_state.", `user_group` = ".(int) $this->_user_group." WHERE `id` = ".(int) $this->_id;

        //SQL-Befehl setzen.
		$this->_database->setQuery($sql);

        //SQL-Befehl ausfuehren.
		return $this->_database->query();
	}

    /**
     * Methode um die automatischen Werte des Benutzers aktualisieren zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    public function updateAutoValues() {

        //Globale Variable von PicVid einbinden.
        global $PICVID;

        //Alle Elemente aus der Datenbank ermitteln.
        $sections = $this->getFromDatabase(0, array("`id`"), "`expiry_time` <> '0000-00-00 00:00:00' OR `publish_end_time` <> '0000-00-00 00:00:00' OR `publish_start_time` <> '0000-00-00 00:00:00'");

        //Array fuer alle SQL-Befehle erzeugen.
        $sql = array();

        //Pruefen ob Elemente ermittelt werden konnten.
        if((is_array($sections) === true) && (count($sections) > 0)) {

            //Instanz fuer eine Section erzeugen.
            $Section = new Section($PICVID['DATABASE']);

            //Durchlaufen aller Elemente.
            foreach($sections as $section) {

                //Laden der Section.
                $Section->loadFromDatabase($section->id);

                //Zeitstempel des Ablaufzeitpunkts ermitteln.
                $expiry_timestamp = strtotime($Section->_expiry_time);

                //Pruefen ob der Zeitstempel ermittelt werden konnte.
                if(($expiry_timestamp !== false) && ($expiry_timestamp != -1) && ($expiry_timestamp < time())) {

                    //Eigenschaften setzen.
                    $Section->_expiry_time = '0000-00-00 00:00:00';
                    $Section->_state = $Section->_expiry_state;
                }

                //Zeitstempel des Veroeffentlichungsende ermitteln.
                $publish_end_timestamp = strtotime($Section->_publish_end_time);

                //Pruefen ob der Zeitstempel ermittelt werden konnte.
                if(($publish_end_timestamp !== false) && ($publish_end_timestamp != -1) && ($publish_end_timestamp < time())) {

                    //Eigenschaften setzen.
                    $Section->_publish_start_time = '0000-00-00 00:00:00';
                    $Section->_publish_end_time = '0000-00-00 00:00:00';
                    $Section->_state = 0;
                }

                //Zeitstempel des Veroefentlichungsbeginn ermitteln.
                $publish_start_timestamp = strtotime($Section->_publish_start_time);

                //Pruefen ob der Zeitstempel ermittelt werden konnte.
                if(($publish_start_timestamp !== false) && ($publish_start_timestamp != -1) && ($publish_start_timestamp < time())) {

                    //Eigenschaften setzen.
                    $Section->_publish_start_time = '0000-00-00 00:00:00';
                    $Section->_state = 1;
                }

                //Section aktualisieren.
                $Section->update();
            }
        }
    }
}
?>