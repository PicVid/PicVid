<form action="index.php?section=profile&admin=1" id="edit_form" name="edit_form" class="form-horizontal" method="post">
	<div class="container">
		<div class="span12">
	        <legend>Email Adresse &Auml;ndern</legend>
	        <table class="table table-bordered table-striped">
			    <tbody>
			      <tr>
			        <td>Neue Email</td>
			        <td>
			        	<input class="input-xlarge" id="database_newMail" name="database_newMail" type="text" maxlength="255"/>
					</td>
			      </tr>
			      <tr>
			        <td>Email Best&auml;tigen</td>
			        <td>
			        	<input class="input-xlarge" id="database_newMailconfirm" name="database_newMailconfirm" type="text" maxlength="255"/>
			        </td>
			      </tr>
  			      <tr>
			        <td>&Auml;nderungshinweis</td>
			        <td>
			        	 <input type="checkbox" id="database_confirm" name="database_confirm" value="1" /> Hiermit best&auml;tige ich, dass mein Account mit der E-mail &auml;nderung deaktiviert wird und nur durch erneutes freischalten wieder Aktiviert werden kann. Die Freischaltung erfolgt durch eine E-Mail best&auml;tigung der neuen E-Mail Adresse.
			        </td>
			      </tr>
			    </tbody>
			  </table>
			<div style="min-height:80px;">
		        <button type="submit" name="changemail" class="btn btn-success btn-large button-right">Email &auml;ndern</button>
				<button type="submit" name="refresh" class="btn btn-danger btn-large button-right">Abbrechen</button>
		    </div> 
		</div>     
	</div>
</form>