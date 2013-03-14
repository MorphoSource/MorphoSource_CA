<?php
	$t_item = $this->getVar("item");
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	$t_item2 = $this->getVar("item2");
	$va_fields2 = $t_item2->getFormFields();
if (!$this->request->isAjax()) {
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Taxonomic Information"); ?>
	</H1>
<?php
}
?>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'itemForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
if (!$this->request->isAjax()) {
		print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "MyProjects", $this->request->getController(), "listItems")."</div>";
}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#itemForm').submit(); return false;"><?php print _t("Save"); ?></a>
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
				print $t_item2->htmlFormElement($vs_f,"<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
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
					print $t_item->htmlFormElement($vs_f,"<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			default:
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
		}
	}
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#itemForm').submit(); return false;"><?php print _t("Save"); ?></a>
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