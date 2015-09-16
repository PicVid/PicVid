<!-- <script src="<?php echo $PICVID['CORE']->getValue('site_url'); ?>/includes/bootstrap/js/bootstrap.min.js"></script> -->
<form action="index.php?section=profile&admin=1" id="edit_form" name="edit_form" class="form-horizontal" method="post">

<div class="container">
	<div class="span12">
		         <h2>Userprofile</h2>
<hr/>
        <legend>User</legend>
        <table class="table table-bordered table-striped">
		    <tbody>
		      <tr>
		        <td>Name</td>
		        <td>
		        	<input class="input-xlarge" id="database_name" name="database_name"  maxlength="255" type="text" value="<?php echo $show_name; ?>"  rel="tooltip" data-original-title="Vor- und Nachname" data-placement="top" data-trigger="hover" />
				</td>
		        <td></td>
		      </tr>
		      <tr>
		        <td>Benutzername</td>
		        <td><input class="input-xlarge" readonly="true" type="text" value="<?php echo $show_username; ?>"/></td>
		        <td></td>
		      </tr>
		      <tr>
		        <td>Gruppenzugeh&ouml;rigkeit</td>
		        <td><input class="input-xlarge" readonly="true" type="text" value="<?php echo $show_groupname; ?>"/></td>
		        <td></td>
		      </tr>
		      <tr>
		        <td>Geburtstag</td>
		        <td>
		        	<input class="input-small" style="width:15px;" placeholder="TT" maxlength="2" id="database_birthday_day" name="database_birthday_day" type="text" value="<?php echo $show_birthday_day; ?>" rel="tooltip" data-original-title="Geburtstag z.B. <b>01</b>.01.1990" data-placement="top" data-trigger="hover"/>
		        	-<input class="input-small" style="width:15px;" placeholder="MM" maxlength="2" id="database_birthday_month" name="database_birthday_month" type="text" value="<?php echo $show_birthday_month; ?>" rel="tooltip" data-original-title="Geburtstag z.B. 01.<b>01</b>.1990" data-placement="top" data-trigger="hover"/>
		        	-<input class="input-small" style="width:30px;" placeholder="JJJJ" maxlength="4" id="database_birthday_year" name="database_birthday_year" type="text" value="<?php echo $show_birthday_year; ?>" rel="tooltip" data-original-title="Geburtstag z.B. 01.01.<b>1990</b>" data-placement="top" data-trigger="hover"/>
		        </td>
		        <td>Anzeigeoptionen: 
		        	<select id="database_opt_birthday" name="database_opt_birthday">
                    	<? generateSelectOptions($opts_birthday,$opt_birthday); ?>
                    </select>
        		</td>
		      </tr>
		      <tr>
		        <td>Wohnort</td>
		        <td>
		        	<input class="input-large" placeholder="Stadt, z.B. Stuttgart"  maxlength="255" id="database_live_city" name="database_live_city" type="text" value="<?php echo $show_live_city; ?>"/>
		        	-<input class="input-large" placeholder="Land, z.B. Deutschland"  maxlength="255" id="database_live_country" name="database_live_country" type="text" value="<?php echo $show_live_country; ?>"/></td>
		        <td>Anzeigeoptionen: 
		        	<select id="database_opt_live" name="database_opt_live" selected="<?php echo $opt_live; ?>">
                    	<? generateSelectOptions($opts_live,$opt_live); ?>
                    	
                    </select>
        		</td>
		      </tr>
		      <tr>
		        <td>Mail</td>
		        <td><input class="input-xlarge" readonly="true" type="text" value="<?php echo $show_mail; ?>"/> <a href="index.php?section=profile&admin=1&action=changemail">Email &auml;ndern</a></td>
		        <td>Anzeigeoptionen: 
		        	<select id="database_opt_mail" name="database_opt_mail" >
                    	<? generateSelectOptions($opts_social,$opt_mail); ?>                    	
                    </select></td>
		      </tr>
  		      <tr>
		        <td>Passwort</td>
		        <td><a href="index.php?section=profile&admin=1&action=changepw">Passwort &auml;ndern</a></td>
		        <td></td>
		      </tr>
		    </tbody>
		  </table>
  </div>
	<div class="span12">
		
					<legend>Social</legend>
                    <table class="table table-bordered table-striped">
					    <tbody>
					      <tr>
					        <td>ICQ</td>
					        <td><input class="input-xlarge" placeholder="123456789" maxlength="10" id="database_icq" name="database_icq" type="text" value="<?php echo $show_icq; ?>" rel="tooltip" data-original-title="Ihre ICQ <b>Nummer</b>." data-placement="top" data-trigger="hover"/></td>
		        			<td>Anzeigeoptionen: 
					        	<select id="database_opt_icq" name="database_opt_icq" >
			                    	<? generateSelectOptions($opts_social,$opt_icq); ?>
			                    </select>
		                    </td>
					      </tr>
					      <tr>
					        <td>MSN</td>
					        <td><input class="input-xlarge" id="database_msn" maxlength="255" placeholder="Max.Mustermann@hotmail.de" name="database_msn" type="text" value="<?php echo $show_msn; ?>" rel="tooltip" data-original-title="Ihre MSN ID" data-placement="top" data-trigger="hover"/></td>
	        				<td>Anzeigeoptionen: 
					        	<select id="database_opt_msn" name="database_opt_msn" >
			                    	<? generateSelectOptions($opts_social,$opt_msn); ?>
			                    </select>
		                    </td>
					      </tr>
					      <tr>
					        <td>Skype</td>
					        <td><input class="input-xlarge" id="database_skype" maxlength="255" placeholder="Mustermann30" name="database_skype" type="text" value="<?php echo $show_skype; ?>"  rel="tooltip" data-original-title="Ihre Skype ID" data-placement="top" data-trigger="hover"/></td>
	        				<td>Anzeigeoptionen: 
					        	<select id="database_opt_skype" name="database_opt_skype" >
			                    	<? generateSelectOptions($opts_social,$opt_skype); ?>
			                    </select>
		                    </td>
					      </tr>
					      <tr>
					        <td>GTalk</td>
					        <td><input class="input-xlarge" id="database_gtalk" maxlength="255" placeholder="Max.Mustermann@gmail.com" name="database_gtalk" type="text" value="<?php echo $show_gtalk; ?>"  rel="tooltip" data-original-title="Ihre Google Adresse" data-placement="top" data-trigger="hover"/></td>
	        				<td>Anzeigeoptionen: 
					        	<select id="database_opt_gtalk" name="database_opt_gtalk" >
			                    	<? generateSelectOptions($opts_social,$opt_gtalk); ?>
			                    </select>
		                    </td>
					      </tr>
					      <tr>
					        <td>Twitter</td>
					        <td><input class="input-xlarge" id="database_twitter" maxlength="255" placeholder="MMustermann30" name="database_twitter" type="text" value="<?php echo $show_twitter; ?>"  rel="tooltip" data-original-title="Ihr Twitter account https://twitter.com/<b>MMustermann30</b>" data-placement="top" data-trigger="hover"/></td>
	        				<td>Anzeigeoptionen: 
					        	<select id="database_opt_twitter" name="database_opt_twitter" >
			                    	<? generateSelectOptions($opts_social,$opt_twitter); ?>
			                    </select>
		                    </td>
					      </tr>
					      <tr>
					        <td>Facebook</td>
					        <td><input class="input-xlarge" id="database_facebook" maxlength="255" placeholder="MaxMustermann1930" name="database_facebook" type="text" value="<?php echo $show_facebook; ?>" rel="tooltip" data-original-title="Ihr Facebook account http://www.facebook.com/<b>MaxMustermann1930</b>" data-placement="top" data-trigger="hover" /></td>
	        				<td>Anzeigeoptionen: 
					        	<select id="database_opt_facebook" name="database_opt_facebook" >
			                    	<? generateSelectOptions($opts_social,$opt_facebook); ?>
			                    </select>
		                    </td>
					      </tr>
					    </tbody>
					  </table>
 
	<div style="min-height:80px;">
        <input name="editid" type="hidden" value="<?php echo $userid; ?>"/>
        <button type="submit" name="save" class="btn btn-success btn-large button-right">Speichern</button>
		<button type="submit" name="abbort" class="btn btn-danger btn-large button-right">Abbrechen</button>
    </form>
    </div> </div>       
       </div>  
       
<script>
	$('#database_name').tooltip();
	$('#database_birthday_day').tooltip();
	$('#database_birthday_month').tooltip();
	$('#database_birthday_year').tooltip();
	$('#database_msn').tooltip();
	$('#database_icq').tooltip();
	$('#database_skype').tooltip();
	$('#database_gtalk').tooltip();
	$('#database_twitter').tooltip();
	$('#database_facebook').tooltip();
</script>      