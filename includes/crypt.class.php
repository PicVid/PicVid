<?php
/**
 * Klasse um Daten verschluesseln und entschluesseln zu koennen.
 *
 * @author Sebastian Brosch
 * @copyright Sebastian Brosch
 * @since 1.0.0
 */
class Encryption {
    /**
     * Eigenschaft um einen Sicherheitsschluessel speichern zu koennen.
     * @var string
     * @since 1.0.0
     */
    private $_security_key = '';

    /**
     * Konstruktor der Klasse.
     * @param string $security_key Optional. Der Sicherheitsschluessel.
     * @since 1.0.0
     */
    public function __construct($security_key = 'd2eb967ab561ccdc67ee2d2f8ffc7d32') {
        $this->_security_key = trim($security_key);
    }

    /**
     * Methode um Daten zu entschluesseln.
     * @param string $data Die Daten welche entschluesselt werden sollen.
     * @return Die entschluesselten Daten.
     * @since 1.0.0
     */
    public function decrypt($data) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_security_key, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    }

    /**
     * Methode um Daten zu verschluesseln.
     * @param string $data Die Daten welche verschluesselt werden sollen.
     * @return Die verschluesselten Daten.
     * @since 1.0.0
     */
    public function encrypt($data) {
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_security_key, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
}
?>