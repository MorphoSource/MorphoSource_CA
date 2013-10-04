<?php
	$t_item = $this->getVar("item");
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	$t_item2 = $this->getVar("item2");
	$va_fields2 = $t_item2->getFormFields();
	$pn_media_id = $this->getVar("media_id");
	$pn_specimen_id = $this->getVar("specimen_id");
	$ps_specimen_name = $this->getVar("specimen_name");

	# --- formatting variables
	# --- all fields in float_fields array  will be floated to the left
	$va_float_fields = array("common_name", "is_extinct", "genus", "species", "subspecies", "variety", "author", "year", "ht_supraspecific_clade", "ht_kingdom", "ht_phylum", "ht_class", "ht_subclass", "ht_order", "ht_suborder", "ht_superfamily", "ht_family", "ht_subfamily", "created_on", "last_modified_on");
	# --- all fields in clear_fields array  will have a clear output after them
	$va_clear_fields = array("is_extinct", "species", "variety", "year", "ht_phylum", "ht_subclass", "ht_suborder", "ht_subfamily", "last_modified_on");

	if($ps_specimen_name){
		print "<H2>Taxonomic Information For ".$ps_specimen_name."</H2>";
	}else{
		print '<div class="blueRule"><!-- empty --></div>';
		print "<H1>"._t("Taxonomic Information")."</H1>";
	}
?>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'taxonomyItemForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
if (!$this->request->isAjax()) {
		print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "MyProjects", $this->request->getController(), "listItems")."</div>";
}else{
	if($pn_media_id){
		print "<div style='float:right;'><a href='#' class='button buttonSmall' onclick='jQuery(\"#mediaSpecimenInfo\").load(\"".caNavUrl($this->request, 'MyProjects', 'Media', 'specimenLookup', array('media_id' => $pn_media_id))."\");'>"._t("Cancel")."</a></div>";
	}elseif($pn_specimen_id){
		print "<div style='float:right;'><a href='#' class='button buttonSmall' onclick='jQuery(\"#specimenTaxonomyInfo\").load(\"".caNavUrl($this->request, 'MyProjects', 'Specimens', 'specimenTaxonomyLookup', array('specimen_id' => $pn_specimen_id))."\");'>"._t("Cancel")."</a></div>";
	}
}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#taxonomyItemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
}
?>
	</div><!-- end formButtons -->

<?php
# --- output fields for ms_taxonomy	
	while (list($vs_f,$vs_field_info) = each($va_fields2)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "created_on":
			case "last_modified_on":
			case "project_id":
			case "user_id":
			case "notes":
				continue;
			break;
			# -----------------------------------------------
			default:
				print $t_item2->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
		}
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		}
	}
# --- fields for ms_taxonomy_names
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "justification":
			case "review_status":
			case "review_notes":
			case "reviewed_on":
			case "reviewed_by_id":
			case "is_primary":
				continue;
			break;
			# -----------------------------------------------
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
# --- if specimen_id is passed this form is being loaded as a quick add in another form and the new taxon needs to be linked to the specimen
if($pn_specimen_id){
	print "<input type='hidden' name='specimen_id' value='".$pn_specimen_id."'>";
}
# --- if media_id is passed this form is being loaded as a quick add in the specimen form of the media info page - need to redirect back to media info page
if($pn_media_id){
	print "<input type='hidden' name='media_id' value='".$pn_media_id."'>";
}
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#taxonomyItemForm').submit(); return false;"><?php print _t("Save"); ?></a>
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
		jQuery('#taxonomyItemForm').submit(function(e){		
			jQuery('#mediaSpecimenInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Taxonomy', 'save'); ?>',
				jQuery('#taxonomyItemForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>
<?php
	}elseif($pn_specimen_id){
?>
<script type='text/javascript'>
	jQuery(document).ready(function() {
		jQuery('#taxonomyItemForm').submit(function(e){		
			jQuery('#specimenTaxonomyInfo').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Taxonomy', 'save'); ?>',
				jQuery('#taxonomyItemForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>
<?php
	}
?>