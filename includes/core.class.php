<?php
/**
 * Klasse welche den Kern von PicVid darstellt.
 * @author Sebastian Brosch
 * @copyright GNU General Public License
 * @since 1.0.0
 */
class Core {
    /**
     * Konstruktor der Klasse.
     * @since 1.0.0
     */
    public function __construct() {

        //Konfiguration in die Klasse laden.
        $this->loadConfiguration();
    }

	/**
	 * Methode um einen Status formatieren zu koennen.
	 * @param int / boolean $state Der Status als Zahl (0 / 1) oder Boolean (true / false).
	 * @param string $type Optional. Der Ausgabetyp wie der Status dargestellt werden soll.
	 * @param string $success Optional. Ein String welcher bei positivem Status verwendet werden soll.
	 * @param string $error Optional. Ein String welcher bei negativem Status verwendet werden soll.
	 * @since 1.0.0
	 */
	public function formatState($state, $type = 'image', $success = '', $error = '') {

		//Pruefen ob ein Bild als Status ausgegeben werden soll.
		if((($state === 1) || ($state === true)) && ($type === 'image')) {
			echo HTML::img(array('alt' => 'Success', 'border' => 0, 'height' => 24, 'src' => '../images/ok_32.png', 'title' => 'Success', 'width' => 24));
		} elseif((($state === 0) || ($state === false)) && ($type === 'image')) {
			echo HTML::img(array('alt' => 'Error', 'border' => 0, 'height' => 24, 'src' => '../images/error_32.png', 'title' => 'Error', 'width' => 24));
		}

		//Pruefen ob ein Text als Status ausgegeben werden soll.
		if((($state === 1) || ($state === true)) && ($type === 'text')) {

			//Pruefen ob ein Text uebergeben wurde.
			if(trim($success) !== '') {
				echo $success;
			} else {
				echo 'Verfügbar';
			}
		} elseif((($state === 0) || ($state === false)) && ($type === 'text')) {

			//Pruefen ob ein Text uebergeben wurde.
			if(trim($error) !== '') {
				echo $error;
			} else {
				echo 'Nicht verfügbar';
			}
		}
	}

	/**
     * Methode um ein Datenbankobjekt eines bestimmten Treibers zu bekommen.
     * @param string $driver Optional. Der Name des Datenbanktreibers.
     * @return Das Objekt der Datenbank.
     * @since 1.0.0
     */
	public function getDatabaseObject($driver = '') {

        //Pruefen ob ein Treiber uebergeben wurde.
        $driver = (trim($driver) === '') ? $this->getValue('db_driver', 'mysql') : $driver;

		//Pruefen welcher Treiber verwendet werden soll.
        switch($driver) {
			case 'mysqli':
                return new DB_MYSQLi($this->getValue('db_hostname'), $this->getValue('db_username'), $this->getValue('db_password'), $this->getValue('db_name'), $this->getValue('db_prefix'), $this->getValue('db_port'));
                break;
			case 'mysql':
            default:
                return new DB_MYSQL($this->getValue('db_hostname'), $this->getValue('db_username'), $this->getValue('db_password'), $this->getValue('db_name'), $this->getValue('db_prefix'), $this->getValue('db_port'));
                break;
		}
	}

	/**
     * Methode um einen Wert aus einem Array auslesen zu koennen.
     * @param array $array Das Array aus welchem der Wert ausgelesen werden soll.
     * @param string $name Der Name des Werts im Array.
     * @param string $default Optional. Ein Standardwert welcher zurueckgegeben wird falls kein Wert verfuegbar ist.
     * @return Der Wert oder der uebergebene Standardwert.
     * @since 1.0.0
     */
	public function getParameter($array, $name, $default = '') {
		return (isset($array[$name]) === true) ? $array[$name] : $default;
  	}

    /**
     * Eigenschaft um einen Wert aus der Klasse ermitteln zu koennen.
     * @param string $value Der Wert welcher ermittelt werden soll.
     * @param string $default Optional. Der Wert welcher als Standard verwendet werden soll.
     * @return Der Wert oder der Standard.
     * @since 1.0.0
     */
    public function getValue($value, $default = '') {

        //Einbinden der globalen Variable.
        global $PICVID;

        //Eigenschaft erweitern.
        $value = '_'.$value;

        //Pruefen ob die Eigenschaft vorhanden ist.
        if(isset($this->$value) === true) {

            //Pruefen ob ein Sicherheitsschluessel vorhanden ist.
            if((isset($this->_security_key) === true) && (trim($this->_security_key) === '')) {
                return $this->$value;
            }

            //Pruefen ob die Eigenschaft verschluesselt sein koennte.
            if(in_array($value, array('_db_hostname', '_db_name', '_db_password', '_db_prefix', '_db_username', '_db_port', '_db_driver')) === true) {

                //Zurueckgeben des entschluesselten Wertes.
                return $this->$value;
            }

            //Zurueckgeben der Eigenschaft (unverschluesselt).
            return $this->$value;
        } else {

            //Pruefen ob der Absolute-Pfad ermittelt werden soll.
            if(($value === '_absolute_path') && ($default === '')) {
                return getcwd();
            }

            //Pruefen ob die Seiten-URL zurueckgegeben werden soll.
            if(($value === '_site_url') && ($default === '')) {
                return 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '').$_SERVER['PHP_SELF'];
            }

            //Standardwert zurueckgeben.
            return $default;
        }
    }

    /**
     * Methode um die Konfiguration in die Klasse zu laden.
     * @return Der Status.
     * @since 1.0.0
     */
    public function loadConfiguration() {

        //Pruefen ob die Konfigurationsdatei vorhanden ist.
        if(((file_exists('../configuration.php') === true) && ($configuration_content = file_get_contents('../configuration.php'))) || ((file_exists('configuration.php') === true) && ($configuration_content = file_get_contents('configuration.php')))) {

        	//Setzen der Konfigurationen als Eigenschaften der Klasse.
            $configuration_content = str_replace('$_', '$this->_', $configuration_content);

           	//PHP-Tags entfernen.
            $configuration_content = str_replace('<?php'."\n", '', $configuration_content);
            $configuration_content = str_replace('?>', '', $configuration_content);

            //Neuen PHP-Code ausfueheren und als Eigenschaft des Kerns registrieren.
            eval($configuration_content);
        } else {

			//Pruefen ob der Benutzer sich bereits in der Installation befindet.
			if(strpos($_SERVER['PHP_SELF'], 'installation', 0) === false) {

				//Weiterleiten auf die Installation.
				header('Location: installation/index.php');
			}
        }
    }

	/**
     * Methode um Weiterleiten zu koennen (mit Fehlermeldung).
     * @param string $url Die URL auf welche weitergeleitet werden soll.
     * @param string $message Optional. Die Nachricht welche in der Session gesetzt werden soll.
     * @param string $level Optional. Das Fehlerlevel welches in der Session gesetzt werden soll.
     * @since 1.0.0
     */
    public function redirect($url, $message = '', $level = 'info') {

        //Pruefen ob eine Nachricht uebergeben wurde.
        if(trim($message) === '') {

            //Pruefen ob die Session vorhanden ist.
            if((isset($_SESSION['message_text']) === true) && (isset($_SESSION['message_level']) === true)) {

                //Zuruecksetzen der Nachricht in der Session.
                unset($_SESSION['message_text'], $_SESSION['message_level']);
            }
        } else {

            //Eigenschaft der Nachricht in die Session setzen.
            $_SESSION['message_text'] = $message;
            $_SESSION['message_level'] = $level;
        }

        //Pruefen ob eine Weiterleitung vorgenommen werden soll.
        if(trim($url) !== '') {

            //Pruefen ob bereits ein Header gesendet wurde.
            if(headers_sent() === true) {

                //JavaScript fuer die Weiterleitung verwenden.
                echo '<script type="text/javascript">'."\n";
                echo '//<![CDATA['."\n";
                echo 'document.location.href="'.$url.'";'."\n";
                echo '//]]>'."\n";
                echo '</script>';
            } else {

                //PHP fuer die Weiterleitung verwenden.
                header('Location: '.$url);
            }
        }
    }

    /**
     * Methode um die Konfiguration schreiben zu koennen.
     * @param string $key Der Name der Einstellung welche gespeichert werden soll.
     * @param string $value Der Wert der Einstellung.
     * @param string $remove Optional. Der Status ob und wie geloescht werden soll (STRICT: Wert und Variable muss passen, ALL: Nur Variable muss passen).
     * @return Der Status.
     * @since 1.0.0
     */
    public function writeConfiguration($key, $value, $remove = '') {

        //Pruefen ob die Konfiguration existiert.
        if((file_exists('../configuration.php') === true) || (strpos($_SERVER['PHP_SELF'], 'installation', 0) !== false)) {
            $config_path = '../configuration.php';
        } elseif(file_exists('configuration.php') === true) {
            $config_path = 'configuration.php';
        } else {
            return false;
        }

        //Neue Zeile der Konfiguration erzeugen.
        $new_line = "\$_".$key." = '".$value."';"."\n";

        //Pruefen ob die Datei existiert und der Inhalt ermittelt werden konnte.
        if((file_exists($config_path) === true) && ($content = file_get_contents($config_path))) {

            //Pruefen ob die neue Zeile bereits vorhanden ist.
            if((strpos($content, $new_line) !== false) && (($remove !== 'STRICT') && ($remove !== 'ALL'))) {
                return true;
            }

            //PHP-Tags entfernen.
            $content = str_replace('<?php'."\n", '', $content);
            $content = str_replace('?>', '', $content);

            //Pruefen ob die Einstellung bereits vorhanden ist.
            if(strpos($content, '$_'.$key) === false) {

                //Pruefen ob die neue Zeile geschrieben werden darf.
                if(($remove === 'STRICT') || ($remove === 'ALL')) {
                    return true;
                } else {

                    //Neue Zeile an das Ende der Datei setzen.
                    $content .= $new_line;
                }
            } else {

                //Zeilen der Konfiguration in einen Array schreiben.
                $content_lines = preg_split("/\n/", $content);

                //Inhalt zuruecksetzen.
                $content = '';

                //Pruefen ob Zeilen vorhanden sind.
                if((is_array($content_lines) === true) && (count($content_lines) > 0)) {

                    //Durchlaufen der Zeilen.
                    foreach($content_lines as $content_line) {

                        //Pruefen ob die aktuelle Zeile geaendert werden soll.
                        if(strpos($content_line, '$_'.$key) === false) {

                            //Aktuelle Zeile ohne Aenderung schreiben.
                            $content .= $content_line."\n";
                        } else {

                            //Pruefen ob die Zeile geloescht werden soll.
                            if((($remove === 'STRICT') && ($new_line === $content_line."\n")) || ($remove === 'ALL')) {
                                continue;
                            } else {

                                //Zeile zertrennen.
                                $content_line = preg_split('/ = /', $content_line);

                                //Eigenschaft mit neuem Wert setzen.
                                $content .= $content_line[0]." = '".$value."';\n";
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        } else {

            //Pruefen ob die neue Zeile geschrieben werden darf.
            if(($remove === 'STRICT') || ($remove === 'ALL')) {
                return true;
            } else {

                //Neue Zeile in die Datei setzen.
                $content = $new_line;
            }
        }

        //Inhalt in die Konfiguration schreiben und Status zurueckgeben.
        return file_put_contents($config_path, '<?php'."\n".trim($content)."\n".'?>');
    }
}
?>