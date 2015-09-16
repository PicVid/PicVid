<?php
//Seitentitel (Tab Titel) festlegen.
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Registrieren</title>');

//Pruefen welcher Befehl verwendet wird.
switch($task) {
    case 'user_create':
        register::user_register();
        break;
    case 'show_login':
        register::show_login();
        break;
    case 'show_register':
    default:
        register::show_register();
        break;
}

/**
 * Klasse um die Registrierung verwalten zu koennen.
 * @since 1.0.0
 */
class register {
    /**
     * Methode um die Login-Maske anzeigen zu koennen.
     * @since 1.0.0
     */
    public static function show_login() {
    ?>
    <h3>Anmelden</h3>
    <hr/>
    <form action="index.php?task=user_login" class="form-horizontal" id="login-form" method="post">
        <div class="control-group">
            <label class="control-label" for="username">Benutzername</label>
            <div class="controls">
                <input class="input-xlarge" id="username" name="username" placeholder="Benutzername" type="text" rel="popover" data-content="Geben Sie hier den vollständigen Namen des neuen Benutzers an.<br/><b>Beispiel:</b> Max Mustermann" data-original-title="Name"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="password">Passwort</label>
            <div class="controls">
                <input class="input-xlarge" id="password" name="password" placeholder="Passwort" type="password" rel="popover" data-content="Geben Sie hier den vollständigen Namen des neuen Benutzers an.<br/><b>Beispiel:</b> Max Mustermann" data-original-title="Name"/>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button class="btn btn-success" type="submit">Anmelden</button>
            </div>
        </div>
    </form>
    <?php
    }

    /**
     * Methode um das Registrierungsformular anzeigen zu koennen.
     * @since 1.0.0
     */
    public static function show_register() {

        //Globale Variable von PicVid einbinden.
        global $PICVID;
    ?>
    <h3>Registrierung / Neuanmeldung</h3>
    <hr/>
    <form action="index.php?section=register" class="form-horizontal" id="register-form" method="post">
        <div class="control-group">
            <label class="control-label" for="user_name">Name</label>
            <div class="controls">
                <input class="input-xlarge" id="user_name" name="user_name" placeholder="Name" type="text" rel="popover" data-content="Geben Sie hier den vollständigen Namen des neuen Benutzers an.<br/><b>Beispiel:</b> Max Mustermann" data-original-title="Name"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="user_email">E-Mail</label>
            <div class="controls">
                <input class="input-xlarge" id="user_email" name="user_email" placeholder="E-Mail" type="text" rel="popover" data-content="Geben Sie hier die E-Mail-Adresse des Benutzers an. Es wird eine Bestätigungsemail versendet!<br/><b>Beispiel:</b> max.mustermann@testmail.com" data-original-title="E-Mail"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="user_username">Benutzername</label>
            <div class="controls">
                <input class="input-xlarge" id="user_username" name="user_username" placeholder="Benutzername" type="text" rel="popover" data-content="Geben Sie hier den Benutzernamen des neuen Benutzers an.<br/><b>Beispiel:</b> Max" data-original-title="Benutzername"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="user_password">Passwort</label>
            <div class="controls">
                <input class="input-xlarge" id="user_password" name="user_password" placeholder="Passwort" type="password" rel="popover" data-content="Geben Sie hier das Passwort an welches für den Benutzer verwendet werden soll.<br/><b>Beispiel:</b> geheim" data-original-title="Passwort"/>
            </div>
        </div>
        <input id="user_group" name="user_group" type="hidden" value="1"/>
        <div class="control-group">
            <div class="controls">
                <button class="btn btn-success" name="task" type="submit" value="user_create">Account erstellen</button>
            </div>
        </div>
    </form>
    <script>
        $('#user_name').popover({trigger:'hover', placement:'right'});
        $('#user_username').popover({trigger:'hover', placement:'right'});
        $('#user_email').popover({trigger:'hover', placement:'right'});
        $('#user_password').popover({trigger:'hover', placement:'right'});
    </script>
    <?php
    }

    /**
     * Methode um einen Benutzer registrieren zu koennen.
     * @since 1.0.0
     */
    public static function user_register() {

        //Globale PicVid-Variable einbinden.
        global $PICVID;

        //Instanz eines neuen Benutzers erstellen.
        $User = new User($PICVID['DATABASE']);

        //Laden des Benutzers aus dem Formular.
        $User->loadFromArray($_POST);

        //Erstellen des neuen Benutzers in der Datenbank.
        if($User->create(true) === true) {
            $PICVID['CORE']->redirect('index.php?section=register&task=show_login', 'Bestätigen Sie nun bitte Ihre E-Mail-Adresse um Ihr neues Konto nutzen zu können.', 'success');
            exit;
        } else {

            //Pruefen ob der Benutzer bereits existiert.
            if($User->getErrorCode() === 'EXISTS_ERROR') {
                $PICVID['CORE']->redirect('index.php?section=register', 'Der Benutzer ('.trim($User->_username).') oder die E-Mail ('.trim($User->_email).') ist bereits registriert.', 'error');
            } else {
                $PICVID['CORE']->redirect('index.php?section=register', 'Der Benutzer '.$User->_username.' konnte nicht registriert werden.', 'error');
            }
        }
    }
}
?>