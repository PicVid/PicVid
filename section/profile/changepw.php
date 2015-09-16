<form action="index.php?section=profile&admin=1" id="edit_form" name="edit_form" class="form-horizontal" method="post">
	<div class="container">
		<div class="span12">
	        <legend>Passwort &Auml;ndern</legend>
	        <table class="table table-bordered table-striped">
			    <tbody>
			      <tr>
			        <td>Altes Passwort Passwort</td>
			        <td>
			        	<input class="input-xlarge" id="database_oldPW" name="database_oldPW" type="password"  maxlength="32"/>
					</td>
			      </tr>
			      <tr>
			        <td>Neues Passwort</td>
			        <td>
			        	<input class="input-xlarge" id="database_newPW" name="database_newPW" type="password" maxlength="32" />
					</td>
			      </tr>
			      <tr>
			        <td>Neues Passwort Best&auml;tigen</td>
			        <td>
			        	<input class="input-xlarge" id="database_newPWconfirm" name="database_newPWconfirm" type="password" maxlength="32" />
			        </td>
			      </tr>
  			      <tr>
			        <td>&Auml;nderungshinweis</td>
			        <td>
			        	 <input type="checkbox" id="database_confirm" name="database_confirm" value="1" /> Hiermit best&auml;tige ich, dass mein Passwort ge&auml;ndert wird und nur durch einen Administrator wieder zur&uuml;ckgesetzt werden kann.
			        </td>
			      </tr>
			    </tbody>
			  </table>
			<div style="min-height:80px;">
		        <button type="submit" name="changepw" class="btn btn-success btn-large button-right">Passwort &auml;ndern</button>
				<button type="submit" name="refresh" class="btn btn-danger btn-large button-right">Abbrechen</button>
		    </div> 
		</div>     
	</div>
</form>