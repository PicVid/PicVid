<?php
//Benoetigte Dateien fuer den Texteditor einbinden.
$PICVID['TEMPLATE_ENGINE']->setInMarker('HEAD', '<link rel="stylesheet" type="text/css" href="'.$PICVID['CORE']->getValue('site_url').'/includes/bootstrap/css/bootstrap-wysihtml5.css"/>');
$PICVID['TEMPLATE_ENGINE']->setInMarker('FOOTER', '<script src="'.$PICVID['CORE']->getValue('site_url').'/includes/bootstrap/js/wysihtml5-0.3.0.js"></script>');
$PICVID['TEMPLATE_ENGINE']->setInMarker('FOOTER', '<script src="'.$PICVID['CORE']->getValue('site_url').'/includes/bootstrap/js/bootstrap-wysihtml5.js"></script>');
$PICVID['TEMPLATE_ENGINE']->setInMarker('FOOTER', '<script>$("#wysihtml5-textarea").wysihtml5();</script>');
?>
<h3><?php echo $headline; ?></h3>
<hr/>
<form action="index.php?section=news&admin=1" id="addedit_form" name="addedit_form" class="form-horizontal" method="post">
    <div class="span10">
        <div class="control-group">
            <label class="control-label" for="database_title">Titel</label>
            <div class="controls">
                <input class="input-xlarge" id="database_title" name="database_title" placeholder="Titel / Überschrift" type="text" value="<?php if(isset($editnewsTitle))echo $editnewsTitle; ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="database_text">Text</label>
            <div class="controls" style="width:600px;">
                <div class="wysihtml5-container">
                    <div id="toolbar" style="float:left !important;">
                        <div data-wysihtml5-dialog="createLink" style="display:none;">
                            <label>Link:<input data-wysihtml5-dialog-field="href" value="http://"></label>
                            <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
						</div>
						<div data-wysihtml5-dialog="insertImage" style="display:none;">
                            <label>Bild:<input data-wysihtml5-dialog-field="src" value="http://"></label>
							<label>Ausrichtung:
                                <select data-wysihtml5-dialog-field="className">
                                    <option value="">default</option>
								    <option value="wysiwyg-float-left">left</option>
								    <option value="wysiwyg-float-right">right</option>
							    </select>
							</label>
							<a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
						</div>
					</div>
					<textarea name="database_text" id="wysihtml5-textarea" style="margin:0px;width:600px;height:350px;" placeholder="Geben Sie hier Ihren Text ein..." autofocus><?php if(isset($editnewsText))echo $editnewsText; ?></textarea>
                </div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-actions">
        <input name="editid" type="hidden" value="<?php echo $id; ?>"/>
        <button type="submit" name="<?php echo $action; ?>" class="btn btn-success" value="1"><i class="icon-ok"></i> <font style="color:#000;">Speichern</font></button>
        <button type="submit" name="cancel" class="btn btn-warning" value="1"><i class="icon-arrow-left"></i> <font style="color:#000;">Abbrechen</font></button>
	</div>
</form>