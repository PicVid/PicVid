<?php
//Caching mit Header deaktivieren.
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

//Alle PHP-Fehlermeldungen ausgeben.
error_reporting(E_ALL);

//Pruefen ob die Konfigurationsdatei bereits vorhanden ist.
if((file_exists('../configuration.php') === true) && (filesize('../configuration.php') > 10)) {
    header('Location: ../index.php');
    exit;
}

//Externe Dateien einbinden.
require_once('../includes/crypt.class.php');
require_once('../includes/html.class.php');
require_once('../includes/core.class.php');
require_once('../includes/database.class.php');
require_once('../includes/user.class.php');

//Instanz des Kerns erzeugen.
$PICVID['CORE'] = new Core();

//Standardwerte ermitteln.
$site_url = str_replace('/installation/install.php', '', $PICVID['CORE']->getValue('site_url'));
$absolute_path = str_replace('\installation', '', $PICVID['CORE']->getValue('absolute_path'));
?>
<?php
//Pruefen ob installiert werden soll.
if($PICVID['CORE']->getParameter($_REQUEST, 'task', '') === 'install') {

    //Sicherheitschluessel der Verschluesselung in die Encryption-Klasse setzen.
    $PICVID['ENCRYPTION'] = new Encryption($PICVID['CORE']->getParameter($_POST, 'security_key', 'd2eb967ab561ccdc67ee2d2f8ffc7d32'));

    //Kopnfiguration schreiben.
    $PICVID['CORE']->writeConfiguration('absolute_path', $PICVID['CORE']->getParameter($_POST, 'absolute_path'));
    $PICVID['CORE']->writeConfiguration('db_name', $PICVID['ENCRYPTION']->encrypt($PICVID['CORE']->getParameter($_POST, 'database_name')));
    $PICVID['CORE']->writeConfiguration('db_password', $PICVID['ENCRYPTION']->encrypt($PICVID['CORE']->getParameter($_POST, 'database_password')));
    $PICVID['CORE']->writeConfiguration('db_username', $PICVID['ENCRYPTION']->encrypt($PICVID['CORE']->getParameter($_POST, 'database_username')));
    $PICVID['CORE']->writeConfiguration('db_prefix', $PICVID['ENCRYPTION']->encrypt($PICVID['CORE']->getParameter($_POST, 'database_prefix')));
    $PICVID['CORE']->writeConfiguration('upload_path', $PICVID['CORE']->getParameter($_POST, 'upload_path'));
    $PICVID['CORE']->writeConfiguration('site_url', $PICVID['CORE']->getParameter($_POST, 'site_url'));
    $PICVID['CORE']->writeConfiguration('db_hostname', $PICVID['ENCRYPTION']->encrypt($PICVID['CORE']->getParameter($_POST, 'database_hostname')));
    $PICVID['CORE']->writeConfiguration('db_driver', $PICVID['ENCRYPTION']->encrypt($PICVID['CORE']->getParameter($_POST, 'database_driver')));
    $PICVID['CORE']->writeConfiguration('db_port', $PICVID['ENCRYPTION']->encrypt($PICVID['CORE']->getParameter($_POST, 'database_port')));
    $PICVID['CORE']->writeConfiguration('security_key', $PICVID['CORE']->getParameter($_POST, 'security_key', 'd2eb967ab561ccdc67ee2d2f8ffc7d32'));

    //Kern von PicVid neu laden.
    $PICVID['CORE']->loadConfiguration();

    //Instanzen weiterer Klassen erzeugen.
    $PICVID['DATABASE'] = $PICVID['CORE']->getDatabaseObject();
    $PICVID['ACT_USER'] = new User($PICVID['DATABASE']);
    $PICVID['USER_GROUP'] = new UserGroup($PICVID['DATABASE']);

    //Ausfuehren aller SQL-befehl fuer die Installation.
    if($PICVID['DATABASE']->splitQuery(file_get_contents('sql/main.sql'), true, $PICVID['CORE']->getValue('db_prefix')) === false) {

		//Loeschen der Konfiguration.
		unlink('../configuration.php');
    }

    //Benutzerinformationen in die Klasse speichern.
    $PICVID['ACT_USER']->_email = $PICVID['CORE']->getParameter($_POST, 'user_email');
    $PICVID['ACT_USER']->_group = $PICVID['USER_GROUP']->getGroupID('Administrator');
    $PICVID['ACT_USER']->_name = $PICVID['CORE']->getParameter($_POST, 'user_name');
    $PICVID['ACT_USER']->_state = 1;
    $PICVID['ACT_USER']->_password = $PICVID['CORE']->getParameter($_POST, 'user_password');
    $PICVID['ACT_USER']->_username = $PICVID['CORE']->getParameter($_POST, 'user_username');

    //Benutzer erstellen.
    $PICVID['ACT_USER']->create();

    //Einbinden des Updates.
    require_once('update.php');

    //Weiterleiten auf den Login des CMS.
    $PICVID['CORE']->redirect($PICVID['CORE']->getValue('site_url'));
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>PicVid - Konfiguration</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../images/favicon.png" type="image/png">
        <link rel="stylesheet" href="../includes/bootstrap/css/bootstrap.min.css" type="text/css"/>
        <script src="../includes/bootstrap/js/jquery.min.js"></script>
        <script src="../includes/bootstrap/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="../template/style.css" type="text/css"/>
	</head>
	<body id="installation">
       <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <img alt="PicVid" border="0" height="30" src="../images/picvid_transparent.png" width="120"/>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="page-header">
                <h1><img alt="Konfiguration" border="0" height="48" src="../images/install_48.png" width="48"/>Installation&nbsp;&nbsp;&nbsp;<small>Konfiguration</small></h1>
            </div>
        </div>
        <div class="container">
            <form action="install.php" id="install_form" class="form-horizontal" method="post">
                <fieldset>
                    <legend>Datenbank</legend>
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="database_hostname">Hostname</label>
                            <div class="controls">
                                <input class="input-xlarge" id="database_hostname" name="database_hostname" type="text" rel="popover" data-content="Geben Sie hier den Namen des Datenbank-Servers <b>ohne</b> Port an.<br/><b>Beispiel:</b> localhost" data-original-title="Server-Name"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="database_name">Name</label>
                            <div class="controls">
                                <input class="input-xlarge" id="database_name" name="database_name" type="text" rel="popover" data-content="Geben Sie hier den Namen der Datenbank an, in welcher PicVid installiert werden soll.<br/><b>Beispiel:</b> picvid" data-original-title="Name der Datenbank"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="database_username">Benutzername</label>
                            <div class="controls">
                                <input class="input-xlarge" id="database_username" name="database_username" type="text" rel="popover" data-content="Geben Sie hier den Benutzernamen des Datenbank-Benutzers an welcher auf die Datenbank zugreifen soll.<br/><b>Beispiel:</b> root" data-original-title="Benutzer der Datenbank"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="database_password">Passwort</label>
                            <div class="controls">
                                <input class="input-xlarge" id="database_password" name="database_password" type="password" rel="popover" data-content="Geben Sie hier das Passwort des Datenbank-Benutzers an welcher auf die Datenbank zugreifen soll.<br/><b>Beispiel:</b> geheim" data-original-title="Passwort des Benutzers"/>
                            </div>
                        </div>
                    </div>
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="database_port">Port</label>
                            <div class="controls">
                                <input class="input-xlarge" id="database_port" name="database_port" type="text" value="3306" rel="popover" data-content="Geben Sie hier den Port des Datenbank-Servers an um eine Verbindung herstellen zu können.<br/><b>Beispiel:</b> 3306" data-original-title="Port des Datenbank-Servers"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="database_prefix">Tabellen-Präfix</label>
                            <div class="controls">
                                <input class="input-xlarge" id="database_prefix" name="database_prefix" type="text" rel="popover" data-content="Geben Sie hier ein Präfix für die Tabellen an.<br/><b>Beispiel:</b> pic_" data-original-title="Tabellen-Präfix"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="database_driver">Treiber</label>
                            <div class="controls">
                                <select class="span3" id="database_driver" name="database_driver" rel="popover" data-content="Wählen Sie hier einen Treiber aus mit welchem Sie Zugriffe auf die Datenbank vornehmen möchten.<br/><b>Beispiel:</b> MySQL" data-original-title="Treiber der Datenbank">
                                    <option value="mysql">MySQL</option>
                                    <option value="mysqli">MySQLi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Seite</legend>
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="site_url">URL</label>
                            <div class="controls">
                                <input class="input-xlarge" id="site_url" name="site_url" type="text" value="<?php echo str_replace('/install.php', '', $site_url); ?>" rel="popover" data-content="Geben Sie hier die URL der Startseite von PicVid an.<br/><b>Hinweis:</b> Muss normalerweise nicht geändert werden.<br/><b>Beispiel:</b> http://www.beispiel.de/" data-original-title="URL der Startseite"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="absolute_path">Absoluter Pfad</label>
                            <div class="controls">
                                <input class="input-xlarge" id="absolute_path" name="absolute_path" type="text" value="<?php echo $absolute_path; ?>" rel="popover" data-content="Geben Sie hier den Speicherort auf der Festplatte / des Webspace an.<br/><b>Hinweis:</b> Muss normalerweise nicht geändert werden.<br/><b>Beispiel:</b> C:\xampp\htdocs\picvid" data-original-title="Speicherort"/>
                            </div>
                        </div>
                    </div>
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="upload_path">Upload-Verzeichnis</label>
                            <div class="controls">
                                <input class="input-xlarge" id="upload_path" name="upload_path" type="text" value="<?php echo $absolute_path.'\upload'; ?>" rel="popover" data-content="Pfad zum Upload-Verzeichnis in welches Dateien hochgeladen werden dürfen.<br/><b>Hinweis:</b> Muss normalerweise nicht geändert werden.<br/><b>Beispiel:</b> C:\xampp\htdocs\picvid\upload" data-original-title="Upload-Verzeichnis"/>
                            </div
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Administrator</legend>
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="user_name">Name</label>
                            <div class="controls">
                                <input class="input-xlarge" id="user_name" name="user_name" type="text" rel="popover" data-content="Geben Sie hier den vollständigen Namen des Administrators an.<br/><b>Beispiel:</b> Max Mustermann" data-original-title="Vollständiger Name"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="user_email">E-Mail</label>
                            <div class="controls">
                                <input class="input-xlarge" id="user_email" name="user_email" type="text" rel="popover" data-content="Geben Sie eine gültige E-Mail-Adresse an um Meldungen zu empfangen.<br/><b>Beispiel:</b> max.mustermann@testmail.de" data-original-title="E-Mail des Administrators"/>
                            </div>
                        </div>
                    </div>
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="user_username">Benutzername</label>
                            <div class="controls">
                                <input class="input-xlarge" id="user_username" name="user_username" type="text" rel="popover" data-content="Geben Sie hier den Benutzernamen des Administrators an mit welchem dieser sich anmelden kann.<br/><b>Beispiel:</b> Max" data-original-title="Anmelde-Name"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="user_password">Passwort</label>
                            <div class="controls">
                                <input class="input-xlarge" id="user_password" name="user_password" type="password" rel="popover" data-content="Geben Sie hier das Passwort des Administrators an mit welchem sich dieser anmelden kann.<br/><b>Beispiel:</b> geheim" data-original-title="Anmelde-Passwort"/>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr/>
                <fieldset>
                    <legend>Sicherheit</legend>
                    <div class="span5">
                        <div class="control-group">
                            <label class="control-label" for="user_name">Sicherheitsschlüssel</label>
                            <div class="controls">
                                <input class="input-xlarge" id="security_key" name="security_key" type="text" rel="popover" data-content="Geben Sie hier einen Schlüssel an um Ihre Daten verschlüsseln zu können.<br/><b>Beispiel:</b> 12347d9e6733169e3706ff2b6f2a8894" data-original-title="Sicherheitsschlüssel" value="12347d9e6733169e3706ff2b6f2a8894"/>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                	<div style="min-height:80px;">
		                <input name="task" type="hidden" value="install"/>
		                <input class="btn btn-success btn-large button-right" type="submit" value="Fertig"/>
	                </div>
                <fieldset>
            </form>
        </div>
        <div class="navbar navbar-fixed-bottom">
            <div class="navbar-inner">
                <div class="container">
                    <font>© 2012 by Jasmin Kemmerich, Manuel Bochröder & Sebastian Brosch</font>
                </div>
            </div>
        </div>
        <div class="modal hide" id="MsgBox">
  			<div class="modal-header">
    			<button type="button" class="close" data-dismiss="modal">×</button>
    			<h3>Fehler</h3>
  			</div>
  			<div class="modal-body"></div>
  			<div class="modal-footer">
    			<a href="#" class="btn btn-success" data-dismiss="modal">OK</a>
  			</div>
		</div>
      	<script>
        $('#database_hostname').popover({trigger:'hover', placement:'top'});
        $('#database_name').popover({trigger:'hover', placement:'top'});
        $('#database_username').popover({trigger:'hover', placement:'top'});
        $('#database_password').popover({trigger:'hover', placement:'top'});
		$('#database_port').popover({trigger:'hover', placement:'top'});
		$('#database_driver').popover({trigger:'hover', placement:'top'});
		$('#database_prefix').popover({trigger:'hover', placement:'top'});
		$('#site_url').popover({trigger:'hover', placement:'top'});
		$('#absolute_path').popover({trigger:'hover', placement:'top'});
		$('#upload_path').popover({trigger:'hover', placement:'top'});
		$('#user_username').popover({trigger:'hover', placement:'top'});
		$('#user_password').popover({trigger:'hover', placement:'top'});
		$('#user_name').popover({trigger:'hover', placement:'top'});
		$('#user_email').popover({trigger:'hover', placement:'top'});
		$('#security_key').popover({trigger:'hover', placement:'top'});

		$('#install_form').submit(function(){
			if($('#database_hostname').val() == '') {
				$('.modal-body').html('Der Hostname des Datenbank-Servers wurde nicht angegeben!<br/><b>Beispiel:</b> localhost');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#database_name').val() == '') {
				$('.modal-body').html('Der Name der Datenbank wurde nicht angegeben!</br><b>Beispiel:</b> picvid');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#database_username').val() == '') {
				$('.modal-body').html('Der Name des Datenbank-Benutzers wurde nicht angegeben!</br><b>Beispiel:</b> root');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#database_password').val() == '') {
				$('.modal-body').html('Es wurde kein Passwort für den angegebenen Datenbank-Benutzer angegeben!</br><b>Beispiel:</b> geheim');
				$('#MsgBox').modal('show');
				return false;
			}
			if(($('#database_port').val() == '') || (!$('#database_port').val().match(/^[0-9]+$/))) {
				$('.modal-body').html('Es wurde kein gültiger Port angegeben um mit dem Datenbank-Server eine Verbindung herstellen zu können!</br><b>Beispiel:</b> 3306');
				$('#MsgBox').modal('show');
				return false;
			}
			if(($('#database_prefix').val() == '') || (!$('#database_prefix').val().match(/^[a-z_]+$/))) {
				$('.modal-body').html('Es wurde kein gültiges Präfix für die Tabellen angegeben!</br><b>Beispiel:</b> pic_');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#site_url').val() == '') {
				$('.modal-body').html('Es wurde keine Startseite von PicVid angegeben!</br><b>Beispiel:</b> http://www.example.de/');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#absolute_path').val() == '') {
				$('.modal-body').html('Der Speicherort auf der Festplatte / dem Webspace wurde nicht angegeben!</br><b>Beispiel:</b> C:\\xampp\\htdocs\\picvid');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#upload_path').val() == '') {
				$('.modal-body').html('Das Upload-Verzeichnis auf der Festplatte / dem Webspace wurde nicht angegeben!</br><b>Beispiel:</b> C:\\xampp\\htdocs\\picvid\\upload');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#user_name').val() == '') {
				$('.modal-body').html('Der vollständige Name des Administrators wurde nicht angegeben!</br><b>Beispiel:</b> Max Mustermann');
				$('#MsgBox').modal('show');
				return false;
			}
			if(($('#user_email').val() == '') || (!$('#user_email').val().match(/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_\-]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/))) {
				$('.modal-body').html('Es wurde keine gültige E-Mail-Adresse für den Administrator angegeben!</br><b>Beispiel:</b> max.mustermann@testmail.com');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#user_username').val() == '') {
				$('.modal-body').html('Der Anmelde-Name des Administrators wurde nicht angegeben!</br><b>Beispiel:</b> Max');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#user_password').val() == '') {
				$('.modal-body').html('Es wurde kein Passwort für den Administrator angegeben!</br><b>Beispiel:</b> geheim');
				$('#MsgBox').modal('show');
				return false;
			}
			if($('#security_key').val() == '') {
                $('.modal-body').html('Es wurde kein Sicherheitschlüssel angegeben!</br><b>Beispiel:</b> 12347d9e6733169e3706ff2b6f2a8894');
                $('#MsgBox').modal('show');
                return false;
            }
		});
        </script>
    </body>
</html>