<?php
/**
 * Klasse um Zugriffe auf eine Datenbank zu ermoeglichen.
 *
 * @author Sebastian Brosch
 * @copyright Sebastian Brosch, 2012
 * @since 1.0.0
 */
abstract class Database {
	/**
	 * Eigenschaft um eine Ergebnismenge speichern zu koennen.
	 * @since 1.0.0
	 * @var resource
	 */
	protected $_cursor = null;

	/**
	 * Eigenschaft um den Namen eines Treibers speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	protected $_driver = '';

	/**
	 * Eigenschaft um einen Fehlercode speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	protected $_error_code = '';

	/**
	 * Eigenschaft um ein Praefix speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	protected $_prefix = '';

	/**
	 * Eigenschaft um eine Verbindungskennung speichern zu koennen.
	 * @since 1.0.0
	 * @var resource
	 */
	protected $_resource = null;

	/**
	 * Eigenschaft um einen SQL-Befehl speichern zu koennen.
	 * @since 1.0.0
	 * @var string
	 */
	protected $_sql = '';

	/**
	 * Der Konstruktor der Klasse welcher einen Verbindungsaufbau zur Datenbank ermoeglicht.
	 * @param string $hostname Der Hostname auf welchem sich der Datenbank-Server befindet.
	 * @param string $username Der Name des Datenbank-Benutzers.
	 * @param string $password Das Passwort des Datenbank-Benutzers.
	 * @param string $database Der Name der Datenbank.
	 * @param string $prefix Optional. Das Praefix der Tabellen.
	 * @param string $port Optional. Der Port des Datenbank-Servers.
	 * @since 1.0.0
	 */
	public abstract function __construct($hostname, $username, $password, $database, $prefix = '', $port = '');

    /**
     * Destruktor der Klasse um eine Verbindung und temporäre Daten zu entfernen.
     * @since 1.0.0
     */
    public abstract function __destruct();

	/**
	 * Methode um die Fehlermeldung ermitteln zu koennen.
	 * @return Die Fehlermeldung falls ein Fehler aufgetreten ist.
	 * @since 1.0.0
	 */
	public abstract function getErrorMessage();

	/**
	 * Methode um die Fehlernummer ermitteln zu koennen.
	 * @return Die Fehlernummer falls ein Fehler aufgetreten ist.
	 * @since 1.0.0
	 */
	public abstract function getErrorNumber();

    /**
     * Methode um eine Zeichenkette maskiert zu erhalten.
     * @param string $string Die Zeichenkette welche maskiert werden soll.
     * @return Die maskierte Zeichenkette.
     * @since 1.0.0
     */
    public abstract function getEscaped($string);

    /**
     * Methode um die ID einer neuen Zeile zu erhalten.
     * @return Die ID der neuen Zeile.
     * @since 1.0.0
     */
    public abstract function getInsertID();

	/**
	 * Methode um die Ergebnismenge als Objekte zu erhalten.
	 * @return Ein Array mit Objekten welche jeweils eine Zeile der Ergebnismenge darstellen.
	 * @since 1.0.0
	 */
	public abstract function getObjectArray();

	/**
	 * Methode um den Wert aus dem ersten Feld der ersten Zeile auslesen zu koennen.
	 * @return Der Wert aus dem ersten Feld der ersten Zeile.
	 * @since 1.0.0
	 */
	public abstract function getResult();

	/**
	 * Methode um einen Befehl ausfuehren zu koennen.
	 * @param string $sql Optional. Der Befehl welcher ausgefuehrt werden soll.
	 * @return Der Status.
	 * @since 1.0.0
	 */
	public abstract function query($sql = '');

	/**
	 * Methode um den Fehlercode der Klasse zurueckgeben zu koennen.
	 * @return Der Fehlercode der Klasse.
	 * @since 1.0.0
	 */
	public function getErrorCode() {
		return $this->_error_code;
	}

	/**
	 * Methode um einen Befehl in der Klasse speichern zu koennen.
	 * @param string $sql Der Befehl welcher gespeichert werden soll.
	 * @param string $prefix Optional. Das Praefix der Tabellen.
	 * @since 1.0.0
	 */
	public function setQuery($sql, $prefix = '') {

		//Praefix ermitteln.
		$prefix = ($prefix === '') ? trim($this->_prefix) : trim($prefix);

		//Alle Platzhalter des Befehls durch das Praefix ersetzen.
		$sql = str_replace('#__', $prefix, $sql);

		//Befehl in der Klasse speichern.
		$this->_sql = $sql;
	}

    /**
     * Methode um mehrere Befehle ausfuehren oder splitten zu koennen.
     * @param string $sql Die Befehle welche gesplittet und eventuell ausgefuehrt werden sollen.
     * @param boolean $execute Optional. Der Status ob die Befehle sofort ausgefuehrt werden sollen.
     * @param string $prefix Optional. Das Praefix der Tabellen.
     * @return Der Status (execute = true) oder der Array mit den Befehlen.
     * @since 1.0.0
     */
    public function splitQuery($sql, $execute = false, $prefix = '') {

        //Fehlercode zuruecksetzen.
        $this->_error_code = '';

        //Pruefen ob ein Praefix uebergeben wurde.
        $prefix = (trim($prefix) !== '') ? trim($this->_prefix) : '';

        //Entfernen aller Absaetze.
        $sql = str_replace(array("\r\n", "\n", "\r"), '', $sql);

        //Alle Platzhalter des Praefix im SQL-Befehl ersetzen.
        $sql = str_replace('#__', $prefix, $sql);

        //Zeichenkette am Semikolon zerlegen.
        $sql_array = preg_split("/;/i", $sql);

        //Array fuer die fertigen SQL-Befehle erzeugen.
        $return_array = array();

        //Durchlaufen aller SQL-Befehle.
        foreach($sql_array as $sql) {

            //Entfernen der Kommentare.
            $sql = preg_replace("/[\#]{2}(.*)[\#]{1}/i", '', $sql);

            //Pruefen ob der Befehl sofort ausgefuehrt werden soll.
            if($execute === true) {

                //SQL-Befehl ausfuehren.
                if(($this->query($sql) === false) && ($this->getErrorCode() > 0)) {

                    //Status zurueckgeben.
                    return false;
                }
            } else {

                //SQL-Befehl in den Rueckgabe-Array schreiben.
                $return_array[] = $sql;
            }
        }

        //Pruefen ob die SQL-Befehle sofort ausgefuehrt werden sollen.
        if($execute === false) {

            //Zurueckgeben des Rueckgabearrays.
            return $return_array;
        }

        //Status zurueckgeben.
        return true;
    }
}

/**
 * Klasse welche einen MYSQL-Treiber darstellt.
 *
 * @author Sebastian Brosch
 * @copyright Sebastian Brosch, 2012
 * @since 1.0.0
 */
class DB_MYSQL extends Database {
	/**
	 * Der Konstruktor der Klasse welcher einen Verbindungsaufbau zur Datenbank ermoeglicht.
	 * @param string $hostname Der Hostname auf welchem sich der Datenbank-Server befindet.
	 * @param string $username Der Name des Datenbank-Benutzers.
	 * @param string $password Das Passwort des Datenbank-Benutzers.
	 * @param string $database Der Name der Datenbank.
	 * @param string $prefix Optional. Das Praefix der Tabellen.
	 * @param string $port Optional. Der Port des Datenbank-Servers.
	 * @since 1.0.0
	 */
	public function __construct($hostname, $username, $password, $database, $prefix = '', $port = '3306') {

		//Name des Treibers in der Klasse speichern.
		$this->_driver = 'mysql';

		//Pruefen ob die Extension geladen ist.
		if(extension_loaded('mysql') === false) {

			//Script beenden und Meldung ausgeben.
			die('MYSQL is not available on this server. Please change the database driver in configuration.php.');
		}

		//Praefix der Tabellen in der Klasse speichern.
		$this->_prefix = trim($prefix);

		//Verbindung zum Server herstellen und ueberpruefen.
		if($this->_resource = mysql_connect($hostname.':'.$port, $username, $password)) {

			//Zeichensatz der Verbindung festlegen.
			mysql_set_charset('utf8', $this->_resource);

			//Verbindung zur Datenbank herstellen und ueberpruefen.
			if(mysql_select_db($database, $this->_resource) === false) {

				//Script beenden und Meldung ausgeben.
				die('Database error on line '.__LINE__.': '.$this->getErrorMessage().' ('.$this->getErrorNumber().')');
			}
		} else {

			//Script beenden und Meldung ausgeben.
			die('Database server error on line '.__LINE__.': '.$this->getErrorMessage().' ('.$this->getErrorMessage().')');
		}
	}

	/**
	 * Destruktor der Klasse um eine Verbindung und temporäre Daten zu entfernen.
	 * @since 1.0.0
	 */
	public function __destruct() {

        //SQL-Befehl zuruecksetzen.
        $this->_sql = '';

        //Ergebnismenge freigeben.
        if(is_resource($this->_cursor) === true) {
            mysql_free_result($this->_cursor);
        }

        //Ergebnismenge in der Klasse zuruecksetzen.
        $this->_cursor = null;

        //Datenbank-Verbindung schliessen.
        if(is_resource($this->_resource) === true) {
            mysql_close($this->_resource);
        }

        //Zureucksetzen der Verbindung.
        $this->_resource = null;
	}

	/**
	 * Methode um die Fehlermeldung ermitteln zu koennen.
	 * @return Die Fehlermeldung falls ein Fehler aufgetreten ist.
	 * @since 1.0.0
	 */
	public function getErrorMessage() {
		return mysql_error($this->_resource);
	}

	/**
	 * Methode um die Fehlernummer ermitteln zu koennen.
	 * @return Die Fehlernummer falls ein Fehler aufgetreten ist.
	 * @since 1.0.0
	 */
	public function getErrorNumber() {
		return mysql_errno($this->_resource);
	}

    /**
     * Methode um eine Zeichenkette maskiert zu erhalten.
     * @param string $string Die Zeichenkette welche maskiert werden soll.
     * @return Die maskierte Zeichenkette.
     * @since 1.0.0
     */
    public function getEscaped($string) {
        return mysql_real_escape_string($string);
    }

    /**
     * Methode um die ID einer neuen Zeile zu erhalten.
     * @return Die ID der neuen Zeile.
     * @since 1.0.0
     */
    public function getInsertID() {
        return mysql_insert_id($this->_resource);
    }

    /**
     * Methode um die Ergebnismenge als Objekte in einem Array zu erhalten.
     * @return Der Array mit den Objekten oder false.
     * @since 1.0.0
     */
    public function getObjectArray() {

        //Befehl ausfuehren und pruefen.
        if($this->query() === true) {

            //Array fuer die Objekte / Zeilen aus der Datenbank erstellen.
            $objects = array();

            //Alle Zeilen aus der Ergebnismenge laden.
            while($row = mysql_fetch_object($this->_cursor)) {

                //Aktuelles Objekt in das Array schreiben.
                $objects[] = $row;
            }

            //Zurueckgeben der Objekte.
            return $objects;
        } else {

            //Status zurueckgeben.
            return false;
        }
    }

    /**
     * Methode um den Wert der ersten Spalte in der ersten Zeile auslesen.
     * @return Der Wert in der ersten Spalte der ersten Zeile oder false.
     * @since 1.0.0
     */
    public function getResult() {

        //Befehl ausfuehren und pruefen.
        if($this->query() === true) {

            //Erste Zeile aus der Ergebnismenge ermitteln.
            if($row = mysql_fetch_row($this->_cursor)) {

                //Zurueckgeben der ersten Spalte.
                return $row[0];
            }
        }

        //Status zurueckgeben.
        return false;
    }

    /**
     * Methode um einen Befehl ausfuehren zu koennen.
     * @param string $sql Optional. Der Befehl welcher ausgefuehrt werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
    public function query($sql = '') {

        //Pruefen ob ein SQL-Befehl uebergeben wurde.
        $sql = (trim($sql) === '') ? trim($this->_sql) : $sql;

        //Semikolon am Ende des Befehls entfernen, falls vorhanden.
        $sql = preg_replace('/;$/', '', trim($sql));

        //Pruefen ob ein Befehl vorhanden ist.
        if(trim($sql) !== '') {

            //Ausfuehren des Befehls.
            if($this->_cursor = mysql_query($sql, $this->_resource)) {

			   	//SQL zuruecksetzen.
				$this->_sql = '';

                //Status zurueckgeben.
                return true;
            } else {

				//Fehlercode setzen.
                $this->_error_code = 'Error on line '.__LINE__.': Query: '.trim($sql).', Error-Message: '.$this->getErrorMessage().', Error-Code: '.$this->getErrorCode();
            }
        }

		//SQL zuruecksetzen.
		$this->_sql = '';

        //Status zurueckgeben.
        return false;
    }
}

/**
 * Klasse welche einen MYSQLi-Treiber darstellt.
 *
 * @author Sebastian Brosch
 * @copyright Sebastian Brosch, 2012
 * @since 1.0.0
 */
class DB_MYSQLi extends Database {
	/**
	 * Der Konstruktor der Klasse welcher einen Verbindungsaufbau zur Datenbank ermoeglicht.
	 * @param string $hostname Der Hostname auf welchem sich der Datenbank-Server befindet.
	 * @param string $username Der Name des Datenbank-Benutzers.
	 * @param string $password Das Passwort des Datenbank-Benutzers.
	 * @param string $database Der Name der Datenbank.
	 * @param string $prefix Optional. Das Praefix der Tabellen.
	 * @param string $port Optional. Der Port des Datenbank-Servers.
	 * @since 1.0.0
	 */
	public function __construct($hostname, $username, $password, $database, $prefix = '', $port = '3306') {

		//Name des Treibers in der Klasse speichern.
		$this->_driver = 'mysqli';

		//Pruefen ob die Extension geladen ist.
		if(extension_loaded('mysqli') === false) {

			//Script beenden und Meldung ausgeben.
			die('MYSQLi is not available on this server. Please change the database driver in configuration.php.');
		}

		//Praefix der Tabellen in der Klasse speichern.
		$this->_prefix = trim($prefix);

		//Verbindung zum Server herstellen und ueberpruefen.
		if($this->_resource = mysqli_connect($hostname, $username, $password, $database, $port)) {

			//Zeichensatz der Verbindung festlegen.
			mysqli_set_charset($this->_resource, 'utf8');
		} else {

			//Script beenden und Meldung ausgeben.
			die('Database server error on line '.__LINE__.': '.$this->getErrorMessage().' ('.$this->getErrorNumber().')');
		}
	}

    /**
     * Destruktor der Klasse um eine Verbindung und temporäre Daten zu entfernen.
     * @since 1.0.0
     */
    public function __destruct() {

        //SQL-Befehl zuruecksetzen.
        $this->_sql = '';

        //Ergebnismenge in der Klasse zuruecksetzen.
        $this->_cursor = null;

        //Datenbank-Verbindung schliessen.
        mysqli_close($this->_resource);

        //Zureucksetzen der Verbindung.
        $this->_resource = null;
    }

	/**
	 * Methode um die Fehlermeldung ermitteln zu koennen.
	 * @return Die Fehlermeldung falls ein Fehler aufgetreten ist.
	 * @since 1.0.0
	 */
	public function getErrorMessage() {
		return mysqli_error($this->_resource);
	}

	/**
	 * Methode um die Fehlernummer ermitteln zu koennen.
	 * @return Die Fehlernummer falls ein Fehler aufgetreten ist.
	 * @since 1.0.0
	 */
	public function getErrorNumber() {
		return mysqli_errno($this->_resource);
	}

    /**
     * Methode um eine Zeichenkette maskiert zu erhalten.
     * @param string $string Die Zeichenkette welche maskiert werden soll.
     * @return Die maskierte Zeichenkette.
     * @since 1.0.0
     */
    public function getEscaped($string) {
        return mysqli_escape_string($this->_resource, $string);
    }

    /**
     * Methode um die ID einer neuen Zeile zu erhalten.
     * @return Die ID der neuen Zeile.
     * @since 1.0.0
     */
    public function getInsertID() {
        return mysqli_insert_id($this->_resource);
    }

    /**
     * Methode um die Ergebnismenge als Objekte in einem Array zu erhalten.
     * @return Der Array mit den Objekten oder false.
     * @since 1.0.0
     */
    public function getObjectArray() {

        //Befehl ausfuehren und pruefen.
        if($this->query() === true) {

            //Array fuer die Objekte / Zeilen aus der Datenbank erstellen.
            $objects = array();

            //Alle Zeilen aus der Ergebnismenge laden.
            while($row = mysqli_fetch_object($this->_cursor)) {

                //Aktuelles Objekt in das Array schreiben.
                $objects[] = $row;
            }

            //Zurueckgeben der Objekte.
            return $objects;
        } else {

            //Status zurueckgeben.
            return false;
        }
    }

    /**
     * Methode um den Wert der ersten Spalte in der ersten Zeile auslesen.
     * @return Der Wert in der ersten Spalte der ersten Zeile.
     * @since 1.0.0
     */
    public function getResult() {

        //Befehl ausfuehren und pruefen.
        if($this->query() === true) {

            //Erste Zeile aus der Ergebnismenge ermitteln.
            if($row = mysqli_fetch_row($this->_cursor)) {

                //Zurueckgeben der ersten Spalte.
                return $row[0];
            }
        }

        //Status zurueckgeben.
        return false;
    }

    /**
     * Methode um einen Befehl ausfuehren zu koennen.
     * @param string $sql Optional. Der Befehl welcher ausgefuehrt werden soll.
     * @return Der Status.
     * @since 1.0.0
     */
    public function query($sql = '') {

        //Pruefen ob ein SQL-Befehl uebergeben wurde.
        $sql = (trim($sql) === '') ? trim($this->_sql) : $sql;

        //Semikolon am Ende des Befehls entfernen, falls vorhanden.
        $sql = preg_replace('/;$/', '', trim($sql));

        //Pruefen ob ein Befehl vorhanden ist.
        if(trim($sql) !== '') {

            //Ausfuehren des Befehls.
            if($this->_cursor = mysqli_query($this->_resource, $sql)) {

                //SQL zuruecksetzen.
                $this->_sql = '';

                //Status zurueckgeben.
                return true;
            } else {

                //Fehlercode setzen.
                $this->_error_code = 'Error on line '.__LINE__.': Query: '.trim($sql).', Error-Message: '.$this->getErrorMessage().', Error-Code: '.$this->getErrorCode();
            }
        }

        //SQL zuruecksetzen.
        $this->_sql = '';

        //Status zurueckgeben.
        return false;
    }
}
?>