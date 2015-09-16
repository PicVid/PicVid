<script type="text/javascript">
    //<![CDATA[
    function changeType(){
        var type = document.forms["addedit_form"].elements["database_type"].value;

        //Pruefen welcher Typ verwendet werden soll.
		switch(type) {
		  case '0':
		  default:
		      document.forms['addedit_form'].database_url.disabled = true;
		      document.forms['addedit_form'].database_sectionid.disabled = false;
		      break;
		  case '1':
		      document.forms['addedit_form'].database_url.disabled = false;
		      document.forms['addedit_form'].database_sectionid.disabled = true;
		      break;
		}
	}
	//]]>
</script>
<form action="index.php?section=menu&admin=1" id="addedit_form" name="addedit_form" class="form-horizontal" method="post">
    <h3><?php echo $headline; ?></h3>
    <hr/>
    <div class="row">
        <div class="span5">
            <div class="control-group">
                <label class="control-label" for="database_showname">Anzeigetitel</label>
                <div class="controls">
                    <input class="input-xlarge" id="database_showname" placeholder="Anzeigetitel" name="database_showname" type="text" maxlength="255" value="<?php if(isset($editmenuShowName))echo $editmenuShowName; ?>"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="database_type">Typ</label>
                <div class="controls">
                    <?php echo $type_select; ?>
                </div>
            </div>
        </div>
        <div class="span5">
            <div class="control-group">
                <label class="control-label" for="database_sectionid">Bereich</label>
                <div class="controls">
                    <?php echo $section_select; ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="database_url">URL</label>
                <div class="controls">
                    <input class="input-xlarge" id="database_url" name="database_url" placeholder="URL" type="text" maxlength="255" value="<?php echo $url; ?>"/>
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <input name="editid" type="hidden" value="<?php echo $id; ?>"/>
        <button name="<?php echo $action; ?>" class="btn btn-success" value="1"><i class="icon-ok"></i> <font style="color:#000;">Speichern</font></button>
        <button name="task" class="btn btn-warning" value="cancel"><i class="icon-arrow-left"></i> <font style="color:#000;">Abbrechen</font></button>
    </div>
</form>
<script type="text/javascript">
    //<![CDATA[
    changeType();
    //]]>
</script>