<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - News Übersicht</title>');

/**
 * Funktion um eine Adminleiste einblenden zu koennen.
 * @param int $id Die ID des Artikels.
 * @since 1.0.0
 */
function adminFunctions($id){

    //Globale Variable von PicVid einbinden.
	global $PICVID;

    //Pruefen ob die Adminleiste angezeigt werden darf.
	if((isset($PICVID['ACT_USER']) === true) && ($PICVID['ACT_USER']->_group > 1)) {
	?>
	<a href="index.php?section=news&admin=1&edit=1&id=<?php echo $id; ?>" class="btn btn-success"><i class="icon-pencil"></i></a>
	<a class="btn btn-danger" data-toggle="modal" data-target="#warning" href="#" onclick="document.getElementById('delete-link').href='index.php?section=news&admin=1&delete=1&id=<?php echo $id; ?>'"><i class="icon-trash"></i></a>
	<?php
	}
}

//ID ermitteln.
$id = $PICVID['CORE']->getParameter($_REQUEST, 'id', 0);

//Pruefen ob ein bestimmtes Element angezeigt werden soll.
if($id > 0) {

    //SQL-Befehl setzen (News-Element ermitteln).
    $PICVID['DATABASE']->setQuery('SELECT * FROM `#__news` WHERE `NewsID` ='.(int) $id);

    //Element aus der Datenbank lesen.
    $news = $PICVID['DATABASE']->getObjectArray();

    //Pruefen ob ein Element ermittelt werden konnte.
    if((is_array($news) === true) && (count($news) > 0)) {
    ?>
        <div class="modal hide" id="warning">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3><font style="color:#f00;">Achtung!</font></h3>
            </div>
            <div class="modal-body">
               <p>Möchten Sie den ausgewählten Artikel wirklich löschen?</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
                <a id="delete-link" href="#" class="btn btn-danger">Löschen</a>
            </div>
        </div>

        <div class="news">
            <h2><?php echo $news[0]->NewsTitle; ?></h2>
            <p style="font-size:0.8em;">Geschrieben am <?php echo date("d.m.Y", strtotime($news[0]->NewsDate)); ?> von <?php echo (($username = $PICVID['USER']->getUsernameFromID($news[0]->NewsUserID)) !== '') ? $username : 'unbekannt'; ?></p>
            <hr/>
            <p><?php echo nl2br($news[0]->NewsContent); ?></p>
            <hr/>
            <p><a class="btn btn-warning" href="index.php?section=news"><i class="icon-arrow-left"></i> <font color="#000">Übersicht</font></a><?php echo adminFunctions($id); ?></p>
        </div>
    <?php
    } else {

        //Weiterleiten mit Fehlermeldung.
        $PICVID['CORE']->redirect('index.php?section=news', 'Der Artikel ist nicht verfügbar!', 'error');
        exit;
    }
} else {
    ?>
    <h3>News Übersicht</h3>
    <hr/>
    <div class="modal hide" id="warning">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3><font style="color:#f00;">Achtung!</font></h3>
        </div>
        <div class="modal-body">
           <p>Möchten Sie den ausgewählten Artikel wirklich löschen?</p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal">Abbrechen</a>
            <a id="delete-link" href="#" class="btn btn-danger">Löschen</a>
        </div>
    </div>
    <?php
    //SQL-Befehl setzen (Artikel ermitteln).
    $PICVID['DATABASE']->setQuery('SELECT * FROM `#__news` ORDER BY `NewsDate` DESC');

    //Artikel ermitteln.
    $articles = $PICVID['DATABASE']->getObjectArray();

    //Pruefen ob Artikel vorhanden sind.
    if((is_array($articles) === true) && (count($articles) > 0)) {

        //Eigenschaften der Ansicht festlegen.
        $data_per_site = 4;
        $count = count($articles);
        $act_site = $PICVID['CORE']->getParameter($_REQUEST, 'page', 1);
        $start = ($act_site * $data_per_site) - $data_per_site;

        //Pruefen ob die Seite gueltig ist.
        if(($act_site > ceil($count / $data_per_site)) || ($act_site < 1)) {

            //Weiterleiten.
            $PICVID['CORE']->redirect('index.php?section=news&page=1');
        }

        //Status fuer das erste Element setzen.
        $first_entry = true;

        //SQL-Befehl setzen (Bereich waehlen).
        $PICVID['DATABASE']->setQuery('SELECT * FROM `#__news` ORDER BY `NewsDate` DESC LIMIT '.(int) $start.', '.$data_per_site);

        //Elemente ermitteln.
        $act_articles = $PICVID['DATABASE']->getObjectArray();

        //Durchlaufen aller Elemente.
        foreach($act_articles as $act_article) {

            //Pruefen ob der erste Eintrag erstellt werden soll.
            if($first_entry === true) {
            ?>
                <div class="news">
                    <h2><?php echo $act_article->NewsTitle; ?></h2>
                    <p style="font-size:0.8em;">Geschrieben am <?php echo date("d.m.Y", strtotime($act_article->NewsDate)); ?> von <?php echo (($username = $PICVID['USER']->getUsernameFromID($act_article->NewsUserID)) !== '') ? $username : 'unbekannt'; ?></p>
                    <hr/>
                    <p><?php echo nl2br($act_article->NewsContent); ?></p>
                    <hr/>
                    <p><?php echo adminFunctions($act_article->NewsID); ?></p>
                </div>
                <hr/>
                <div class="row">
            <?php
            $first_entry = false;
            } else {
            ?>
            <div class="span4">
                <h3><?php echo $act_article->NewsTitle; ?></h3>
                <p style="font-size:0.8em;">Geschrieben am <?php echo date("d.m.Y", strtotime($act_article->NewsDate)); ?> von <?php echo (($username = $PICVID['USER']->getUsernameFromID($act_article->NewsUserID)) !== '') ? $username : 'unbekannt'; ?></p>
                <hr/>
                <p><?php echo substr(nl2br($act_article->NewsContent), 0, 147); ?>...</p>
                <hr/>
                <p><a class="btn" href="index.php?section=news&id=<?php echo $act_article->NewsID; ?>">mehr lesen <i class="icon-arrow-right"></i></a><?php echo adminFunctions($act_article->NewsID); ?></p>
            </div>
            <?php
            }
        }

        //Zaehler setzen.
        $counter = 1;
        ?>
        </div>
        <div class="clearfix"></div>
        <hr/>
        <ul class="pagination">
        <?php if($act_site == 1) { ?>
            <li class="disabled"><a href="#">&laquo;</a></li>
        <?php } else { ?>
            <li><a href="index.php?section=news&page=<?php echo ($act_site - 1); ?>">&laquo;</a></li>
        <?php }

            //Seiten-Tabs erzeugen.
            for($nums = 0; $nums < $count; $nums = $nums + $data_per_site) {

                //Pruefen ob ein inaktives Element erstellt werden soll.
                if($counter == $act_site) {
                ?>
                    <li class="disabled"><a href="index.php?section=news&page=<?php echo $counter; ?>"><?php echo $counter; ?></a></li>
                <?php
                } else {
                ?>
                    <li><a href="index.php?section=news&page=<?php echo $counter; ?>"><?php echo $counter; ?></a></li>
                <?php
                }

                //Zaehler erhoehen.
                $counter++;
            }

            //Rechte Navigation erzeugen.
            if($act_site == ceil($count / $data_per_site)) {
            ?>
                <li class="disabled"><a href="#">&raquo;</a></li>
            <?php
            } else {
            ?>
                <li><a href="index.php?section=news&page=<?php echo ($act_site + 1); ?>">&raquo;</a></li>
            <?php
            }
        ?>
        </ul>
        <?php
    } else {
    ?>
    <p class="info">Es sind keine Artikel vorhanden!</p>
    <?php
    }
}
?>