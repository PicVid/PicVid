<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Videos</title>');

//Dateien fuer Video-JS einbinden.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<link rel="stylesheet" href="'.$PICVID['CORE']->getValue('site_url').'/includes/video-js/video-js.min.css">');
$PICVID['TEMPLATE_ENGINE']->setInMarker('FOOTER', '<script src="'.$PICVID['CORE']->getValue('site_url').'/includes/video-js/video.min.js"></script>');

//Pruefen ob eine Datei heruntergeladen werden soll.
if($PICVID['CORE']->getParameter($_REQUEST, 'task', '') === 'download') {

    //Pruefen ob ein Benutzer angemeledet ist.
    if($PICVID['ACT_USER']->_group > 0) {

        //Instanz eines Mediums erzeugen.
        $Media = new Media($PICVID['DATABASE']);

        //Medium herunterladen.
        $Media->download('video', $PICVID['CORE']->getParameter($_REQUEST, 'id', 0));
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=dashboard', 'Sie müssen angemeldet sein um Videos herunterladen zu können.', 'error');
        exit;
    }
}
?>
<h3>Videos</h3>
<hr/>
<div id="ext-slideshow">
    <?php
    //Letzten 5 neusten Videos ermitteln.
    $videos = $PICVID['MEDIA']->getFromDatabase('video', 0, array('`id`', '`type`'), "0 = 0 ORDER BY `id` DESC");

    //Pruefen ob Videos vorhanden sind.
    if((is_array($videos) === true) && (count($videos) > 0)) {

        //Durchlaufen aller Videos.
        foreach($videos as $video) {
            ?>
           	<video id="videoplayer" class="span6 video-js vjs-default-skin" controls width="500" data-setup="{}">
            <?php
            //Pruefen ob ein MP4-Video angezeigt werden soll.
            if(($video->type === 'video/mp4') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$video->id.'.mp4') === true)) {
            ?>
            <source src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/videos/<?php echo $video->id; ?>.mp4" type="video/mp4" />
            <?php
            } elseif(($video->type === 'video/webm') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$video->id.'.webm') === true)) {
            ?>
            <source src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/videos/<?php echo $video->id; ?>.webm" type="video/webm" />
            <?php
            } elseif(($video->type === 'video/ogg') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/videos/'.$video->id.'.ogg') === true)) {
            ?>
            <source src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/videos/<?php echo $video->id; ?>.ogg" type="video/ogg" />
            <?php
            }
            ?>
           	</video>
    <?php
        }
    }
    ?>
</div>