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
if(file_exists('../configuration.php') === true) {
    header('Location: ../index.php');
    exit;
}

//Externe Dateien einbinden.
require_once('../includes/html.class.php');
require_once('../includes/core.class.php');

//Instanz des Kerns erzeugen.
$PICVID['CORE'] = new Core();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>PicVid - Voraussetzungen</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../images/favicon.png" type="image/png">
		<link rel="stylesheet" href="../includes/bootstrap/css/bootstrap.min.css" type="text/css"/>
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
                <h1><img alt="Installation" border="0" height="48" src="../images/install_48.png" width="48"/>Installation&nbsp;&nbsp;&nbsp;<small>Voraussetzungen</small></h1>
            </div>
        </div>
        <div class="container">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Empfohlen</th>
                        <th>Aktuell</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PHP-Version</td>
                        <td>5.2.0</td>
                        <td><?php echo phpversion(); ?></td>
                        <td><?php (strnatcmp(phpversion(), '5.2.0') >= 0) ? $PICVID['CORE']->formatState(1, 'image') : getStateImage(0, 'image'); ?></td>
                    </tr>
                    <tr>
                        <td>MySQL</td>
                        <td>Verfügbar</td>
                        <td><?php function_exists('mysql_connect') ? $PICVID['CORE']->formatState(1, 'text', 'Verfügbar') : $PICVID['CORE']->formatState(0, 'text', '', 'Nicht Verfügbar'); ?></td>
                        <td><?php function_exists('mysql_connect') ? $PICVID['CORE']->formatState(1, 'image') : $PICVID['CORE']->formatState(0, 'image'); ?></td>
                    </tr>
                    <tr>
                        <td>MySQLi</td>
                        <td>(Verfügbar)</td>
                        <td><?php function_exists('mysqli_connect') ? $PICVID['CORE']->formatState(1, 'text', 'Verfügbar') : $PICVID['CORE']->formatState(0, 'text', '', 'Nicht Verfügbar'); ?></td>
                        <td><?php function_exists('mysqli_connect') ? $PICVID['CORE']->formatState(1, 'image') : $PICVID['CORE']->formatState(0, 'image'); ?></td>
                    </tr>
                    <tr>
                        <td>File Uploads</td>
                        <td>Aktiviert</td>
                        <td><?php ini_get('file_uploads') ? $PICVID['CORE']->formatState(1, 'text', 'Aktiviert') : $PICVID['CORE']->formatState(0, 'text', '', 'Deaktiviert'); ?></td>
                        <td><?php ini_get('file_uploads') ? $PICVID['CORE']->formatState(1, 'image') : $PICVID['CORE']->formatState(0, 'image'); ?></td>
                    </tr>
                    <tr>
                        <td>Display Errors</td>
                        <td>Aktiviert</td>
                        <td><?php ini_get('display_errors') ? $PICVID['CORE']->formatState(1, 'text', 'Aktiviert') : $PICVID['CORE']->formatState(0, 'state', '', 'Deaktiviert'); ?></td>
                        <td><?php ini_get('display_errors') ? $PICVID['CORE']->formatState(1, 'image') : $PICVID['CORE']->formatState(0, 'image'); ?></td>
                    </tr>
                    <tr>
                        <td>Register Globals</td>
                        <td>Deaktiviert</td>
                        <td><?php ini_get('register_globals') ? $PICVID['CORE']->formatState(1, 'text', 'Aktiviert') : $PICVID['CORE']->formatState(0, 'text', '', 'Deaktiviert'); ?></td>
                        <td><?php ini_get('register_globals') ? $PICVID['CORE']->formatState(0, 'image') : $PICVID['CORE']->formatState(1, 'image'); ?></td>
                    </tr>
                    <tr>
                        <td>Save Mode</td>
                        <td>Deaktiviert</td>
                        <td><?php ini_get('save_mode') ? $PICVID['CORE']->formatState(1, 'text', 'Aktiviert') : $PICVID['CORE']->formatState(0, 'text', '', 'Deaktiviert'); ?></td>
                        <td><?php ini_get('save_mode') ? $PICVID['CORE']->formatState(0, 'image') : $PICVID['CORE']->formatState(1, 'image'); ?></td>
                    </tr>
                </tbody>
            </table>
            <a class="btn btn-success btn-large button-right" href="install.php">Konfiguration</a>
        </div>
        <div class="navbar navbar-fixed-bottom">
            <div class="navbar-inner">
                <div class="container">
                    <font>© 2012 by Jasmin Kemmerich, Manuel Bochröder & Sebastian Brosch</font>
                </div>
            </div>
        </div>
    </body>
</html>