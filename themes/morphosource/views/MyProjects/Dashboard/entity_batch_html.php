<?php
	$vs_message = $this->getVar("message");
	$t_media = new ms_media();

	print "<div id='entityMediaBatchFormContainer'>";

	// media batch edit general form
	print "<div id='mediaBatchGeneralContainer' style=''>";

	if ($vs_message['general']) {
		print "<div class='formErrors'>".$vs_message['general']."</div>";
	}
	
	print caFormTag($this->request, 'batchGeneralSave', 'batchGeneralForm', 
		null, 'post', 'multipart/form-data', '', 
		array('disableUnsavedChangesWarning' => true));


	print "<div id='batchColLeft'>";

// media bibliographic citation
	print "<div id='mediaBibLookupContainer' ".
		"style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Media bibliography</h2>";
	print "<div class='formLabel entityBatchFormItem'>Look up a bibliographic citation:</br>";
	print caHTMLTextInput('bibliography_lookup', 
		array('id' => 'msBibliographyID', 'class' => 'lookupBg'), 
		array('width' => '350px', 'height' => 1));
	print "<input type='hidden' value='' name='bibliography_id' ".
		"id='bibliography_id'>";
	print "</div>";

	// if($vs_message['bibliography']){
	// 	print "<div class='formErrors'>".$vs_message['bibliography']."</div>";
	// }

	print "</div><!-- end mediaBibLookupContainer -->";

	// media citation instructions
	print "<div id='mediaCitationContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Media citation instructions</h2>";
	// print "<div class='formLabel'>Media citation instructions</div>";

	print "<div class='formLabel entityBatchFormItem' style='width: 390px;'>".
		$t_media->htmlFormElement("media_citation_instruction1", "^ELEMENT").
		"provided access to these data".
		$t_media->htmlFormElement("media_citation_instruction2", "^ELEMENT").
		$t_media->htmlFormElement("media_citation_instruction3", "^ELEMENT").
		". The files were downloaded from www.MorphoSource.org, Duke University.</div>";

	// if ($vs_message['media_citation']) {
	// 	print "<div class='formErrors'>".$vs_message['media_citation']."</div>";
	// }

	print "</div><!-- end mediaCitationContainer -->";

	// grant support
	print "<div id='mediaGrantSupportContainer' ".
		"style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Support information:</h2>";
	
	print "<div class='formLabel entityBatchFormItem'>Grant support:</br>";
	print $t_media->htmlFormElement('grant_support', '^ELEMENT', 
		array('width' => '350px'));
	print "</div>";

	// if($vs_message['grant_support']){
	// 	print "<div class='formErrors'>".$vs_message['grant_support']."</div>";
	// }

	print "</div><!-- end mediaGrantSupportContainer -->";

	print "</div><!-- end batchColLeft -->";

	print "<div id='batchColRight'>";

	// copyright information
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

	// if($vs_message['copyright']){
	// 	print "<div class='formErrors'>".$vs_message['copyright']."</div>";
	// }

	print "</div><!-- end mediaCopyrightContainer -->";

	print "<div id='batchPublicationLinkContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Publication information</h2>";
	print "<div style='margin-left: 5px;'>".caNavLink($this->request, _t("Click here to batch edit publication settings."), "", "MyProjects", "Media", "reviewPublicationSettings")."</div>";
	print "</div><!-- end batchPublicationLinkContainer -->";

	print "</div><!-- end batchColRight -->";

	


	print "</form><!-- end batchGeneralForm -->";
	print "</div><!-- end mediaBatchGeneralContainer -->";

	// media batch edit scan origin form
	print "<div id='mediaBatchScanOriginContainer' style='display: none;'>";
		
	// $va_scan_fields = array('facility_id', 'scanner_id', 'scanner_x_resolution', 
	// 	'scanner_y_resolution', 'scanner_z_resolution', 'scanner_voltage', 
	// 	'scanner_amperage', 'scanner_watts', 'scanner_exposure_time', 
	// 	'scanner_filter', 'scanner_projections', 'scanner_frame_averaging', 
	// 	'scanner_wedge', 'scanner_calibration', 
	// 	'scanner_calibration_description', 'scanner_technicians');
	$scanner_calibration_fields = array(
		'scanner_calibration_shading_correction', 
		'scanner_calibration_flux_normalization', 
		'scanner_calibration_geometric_calibration');
	// $va_float_fields = array('facility_id', 'scanner_id', 
	// 	'scanner_x_resolution', 'scanner_y_resolution', 'scanner_z_resolution', 
	// 	'scanner_voltage', 'scanner_amperage', 'scanner_watts', 
	// 	'scanner_exposure_time', 'scanner_filter', 'scanner_projections', 
	// 	'scanner_frame_averaging', 'scanner_wedge', 'scanner_calibration', 
	// 	'scanner_calibration_description', 'scanner_technicians');
	

	print caFormTag($this->request, 'batchScanOriginSave', 
		'batchScanOriginForm', null, 'post', 'multipart/form-data', '', 
		array('disableUnsavedChangesWarning' => true));

	print "<div id='batchColLeft'>";

	// Facility and scanner options
	print "<div id='mediaFacilityScannerContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Facility and scanner</h2>";

	print "<div class='formLabel entityBatchFormItem'>Find the facilty this media file was created at:</br>";
	print $t_media->htmlFormElement('facility_id', '^ELEMENT', array('id' => 'msFacilityID', 'nullOption' => '-', "width" => '350px'))."</div>";

	print "<div>";
	print "<div class='formLabel entityBatchFormItem'>Choose scanner used:</br>";
	print $t_media->htmlFormElement('scanner_id', '^ELEMENT', array('id' => 'msScannerID', 'nullOption' => '-', "width" => '350px'))."</div>";
	print "</div>";

	print "</div><!-- end mediaFacilityScannerContainer -->";

	// Scanner Options
	print "<div id='mediaScannerOptionsContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Scanner options</h2>";

	$va_scan_option_fields = array('scanner_x_resolution', 
		'scanner_y_resolution', 'scanner_z_resolution', 'scanner_voltage', 
		'scanner_amperage', 'scanner_watts', 'scanner_exposure_time', 
		 'scanner_filter', 'scanner_projections', 'scanner_frame_averaging',  
		'scanner_wedge');
	$va_clear_fields = array('scanner_z_resolution', 'scanner_watts', 'scanner_filter');
	$std_size_fields = array('scanner_x_resolution', 
		'scanner_y_resolution', 'scanner_z_resolution', 'scanner_voltage', 
		'scanner_amperage', 'scanner_watts', 'scanner_exposure_time');

	foreach ($va_scan_option_fields as $f) {
		print "<div id='scanOption".str_replace('_', '', ucwords($f, '_')).
			"' style='margin: 0px 0px 0px 0px;".((in_array($f, $std_size_fields)) ? " width: 120px;": "")." float: left;'>";
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
	print "<div id='mediaCalibrationOptionsContainer' style='margin: 15px 0px 0px 0px;'>";
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

	print "<div class='formLabel entityBatchFormItem'>Calibration description:</br>";
	print $t_media->htmlFormElement('scanner_calibration_description', '^ELEMENT')."</div>";

	print "</div><!-- end mediaCalibrationOptionsContainer -->";

	// Other options
	print "<div id='mediaOthersContainer' style='margin: 15px 0px 0px 0px;'>";
	print "<h2>Other information</h2>";
	print "<div class='formLabel entityBatchFormItem'>Technicians:</br>";
	print $t_media->htmlFormElement('scanner_technicians', '^ELEMENT')."</div>";
	print "</div><!-- end mediaOthersContainer -->";
	
	print "</div><!-- end batchColRight -->";

	print "</div>";

	print "</form><!-- end batchScanOriginForm -->";
	print "</div><!--  end entityMediaBatchFormContainer -->";

	// print "</div>";

	// save and cancel buttons
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