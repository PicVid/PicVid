<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Einzelansicht</title>');

//URL-Parameter ermitteln.
$location = $PICVID['CORE']->getParameter($_REQUEST, 'location', '');
$id = $PICVID['CORE']->getParameter($_REQUEST, 'id', 0);

//Pruefen ob eine ID vorhanden ist.
if($id < 1) {

    //SQL-Befehl setzen.
    $PICVID['DATABASE']->setQuery("SELECT MIN(`id`) FROM `#__".trim($location)."`");
    $id = $PICVID['DATABASE']->getResult();

    //Pruefen ob immer noch keine ID vorhanden ist.
    if($id < 1) {

        //Weiterleiten auf den Upload.
        $PICVID['CORE']->redirect('index.php?section=upload', 'Keine Elemente mehr verfügbar! Laden Sie neue Medien hoch.', 'error');
        exit;
    }
}

//Pruefen welcher Befehl ausgefuehrt werden soll.
if($PICVID['CORE']->getParameter($_REQUEST, 'delete', 0) == 1) {

    //Element abrufen fuer Zusatzinformationen.
    $mediaElement = $PICVID['MEDIA']->getFromDatabase($location, $id);

    //Loeschen des Bildes aus der Datenbank.
    if($PICVID['MEDIA']->delete($location, $id) === true) {

        //Typ pruefen.
        switch($mediaElement[0]->type) {
            case 'image/jpeg':
                unlink($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$id.'.jpeg');
        		break;
        	case 'image/png':
        		unlink($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$id.'.png');
        		break;
        	case 'image/tiff':
        		unlink($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$id.'.tif');
        		break;
            case 'video/mp4':
                unlink($PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$id.'.mp4');
                break;
            case 'video/webm':
                unlink($PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$id.'.webm');
                break;
            case 'video/ogg':
                unlink($PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$id.'.ogg');
                break;
    	}

        //Element zuvor ermitteln.
        $PICVID['DATABASE']->setQuery("SELECT `id` FROM `#__".trim($location)."` WHERE `id` < ".(int) $id." OR `id` > ".(int) $id." ORDER BY `id` DESC");

        //ID ermitteln.
        $id = $PICVID['DATABASE']->getResult();

        //Pruefen ob noch ein Element vorhanden ist.
        if($id > 0) {

            //Weiterleiten mit Statusmeldung.
            $PICVID['CORE']->redirect('index.php?section=detailed&location='.trim($location).'&id='.$id, 'Element wurde erfolgreich gelöscht.', 'success');
            exit;
        } else {

            //Weiterleiten mit Fehlermeldung.
            $PICVID['CORE']->redirect('index.php?section=upload', 'Keine Elemente mehr verfügbar! Laden Sie neue Medien hoch.', 'error');
            exit;
        }
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=detailed&location='.trim($location).'&id='.$id, 'Element konnte nicht gelöscht werden.', 'error');
        exit;
    }
}

//Pruefen ob eine Location angegeben wurde.
if((($location === 'image') || ($location === 'video')) && ($id > 0)) {

    //Instanz eines Mediums erzeugen.
    $Media = new Media($PICVID['DATABASE']);

    //Medium aus der Datenbank laden.
    $Media->loadFromDatabase($location, $id);

    //Pruefen ob das Bild gefunden wurde.
    if($Media->_id > 0) {

        //Pruefen ob ein Bild angezeigt werden soll.
        switch($Media->_type){
            case 'image/jpeg':
                $medialink = $PICVID['CORE']->getValue('site_url').'/gallery/images/'.$Media->_id.'.jpeg';
                $checklink = $PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$Media->_id.'.jpeg';
                break;
            case 'image/png':
                $medialink = $PICVID['CORE']->getValue('site_url').'/gallery/images/'.$Media->_id.'.png';
                $checklink = $PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$Media->_id.'.png';
                break;
            case 'image/tiff':
                $medialink = $PICVID['CORE']->getValue('site_url').'/gallery/images/'.$Media->_id.'.tif';
                $checklink = $PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$Media->_id.'.tif';
                break;
            case 'video/mp4':
                $medialink = $PICVID['CORE']->getValue('site_url').'/gallery/videos/'.$Media->_id.'.mp4';
                $checklink = $PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$Media->_id.'.mp4';
                break;
            case 'video/webm':
                $medialink = $PICVID['CORE']->getValue('site_url').'/gallery/videos/'.$Media->_id.'.webm';
                $checklink = $PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$Media->_id.'.webm';
                break;
            case 'video/ogg':
                $medialink = $PICVID['CORE']->getValue('site_url').'/gallery/videos/'.$Media->_id.'.ogg';
                $checklink = $PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$Media->_id.'.ogg';
                break;
            case 'url':
                $medialink = $Media->_name;
                $checklink = $Media->_name;
                break;
        }

        //Link fuer Shares setzen.
        $link = $PICVID['CORE']->getValue('site_url').'/index.php?section=detailed&location='.$location.'&id='.$Media->_id;

        //Medienname setzen.
        $medianame = ($location === 'video') ? 'Video' : 'Bild';

        //Zeiten als Sekunden ermitteln.
        $now = strtotime(date('Y-m-d H:i:s'));
        $timestamp = strtotime($Media->_create_time);

        //Pruefen ob das Bild neu ist.
        if($now > ($now - 604800)) {
            $label = '<span class="label label-info" style="margin-top:8px;float:right;">Neu!</span>';
        } else {
            $label = '';
        }

        //Pruefen ob die Datei existiert.
        if((file_exists($checklink) === true) || (($Media->_type === 'url') && (file_get_contents($medialink) !== false))) {
        ?>
        <h3><?php echo ($Media->_title !== '') ? $Media->_title : 'Kein Titel'; ?><?php echo $label; ?></h3>
        <hr/>
        <div class="row" align="center">
        <?php
        //Pruefen ob ein Administrator angemeldet ist.
        if((isset($PICVID['ACT_USER']) === true) && ($PICVID['ACT_USER']->_group == 3)) {

            //SQL-Befehl setzen (Pruefen ob ein Element davor verfuegbar ist).
            $PICVID['DATABASE']->setQuery("SELECT `id` FROM `#__".trim($location)."` WHERE `id` < '".(int) $id."' ORDER BY `id` DESC");

            //ID des vorherigen Bildes ermitteln.
            $previousPictureID = $PICVID['DATABASE']->getResult();

            //Pruefen ob ein Bild zuvor verfuegbar ist.
            if($previousPictureID > 0) {

                //Eigenschaften fuer den Button setzen.
                $pre_link = 'index.php?section=detailed&location='.$location.'&id='.$previousPictureID;
                $pre_buttonoptions ='btn-primary';
            } else {

                //Eigenschaften fuer den Button zuruecksetzen.
                $pre_link = '';
                $pre_buttonoptions ='disabled';
            }

            //SQL-Befehl setzen (Pruefen ob ein Element danach verfuegbar ist).
            $PICVID['DATABASE']->setQuery("SELECT `id` FROM `#__".trim($location)."` WHERE `id` > '".(int) $id."' ORDER BY `id` ASC");
            //ID des vorherigen Bildes ermitteln.
            $nextPictureID = $PICVID['DATABASE']->getResult();

            //Pruefen ob ein Bild danach verfuegbar ist.
            if($nextPictureID > 0) {

                //Eigenschaften fuer den Button setzen.
                $nex_link = 'index.php?section=detailed&location='.$location.'&id='.$nextPictureID;
                $nex_buttonoptions ='btn-primary';
            } else {

                //Eigenschaften fuer den Button zuruecksetzen.
                $nex_link = '';
                $nex_buttonoptions ='disabled';
            }
        ?>
        <div class="modal hide" id="warning">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3><font style="color:#f00;">Achtung!</font></h3>
            </div>
            <div class="modal-body">
               <p>Möchten Sie das Element wirklich löschen?</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
                <a id="delete-link" href="#" class="btn btn-danger">Löschen</a>
            </div>
        </div>
        <div class="span2" style="float:left;">
            <a style="float:left;" href="<?php echo $pre_link; ?>" class="btn <?php echo $pre_buttonoptions; ?>">Vorheriges <?php echo $medianame; ?></a>
        </div>
        <div class="span8">
            <a href="#" data-toggle="modal" data-target="#warning" onclick="document.getElementById('delete-link').href='index.php?section=detailed&id=<?php echo $Media->_id; ?>&location=<?php echo $location; ?>&delete=1'" class="btn btn-danger"><?php echo $medianame; ?> löschen</a>
        </div>
        <div class="span2" style="float:right;">
            <a class="btn <?php echo $nex_buttonoptions; ?>" style="float:right;" href="<?php echo $nex_link; ?>">Nächstes <?php echo $medianame; ?></a>
        </div>
        <div class="clearfix"></div>
        <?php } ?>
        <div class="span12" style="margin-top:20px;">
            <?php if($location === 'image') { ?>
            <a href="<?php echo $medialink; ?>" style="margin-top:20px;" target="_blank">
                <img style="box-shadow: 0px 0px 6px #7A7A7A; max-height: 800px; max-width=90%;" src="<?php echo $medialink; ?>"/>
            </a>
            <?php } elseif($location === 'video') { ?>
                <video style="box-shadow: 0px 0px 6px #7A7A7A; max-height: 800px; max-width=90%;" id="videoplayer" controls data-setup="{}">
                    <source src="<?php echo $medialink; ?>" type="<?php echo $Media->_type; ?>" />
                </video>
            <?php } ?>
        </div>

        <div class="span12" style="margin-top:30px;">
            <h3><?php echo $medianame; ?>informationen</h3>
            <hr/>
            <table id="upload-information" class="table table-striped table-bordered">
                <tbody>
                    <tr>
                        <td width="150px;">Titel</td>
                        <td><?php echo ($Media->_title !== '') ? $Media->_title : 'Kein Titel'; ?></td>
                    </tr>
                    <tr>
                        <td>Beschreibung</td>
                        <td><?php echo ($Media->_description !== '') ? $Media->_description : 'Keine Beschreibung'; ?></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><?php echo ($Media->_name !== '') ? $Media->_name : 'Kein Name'; ?></td>
                    </tr>
                    <tr>
                        <td>Hochgeladen am</td>
                        <td><?php echo $Media->_create_time; ?></td>
                    </tr>
                    <tr>
                        <td>Hochgeladen von</td>
                        <td>
                            <a href="index.php?section=profile&id=<?php echo $Media->_create_user; ?>">
                                <?php echo ($Media->_create_user > 0) ? $PICVID['USER']->getUsernameFromID($Media->_create_user) : 'Anonymer Benutzer'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php if($Media->_type !== 'url') {

                        //Informationen zur Datei ermitteln.
                        $fileinfo = stat($checklink);
                    ?>
                    <tr>
                        <td>Dateigröße</td>
                        <td><?php echo $fileinfo[7]; ?> Bytes</td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>Downloadlink</td>
                        <td><a class="btn btn-primary" href="index.php?section=<?php echo ($location === 'video') ? 'videoshow' : 'slideshow'; ?>&task=download&id=<?php echo $Media->_id; ?>">Download</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php if($location === 'image') { ?>
        <div class="span12" style="margin-top: 20px;">
            <h3>Links um dieses Bild mit anderen zu teilen.</h3>
            <hr/>
            <table id="upload-information" class="table table-striped table-bordered">
                <tr>
                    <td width="150px;"><a href="<?php echo $link; ?>">Link</a></td>
                    <td><input class="input-xlarge" onclick="this.select();" type="text" value="<?php echo $link; ?>"/></td>
                </tr>
                <tr>
                    <td><a href="<?php echo $medialink; ?>">Direkt-Link</a></td>
                    <td><input class="input-xlarge" onclick="this.select();" type="text" value="<?php echo $medialink; ?>"/></td>
                </tr>
                <tr>
                    <td>Forum-Code</td>
                    <td><input class="input-xlarge" onclick="this.select();" type="text" value="[URL=<?php echo $link; ?>][IMG]<?php echo $medialink; ?>[/IMG]"/></td>
                </tr>
                <tr>
                    <td>ALT-Forum-Code</td>
                    <td><input class="input-xlarge" onclick="this.select();" type="text" value="[URL=<?php echo $link; ?>][IMG=<?php echo $medialink; ?>][/IMG][/URL]"/></td>
                </tr>
               <tr>
                    <td>HTML-Code</td>
                    <td><input class="input-xlarge" onclick="this.select();" type="text" value='<a target="_blank" href="<?php echo $medialink; ?>"><img src="<?php echo $link; ?>" border="0"/></a>'/></td>
                </tr>
            </table>
        </div>
        <?php
            }
        }
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=detailed&location='.$location.'&error=404');
        exit;
    }
}