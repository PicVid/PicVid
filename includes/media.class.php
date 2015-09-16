<?php
/**
 * Klasse um Inhalte mit Text verwalten zu koennen.
 *
 * @author Sebastian Brosch
 * @since 1.0.0
 */
class Media {
    /**
     * Eigenschaft um den Erstellzeitpunkt speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_create_time = '0000-00-00 00:00:00';

    /**
     * Eigenschaft um den Erstellbenutzer speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_create_user = 0;

    /**
     * Eigenschaft um dei Beschreibung speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_description = '';

    /**
     * Eigenschaft um die ID speichern zu koennen.
     * @since 1.0.0
     * @var int
     */
    public $_id = 0;

    /**
     * Eigenschaft um den Bildnamen speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_name = '';

    /**
     * Eigenschaft um den Titel speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_title = '';

    /**
     * Eigenschaft um den Typ speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    public $_type = '';

    /**
     * Eigenschaft um ein Datenbankobjekt speichern zu koennen.
     * @since 1.0.0
     * @var object
     */
    private $_database = null;

    /**
     * Konstruktor der Klasse.
     * @param object $database Ein Datenbankobjekt.
     * @since 1.0.0
     */
    public function __construct($database) {
        $this->_database = (is_object($database) === true) ? $database : null;
    }

    /**
     * Methode um ein Element in der Datenbank erstellen zu koennen.
     * @param string $location Der Typ der Datei (image oder video).
     * @param string $name Der vollstaendige Dateiname.
     * @param string $type Der Dateityp.
     * @return Der Status.
     * @since 1.0.0
     */
    public function create($location, $name, $type) {

        //Globale Variable von PicVid einbinden.
        global $PICVID;

        //Ubergabeparameter setzen.
        $this->_type = $type;

        //Pruefen ob ein Name vorhanden ist.
        if(trim($this->_name) !== '') {

            //Pruefen welcher Typ verwendet werden soll.
            switch($this->_type) {
                case 'image/png':
                    $this->_name = $this->_name.'.png';
                    break;
                case 'image/jpeg':
                    $this->_name = $this->_name.'.jpg';
                    break;
                case 'image/tif':
                    $this->_name = $this->_name.'.tif';
                    break;
                case 'video/mp4':
                    $this->_name = $this->_name.'.mp4';
                    break;
                case 'video/webm':
                    $this->_name = $this->_name.'.webm';
                    break;
                case 'video/ogg':
                    $this->_name = $this->_name.'.ogg';
                    break;
                case 'url':
                    $this->_name = $name;
                    break;
            }
        } else {

            //Name des Mediums setzen.
            $this->_name = $name;
        }

        //Pruefen ob ein gueltiger Bereich uebergeben wurde.
        if(($location === 'image') || ($location === 'video')) {

            //SQL-Befehl erzeugen (Medium erzeugen).
            $sql = "INSERT INTO `#__".$location."` SET `create_time` = NOW(), `create_user` = ".(int) $PICVID['ACT_USER']->_id.", ";
            $sql .= "`description` = '".$this->_database->getEscaped(trim($this->_description))."', `name` = '".$this->_database->getEscaped(trim($this->_name))."', ";
            $sql .= "`title` = '".$this->_database->getEscaped(trim($this->_title))."', `type` = '".$this->_database->getEscaped(trim($this->_type))."'";

            //SQL-Befehl setzen.
            $this->_database->setQuery($sql);

            //SQL-Befehl ausfuehren.
            return $this->_database->query();
        }

        //Status zurueckgeben.
        return false;
    }

    /**
     * Eigenschaft um ein Element aus der Datenbank loeschen zu koennen.
     * @param string $location Der Typ des Elements.
     * @param int $id Die ID des Elements.
     * @return Der Status.
     * @since 1.0.0
     */
    public function delete($location, $id) {

        //Pruefen ob ein gueltiger Typ uebergeben wurde.
        if(($location === 'image') || ($location === 'video')) {

            //SQL-Befehl setzen und ausfuehren.
            $this->_database->setQuery("DELETE FROM `#__".trim($location)."` WHERE `id` = ".(int) $id);
            return $this->_database->query();
        }
    }

    /**
     * Methode um eine Datei herunterladen zu koennen.
     * @param string $location Der Name des Bereich aus welchem heruntergeladen werden soll.
     * @param int $id Die ID des Elements welches heruntergeladen werden soll.
     * @since 1.0.0
     */
    public function download($location, $id) {

        //Globale Variable von PicVid einbinden.
        global $PICVID;

        //Pruefen ob die Werte gueltig sind.
        if(($location === 'image') || ($location === 'video') || ($id > 0)) {

            //Laden der Dateiinformationen.
            $this->loadFromDatabase($location, $id);

            //Ausgabe leeren.
            ob_end_clean();

            //Eigenschaften setzen.
            $filename = $this->_name;

            //Pruefen welche Dateiendung verwendet werden soll.
            switch($this->_type) {
                case 'image/jpeg':
                    $content = file_get_contents($PICVID['CORE']->getValue('site_url').'/gallery/images/'.$this->_id.'.jpeg');
                    break;
                case 'image/png':
                    $content = file_get_contents($PICVID['CORE']->getValue('site_url').'/gallery/images/'.$this->_id.'.png');
                    break;
                case 'image/tif':
                    $content = file_get_contents($PICVID['CORE']->getValue('site_url').'/gallery/images/'.$this->_id.'.tif');
                    break;
                case 'video/mp4':
                    $content = file_get_contents($PICVID['CORE']->getValue('site_url').'/gallery/videos/'.$this->_id.'.mp4');
                    break;
                case 'video/webm':
                    $content = file_get_contents($PICVID['CORE']->getValue('site_url').'/gallery/videos/'.$this->_id.'.webm');
                    break;
                case 'video/ogg':
                    $content = file_get_contents($PICVID['CORE']->getValue('site_url').'/gallery/videos/'.$this->_id.'.ogg');
                    break;
                case 'url':
                    $filename = substr(strrchr($this->_name, "/"), 1);
                    $content =  file_get_contents($this->_name);
                    break;
            }

            //Header fuer den Download setzen.
            header("Content-Type: application/download");
            header('Content-Disposition: attachment; filename='.$filename);

            //Medium ausgeben um herunterladen zu koennen.
            echo $content;

            //Script beenden.
            exit;
        }
    }

    /**
     * Eigenschaft um Bilder oder Videos aus der Datenbank ermitteln zu koennen.
     * @param string $location Der Typ des Element (image oder video).
     * @param int $id Optional. Die ID des Elements welches abgerufen werden soll.
     * @param array $fields Optional. Die Felder welche zurueckgegeben werden sollen.
     * @param string $filter Optional. Ein SQL-Filter fuer die Where-Clause.
     * @return Ein Array mit Objekten welche Videos oder Bilder darstellen.
     * @since 1.0.0
     */
    public function getFromDatabase($location, $id = 0, $fields = 0, $filter = '') {

        //Typ pruefen.
        if(($location === 'image') || ($location === 'video')) {

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
                $this->_database->setQuery("SELECT ".implode(', ', $fields)." FROM `#__".trim($location)."`".$filter);
            } else {

                //SQL-Befehl setzen (Benutzer ermitteln).
                $this->_database->setQuery("SELECT * FROM `#__".trim($location)."`".$filter);
            }

            //SQL-Befehl ausfuehren und Array mit Objekten zurueckgeben.
            return $this->_database->getObjectArray();
        }
    }

    /**
     * Eigenschaft um die ID fuer ein Bild ermitteln zu koennen.
     * @return Die ID fuer ein neues Bild.
     * @since 1.0.0
     */
    public function getID() {

        //Zurueckgeben der ID fuer ein neues Bild.
        return $this->_database->getInsertID();
    }

    /**
     * Eigenschaft um einen Laengenwert eines Bildes ermitteln zu koennen.
     * @param string $path Der Pfad zur Bild-Datei.
     * @param int $height Die Hohe des Bildes welche verwendet werden soll.
     * @param int width Die Breite des Bildes welche verwendet werden soll.
     * @return Ein Array mit der Breite und Hoehe des Bildes.
     * @since 1.0.0
     */
    public function getImageSize($path, $height = 0, $width = 0) {

        //Informationen ermitteln.
        $image_info = getimagesize($path);

        //Werte schreiben.
        $image_height = $image_info[1];
        $image_width = $image_info[0];

        //Pruefen ob die Hoehe veraendert werden soll.
        if($height != 0) {
            $image_width = intval($image_info[0] * $height / $image_info[1]);
            $image_height = $height;
        } else {

            //Pruefen ob die Breite veraendert werden soll.
            if($width != 0) {
                $image_height = intval($image_info[1] * $width / $image_info[0]);
                $image_width = $width;
            }
        }

        //Zurueckgeben der Werte fuer die Groesse als Array.
        return array($image_height, $image_width);
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
                $array_property = 'media'.$property;

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
     * @param string $location Der Bereich des Mediums.
     * @param int $id Die ID des Benutzers welcher geladen werden soll.
     * @since 1.0.0
     */
    public function loadFromDatabase($location, $id) {

        //Informationen des Elements aus der Datenbank lesen.
        $object = $this->getFromDatabase($location, $id);

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
?>