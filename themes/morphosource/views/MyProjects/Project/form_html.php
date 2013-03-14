<?php
	$t_item = $this->getVar("project");
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Project Information"); ?>
	</H1>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'projectForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
		if($t_item->get("project_id")){
			print "<div style='float:right;'>".caNavLink($this->request, _t("Back to Project Info"), "button buttonSmall", "MyProjects", "Dashboard", "Dashboard")."</div>";
		}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="document.forms.projectForm.submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get("project_id")){
			print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Project", "Delete", array("id" => $t_item->get("project_id")));
		}
?>
	</div><!-- end formButtons -->

<?php
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "published_on":
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
		<a href="#" name="save" class="button buttonSmall" onclick="document.forms.projectForm.submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get("project_id")){
			print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Project", "Delete", array("id" => $t_item->get("project_id")));
		}
?>
	</div><!-- end formButtons -->
</form>
</div>