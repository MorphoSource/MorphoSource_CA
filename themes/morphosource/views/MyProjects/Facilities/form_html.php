<?php
	$t_item = $this->getVar("item");
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	
	# --- formatting varibales
	# --- all fields in float_fields array  will be floated to the left
	$va_float_fields = array("address1", "address2", "city", "stateprov", "postalcode", "country", "created_on", "last_modified_on");
	# --- all fields in clear_fields array  will have a clear output after them
	$va_clear_fields = array("address2", "stateprov", "country", "last_modified_on");
	
if (!$this->request->isAjax()) {
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Facility"); ?>
	</H1>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'itemForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
		print "<div style='float:right;'>".caNavLink($this->request, _t("Back"), "button buttonSmall", "MyProjects", $this->request->getController(), "listItems")."</div>";
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#itemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
?>
	</div><!-- end formButtons -->
<?php
}else{
?>
	<H2 class="ltBlueBottomRule" style="margin-bottom:10px;">
		<?php print _t("Facility Information"); ?>
	</H2>
	<div class="ltBlueBottomRule" style="margin-bottom:15px;">
<?php
}
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "created_on":
			case "last_modified_on":
			case "approval_status":
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
if (!$this->request->isAjax()) {
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#itemForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
		if($t_item->get($ps_primary_key)){
			print "&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", $this->request->getController(), "Delete", array($ps_primary_key => $t_item->get($ps_primary_key)));
		}
?>
	</div><!-- end formButtons -->
</form>
</div>
<?php
}else{
?>
	</div><!-- end ltBlueBottomRule -->
	<!-- form is loaded via AJAX as part of media form - check name of facility is entered to avoid errors when laded in the media info form -->
<?php
	if($this->getVar("media_id")){
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#mediaForm').submit(function(){
				if($('#name').val() == ''){
					alert("Pease enter the name of the facility");
					return false;
				}else{
					return true;
				}
			});
		});
	</script>
<?php
	}
}
?>