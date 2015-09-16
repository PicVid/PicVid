<?php
/**
 * Klasse um die Ausgabe eines Template steuern und anpassen zu koennen.
 *
 * @author Sebastian Brosch <sebastian.brosch@brosch-software.de>
 * @copyright Sebastian Brosch, 2012
 * @license http://www.gnu.org/licenses/gpl.html GPL License.
 * @name Cito TemplateEngine
 * @since 1.0.0
 */
class TemplateEngine {
    /**
     * Eigenschaft um den Status der Pufferung speichern zu koennen.
     * @since 1.0.0
     * @var bool
     */
    private $_buffer_state = false;

    /**
     * Eigenschaft um den Status der Deflate-Komprimierung speichern zu koennen.
     * @since 1.0.0
     * @var bool
     */
    private $_deflate_compression_state = false;

    /**
     * Eigenschaft um den Status der manuellen GZIP-Komprimierung speichern zu koennen.
     * @since 1.0.0
     * @var bool
     */
    private $_gzip_compression_state = false;

    /**
     * Eigenschaft um alle Werte aller Marker speichern zu koennen.
     * @since 1.0.0
     * @var array
     */
    private $_markers = array();

    /**
     * Eigenschaft um die gepufferte Seite speichern zu koennen.
     * @since 1.0.0
     * @var string
     */
    private $_site_buffer = '';

    /**
     * Konstruktor der Klasse.
     * @since 1.0.0
     */
    public function __construct() {

        //GZIP-Komprimierung initialisieren.
        $this->initGZIP();

        //Ausgabepufferung starten und Status in die Klasse schreiben.
        $this->_buffer_state = ob_start();
    }

    /**
     * Methode um einen beliebigen Inhalt in einen Marker zu setzen.
     * @param string $name Der Name des Markers.
     * @param string $value Der Wert welcher in den Marker gesetzt werden soll.
     * @since 1.0.0
     */
    public function setInMarker($name, $value) {

        //Beliebigen Inhalt in den Marker setzen.
        $this->_markers[$name][] = $value;
    }

    /**
     * Methode um die Ausgabepufferung durchzufuehren.
     * @since 1.0.0
     */
    public function execute() {

        //Pruefen ob die Ausgabepufferung erfolgreich gestartet wurde.
        if($this->_buffer_state === true) {

            //Puffer in die Klasse schreiben.
            $this->_site_buffer = ob_get_contents();

            //Pruefen ob der Puffer erfolgreich geschrieben wurde.
            if($this->_site_buffer !== false) {

                //Ausgabe leeren.
                ob_end_clean();

                //Gepufferten Inhalt generieren und ausgeben.
                $this->render();
            }
        }
    }

    /**
     * Methode um die Deflate-Komprimierung aktivieren zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    private function executeDeflate() {

        //Pruefen ob der Status der Deflate-Komprimierung gesetzt ist.
        if($this->_deflate_compression_state === true) {

            //Seiten-Inhalt mit Deflate komprimieren.
            $this->_site_buffer = gzdeflate($this->_site_buffer, 9);

            //Seitenheader setzen.
            header('Content-Encoding: deflate');
        }
    }

    /**
     * Methode um die GZIP-Komprimierung manuell ausfuehren zu koennen.
     * @return Der Status.
     * @since 1.0.0
     */
    private function executeGZIP() {

        //Pruefen ob die manuelle GZIP-Komprimierung aktiviert wurde.
        if($this->_gzip_compression_state === true) {

            //Groesse des Inhalts ermitteln.
            $content_size = strlen($this->_site_buffer);

            //Checksum des Inhalts ermitteln.
            $content_checksum = crc32($this->_site_buffer);

            //Komprimieren des Inhalts.
            $this->_site_buffer = gzcompress($this->_site_buffer, 9);

            //Die letzten vier Zeichen des komprimierten Inhalts entfernen.
            $this->_site_buffer = substr($this->_site_buffer, 0, strlen($this->_site_buffer) - 4);

            //Seitenheader setzen.
            header('Content-Encoding: gzip');

            //Erzeugen des GZIP-Inhalts welcher ausgegeben werden kann.
            $this->_site_buffer = "\x1f\x8b\x08\x00\x00\x00\x00\x00".$this->_site_buffer.pack('V', $content_checksum).pack('V', $content_size);
        }
    }

    /**
     * Methode um die GZIP-Komprimierung zu initialisieren.
     * @since 1.0.0
     */
    private function initGZIP() {

        //Globale Variablen einbinden.
        global $PICVID;

        //Pruefen ob die GZIP-Komprimierung verfuegbar ist.
        if(intval(ini_get('zlib.output_compression')) !== 1) {

            //Abrufen der PHP-Version.
            $php_version = phpversion();

            //Abrufen des User-Agents.
            $user_agent = $PICVID['CORE']->getParameter($_SERVER, 'HTTP_USER_AGENT', '');

            //Abrufen der Encoding.
            $encoding = $PICVID['CORE']->getParameter($_SERVER, 'HTTP_ACCEPT_ENCODING', '');

            //Pruefen ob ein Gecko-Browser verwendet wird.
            if(strpos($user_agent, 'Gecko') !== false) {

                //Pruefen ob eine Deflate-Komprimierung moeglich ist.
                if(strpos($encoding, 'deflate') !== false) {

                    //Status der Deflate-Komprimierung setzen.
                    $this->_deflate_compression_state = true;
                } elseif(strpos($encoding, 'gzip') !== false) {

                    //Status der manuellen GZIP-Komprimierung setzen.
                    $this->_gzip_compression_state = true;
                }
            } else {

                //Pruefen ob die Vorraussetzungen passen.
                if((version_compare($php_version, '4.0.5') >= 0) && (strpos($encoding, 'gzip') !== false)) {

                    //Pruefen ob die Extension zur GZIP-Komprimierung geladen wurde.
                    if(extension_loaded('zlib') === true) {

                        //Ausgabepufferung mit Komprimierung starten.
                        ob_start('ob_gzhandler');

                        //Funktion beenden.
                        return true;
                    }
                }
            }
        }
    }

    /**
     * Methode um einen Marker zu registrieren.
     * @param string $name Der Name des Markers welcher registriert werden soll.
     * @since 1.0.0
     */
    public function registerMarker($name) {

        //Marker in den Marker-Array schreiben.
        $this->_markers[$name][] = '';

        //Marker ausgeben.
        echo '##-#'.$name.'#-##';
    }

    /**
     * Methode um das Template zu generieren und anpassen zu koennen.
     * @since 1.0.0
     */
    public function render() {

        //Alle registrierten Marker des Systems ersetzen.
        $this->replaceMarkers();

        //Deflate-Komprimierung ausfuehren.
        $this->executeDeflate();

        //Manuelle GZIP-Komprimierung ausfuehren.
        $this->executeGZIP();

        //Ausgeben des gepufferten Inhalts.
        echo $this->_site_buffer;
    }

    /**
     * Methode um die Marker durch die Inhalte ersetzen zu koennen.
     * @since 1.0.0
     */
    public function replaceMarkers() {

        //Inhalt eines Markers zuruecksetzen.
        $marker_content = '';

        //Pruefen ob das Template Marker enthaelt.
        if(count($this->_markers)) {

            //Alle Marker durchlaufen.
            foreach($this->_markers as $marker => $value) {

                //Durchlaufen aller Marker-Inhalte.
                foreach($value as $marker_element => $value) {

                    if($value === '') {

                        //Marker-Inhalt erweitern.
                        $marker_content .= $value;
                    } else {

                        //Marker-Inhalt erweitern.
                        $marker_content .= $value."\n";
                    }
                }

                //Marker im gepufferten Inhalt ersetzen durch die Inhalte.
                $this->_site_buffer = str_replace('##-#'.$marker.'#-##', $marker_content, $this->_site_buffer);
            }
        }
    }
}
?>