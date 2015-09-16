<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Bilder</title>');

//Dateien fuer Fancybox einbinden.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<link rel="stylesheet" href="'.$PICVID['CORE']->getValue('site_url').'/includes/fancybox/jquery.fancybox.css">');
$PICVID['TEMPLATE_ENGINE']->setInMarker('FOOTER', '<script src="'.$PICVID['CORE']->getValue('site_url').'/includes/fancybox/jquery.fancybox.js"></script>');

//Dateien fuer die Buttons einbinden.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<link rel="stylesheet" href="'.$PICVID['CORE']->getValue('site_url').'/includes/fancybox/helpers/jquery.fancybox-buttons.css">');
$PICVID['TEMPLATE_ENGINE']->setInMarker('FOOTER', '<script src="'.$PICVID['CORE']->getValue('site_url').'/includes/fancybox/helpers/jquery.fancybox-buttons.js"></script>');

//Pruefen ob eine Datei heruntergeladen werden soll.
if($PICVID['CORE']->getParameter($_REQUEST, 'task', '') === 'download') {

    //Pruefen ob ein Benutzer angemeldet ist.
    if($PICVID['ACT_USER']->_group > 0) {

        //Instanz eines Mediums erzeugen.
        $Media = new Media($PICVID['DATABASE']);

        //Medium herunterladen.
        $Media->download('image', $PICVID['CORE']->getParameter($_REQUEST, 'id', 0));
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=dashboard', 'Sie müssen angemeldet sein um Bilder herunterladen zu können.', 'error');
        exit;
    }
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.fancybox-buttons').fancybox({
            openEffect : 'elastic',
            closeEffect : 'elastic',
            prevEffect : 'fade',
            nextEffect : 'fade',
            closeBtn : false,

            helpers : {
                title : {
                    type : 'over'
                },
                buttons : {}
            }
		});
    });
</script>
<h3>Slideshow</h3>
<hr/>
<div id="section-slideshow">
    <?php
    //Alle Bilder ermitteln.
    $images = $PICVID['MEDIA']->getFromDatabase('image', 0, array('`create_time`', '`create_user`', '`description`', '`id`', '`name`', '`title`', '`type`'), "`type` <> 'image/tiff' ORDER BY `id` DESC");

    //Faktor des Maximal-Verhaeltnisses setzen.
    $max_factor = 4;

    //Pruefen ob Bilder vorhanden sind.
    if((is_array($images) === true) && (count($images) > 0)) {

        //Durchlaufen aller Bilder.
        foreach($images as $image) {

            //Elemente der Beschreibung setzen.
            $title = (trim($image->title) !== '') ? $image->title : '';
            $user = ($image->create_user > 0) ? "<a href='index.php?section=profile&id=".(int) $image->create_user."'>".$PICVID['USER']->getUsernameFromID($image->create_user)."</a>&nbsp;(".$image->create_time.")" : $image->create_time;

            //Pruefen ob ein Titel vorhanden ist.
            if(trim($title) === '') {
                $title = $user;
            } else {
                $title = $user."&nbsp;-&nbsp;".$title;
            }

            //Bildbeschreibung setzen.
            $description = (trim($image->description) !== '') ? $title."<br/><br/>".$image->description : $title;

            //Download-Link erzeugen.
            $download = "<br/><br/><a href='index.php?section=slideshow&task=download&id=".(int) $image->id."'>Download</a> | <a href='index.php?section=detailed&location=image&id=".(int) $image->id."'>Details</a>";
            $description .= $download;

            //Pruefen ob ein JPEG-Bild angezeigt werden soll.
            if(($image->type === 'image/jpeg') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.jpeg') === true)) {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.jpeg', 110);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.jpeg" title="<?php echo $description; ?>"><img class="slideshow-image" height="<?php echo $size[0]; ?>" src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.jpeg" title="<?php echo $image->name; ?>" width="<?php echo $size[1]; ?>" alt="<?php echo $image->name; ?>"/></a>
                <?php
                }
            } elseif(($image->type === 'image/png') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.png') === true)) {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.png', 110);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.png" title="<?php echo $description; ?>"><img class="slideshow-image" height="<?php echo $size[0]; ?>" src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.png" title="<?php echo $image->name; ?>" width="<?php echo $size[1]; ?>" alt="<?php echo $image->name; ?>"/></a>
                <?php
                }
            } elseif(($image->type === 'image/tiff') && (file_exists($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.tif') === true)) {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($PICVID['CORE']->getValue('absolute_path').'/gallery/images/'.$image->id.'.tif', 110);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.tif" title="<?php echo $description; ?>"><img class="slideshow-image" height="<?php echo $size[0]; ?>" src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/gallery/images/<?php echo $image->id; ?>.tif" title="<?php echo $image->name; ?>" width="<?php echo $size[1]; ?>" alt="<?php echo $image->name; ?>"/></a>
                <?php
                }
            } elseif($image->type === 'url') {

                //Werte fuer die Groesse ermitteln.
                $size = $PICVID['MEDIA']->getImageSize($image->name, 110);

                //Pruefen ob ein sehr langes Bild angezeigt werden soll.
                if((($size[1] > $size[0] * $max_factor) || ($size[0] > $size[1] * $max_factor)) === false) {
                ?>
                <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $image->name; ?>" title="<?php echo $description; ?>"><img class="slideshow-image" height="<?php echo $size[0]; ?>" src="<?php echo $image->name; ?>" width="<?php echo $size[1]; ?>" title="<?php echo $image->name; ?>" alt="<?php echo $image->name; ?>"/></a>
                <?php
                }
            }
        }
    }
    ?>
</div>