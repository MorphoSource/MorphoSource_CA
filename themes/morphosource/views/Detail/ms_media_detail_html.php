<?php
	$t_media = $this->getVar("item");
	$vs_media = $t_media->getMediaTag("media", "medium");
	$va_bib_citations = $this->getVar("bib_citations");
?>
<div class="blueRule"><!-- empty --></div>
<H1>
<?php 
	if($vs_media){
		
	print _t("Media: M%1", $t_media->get("media_id"));
?>
</H1>
<div id="mediaDetail">
<?php
	
	$vn_width = 0;
	if($vs_media){
		$va_media_info = $t_media->getMediaInfo("media", "medium");
		$vn_width = $va_media_info["WIDTH"];
		print "<div class='mediaDetailMedia' style='width:".$vn_width."px;'><a href='#' onclick='msMediaPanel.showPanel(\"".caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'mediaViewer', array('media_id' => $t_media->getPrimaryKey()))."\"); return false;'>".$vs_media."</a></div>";
		print "<br style='clear: left;'/>\n";
		
if ($this->request->isLoggedIn()) {
		switch((int)$t_media->get('published')) {
				case 1:
					print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadMedia", array("media_id" => $t_media->get("media_id")))."</div>";
					break;
				case 2:
					if (is_array($va_prev_requests = $t_media->getDownloadRequests(null, array('user_id' => $this->request->getUserID(), 'status' => __MS_DOWNLOAD_REQUEST_NEW__))) && (sizeof($va_prev_requests) > 0)){
						print "<div style='float:right; clear: right; cursor:default;' class='button buttonLarge' onclick='return false;'>"._t("Access to Media Pending")."</div>";
					} else {
						if (is_array($va_prev_requests = $t_media->getDownloadRequests(null, array('user_id' => $this->request->getUserID(), 'status' => __MS_DOWNLOAD_REQUEST_APPROVED__))) && (sizeof($va_prev_requests) > 0)){
							print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadMedia", array("media_id" => $t_media->get("media_id")))."</div>";
						} elseif (is_array($va_prev_requests = $t_media->getDownloadRequests(null, array('user_id' => $this->request->getUserID(), 'status' => __MS_DOWNLOAD_REQUEST_DENIED__))) && (sizeof($va_prev_requests) > 0)){
							print "<div style='float:right; clear: right;'><a href='#' class='button buttonLarge'>"._t('You may not download this media')."</a></div>";
						} else {
							print "<div style='float:right; clear: right;'><a href='#' class='button buttonLarge' onclick='jQuery(\"#msMediaDownloadRequestFormContainer\").slideDown(250); return false;'>"._t("Request Download of Media")."</a></div>";
						}
					}
					print "<div id='msMediaDownloadRequestFormContainer'>\n";
					print caFormTag($this->request, 'RequestDownload', 'msMediaDownloadRequestForm', null, 'post', 'multipart/form-data', '_top', array('disableUnsavedChangesWarning' => true, 'noTimestamp' => true));
	?>
		<div class='msMediaDownloadRequestFormHelpText'>
			<?php print _t('The author will provide this media only upon request. Please explain how you plan to use this media below. The author will review your request and reply shortly.'); ?>
		</div>
	<?php
					$t_req = new ms_media_download_requests();
					print $t_req->htmlFormElement('request', "<div class='msMediaDownloadRequestFormLabel'>^LABEL<br/>^ELEMENT</div>");
					print caHTMLHiddenInput("media_id", array('value' => $t_media->getPrimaryKey()));
					print caHTMLHiddenInput("user_id", array('value' => $this->request->getUserID()));
					print caFormSubmitLink($this->request,_t('Send'), 'msMediaDownloadRequestFormSubmit', 'msMediaDownloadRequestForm');
					print "<a href='#' class='msMediaDownloadRequestFormCancel' onclick='jQuery(\"#msMediaDownloadRequestFormContainer\").slideUp(250); return false;'>"._t('Cancel')."</a>";
					print "</form>";
					print "</div>\n";
					//print "<br style='clear: both;'/>\n";
					break;
			}
} else {
	print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Login to download"), "button buttonLarge", "", "LoginReg", "form", array("media_id" => $t_media->get("media_id"), 'site_last_page' => 'MediaDetail'))."</div>";
}	
		}
	}
	print "<div ".(($vn_width) ? "style='width:".(830 - $vn_width)."px;'" : "").">";
	if($t_media->get("specimen_id")){
?>
		<div class="tealRule"><!-- empty --></div>
		<H2>Specimen Information</H2>
			<div class="unit">
<?php
		$t_specimen = new ms_specimens($vn_specimen_id = $t_media->get("specimen_id"));
		$vs_specimen_name = $t_specimen->getSpecimenName();
		if($vs_specimen_name){
			print "<b>Specimen:</b> ".caNavLink($this->request, $vs_specimen_name, '', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $vn_specimen_id))."<br/>";
		}
		$va_specimen_taxonomy = $t_specimen->getSpecimenTaxonomy();
		if(is_array($va_specimen_taxonomy) && sizeof($va_specimen_taxonomy)){
			print "<b>Specimen taxonomy:</b> ".join(", ", $va_specimen_taxonomy)."<br/>";
		}
		if($t_specimen->get("institution_id")){
			$t_institution = new ms_institutions($t_specimen->get("institution_id"));
			print "<b>Institution: </b>".$t_institution->get("name");
			if($t_institution->get("location_city")){
				print ", ".$t_institution->get("location_city");
			}
			if($t_institution->get("location_state")){
				print ", ".$t_institution->get("location_state");
			}
			if($t_institution->get("location_country")){
				print ", ".$t_institution->get("location_country");
			}
		}
?>
			</div><!-- end unit -->
<?php

	}
?>
		<div class="tealRule"><!-- empty --></div>
		<H2>Scan Information</H2>
			<div class="unit">
<?php

					$vs_mimetype = $t_media->getMediaInfo('media', 'original', 'MIMETYPE');
					$vs_media_class = caGetMediaClassForDisplay($vs_mimetype); 
					$vs_mimetype_name = caGetDisplayNameForMimetype($vs_mimetype);
					print "<b>Type: </b>{$vs_media_class} ({$vs_mimetype_name})<br/>\n";
					

					$va_properties = $t_media->getMediaInfo('media', 'original');
					print "<b>Filesize: </b>".caFormatFilesize($va_properties['PROPERTIES']['filesize'])."<br/>\n";
					
	$va_fields = $t_media->getFormFields();
	$va_media_display_fields = array("notes", "facility_id", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_type", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_check", "scanner_calibration_description", "scanner_technicians", "created_on", "created_on", "last_modified_on");
	foreach($va_fields as $vs_field => $va_field_attr){
		if(in_array($vs_field, $va_media_display_fields) && $t_media->get($vs_field)){
			switch($vs_field){
				case "facility_id":
					if($t_media->get("facility_id")){
						$t_facility = new ms_facilities($t_media->get("facility_id"));
						print "<b>Facility: </b>".$t_facility->get("name")."<br/>";
					}
				break;
				# ------------------------------
				case "is_copyrighted":
					print "<b>".$va_field_attr["LABEL"].": </b>";
					if($t_media->get("is_copyrighted")){
						print "Yes";
					}else{
						print "No";
					}
					print "<br/>";
				break;
				# ------------------------------
				case "copyright_permission":
				case "copyright_license":
				case "scanner_type":
				case "scanner_calibration_check":
					print "<b>".$va_field_attr["LABEL"].": </b>".$t_media->getChoiceListValue($vs_field, $t_media->get($vs_field))."<br/>";
				break;
				# ------------------------------
				default:
					print "<b>".$va_field_attr["LABEL"].": </b>".($vs_field_value = $t_media->get($vs_field));
					if (is_numeric($vs_field_value) && ($vs_suffix = $t_media->getFieldInfo($vs_field, 'SUFFIX'))) {
						print " {$vs_suffix}";
					}
					print "<br/>";
				break;
				# ------------------------------
			}
		}
	}
?>
	</div><!-- end unit -->
<?php
	if(is_array($va_bib_citations) && sizeof($va_bib_citations)){
?>
		<div class="tealRule"><!-- empty --></div>
		<H2>Bibliography</H2>
			<div class="unit">
<?php
		foreach($va_bib_citations as $vn_link_id => $va_citation_info){
			print "<div class='mediaDetailcitation'>".$va_citation_info["citation"];
			if($va_citation_info["page"]){
				print "<br/>Page(s): ".$va_citation_info["page"];
			}
			print "</div>";
		}
	}
	print "</div>";
?>
</div>