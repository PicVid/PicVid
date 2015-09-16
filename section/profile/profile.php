<?php
//STD 
$Core = new Core();
$Database = $Core->getDatabaseObject();

//#######################START CODE REGION###############
//DESC: Benutzer bei Rich Profile anlegen, die bei _user vorhanden sind.
$Database->setQuery('SELECT  `id` FROM  `#__user`');
$existingusers = $Database->getObjectArray();
foreach ($existingusers as $user_existing) {
	$Database->setQuery('SELECT  `id` FROM  `#__rich_profile` WHERE `id` = `'. $user_existing->id .'` `');
	$result = $Database->getObjectArray();
	if($result == 0){
		$sql = "INSERT INTO `#__rich_profile` (`user_id`) VALUES ('". $user_existing->id ."');";
		$Database->setQuery($sql);
		$Database->query();
	}
}
//########################END CODE REGION###############
	
	
//#######################START CODE REGION###############
//DESC: VAriablen deklaration, da sonst eventuelle fehler
	//declares std variables
	$show_name = "";	
	$show_username = "";
	$show_birthday_day = "";
	$show_birthday_month = "";
	$show_birthday_year = "";
	$show_live_city = "";
	$show_live_country = "";
	$show_mail = "";
	
	//declares social variables
	$show_icq = "";
	$show_msn = "";
	$show_skype = "";
	$show_gtalk = "";
	$show_twitter = "";
	$show_facebook = "";
	
	//declares options for showing
	$opt_birthday = 0;
	$opts_birthday = array("Verbergen","Nur Alter","Alter und Datum");	
	
	$opt_live = 0;
	$opts_live = array("Verbergen","Nur Land","Stadt und Land");	
	
	$opt_mail = 0;
	
	$opts_social = array("Verbergen","Nur für Registrierte Benutzer anzeigen","Für Besucher sichtbar");
	$opt_icq = 0;
	$opt_msn = 0;
	$opt_skype = 0;
	$opt_gtalk = 0;
	$opt_twitter = 0;
	$opt_facebook = 0;
//########################END CODE REGION###############
	
	
	
//#######################START CODE REGION###############
//DESC: UserID uns ViewAs Modus einstellungen
	//UserID und Errorcode auf 0 deklarieren
	$userid = 0;
	$errorcode = 0;
	
	//Wenn error Code existiert, übernehmen
	if(ISSET($_GET['error'])) $errorcode = $_GET['error'];

	//Ist UserID angegeben????
	IF(ISSET($_GET['id'])){
		 //wenn userid angegeben
		$userid = $_GET['id'];
		$Database->setQuery('SELECT  `id` FROM  `#__user` WHERE `id` = '.$userid);//existiert user???
		if($Database->getResult() == NULL){
			//user existiert nicht!!
			//wenn error code nicht schon auf 1 ist (dauerloop verhindern)
			if($errorcode != 1)$PICVID['CORE']->redirect('index.php?section=profile&id='.$userid.'&error=1', 'Benutzer ist nicht vorhanden!', 'error'); //weiterleitung auf error page
			$Database->setQuery('SELECT  `id` FROM  `#__user`'); //ersten user auswählen
			$userid = $Database->getResult();
		}
	}
	elseif($userid == NULL AND ISSET($PICVID['ACT_USER']->_id)){ //wenn user id nicht angegeben aber user angemeldet
		$userid = $PICVID['ACT_USER']->_id;	//keine id vorhanden - die id vom user übernehmen (eigenes profil anzeigen)
	}
	else{
		//Keine userid angegeben
		//wenn error code nicht schon auf 1 ist (dauerloop verhindern)
		if($errorcode != 1) $PICVID['CORE']->redirect('index.php?section=profile&error=1', 'Keine Benutzer ID angegeben!', 'error');//weiterleitung auf error page
		$Database->setQuery('SELECT  `id` FROM  `#__user`'); //erste id vom user nehmen
		$userid = $Database->getResult();
		
	}
	
		//Anzeigemodus setzen
	if(ISSET($_POST['view_as'])){
		//View as mode.
		$view_as = $_POST['view_as'];
		switch ($view_as) {
			default:
			case 0:
				//als user selbst
				$view_as = 0;
				echo '<div class="alert alert-info">Alle Felder werden ausgegeben, da Sie ihr Profil selbst betrachten. Um zu sehen wie es ein anderer Benutzer sieht ändern Sie bitte die Ansicht.</div>';
				break;
			case 1:
				//als registrieter
				$view_as = 1;
				break;
			case 2:
				//als besucher
				$view_as = 2;
				break;
		}
	}
	elseif(isset($PICVID['ACT_USER']->_id)){
		
		if($userid == $PICVID['ACT_USER']->_id)
		{		
			//schaut eigenes profil an		
			$view_as = 0; 
			echo '<div class="alert alert-info">Alle Felder werden ausgegeben, da Sie ihr Profil selbst betrachten. Um zu sehen wie es ein anderer Benutzer sieht ändern Sie bitte die Ansicht.</div>';
		}
		elseif($userid != $PICVID['ACT_USER']->_id){
			//Benutzer schaut nicht sein eigenes Profil an ist aber angemeldet
			$view_as = 1; 
		}
		else{
			//ist nicht angemeldet
			$view_as = 2;
		}		
	}
	else{
		//std: Visitor
		$view_as = 2; //view as zurückstellen (error in html ausgabe verhindern)
	}


//Anzeigename
$Database->setQuery('SELECT  `name` FROM  `#__user` WHERE `id` = '.$userid); //erste id vom user nehmen
$username_show = $Database->getResult();		
$PICVID['TEMPLATE_ENGINE']->setInMarker("HEAD", '<title>PicVid - Profilansicht '.$username_show.'</title>');

//########################END CODE REGION###############
	
	
	
//#######################START CODE REGION###############
//DESC: Verabreitung von Daten z.B. aus der Datenbank
	
	//DAten aus der _user datenbank ( unveränderlich) auslesen und in variablen schreiben
	$Database->setQuery('SELECT  `email`, `name`, `username`, `group` FROM  `#__user` WHERE `id` = '.$userid);
	$thisUserNormal = $Database->getObjectArray(); 
	$show_name = $thisUserNormal[0]->name;	
	$show_username = $thisUserNormal[0]->username;
	$show_mail = $thisUserNormal[0]->email;
	$usergroup = $thisUserNormal[0]->group;
	
	
	//Benutzergruppe des anzuzeigenden users auslesen und in variable speichern 
	$Database->setQuery('SELECT  `name` FROM  `#__user_group` WHERE `id` = '.$usergroup);
	$show_groupname = $Database->getResult();	
	
	//Daten aus der #__rich_profile tabelle auslesen und abspeichern (lediglich optionen und sonderdaten, social daten werden dynamisch ausgelesen)
	$Database->setQuery('SELECT  * FROM  `#__rich_profile` WHERE `user_id` = '.$userid);
	$thisUserRich = $Database->getObjectArray();

	//Datums Format umwandeln
	$datetime = strtotime($thisUserRich[0]->birthday);
	$show_birthday_day = date('d', $datetime);
	$show_birthday_month = date('m', $datetime);
	$show_birthday_year = date('Y', $datetime);
	$show_live_city = $thisUserRich[0]->live_city;
	$show_live_country = $thisUserRich[0]->live_country ;
	
	//Spezielle Optionen einstellen
	$opt_birthday = $thisUserRich[0]->opt_birthday;		
	$opt_live = $thisUserRich[0]->opt_live;		
	$opt_mail = $thisUserRich[0]->opt_mail;
	
	
	//wenn view as = 0 -> eigene ansicht -> alle anzeigen auf ganz hoch (sichtbaer)
	if($view_as == 0){
		$opt_birthday = 2;		
		$opt_live = 2;		
		$opt_mail = 2;
	}
	
	$show_puploaded = 0;
	$show_vuploaded = 0;
		
	$Database->setQuery('SELECT  * FROM  `#__image` WHERE `create_user` = '.$userid);
	$puploaded = $Database->getObjectArray();
	$show_puploaded = count($puploaded);
	
	$Database->setQuery('SELECT  * FROM  `#__video` WHERE `create_user` = '.$userid);
	$vuploaded = $Database->getObjectArray();
	$show_vuploaded = count($vuploaded);
//########################END CODE REGION###############
	
	
//#######################START CODE REGION###############
//DESC: Anzuzeigende Texte generieren
	//Show birthdaystring - Wie soll der Geburtstag angezeigt werden
	//#######################################################################################################NOT READY
	$show_birthdaystring = "";
	switch ($opt_birthday) {
		default:
		case 0:
				//NONE
			$show_birthdaystring = "Privat";
			break;
		case 1:
				//AGE
			$show_birthdaystring = birthday($show_birthday_year."-".$show_birthday_month."-".$show_birthday_day);
			break;
		case 2:
				//AGE AND DATE
			$show_birthdaystring = $show_birthday_day.".".$show_birthday_month.".".$show_birthday_year." (".birthday($show_birthday_year."-".$show_birthday_month."-".$show_birthday_day).")";
			break;
	}
	
	//show livestring - wo die person lebt
	$show_livestring = "";
	switch ($opt_live) {
		default:
		case 0:
				//NONE
			$show_livestring = "Privat";
			break;
		case 1:
				//Country
			$show_livestring = $show_live_country;
			break;
		case 2:
				//City and Country
			$show_livestring = $show_live_city . ", " . $show_live_country;
			break;
	}
	//show mailstring - Ob Mail angezeigt wird
	$show_mailstring = "";
	switch ($opt_mail) {
		default:
		case 0:
				//NONE
			$show_mailstring = "Privat";
			break;
		case 2:
			//visitros
			$show_mailstring =  $show_mail;
			break;
		case 1:
			//registered
			if($view_as == 1){
				$show_mailstring =  $show_mail;
			}
			else{
				$show_mailstring = "Nur f&uuml;r Registrierte.";
			}
			break;
	}
	//show std - Soziale Variablen und deren Funktionen laden
	function getStdInfo($name){
		global $Core, $Database, $userid, $PICVID,$watcher_group, $view_as;		
		$Database->setQuery('SELECT  * FROM  `#__rich_profile` WHERE `user_id` = '.$userid);
		$thisUserRich = $Database->getObjectArray();
		
		$option = "opt_".$name;
		$option = $thisUserRich[0]->$option;
		$string = $thisUserRich[0]->$name;
		//wenn view as = 0 -> eigene ansicht -> alle anzeigen auf ganz hoch (sichtbaer)
		if($view_as == 0){
			$option = 2;
		}
		
		switch ($option) {
		default:
		case 0:
				//NONE
			return "Privat";
			break;
		case 1:
			//registered
			if($view_as == 1){
				return $string;
				
			}
			else{
				return "Nur f&uuml;r Registrierte.";
			}
			break;
		case 2:
				//visitros
			return $string;
			break;
	}
	}
//########################END CODE REGION###############
	
	//calculate years of age (input string: YYYY-MM-DD)
	function birthday ($birthday){
	list($year,$month,$day) = explode("-",$birthday);
	$year_diff  = date("Y") - $year;
	$month_diff = date("m") - $month;
	$day_diff   = date("d") - $day;
	if ($day_diff < 0 || $month_diff < 0)
	$year_diff--;
	return $year_diff;
	}
?>

<div class="container">
	
		<?php
			if(ISSET($PICVID['ACT_USER']->_id)){
				if($PICVID['ACT_USER']->_id == $userid){
					echo '<div class="span12" style="float:left !important;" ><form style="width:48%;float:left;" action="index.php?section=profile&admin=1" name="form1" method="POST" class="form-horizontal" >
						<button type="submit" class="btn btn-success btn-large button-right">Bearbeiten</button>
						</form><form  style="width:48%;float:left;" action="index.php?section=profile" name="view_as_form" method="POST" class="form-horizontal" >
						<select name="view_as">
						  <option value="0';
						  if($view_as == 0) echo "selected";
					echo '">Meine Ansicht</option>
						  <option value="1"';
						  if($view_as == 1) echo "selected";
					echo '>Ansicht als Registrierter Benutzer</option>
						  <option value="2"';
						  if($view_as == 2) echo "selected";
					echo '>Ansicht als Besucher</option>
						</select>
						<button type="submit" class="btn btn-success btn-large button-right">Ansicht &auml;ndern</button>
						</form></div>';
					
				}
			}
		?>
					<div class="span10"><h2>Userprofile</h2>
<hr/></div>
		<div class="span5">

        <legend>User</legend>
        <table class="table table-bordered table-striped">
		    <tbody>
		      <tr>
		        <td>Name</td>
		        <td><? echo $show_name; ?></td>
		      </tr>
		      <tr>
		        <td>Username</td>
		        <td><? echo $show_username; ?></td>
		      </tr>
		      <tr>
		        <td>Group</td>
		        <td><? echo $show_groupname; ?></td>
		      </tr>
		      <tr>
		        <td>Age</td>
		        <td><? echo $show_birthdaystring; ?></td>
		      </tr>
		      <tr>
		        <td>Live in</td>
		        <td><? echo $show_livestring; ?></td>
		      </tr>
		      <tr>
		        <td>Mail</td>
		        <td><? echo $show_mailstring; ?></td>
		      </tr>
		    </tbody>
		  </table>
  </div>
	<div class="span5">
					<legend>Social</legend>
                    <table class="table table-bordered table-striped">
					    <tbody>
					      <tr>
					        <td>ICQ</td>
					        <td><? echo getStdInfo("icq"); ?></td>
					      </tr>
					      <tr>
					        <td>MSN</td>
					        <td><? echo getStdInfo("msn"); ?></td>
					      </tr>
					      <tr>
					        <td>Skype</td>
					        <td><? echo getStdInfo("skype"); ?></td>
					      </tr>
					      <tr>
					        <td>gTalk</td>
					        <td><? echo getStdInfo("gtalk"); ?></td>
					      </tr>
					      <tr>
					        <td>twitter</td>
					        <td><? echo getStdInfo("twitter"); ?></td>
					      </tr>
					      <tr>
					        <td>Facebook</td>
					        <td><? echo getStdInfo("facebook"); ?></td>
					      </tr>
					    </tbody>
					  </table>
  </div>
	<div class="span10">
					  
                    <legend>Stats</legend>
                    <table class="table table-bordered table-striped">
					    <tbody>
					      <tr>
					        <td>Pic uploaded</td>
					        <td><? echo $show_puploaded; ?></td>
					      </tr>
					      <tr>
					        <td>Vid uploaded</td>
					        <td><? echo $show_vuploaded; ?></td>
					      </tr>
					    </tbody>
					  </table>
                    
            </div>        
       </div>