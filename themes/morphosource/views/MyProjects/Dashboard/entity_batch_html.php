<?php
	$vs_message = $this->getVar("message");
	$t_media = new ms_media();
	$t_project = $this->getVar("project");

	// Entity viewer batch editing menu
	print "<div id='entityMediaBatchFormContainer'>";

	// General options tab menu
	print "<div id='mediaBatchGeneralContainer' style=''>";

	if ($vs_message['general']) {
		print "<div class='formErrors'>".$vs_message['general']."</div>";
	}
	
	print caFormTag($this->request, 'batchGeneralSave', 'batchGeneralForm', 
		null, 'post', 'multipart/form-data', '', 
		array('disableUnsavedChangesWarning' => true));

	print "<div id='batchColLeft'>";

	// Media bibliographic citation
	print "<div id='mediaBibLookupContainer' ".
		"style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Media bibliography</h2>";
	print "<div class='formLabel entityBatchFormItem'>Look up a bibliographic 
		citation:</br>";
	print caHTMLTextInput('bibliography_lookup', 
		array('id' => 'msBibliographyID', 'class' => 'lookupBg'), 
		array('width' => '350px', 'height' => 1));
	print "<input type='hidden' value='' name='bibliography_id' ".
		"id='bibliography_id'>";
	print "</div>";
	print "</div><!-- end mediaBibLookupContainer -->";

	// Media citation instructions
	print "<div id='mediaCitationContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Media citation instructions</h2>";
	print "<div class='formLabel entityBatchFormItem' style='width: 390px;'>".
		$t_media->htmlFormElement("media_citation_instruction1", "^ELEMENT").
		"provided access to these data".
		$t_media->htmlFormElement("media_citation_instruction2", "^ELEMENT").
		$t_media->htmlFormElement("media_citation_instruction3", "^ELEMENT").
		". The files were downloaded from www.MorphoSource.org, 
		Duke University.</div>";
	print "</div><!-- end mediaCitationContainer -->";

	// Grant support
	print "<div id='mediaGrantSupportContainer' ".
		"style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Support information:</h2>";
	print "<div class='formLabel entityBatchFormItem'>Grant Support:</br>";
	print $t_media->htmlFormElement('grant_support', '^ELEMENT', 
		array('width' => '350px'));
	print "</div>";
	print "</div><!-- end mediaGrantSupportContainer -->";

	print "</div><!-- end batchColLeft -->";

	print "<div id='batchColRight'>";

	// Copyright information
	print "<div id='mediaCopyrightContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Copyright information</h2>";
	print "<div class='formLabel entityBatchFormItem'>Permission:</br>";
	print $t_media->htmlFormElement('copyright_permission', '^ELEMENT', 
		array('width' => '350px'))."</div>";
	print "<div class='formLabel entityBatchFormItem'>License:</br>";
	print $t_media->htmlFormElement('copyright_license', '^ELEMENT', 
		array('width' => '350px'))."</div>";
	print "<div class='formLabel entityBatchFormItem'>Copyright Holder:</br>";
	print $t_media->htmlFormElement('copyright_info', 
		'^ELEMENT', array('width' => '350px'))."</div>";
	print "</div><!-- end mediaCopyrightContainer -->";

	// Edit publication status 
	print "<div id='batchPublicationLinkContainer' 
		style='margin: 15px 0px 0px 0px; width: 360px;'>";

	print "<h2>Publication information</h2>";
	print "<div class='formLabel entityBatchFormItem'>Publication Status of Media Groups:</br>";
	print $t_media->htmlFormElement('published', '^ELEMENT', array('width' => '350px'))."</div>";
	
	$va_members = $t_project->getMembers();
	if(sizeof($va_members)){
		print "<div class='formLabel entityBatchFormItem'>Download Requests Reviewed By:</br>";
		print "<select name='reviewer_id' id='reviewer_id' style='width: 350px;'".(($t_media->get("published") == 2) ? "" : "disabled").">\n";
		print "<option value=''>"._t("Use project default")."</option>\n";
		foreach($va_members as $va_member){
			if($va_member["membership_type"] == 1){
				print "<option value='".$va_member["user_id"]."' ".(($t_media->get("reviewer_id") == $va_member["user_id"]) ? "selected" : "").">".$va_member["fname"]." ".$va_member["lname"].", ".$va_member["email"]."</option>\n";
			}
		}
		print "</select>";
		print "</div>";
	}

	print "<div style='margin-left: 5px;'>".
		caNavLink($this->request, 
			"Batch edit publication of media files in media groups <i class='fa fa-external-link'></i>", 
			"", "MyProjects", "Media", "reviewPublicationSettings").
		"</div>";
	print "</div><!-- end batchPublicationLinkContainer -->";

	print "</div><!-- end batchColRight -->";

	print "</form><!-- end batchGeneralForm -->";
	print "</div><!-- end mediaBatchGeneralContainer -->";

	// Scan origin options tab menu
	print "<div id='mediaBatchScanOriginContainer' style='display: none;'>";
		
	print caFormTag($this->request, 'batchScanOriginSave', 
		'batchScanOriginForm', null, 'post', 'multipart/form-data', '', 
		array('disableUnsavedChangesWarning' => true));

	print "<div id='batchColLeft'>";

	// Facility and scanner options
	print "<div id='mediaFacilityScannerContainer' 
		style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Facility and scanner</h2>";
	print "<div class='formLabel entityBatchFormItem'>Find the facilty this 
		media file was created at:</br>";
	print $t_media->htmlFormElement('facility_id', '^ELEMENT', 
		array('id' => 'msFacilityID', 'nullOption' => '-', "width" => '350px')).
		"</div>";
	print "<div>";
	print "<div class='formLabel entityBatchFormItem'>Choose scanner used:</br>";
	print $t_media->htmlFormElement('scanner_id', '^ELEMENT', 
		array('id' => 'msScannerID', 'nullOption' => '-', "width" => '350px')).
		"</div>";
	print "</div>";
	print "</div><!-- end mediaFacilityScannerContainer -->";

	// Scanner Options
	$va_scan_option_fields = array('scanner_x_resolution', 
		'scanner_y_resolution', 'scanner_z_resolution', 'scanner_voltage', 
		'scanner_amperage', 'scanner_watts', 'scanner_exposure_time', 
		 'scanner_filter', 'scanner_projections', 'scanner_frame_averaging',  
		'scanner_wedge');
	$va_clear_fields = array('scanner_z_resolution', 'scanner_watts', 
		'scanner_filter');
	$std_size_fields = array('scanner_x_resolution', 
		'scanner_y_resolution', 'scanner_z_resolution', 'scanner_voltage', 
		'scanner_amperage', 'scanner_watts', 'scanner_exposure_time');

	print "<div id='mediaScannerOptionsContainer' 
		style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Scanner options</h2>";
	foreach ($va_scan_option_fields as $f) {
		print "<div id='scanOption".str_replace('_', '', ucwords($f, '_')).
			"' style='margin: 0px 0px 0px 0px;".
			((in_array($f, $std_size_fields)) ? " width: 120px;": "").
			" float: left;'>";
		$vs_suffix = $t_media->getFieldInfo($f, 'SUFFIX');
		print $t_media->htmlFormElement($f,
			"<div class='formLabel'>^LABEL<br>^ELEMENT {$vs_suffix}".
			"</div>");
		print "</div><!-- end scanOrigin".
			str_replace('_', '', ucwords($f, '_'))."-->";
		if(in_array($f, $va_clear_fields)){
			print "<div style='clear: both;'><!-- empty --></div>";
		}
	}
	print "</div><!-- end mediaScannerOptionsContainer -->";

	print "</div><!-- end batchColLeft -->";

	print "<div id='batchColRight'>";

	// Calibration options
	$scanner_calibration_fields = array(
		'scanner_calibration_shading_correction', 
		'scanner_calibration_flux_normalization', 
		'scanner_calibration_geometric_calibration');

	print "<div id='mediaCalibrationOptionsContainer' 
		style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Calibration options</h2>";
	print "<div style='margin: 0px 0px 15px 5px;'>";
	foreach ($scanner_calibration_fields as $sc_f) {
		print $t_media->htmlFormElement($sc_f,
			"<div class='formLabel' style='display: inline-block; ".
			"width: 135px; margin: 0px 0px 0px 0px;'>^LABEL ".
			"<span style='margin: 0px 1px 0px 1px;'></span> ".
			"^ELEMENT {$vs_suffix}</div>");
	}
	print "</div>";
	print "<div class='formLabel entityBatchFormItem'>
		Calibration description:</br>";
	print $t_media->htmlFormElement('scanner_calibration_description', 
		'^ELEMENT')."</div>";
	print "</div><!-- end mediaCalibrationOptionsContainer -->";

	// Other options
	print "<div id='mediaOthersContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Other information</h2>";
	print "<div class='formLabel entityBatchFormItem'>Technicians:</br>";
	print $t_media->htmlFormElement('scanner_technicians', '^ELEMENT')."</div>";
	print "</div>";
	print "</div><!-- end mediaOthersContainer -->";

	print "</div><!-- end batchColRight -->";

	print "</form><!-- end batchScanOriginForm -->";

	print "</div><!--  end entityMediaBatchFormContainer -->";

	// Save and cancel buttons below form panes
	print "<div id='batchButtonContainer' ".
		"style='margin: 15px 0px 0px 0px; text-align: center;'>";
	print "<a href='#' name='saveButton' class='button buttonLarge batchButton' ".
		"id='batchSaveButton' style='margin: 5px 20px 0px 0px;'>".
		_t("Save")."</a>";
	print "<a href='#' name='cancelButton' class='button buttonLarge batchButton' ".
		"id='batchCancelButton' style='margin: 5px 0px 0px 20px;'>".
		_t("Cancel")."</a>";
	print "</div><!-- end batchButtonContainer -->";

?>

<script>
	$('#published').prepend($("<option></option>").attr("value","").text("")); 
	$('#published :nth-child(1)').prop('selected', true);
	$('#reviewer_id').prepend($("<option></option>").attr("value","").text("")); 
	$('#reviewer_id :nth-child(1)').prop('selected', true);
	$('#reviewer_id :nth-child(2)').prop('value', -1);
	$('#copyright_permission').prepend($("<option></option>").attr("value","").text("")); 
	$('#copyright_permission :nth-child(1)').prop('selected', true);
	$('#copyright_license').prepend($("<option></option>").attr("value","").text("")); 
	$('#copyright_license :nth-child(1)').prop('selected', true);


	jQuery('#published').on('change', function() {
	  if($(this).val() == 2){
	  	$('#reviewer_id').prop("disabled", false);
	  }else{
	  	$('#reviewer_id').val('');
	  	$('#reviewer_id').prop("disabled", true);
	  }
	});
</script>