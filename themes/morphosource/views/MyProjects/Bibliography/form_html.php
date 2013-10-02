<?php
	$t_item = $this->getVar("item");
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	# --- media means this is a quick add for the media form being loaded via ajax
	$pn_media_id = $this->getVar("media_id");
	# --- specimen_id means this is a quick add for the specimen form being loaded via ajax
	$pn_specimen_id = $this->getVar("specimen_id");
	
	# --- formatting varibales
	# --- all fields in float_fields array  will be floated to the left
	$va_float_fields = array("article_title", "article_secondary_title", "journal_title", "monograph_title", "secondary_authors", "editors", "vol", "num", "edition", "publisher", "pubyear", "place_of_publication", "collation", "sect", "worktype", "pp", "isbn", "url", "external_identifier", "electronic_resource_num", "language", "keywords", "created_on", "last_modified_on");
	# --- all fields in clear_fields array  will have a clear output after them
	$va_clear_fields = array("article_secondary_title", "monograph_title", "editors", "edition", "place_of_publication", "pp", "url", "electronic_resource_num", "keywords", "last_modified_on");
	
if (!$this->request->isAjax()) {
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Bibliographic Information"); ?>
	</H1>
<?php
}
?>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'bibItemForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
if (!$this->request->isAjax()) {
		print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "MyProjects", $this->request->getController(), "listItems")."</div>";
}else{
	if($pn_media_id){
		print "<div style='float:right;'><a href='#' class='button buttonSmall' onclick='jQuery(\"#mediaBibliographyInfo\").load(\"".caNavUrl($this->request, 'MyProjects', 'Media', 'bibliographyLookup', array('media_id' => $pn_media_id))."\");'>"._t("Cancel")."</a></div>";
	}elseif($pn_specimen_id){
		print "<div style='float:right;'><a href='#' class='button buttonSmall' onclick='jQuery(\"#specimenBibliographyInfo\").load(\"".caNavUrl($this->request, 'MyProjects', 'Specimens', 'bibliographyLookup', array('specimen_id' => $pn_specimen_id))."\");'>"._t("Cancel")."</a></div>";
	}
}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#bibItemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
}
?>
	</div><!-- end formButtons -->

<?php
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "created_on":
			case "last_modified_on":
				if($t_item->get($vs_f)){
					print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			default:
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
		}
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		}
	}
	if($pn_media_id){
		print "<input type='hidden' value='".$pn_media_id."' name='media_id'>";
	}
	if($pn_specimen_id){
		print "<input type='hidden' value='".$pn_specimen_id."' name='specimen_id'>";
	}
#	if($pn_media_id || $pn_specimen_id){
#		print "<div class='formLabel'>Page(s)<br/><input type='text' name='page' style='width:40px;'></div>";	
#	}
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#bibItemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
}
?>
	</div><!-- end formButtons -->
</form>
</div>
<?php
	if($pn_media_id){
?>
<script type='text/javascript'>
	jQuery(document).ready(function() {
		jQuery('#bibItemForm').submit(function(e){		
			jQuery('#mediaBibliographyInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Bibliography', 'save'); ?>',
				jQuery('#bibItemForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>
<?php
	}
	if($pn_specimen_id){
?>
<script type='text/javascript'>
	jQuery(document).ready(function() {
		jQuery('#bibItemForm').submit(function(e){		
			jQuery('#specimenBibliographyInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Bibliography', 'save'); ?>',
				jQuery('#bibItemForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>
<?php
	}
?>