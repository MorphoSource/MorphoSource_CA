`<?php
	$vn_specimen_id = $this->request->getParameter("specimen_id", pInteger);
	$t_specimen = new ms_specimens();
	
	$t_item = $this->getVar("item");
	$vn_media_id = $t_item->get("media_id");
	
	if (!$vn_media_id) { // new media
		$t_item->set('specimen_id', $vn_specimen_id);	// if specimen_id is present on request for newly created media set it here
	}
	
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
# --- formatting variables
# --- all fields in float_fields array  will be floated to the left
$va_float_fields = array("scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "created_on", "approval_status");
# --- all fields in clear_fields array  will have a clear output after them
$va_clear_fields = array("scanner_z_resolution", "scanner_watts", "scanner_wedge", "approval_status");

?>
	<div id='formArea'>
	
<?php
print caFormTag($this->request, 'save', 'mediaForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="formButtons tealTopBottomRule">
<?php
if (!$this->request->isAjax()) {
	print "<div style='float:right;'>".caNavLink($this->request, _t("Back to Project Page"), "button buttonSmall", "MyProjects", "Dashboard", "Dashboard")."</div>";
}else{
	print "<div style='float:right;'>".caNavLink($this->request, _t("Cancel"), "button buttonSmall", "MyProjects", "Media", "MediaInfo", array("media_id" => $vn_media_id))."</div>";
}
?>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#mediaForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if (($vn_specimen_id > 0) && (!$vn_media_id)) {
?>
	<div style="width: 100%; text-align: center;"><h2><em>Will be added to specimen <strong><?php print $t_specimen->getSpecimenName($vn_specimen_id); ?></strong> upon save</em></h2>
<?php
		}

		if($vn_media_id){
			print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Media", "Delete", array("id" => $vn_media_id));
		}
}
?>
	</div><!-- end formButtons -->

<?php
if (!$this->request->isAjax()) {
	print "<div style='float:left; width:430px; padding-top:10px;'>";
}
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		if($va_errors[$vs_f]){
			print "<div class='formErrors'>".$va_errors[$vs_f]."</div>";
		}
		switch($vs_f){
			case "scanner_type":
				if (!$this->request->isAjax()) {
					print "</div><!-- end float --><div style='float:left; width:420px;'>";
				}
				print "<H2 class='ltBlueBottomRule' style='width:390px; margin: 10px 0px 10px 0px;'>Scanner Information</H2>";
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
			case "is_copyrighted":
				print "<H2 class='ltBlueBottomRule' style='width:390px; margin:20px 0px 10px 0px;'>Copyright Information</H2>";
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				print "<div id='copyrightBlock' style='display:none;'>";
			break;
			# -----------------------------------------------
			case "copyright_info":
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>");
				print "</div><!-- end copyrightBlock -->";
			break;
			# -----------------------------------------------
			case "created_on":
			case "last_modified_on":
				if($t_item->get($vs_f)){
					print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."' style='padding-right:25px;'>^LABEL<br>^ELEMENT</div>");
				}
			break;
			# -----------------------------------------------
			case "facility_id":
				print "<div id='facilityInfo'><div class='formLabel'>";
				print "Enter the facility this file was created at:<br/>".caHTMLTextInput("media_facility_lookup", array("id" => 'msFacilityID', 'class' => 'lookupBg', 'value' => $this->getVar("facility_name")), array('width' => "200px", 'height' => 1));
				print "</div>";
				print "<input type='hidden' id='facility_id' name='facility_id' value='".$t_item->get("facility_id")."'>";
				print "</div>";
			break;
			# -----------------------------------------------
			case "media":
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL".(($t_item->get("media")) ? " replacement" : "")."<br>^ELEMENT</div>");
			break;
			# -----------------------------------------------
			default:
				$vs_suffix = $t_item->getFieldInfo($vs_f, 'SUFFIX');
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT {$vs_suffix}</div>");
			break;
			# -----------------------------------------------
		}
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		}
	}
if (!$this->request->isAjax()) {
	print "</div><!-- end float -->";
	print "<div style='clear:both;'><!--empty--></div>";
}
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#mediaForm').submit(); return false;"><?php print _t("Save"); ?></a>
<?php
if (!$this->request->isAjax()) {
		if($vn_media_id){
			print caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Media", "Delete", array("id" => $vn_media_id));
		}
}
?>
	</div><!-- end formButtons -->
</form>
</div>
<script type='text/javascript'>
	$(document).ready(function(){
		if ($('input[name="is_copyrighted"]').attr('checked')) {
			$('#copyrightBlock').slideDown(200);
		}
		$('input[name="is_copyrighted"]').click(function() {
			if ($('input[name="is_copyrighted"]').attr('checked')) {
				$('#copyrightBlock').slideDown(200);
			} else {
				$('#copyright_permission').val('0');
				$('#copyright_license').val('0');
				$('#copyright_info').val('');
				$('#copyrightBlock').slideUp(200);
			}
		});
	});

	jQuery('#msFacilityID').autocomplete(
		{ 
			source: '<?php print caNavUrl($this->request, 'lookup', 'Facilities', 'Get', array("max" => 500, "quickadd" => true)); ?>', 
			minLength: 3, delay: 800, html: true,
			select: function(event, ui) {
				var facility_id = parseInt(ui.item.id);
				if (facility_id < 1) {
					// nothing found...
					//alert("Create new facility since returned id was " + facility_id);
					jQuery("#facilityInfo").load("<?php print caNavUrl($this->request, 'MyProjects', 'Facilities', 'form', array('media_id' => $t_item->get('media_id'))); ?>");
				} else {
					// found an id
					//alert("found facility id: " + facility_id);
					jQuery('#facility_id').val(facility_id);
					//alert("facility id set to: " + jQuery('#facility_id').val());
				}
			}
		}
	).click(function() { this.select(); });
</script>