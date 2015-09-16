<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Profil</title>');

$Core = new Core();
$Database = $Core->getDatabaseObject();
//Benutzer bei Rich Profile anlegen, die bei _user vorhanden sind.
$Database->setQuery('SELECT  `id` FROM  `#__user`');
$existingusers = $Database->getObjectArray();
foreach ($existingusers as $user_existing) {
	$Database->setQuery('SELECT  `id` FROM  `#__rich_profile` WHERE `id` = `'. $user_existing->id .'` `');
	$result = $Database->getObjectArray();
	if($result == 0){
		$sql = "INSERT INTO `#__rich_profile` (`user_id`) VALUES ('". $user_existing->id ."');";
		$Database->setQuery($sql);
		$Database->query();
	}
}

//Html ausgabe von Optionen mit selectierung erzeugen
function generateSelectOptions($options = array(), $selected = 0) {
		$i=0;
		$html = "";
	    foreach ($options as $option) {
	        if ($i == $selected) {
	            $html .= '<option value='.$i.' selected="selected">'.$option.'</option>';
	        } else {
	            $html .= '<option value='.$i.'>'.$option.'</option>';
	        }
			$i++;
	    }
	    echo $html;
	}

//Wenn Abbrechen gedrückt wird
if(isset($_POST['abbort']))
{
	$PICVID['CORE']->redirect('index.php');
}

//Wird eine Action verlangt? 
elseif(isset($_GET['action'])){
	//Auf existierende tastks "changemail" und "changepw" überprüfen -> ansonsten redirect	
	if($_GET['action'] != "changemail" AND $_GET['action'] != "changepw")
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1', 'Diese Seite existiert nicht!', 'error');
	include_once($_GET['action'].".php"); //Bestimmte Action-form einbinden
}

//Wenn "changemail" abgeschickt wurde
elseif(isset($_POST['changemail']))
{
	//Überprüfungen
	//Beide Felder ausgefüllt?
	if($_POST['database_newMail'] == "" OR $_POST['database_newMailconfirm'] == ""){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changemail', 'Beide Felder m&uuml;ssen ausgef&uuml;llt sein!', 'error');
		die();		
	}
	//Bestätigt?
	if(!isset($_POST['database_confirm'])){		
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changemail', 'Sie m&uuml;ssen die &Auml;nderung best&auml;tigen!', 'error');
		die();
	}
	//Beide Mails gleich (groß klein schreibung nicht beachten)?
	if(strtolower($_POST['database_newMail']) != strtolower($_POST['database_newMailconfirm'])){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changemail', 'Email Adressen m&uuml;ssen identisch sein!', 'error');
		die();		
	}
	//Vadile Mail adresse (database_newMail)?
	if(preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_\-]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST['database_newMail']) == 0){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changemail', 'Es m&uuml;ssen gültige Email Adressen angegeben werden!', 'error');
		die();		
	}
	//Vadile Mail adresse (database_newMailconfirm)?
	if(preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_\-]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $_POST['database_newMailconfirm']) == 0){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changemail', 'Es m&uuml;ssen gültige Email Adressen angegeben werden!', 'error');
		die();		
	}
	//Besitzt ein anderer benutzer diese email?
	$Database->setQuery("SELECT COUNT(`id`) FROM `#__user` WHERE `email` = '".$_POST['database_newMail']."'");
	if($Database->getResult() > 0){		
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changemail', 'Email Adresse schon in benutzung!', 'error');
		die();
	}
	
	$PICVID['ACT_USER']->_email = $_POST['database_newMail']; //neue email wird gesetzt
	$PICVID['ACT_USER']->deactivate();//benutzer wird deaktiviert
	$PICVID['ACT_USER']->update();//neue email wird in die Datenbank geschrieben
	$PICVID['USER_SESSION']->delete(); //user wird ausgeloggt
	$PICVID['CORE']->redirect('index.php', 'Email Adresse erfolgreich ge&auml;ndert. Sie wurden ausgeloggt und k&ouml;nnen sich erst nach der Best&auml;tigung wieder einloggen.', 'success');
	die();
}

//wenn changepw abgeschickt wurde
elseif(isset($_POST['changepw']))
{
	//Überprüfung
	//Alle Felder ausgefüllt?
	if($_POST['database_newPW'] == "" OR $_POST['database_newPWconfirm'] == "" OR $_POST['database_oldPW'] == "" or !isset($_POST['database_oldPW']) or !isset($_POST['database_newPW']) or !isset($_POST['database_newPWconfirm']) ){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changepw', 'Alle Felder m&uuml;ssen ausgef&uuml;llt sein!', 'error');
		die();
	}

	//Bestätigt?
	if(!isset($_POST['database_confirm'])){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changepw', 'Sie m&uuml;ssen die &Auml;nderung best&auml;tigen!', 'error');
		die();		
	}

	//Beide Passwörter gleich (groß klein schreibung beachten)?
	if($_POST['database_newPW'] != $_POST['database_newPWconfirm']){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changepw', 'Passw&ouml;rter m&uuml;ssen identisch sein!', 'error');
		die();		
	}

	//Altes Passwort richtig?
	//SQL-Befehl setzen (Benutzer abrufen).
    $Database->setQuery("SELECT `id` FROM `#__user` WHERE `username` = '".$PICVID['ACT_USER']->_username."' AND `password` = '".md5($_POST['database_oldPW'])."'");
	//ID des Benutzers erhalten.
    $user_id = $Database->getResult();
    //Pruefen ob eine ID ermittelt wurde.
	if($user_id == 0){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1&action=changepw', 'Altes Passwort war falsch!', 'error');
		die();		
	}
	
	$PICVID['ACT_USER']->_password = $_POST['database_newPW']; //neues passwort wird gesetzt
	$PICVID['ACT_USER']->update();//neues passwort wird in die Datenbank geschrieben
	$PICVID['USER_SESSION']->delete(); //user ausloggen
	$PICVID['CORE']->redirect('index.php', 'Passwort erfolgreich ge&auml;ndert. Sie wurden ausgeloggt.', 'success');
}

//Wenn "save" abgeschickt wurde (speichern der rich profile daten)
elseif(isset($_POST['save']))
{
	//Alle variablen übernehmen 
	$id = $_POST['editid'];
	$live_city = $_POST['database_live_city'];
	$opt_birthday = $_POST['database_opt_birthday'];
	$live_country = $_POST['database_live_country'];
	$opt_live = $_POST['database_opt_live'];
	$opt_mail = $_POST['database_opt_mail'];
	$icq = $_POST['database_icq'];
	if($icq == "") $icq = NULL;
	$opt_icq = $_POST['database_opt_icq'];
	$msn = $_POST['database_msn'];
	$opt_msn = $_POST['database_opt_msn'];
	$skype = $_POST['database_skype'];
	$opt_skype = $_POST['database_opt_skype'];
	$gtalk = $_POST['database_gtalk'];
	$opt_gtalk = $_POST['database_opt_gtalk'];
	$twitter = $_POST['database_twitter'];
	$opt_twitter = $_POST['database_opt_twitter'];
	$facebook = $_POST['database_facebook'];
	$opt_facebook = $_POST['database_opt_facebook'];

	$birthday_day = $_POST['database_birthday_day'];
	$birthday_month = $_POST['database_birthday_month'];
	$birthday_year = $_POST['database_birthday_year'];
	
	//Validieren
	if($birthday_day == "" OR !is_numeric($birthday_day)) $birthday_day = 1;
	if($birthday_month == "" OR !is_numeric($birthday_month)) $birthday_month = 1;
	if($birthday_year == "" OR !is_numeric($birthday_year)) $birthday_year = 1930;
	
	//SQL Befehl für Rich Profile erstellen
	$SQL = "UPDATE `#__rich_profile` SET `birthday` = '".$birthday_year."-".$birthday_month."-".$birthday_day." 00:00:00', `live_city` = '".$live_city."', `live_country` = '".$live_country."',
	`icq` = '".$icq."', `msn` = '".$msn."', `skype` = '".$skype."', `gtalk` = '".$gtalk."', `twitter` = '".$twitter."', `facebook` = '".$facebook."', `opt_birthday` = '".$opt_birthday."',
	`opt_live` = '".$opt_live."', `opt_icq` = '".$opt_icq."', `opt_msn` = '".$opt_msn."', `opt_skype` = '".$opt_skype."', `opt_gtalk` = '".$opt_gtalk."', `opt_twitter` = '".$opt_twitter."', `opt_facebook` = '".$opt_facebook."',
	`opt_mail` = '".$opt_mail."' WHERE `#__rich_profile`.`user_id` = ".$id.";";
	$Database->setQuery($SQL); //SQL Befehl setzen
	$Database->query(); //SQL Befehl ausführen
	 
	//Usereinstellungen updaten
	$PICVID['ACT_USER']->_name = $_POST['database_name']; //name auslesen
	
	// User aktuallisieren und auf SQL Fehler prüfen
	if($PICVID['ACT_USER']->update() == false){
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1', 'Ein Fehler ist aufgetreten: '.$PICVID['DATABASE']->getErrorCode(), 'error');
	} 
	if($PICVID['ACT_USER']->getErrorCode() == ""){	
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1', 'Daten erfolgreich gespeichert!', 'success');
	}
	else{
		$PICVID['CORE']->redirect('index.php?section=profile&admin=1', 'Ein Fehler ist aufgetreten: '.$PICVID['ACT_USER']->getErrorCode(), 'error');
	}
	//$Core->redirect("index.php?section=profile&admin=1");//weiterleitung
}

//Datei wurde aufgerufen ohne übergabe -> Eigenes Profil Bearbeiten
else{
	//Variablen werden deklariert um fehlermeldungen in der ausgabe zu vermeiden!!!
	//declares variables
	$show_name = "";
	$show_username = "";
	$show_birthday_day = "";
	$show_birthday_month = "";
	$show_birthday_year = "";
	$show_live_city = "";
	$show_live_country = "";
	$show_mail = "";

	//declares social variables
	$show_icq = "";
	$show_msn = "";
	$show_skype = "";
	$show_gtalk = "";
	$show_twitter = "";
	$show_facebook = "";

	//declares options
	$opt_birthday = 0;
	$opts_birthday = array("Verbergen","Nur Alter","Alter und Datum");

	$opt_live = 0;
	$opts_live = array("Verbergen","Nur Land","Stadt und Land");

	$opt_mail = 0;

	$opts_social = array("Verbergen","Nur für Registrierte Benutzer anzeigen","Für Besucher sichtbar");
	$opt_icq = 0;
	$opt_msn = 0;
	$opt_skype = 0;
	$opt_gtalk = 0;
	$opt_twitter = 0;
	$opt_facebook = 0;
  

	//USERID in Variable speichern
	$userid = $PICVID['ACT_USER']->_id;
	$show_username = $PICVID['ACT_USER']->_username;
	$show_mail = $PICVID['ACT_USER']->_email;
	
	//Benutzergruppe auslesen und Name Anzeigen
	$Database->setQuery('SELECT  `name` FROM  `#__user_group` WHERE `id` = '.$PICVID['ACT_USER']->_group);
	$show_groupname = $Database->getResult();

	//Daten aus der _user datenbank auslesen und ausgeben (Fehler beim namen in User Klasse)
	$Database->setQuery('SELECT  `name` FROM  `#__user` WHERE `id` = '.$userid);
	$show_name = $Database->getResult();

	//Variablen aus rich_profile Tabelle auslesen
	$Database->setQuery('SELECT  * FROM  `#__rich_profile` WHERE `user_id` = '.$userid);
	$thisUserRich = $Database->getObjectArray();
	
	$datetime = strtotime($thisUserRich[0]->birthday);
	$show_birthday_day = date('d', $datetime);
	$show_birthday_month = date('m', $datetime);
	$show_birthday_year = date('Y', $datetime);
	$show_live_city = $thisUserRich[0]->live_city;
	$show_live_country = $thisUserRich[0]->live_country ;

	//declares social variables
	$show_icq = $thisUserRich[0]->icq;
	if($show_icq == 0 OR $show_icq == NULL ) $show_icq = "";
	$show_msn = $thisUserRich[0]->msn;
	$show_skype = $thisUserRich[0]->skype;
	$show_gtalk = $thisUserRich[0]->gtalk;
	$show_twitter = $thisUserRich[0]->twitter;
	$show_facebook = $thisUserRich[0]->facebook;

	//declares options
	$opt_birthday = $thisUserRich[0]->opt_birthday;
	$opt_live = $thisUserRich[0]->opt_live;
	$opt_mail = $thisUserRich[0]->opt_mail;

	$opt_icq = $thisUserRich[0]->opt_icq;
	$opt_msn = $thisUserRich[0]->opt_msn;
	$opt_skype = $thisUserRich[0]->opt_skype;
	$opt_gtalk = $thisUserRich[0]->opt_gtalk;
	$opt_twitter = $thisUserRich[0]->opt_twitter;
	$opt_facebook = $thisUserRich[0]->opt_facebook;
	
	//Form zur ansicht einbinden.
	include_once("form.php");
}

?>

