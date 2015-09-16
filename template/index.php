<!DOCTYPE html>
<html>
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/images/favicon.png" type="image/png">
        <link rel="stylesheet" type="text/css" href="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/includes/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/template/style.css"/>
        <script src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/includes/bootstrap/js/jquery.min.js"></script>
        <?php $PICVID['TEMPLATE_ENGINE']->registerMarker('HEAD'); ?>
        <title>PicVid</title>
	</head>
	<body id="template<?php echo ((is_object($PICVID['ACT_USER']) === true) && ($PICVID['ACT_USER']->_group > 1)) ? '-admin' : ''; ?>">
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <div class="btn-group pull-right">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            <?php echo (is_object($PICVID['ACT_USER']) === true) ? '<i class="icon-user"></i>&nbsp;'.$PICVID['ACT_USER']->_username : '<i class="icon-user"></i>&nbsp;Anmelden'; ?>
                            <span class="caret"></span>
                        </a>
                        <?php if(is_object($PICVID['ACT_USER']) === true) { ?>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?section=profile&admin=1">Mein Profil bearbeiten</a></li>
                            <li><a href="index.php?section=profile">Mein Profil anzeigen</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?task=user_logout">Abmelden</a></li>
                        </ul>
                        <?php } else { ?>
                        <ul class="dropdown-menu login-form">
                            <form action="index.php" method="post">
                                <li><input name="username" placeholder="Benutzername" type="text"/></li>
                                <li><input name="password" placeholder="Passwort" type="password"/></li>
                                <li>
                                    <button class="btn btn-success" name="task" value="user_login">Anmelden</button>
                                    <button class="btn btn-success btn-right" name="task" value="user_register">Registrieren</button>
			                    </li>
                            </form>
                        </ul>
                        <?php } ?>
                    </div>
                    <div class="nav-collapse" id="user-menu">
                        <ul class="nav">
                            <li><a class="logo" href="index.php"><img alt="PicVid" border="0" height="30" src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/images/picvid_transparent.png" width="120"/></a></li>
                            <?php require_once($PICVID['CORE']->getValue('absolute_path').'/section/menu/show_menu.php'); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
        //Pruefen ob das Administrationsmenue eingeblendet werden soll.
        if(is_object($PICVID['ACT_USER']) === true) {
            require_once($PICVID['CORE']->getValue('absolute_path').'/section/menu/show_admin_menu.php');
        }
        ?>
        <div class="container">
            <?php
            //Pruefen ob eine Meldung ausgegeben werden soll.
            if((isset($_SESSION['message_text']) === true) && ($_SESSION['message_text'] !== '')) {
                echo '<div class="alert alert-'.trim($_SESSION['message_level']).'">'.trim($_SESSION['message_text']).'<a class="close" data-dismiss="alert" href="#">×</a></div>';
                unset($_SESSION['message_text'], $_SESSION['message_level']);
    		}

            //Einbinden der Section.
            require_once($PICVID['SECTION']->getSection());
        	?>
        </div>
        <div class="navbar navbar-inverse navbar-fixed-bottom" id="footer">
            <div class="navbar-inner">
                <div class="container">&copy; 2012 by Jasmin Kemmerich, Sebastian Brosch und Manuel Bochr&ouml;der</div>
            </div>
        </div>
        <script>
        $('.dropdown-menu input').bind('click', function (e) {
            e.stopPropagation()
        })
    	</script>
        <?php $PICVID['TEMPLATE_ENGINE']->registerMarker('FOOTER'); ?>
        <script src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/includes/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>