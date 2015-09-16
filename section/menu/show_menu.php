<?php
//SQL-Befehl setzen (Menueelemente ermitteln).
$PICVID['DATABASE']->setQuery('SELECT * FROM `#__menu` ORDER BY `OrderNum`');

//Menueelemente ermitteln.
$menuElements = $PICVID['DATABASE']->getObjectArray();

//Pruefen ob Elemente vorhanden sind.
if((is_array($menuElements) === true) && (count($menuElements) > 0)) {

    //Durchlaufen aller Menueelemente.
    foreach($menuElements as $menuElement) {

        //Pruefen welcher Typ verwendet werden soll.
        switch($menuElement->Type) {
            case 0:

                //Bereich aus der Datenbank ermitteln.
                $section = $PICVID['SECTION']->getFromDatabase($menuElement->SectionID);

                //Pruefen ob der Bereich vorhanden ist.
                if((is_array($section) === true) && (count($section) > 0) && ($section[0]->state == 1)) {

                    //Menueelement ausgeben.
                    echo '<li><a href="index.php?section='.$section[0]->name.'">'.$menuElement->ShowName.'</a></li>';
                }
                break;
            case 1:

                //Menueelement (URL) ausgeben.
                echo '<li><a href="'.$menuElement->URL.'">'.$menuElement->ShowName.'</a></li>';
                break;
        }
    }
} else {

    //Kein Eintrag (Startseite anzeigen).
    echo '<li><a href="index.php">Startseite</a></li>';
}
?>