<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Upload</title>');

//Pruefen ob eine Datei hochgeladen werden soll.
if($PICVID['CORE']->getParameter($_POST, 'task', '') === 'upload') {

    //Maximalfaktor setzen.
    $max_factor = 4;

    //Instanz eines Mediums erzeugen.
    $Media = new Media($PICVID['DATABASE']);

    //Laden des Mediums aus der Datenbank.
    $Media->loadFromArray($_POST);

    //Array mit allen Unterstützen Formaten erzeugen.
    $files = array();
    $files['images'] = array('image/png', 'image/jpeg', 'image/tiff');
    $files['videos'] = array('video/mp4', 'video/webm', 'video/ogg');

    //Pruefen ob eine Datei verfuegbar ist.
    if((isset($_FILES['file']['type']) === true) && (isset($_FILES['file']['name']) === true) && (isset($_FILES['file']['size']) === true) && (isset($_FILES['file']['tmp_name']) === true) && (trim($_FILES['file']['tmp_name']) !== '')) {

        //Dateiformat ueberpruefen (Bilder).
        if((in_array($_FILES['file']['type'], $files['images']) === true) && ($_FILES['file']['size'] < 5242880) && ($_FILES['file']['size'] > 0)) {

            //Groesse der Datei ermitteln.
            $size = getimagesize($_FILES['file']['tmp_name']);

            //Pruefen ob die Datei ein gueltiges Verhaeltniss besitzt.
            if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === true) {
                $PICVID['CORE']->redirect('index.php?section=upload', 'Das Bild besitzt kein zulässiges Seitenverhältnis.', 'error');
                exit;
            }

            //Erstellen der Datei in der Datenbank.
            if($Media->create('image', $_FILES['file']['name'], $_FILES['file']['type']) === false) {

                //Weiterleiten mit Fehlermeldung.
                $PICVID['CORE']->redirect('index.php?section=upload', 'Das Bild konnte nicht hochgeladen werden. Versuchen Sie es später nocheinmal.', 'error');
                exit;
            } else {

                //Status zuruecksetzen.
                $upload_state = false;

                //InsertID ermitteln.
                $insertID = $Media->getID();

                //Pruefen welche Erweiterung verwendet werden soll.
                switch($_FILES['file']['type']) {
                    case 'image/jpeg':
                        $upload_state = move_uploaded_file($_FILES['file']['tmp_name'], $PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$insertID.'.jpeg');
                        break;
                    case 'image/png':
                        $upload_state = move_uploaded_file($_FILES['file']['tmp_name'], $PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$insertID.'.png');
                        break;
                    case 'image/tiff':
                        $upload_state = move_uploaded_file($_FILES['file']['tmp_name'], $PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$insertID.'.tif');
                        break;
                    default:
                        $upload_state = false;
                        break;
                }

                //Zuruecksetzen der Datei.
                unset($_FILES);

                //Pruefen ob die Datei hochgeladen werden konnte.
                if($upload_state === true) {

                    //Weiterleiten mit Statusmeldung.
                    $_SESSION['media_id'] = $insertID;
                    $_SESSION['media_location'] = 'image';
                    $PICVID['CORE']->redirect('index.php?section=upload', 'Das Bild wurde erfolgreich hochgeladen.', 'success');
                    exit;
                }

                //Loeschen des Datenbankeintrags.
                $Media->delete('image', $insertID);

                //Weiterleiten mit Fehlermeldung.
                $PICVID['CORE']->redirect('index.php?section=upload', 'Das Bild konnte nicht hochgeladen werden.', 'error');
                exit;
            }
        }

        //Dateiformat und Groesse ueberpruefen (Videos).
        if((in_array($_FILES['file']['type'], $files['videos']) === true) && ($_FILES['file']['size'] < 52428800) && ($_FILES['file']['size'] > 0)) {

            //Erstellen der Datei in der Datenbank.
            if($Media->create('video', $_FILES['file']['name'], $_FILES['file']['type']) === false) {

                //Weiterleiten mit Fehlermeldung.
                $PICVID['CORE']->redirect('index.php?section=upload', 'Das Video konnte nicht hochgeladen werden. Versuchen Sie es später nocheinmal.', 'error');
                exit;
            } else {

                //Status zuruecksetzen.
                $upload_state = false;

                //InsertID ermitteln.
                $insertID = $Media->getID();

                //Pruefen welche Erweiterung verwendet werden soll.
                switch($_FILES['file']['type']) {
                    case 'video/mp4':
                        $upload_state = move_uploaded_file($_FILES['file']['tmp_name'], $PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$insertID.'.mp4');
                        break;
                    case 'video/webm':
                        $upload_state = move_uploaded_file($_FILES['file']['tmp_name'], $PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$insertID.'.webm');
                        break;
                    case 'video/ogg':
                        $upload_state = move_uploaded_file($_FILES['file']['tmp_name'], $PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$insertID.'.ogg');
                        break;
                    default:
                        $upload_state = false;
                        break;
                }

                //Zuruecksetzen der Datei.
                unset($_FILES);

                //Pruefen ob die Datei hochgeladen werden konnte.
                if($upload_state === true) {

                    //Weiterleiten mit Statusmeldung.
                    $_SESSION['media_id'] = $insertID;
                    $_SESSION['media_location'] = 'video';
                    $PICVID['CORE']->redirect('index.php?section=upload', 'Das Video wurde erfolgreich hochgeladen.', 'success');
                    exit;
                }

                //Loeschen des Datenbankeintrags.
                $Media->delete('video', $insertID);

                //Weiterleiten mit Fehlermeldung.
                $PICVID['CORE']->redirect('index.php?section=upload', 'Das Video konnte nicht hochgeladen werden.', 'error');
                exit;
            }
        } else {

            //Zuruecksetzen der Datei.
            unset($_FILES);

            //Weiterleiten mit Fehlermeldung.
            $PICVID['CORE']->redirect('index.php?section=upload', 'Datei konnte nicht hochgeladen werden. Überprüfen Sie die Voraussetzungen.', 'error');
            exit;
        }
    } else {

        //Pruefen ob die Datei zu groß fuer den Server ist.
        if($_FILES['file']['error'] == 1) {

            //Weiterleiten mit Fehlermeldung.
            $PICVID['CORE']->redirect('index.php?section=upload', 'Diese Datei ist aktuell zu groß für den Server. Versuchen Sie es später nocheinmal.', 'error');
            exit;
        }

        //URL ermitteln.
        $url = trim($PICVID['CORE']->getParameter($_POST, 'url', ''));

        //Pruefen welche Endung die Datei besitzt.
        if((preg_match('/.jpg$/', $url) != 0) || (preg_match('/.jpeg$/', $url) != 0) || (preg_match('/.png$/', $url) != 0) || (preg_match('/.tif$/', $url) != 0)) {

            //Groesse der Datei ermitteln.
            $size = getimagesize($url);

            //Pruefen ob die Datei ein gueltiges Verhaeltniss besitzt.
            if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === true) {
                $PICVID['CORE']->redirect('index.php?section=upload', 'Das Bild besitzt kein zulässiges Seitenverhältnis.', 'error');
                exit;
            }

            //Erstellen der Medien.
            if($Media->create('image', $url, 'url')) {

                //InsertID ermitteln.
                $insertID = $Media->getID();

                //Weiterleiten mit Statusmeldung.
                $_SESSION['media_id'] = $insertID;
                $_SESSION['media_location'] = 'image';
                $PICVID['CORE']->redirect('index.php?section=upload', 'Das Bild wurde erfolgreich verknüpft.', 'success');
                exit;
            }
        } else {

            //Weiterleiten mit Fehlermeldung.
            $PICVID['CORE']->redirect('index.php?section=upload', 'Die Datei konnte nicht hochgeladen oder verknüpft werden.', 'error');
            exit;
        }

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=upload', 'Fehler beim hochladen der Datei oder erstellen der Verknüpfung. Versuchen Sie es später nocheinmal.', 'error');
        exit;
    }
}
?>
<h3>Dateien hochladen</h3>
<hr/>
<div class="span5">
    Über dieses Formular haben Sie die Möglichkeit Bilder und Videos hochzuladen. Beachten Sie jedoch dass nur Bilder und Videos mit den in der
    Tabelle aufgeführten Voraussetzungen erfolgreich hochgeladen werden können. Ziehen Sie das Bild einfach in den markierten Bereich und klicken Sie
    auf Hochladen.<br/><br/>
    Sie haben auch die Möglichkeit eine URL zu einem Bild anzugeben welches ebenfalls in PicVid verknüpft wird.
</div>
<div class="span6">
    <table class="table table-striped table-bordered">
        <tr>
            <th></th>
            <th>Unterstützte Formate</th>
            <th>Maximale Größe</th>
        </tr>
        <tr>
            <td><i class="icon-picture"></i>&nbsp;Bild</td>
            <td>image/jpeg, image/png, image/tiff</td>
            <td>5 MB</td>
        </tr>
        <tr>
            <td><i class="icon-film"></i>&nbsp;Video</td>
            <td>video/mp4, video/webm, video/ogg</td>
            <td>50 MB</td>
        </tr>
        <tr>
            <td><i class="icon-picture"></i>&nbsp;Online-Bild</td>
            <td>image/jpeg, image/png, image/tiff</td>
            <td style="font-size:2em;">∞</td>
        </tr>
    </table>
</div>
<div class="clearfix"></div>
<hr/>
<form action="index.php?section=upload" class="span11" enctype="multipart/form-data" method="post">
    <input class="input-xlarge" id="media_title" name="media_title" placeholder="Titel (Optional)" type="text" value=""/>
    <input class="input-xlarge" id="media_name" name="media_name" placeholder="Neuer Dateiname (Optional)" type="text" value=""/>
    <textarea style="width:100%;" rows="6" id="media_description" name="media_description" placeholder="Beschreibung (Optional)"></textarea>
    <input class="upload-area" name="file" type="file">
    <span class="label label-inverse label-format">oder</span>
    <input style="width:100%;" class="input-xlarge span11" id="url" name="url" placeholder="URL zum Online-Bild" type="text" value="">
    <button class="btn btn-primary" id="task" name="task" value="upload">Hochladen</button>
</form>
<?php
//Ermitteln der Medium-Informationen.
$file_id = $PICVID['CORE']->getParameter($_SESSION, 'media_id', 0);
$file_location = $PICVID['CORE']->getParameter($_SESSION, 'media_location', '');

//Pruefen ob das Bild erflgreich hochgeladen wurde.
if(($file_id > 0) && ($file_location === 'image' || $file_location === 'video')) {

    //Zuruecksetzen der Bildinformationen.
    unset($_SESSION['media_id'], $_SESSION['media_location']);

    //Instanz eines Mediums erzeugen.
    $Media = new Media($PICVID['DATABASE']);

    //Laden des Mediums aus der Datenbank.
    $Media->loadFromDatabase($file_location, $file_id);

    //Pfad zuruecksetzen.
    $path = '';

    //Typ des Bildes pruefen und Pfad setzen.
    if($Media->_type === 'image/png') {
        $path = $PICVID['CORE']->getValue('site_url').'/gallery/images/'.$Media->_id.'.png';
    } elseif($Media->_type === 'image/jpeg') {
        $path = $PICVID['CORE']->getValue('site_url').'/gallery/images/'.$Media->_id.'.jpeg';
    } elseif($Media->_type === 'image/tiff') {
        $path = $PICVID['CORE']->getValue('site_url').'/gallery/images/'.$Media->_id.'.tif';
    } elseif($Media->_type === 'url') {
        $path = $Media->_name;
    }

    //Pruefen ob ein Pfad vorhanden ist.
    if($path !== '' && $file_location === 'image') {

        //Bildinformationen ermitteln.
        $size = getimagesize($path);
    }
    ?>
    <div class="clearfix"></div>
    <hr/>
    <div class="span11">
    	<?php if($file_location === 'image') { ?>
    		Hier finden Sie die Informationen des aktuellen Bilds sowie verschiedene Codes um das Bild in Foren oder auf einer Website einzubinden.
    	<?php } else { ?>
    		Hier finden Sie die Informationen des aktuellen Videos.
    	<?php } ?>
    </div>
    <div class="span11">
        <table id="upload-information" class="table table-striped table-bordered">
            <?php if($file_location === 'image') { ?>
            <tr>
                <th width="180">Information</th>
                <th>Wert</th>
            </tr>
            <tr>
                <td>Breite</td>
                <td><?php echo $size[0]; ?>px</td>
            </tr>
            <tr>
                <td>Höhe</td>
                <td><?php echo $size[1]; ?>px</td>
            </tr>
            <tr>
                <td>URL</td>
                <td><input class="input-xlarge" type="text" value="<?php echo $path; ?>"/></td>
            </tr>
            <tr>
                <td>HTML-Code (mit Hyperlink)</td>
                <td><input class="input-xlarge" type="text" value="<?php echo htmlentities('<a target="_blank" title="'.$Media->_title.'" href="'.$path.'"><img src="'.$path.'" border="0"/></a>'); ?>"/></td>
            </tr>
            <tr>
                <td>HTML-Code (ohne Hyperlink)</td>
                <td><input class="input-xlarge" type="text" value="<?php echo htmlentities('<img height="'.$size[1].'" src="'.$path.'" width="'.$size[0].'"/>'); ?>"/></td>
            </tr>
            <tr>
                <td>Forum-Code</td>
                <td><input class="input-xlarge" type="text" value="[URL=<?php echo $path; ?>/][IMG]<?php echo $path; ?>[/IMG][/URL]"/></td>
            </tr>
            <tr>
                <td>Alt-Forum-Code</td>
                <td><input class="input-xlarge" type="text" value="[URL=<?php echo $path; ?>/][IMG=<?php echo $path; ?>][/IMG][/URL]"/></td>
            </tr>
            <tr>
                <td>Download</td>
                <td><input class="input-xlarge" type="text" value="<?php echo $PICVID['CORE']->getValue('site_url').'/index.php?section=slideshow&task=download&id='.$file_id; ?>"/></td>
            </tr>
            <?php } elseif($file_location === 'video') { ?>
            <tr>
                <td>Download</td>
                <td><input class="input-xlarge" type="text" value="<?php echo $PICVID['CORE']->getValue('site_url').'/index.php?section=videoshow&task=download&id='.$file_id; ?>"/></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php
}
?>