<?php
//Beim update allein brauche ich einen anderen Pfad
require_once('../includes/core.class.php');
require_once('../includes/database.class.php');
require_once('../includes/section.class.php');
require_once('../includes/crypt.class.php');

//Instanzen erzeugen.
$PICVID['CORE'] = new Core();
$PICVID['ENCRYPTION'] = new Encryption($PICVID['CORE']->getValue('security_key'));
$PICVID['DATABASE'] = $PICVID['CORE']->getDatabaseObject();
$PICVID['SECTION'] = new Section($PICVID['DATABASE']);

//Pfad der Sections setzen.
$path = $PICVID['CORE']->getValue('absolute_path').'/section';

//Pruefen ob alle section-Dateien bereits vorhanden sind.
if(file_exists($path) === true) {

    //Alle Verzeichnisse in section ermitteln.
	$files = scandir($path);

    //Pruefen ob Elemente vorhanden sind.
    if((is_array($files) === true) && (count($files) > 0)) {

        //Alle Ordner durchlaufen.
        foreach($files as $file){

            //Ungueltige Dateien werden nicht beruecksichtigt.
            if(preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file) == 0){

                //Name der neuen Section setzen.
                $PICVID['SECTION']->_name = $file;

                //Neue Section erstellen.
                $PICVID['SECTION']->create();

                //Update ausfuehren.
				$PICVID['SECTION']->executeUpdate($file, $PICVID['SECTION']->getVersion($file));
            }
        }
    }
}

//SQL-Befehl setzen (Tabellen optimieren).
$PICVID['DATABASE']->setQuery("OPTIMIZE TABLE `#__image` ,`#__menu` ,`#__news` ,`#__rich_profile` ,`#__section` ,`#__user` ,`#__user_group` ,`#__user_session` ,`#__video`");

//SQL-Befehl ausfuehren.
$PICVID['DATABASE']->query();

//Pruefen ob die letzte Seite verfuegbar ist.
if((isset($_SERVER['HTTP_REFERER']) === true) && (strpos($_SERVER['HTTP_REFERER'], $PICVID['CORE']->getValue('site_url')) !== false)) {
    $PICVID['CORE']->redirect($_SERVER['HTTP_REFERER']);
} else {
    $PICVID['CORE']->redirect('index.php');
}
?>