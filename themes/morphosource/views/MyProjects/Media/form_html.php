<?php
	$t_item = $this->getVar("item");
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
if (!$this->request->isAjax()) {
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Media Information"); ?>
	</H1>
<?php
}
?>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'mediaForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
if (!$this->request->isAjax()) {
		print "<div style='float:right;'>".caNavLink($this->request, _t("Back to Project Page"), "button buttonSmall", "MyProjects", "Dashboard", "Dashboard")."</div>";
}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#mediaForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($t_item->get("media_id")){
			print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Media", "Delete", array("id" => $t_item->get("media_id")));
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
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#mediaForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($t_item->get("media_id")){
			print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Media", "Delete", array("id" => $t_item->get("media_id")));
		}
}
?>
	</div><!-- end formButtons -->
</form>
</div>