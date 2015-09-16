<?php
//Pruefen ob ein Benutzer angemeldet ist und die Rechte stimmen.
if((is_object($PICVID['ACT_USER']) === true) && ($PICVID['ACT_USER']->_group > 1)) {
?>
<div class="navbar navbar-inverse navbar-fixed-top" id="admin-menu">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">
            <?php
            //Alle Bereiche der Kategorie Bilder / Videos ermitteln.
            $PICVID['DATABASE']->setQuery('SELECT * FROM  `#__section` WHERE `category_id` = 1 AND `admin_group` <= '.(int) $PICVID['ACT_USER']->_group);
            $sections = $PICVID['DATABASE']->getObjectArray();

            //Pruefen ob Bereiche vorhanden sind.
            if((is_array($sections) === true) && (count($sections) > 0)) {
            ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Bilder / Videos<b class="caret"></b></a>
                    <ul class="dropdown-menu">
            <?php
                //Durchlaufen aller Bereiche.
                foreach($sections as $section) {
            ?>
                    <li><a href="index.php?section=<?php echo $section->name; ?>&admin=1"><?php echo $section->menu_title; ?></a></li>
            <?php
                }
            ?>
                    </ul>
                </li>
            <?php
            }

            //Alle Bereiche der Kategorie Verwaltung ermitteln.
            $PICVID['DATABASE']->setQuery('SELECT * FROM  `#__section` WHERE `category_id` = 2 AND `admin_group` <= '.(int) $PICVID['ACT_USER']->_group);
            $sections = $PICVID['DATABASE']->getObjectArray();

            //Pruefen ob Bereiche vorhanden sind.
            if((is_array($sections) === true) && (count($sections) > 0)) {
            ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Verwaltung<b class="caret"></b></a>
                    <ul class="dropdown-menu">
            <?php
                //Durchlaufen aller Bereiche.
                foreach($sections as $section) {
            ?>
                    <li><a href="index.php?section=<?php echo $section->name; ?>&admin=1"><?php echo $section->menu_title; ?></a></li>
            <?php
                }
            ?>
                    </ul>
                </li>
            <?php
            }

            //Alle Bereiche der Kategorie Extensions ermitteln.
            $PICVID['DATABASE']->setQuery('SELECT * FROM  `#__section` WHERE `category_id` = 3 AND `admin_group` <= '.(int) $PICVID['ACT_USER']->_group);
            $sections = $PICVID['DATABASE']->getObjectArray();

            //Pruefen ob Bereiche vorhanden sind.
            if((is_array($sections) === true) && (count($sections) > 0)) {
            ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Extensions<b class="caret"></b></a>
                    <ul class="dropdown-menu">
            <?php
                //Durchlaufen aller Bereiche.
                foreach($sections as $section) {
            ?>
                    <li><a href="index.php?section=<?php echo $section->name; ?>&admin=1"><?php echo $section->menu_title; ?></a></li>
            <?php
                }
            ?>
                    </ul>
                </li>
            <?php
            }
            ?>
            </ul>
            <ul class="nav" style="float:right">
              <li>
                  <a href="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/installation/update.php"><i class="icon-refresh icon-white"></i> Update</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
}
?>