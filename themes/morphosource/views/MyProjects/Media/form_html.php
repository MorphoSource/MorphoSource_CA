<?php
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
			# -----------------------------------------------
			case "facility_id":
				if (!$this->request->isAjax()) {
					print "</div><!-- end float --><div style='float:left; width:420px;'>";
				}
				print "<H2 class='ltBlueBottomRule' style='width:390px; margin: 10px 0px 10px 0px;'>Scanner Information</H2>";
				print "<div id='facilityInfo'>"; //<div class='formLabel'>";
				//print "Enter the facility this file was created at:<br/>".caHTMLTextInput("media_facility_lookup", array("id" => 'msFacilityID', 'class' => 'lookupBg', 'value' => $this->getVar("facility_name")), array('width' => "375px", 'height' => 1));
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>", array('id' => 'msFacilityID', 'nullOption' => '-'));
				print "</div>";
				//print "<input type='hidden' id='facility_id' name='facility_id' value='".$t_item->get("facility_id")."'>";
				//print "</div>";
			break;
			# -----------------------------------------------
			case "scanner_id":
				print $t_item->htmlFormElement($vs_f,"<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>^LABEL<br>^ELEMENT</div>", array('id' => 'msScannerID', 'nullOption' => '-'));
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
			case "media":
				$vs_media_url = $t_item->getMediaUrl('media', 'original');
				
				print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>";
				print $t_item->htmlFormElement($vs_f, "^LABEL".(($vs_media_url) ? " replacement" : "")."<br>^ELEMENT");
				
				if ($vs_media_url) {
					print "<div style='float: right; width: 125px;'>".caHTMLCheckboxInput("updatePreviews", array('value' => '1'))." Update preview icons only</div>";
				}
				print "</div>";
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
	jQuery(document).ready(function(){
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

	var scannerListByFacilityID = <?php print json_encode($this->getVar('scannerListByFacilityID')); ?>;
	
	jQuery('#msFacilityID').bind('change', function(event) {
				var facility_id = jQuery('#msFacilityID').val();
				console.log("facilityID", facility_id);
				if (facility_id < 1) {
					return;
				} else {
					// Load scanner list into drop-down
					var optionsAsString = "<option value=''>-</option>";
					var scannerList = scannerListByFacilityID[facility_id];
					console.log(scannerList);
					if (scannerList) {
						for(var scanner_id in scannerList) {
							optionsAsString += "<option value='" + scanner_id + "'>" + scannerList[scanner_id].name + "</option>";
						}
					}
					jQuery("select#msScannerID").find('option').remove().end().append(jQuery(optionsAsString));
				}
			}
	);
</script>