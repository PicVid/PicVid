<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Startseite</title>');
?>
<div id="welcome" class="hero-unit">
    <h1>Willkommen</h1>
    <p>Sie suchen eine Plattform um Bilder und Videos schnell zu veröffentlichen?<br/>Dann sind Sie hier richtig!</p>
    <p><a class="btn btn-primary btn-large" href="index.php?section=upload">Los geht's »</a></p>
</div>
<h3>Neuste Bilder</h3>
<hr/>
<div class="container" style="overflow:hidden;height:180px;display:block;">
    <?php
    //Letzten 7 neusten Bilder ermitteln.
    $images = $PICVID['MEDIA']->getFromDatabase('image', 0, array('`id`', '`name`', '`type`'), "`type` <> 'image/tiff' ORDER BY `id` DESC LIMIT 10");

    //Faktor des Maximal-Verhaeltnisses setzen.
    $max_factor = 4;

    //Pruefen ob Bilder vorhanden sind.
    if((is_array($images) === true) && (count($images) > 0)) {

        //Abrufen der Section fuer die Slideshow.
        $sections = $PICVID['SECTION']->getFromDatabase(0, array('`name`'), "`name` = 'slideshow' AND `state` = 1");

        //Durchlaufen aller Bilder.
        foreach($images as $image) {

            //Pruefen ob die Slideshow vorhanden ist.
            if((is_array($sections) === true) && (count($sections) > 0)) {
            ?>
            <a href="index.php?section=slideshow">
            <?php
            }

            //Pruefen ob ein JPEG-Bild angezeigt werden soll.
            if(($image->type === 'image/jpeg') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.jpeg') === true)) {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.jpeg', 180);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <img height="<?php echo $size[0]; ?>" src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.jpeg" width="<?php echo $size[1]; ?>"/>
                <?php
                }
            } elseif(($image->type === 'image/png') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.png') === true)) {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.png', 180);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <img height="<?php echo $size[0]; ?>" src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.png" width="<?php echo $size[1]; ?>"/>
                <?php
                }
            } elseif(($image->type === 'image/tiff') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.tif') === true)) {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.tif', 180);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <img height="<?php echo $size[0]; ?>" src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.tif" width="<?php echo $size[1]; ?>"/>
                <?php
                }
            } elseif($image->type === 'url') {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($image->name, 180);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <img height="<?php echo $size[0]; ?>" src="<?php echo $image->name; ?>" width="<?php echo $size[1]; ?>"/>
                <?php
                }
            }

            //Pruefen ob die Slideshow vorhanden ist.
            if((is_array($sections) === true) && (count($sections) > 0)) {
            ?>
            </a>
            <?php
            }
        }
    }
    ?>
</div>
<h3 style="margin-top:14px;">Neuste Videos</h3>
<hr/>
<div class="container" style="overflow:hidden;height:180px;display:block;">
    <?php
    //Letzten 5 neusten Videos ermitteln.
    $videos = $PICVID['MEDIA']->getFromDatabase('video', 0, array('`id`', '`name`', '`type`'), "0 = 0 ORDER BY `id` DESC LIMIT 5");

    //Pruefen ob Videos vorhanden sind.
    if((is_array($videos) === true) && (count($videos) > 0)) {

        //Abrufen der Section fuer die Slideshow.
        $sections = $PICVID['SECTION']->getFromDatabase(0, array('`name`'), "`name` = 'videoshow' AND `state` = 1");

        //Durchlaufen aller Videos.
        foreach($videos as $video) {

            //Pruefen ob die Videoshow vorhanden ist.
            if((is_array($sections) === true) && (count($sections) > 0)) {
            ?>
            <a href="index.php?section=videoshow">
            <?php
            }
            ?>
            <video height="180">
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
            //Pruefen ob die Videoshow vorhanden ist.
            if((is_array($sections) === true) && (count($sections) > 0)) {
            ?>
            </a>
            <?php
            }
        }
    }
    ?>
</div>