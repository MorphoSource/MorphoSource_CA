<?php
	$t_media = $this->getVar("item");
	$va_bib_citations = $this->getVar("bib_citations");
	$vb_show_edit_link = $this->getVar("show_edit_link");
	$vb_show_download_link = $this->getVar("show_download_link");
	
	# --- get all media files linked to this media record
	$o_db = new Db();
	$q_media_files = $o_db->query("SELECT media, media_file_id, side, element, title, notes FROM ms_media_files where media_id = ? and published = 1", $t_media->get("media_id"));

	$t_media_file = new ms_media_files();
?>
<div class="blueRule"><!-- empty --></div>
<?php
	$vs_back_link = "";
	switch(ResultContext::getLastFind($this->request, "ms_specimens")){
		case "specimen_browse":
			$vs_back_link = caNavLink($this->request, _t("Back"), 'button buttonLarge', '', 'Browse', 'Index', array(), array('id' => 'back'));
		break;
		# ----------------------------------
		case "basic_search":
			$vs_back_link = caNavLink($this->request, _t("Back"), 'button buttonLarge', '', 'Search', 'Index', array(), array('id' => 'back'));
		break;
		# ----------------------------------
	}
	if (($this->getVar('is_in_result_list'))) {
		if ($this->getVar('next_id') > 0) {
			print "<div style='float:right; padding:15px 0px 0px 15px;'>".caNavLink($this->request, _t("Next"), 'button buttonLarge', 'Detail', 'MediaDetail', 'Show', array('media_id' => $this->getVar('next_id')), array('id' => 'next'))."</div>";
		}
		print "<div style='float:right; padding:15px 0px 0px 15px;'>".$vs_back_link."</div>";
		if ($this->getVar('previous_id')) {
			print "<div style='float:right; padding:15px 0px 0px 15px;'>".caNavLink($this->request, _t("Previous"), 'button buttonLarge', 'Detail', 'MediaDetail', 'Show', array('media_id' => $this->getVar('previous_id')), array('id' => 'previous'))."</div>";
		}
	}
?>
<H1>
<?php
	print _t("Media: M%1", $t_media->get("media_id"));
?>
</H1>
<div id="mediaDetail">
<?php
	$vn_width = 0;
	if($q_media_files->numRows()){
		if($q_media_files->numRows() == 1){
			$q_media_files->nextRow();
			$va_properties = $q_media_files->getMediaInfo('media', 'medium');
			$vn_width = $va_properties["WIDTH"];
			$q_media_files->seek(0);
		}else{
			$vn_width = 420;
		}
		print "<div class='mediaDetailMedia' style='width:".$vn_width."px;'>";
?>
		<H2 style="text-align:right;"><?php print $q_media_files->numRows(); ?> media file<?php print ($q_media_files->numRows() == 1) ? "" : "s"; ?></H2>
		<div id='mediaDetailImageScrollArea'>
<?php
		while($q_media_files->nextRow()){
?>
			<div class="mediaDetailImage" id="media<?php print $q_media_files->get("media_file_id"); ?>">
				<a href="#" onclick="msMediaPanel.showPanel('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'mediaViewer', array('media_id' => $t_media->getPrimaryKey(), 'media_file_id' => $q_media_files->get("media_file_id"))); ?>'); return false;"><?php print $q_media_files->getMediaTag("media", "preview190"); ?></a>
<?php 
				print "<div class='mediaDetailImageCaption'>";
				print "<b class='blueText'>M".$t_media->get("media_id")."-".$q_media_files->get("media_file_id")."</b><br/>";
				$va_versions = $q_media_files->getMediaVersions('media');
				$va_properties = $q_media_files->getMediaInfo('media', in_array('_archive_', $va_versions) ? '_archive_' : 'original');
				print msGetMediaFormatDisplayString($q_media_files).", ".caFormatFilesize(isset($va_properties['FILESIZE']) ? $va_properties['FILESIZE'] : $va_properties['PROPERTIES']['filesize'])."<br/>";
				if($q_media_files->get("title")){
					print $q_media_files->get("title")."<br/>";
				}
				$vs_side = $t_media->getChoiceListValue("side", $t_media->get("side"));
				if($q_media_files->get("side")){
					$vs_side = $t_media_file->getChoiceListValue("side", $q_media_files->get("side"));
				}
				$vs_element = $t_media->get("element");
				if($q_media_files->get("element")){
					$vs_element = $q_media_files->get("element");
				}
				if($vs_element){
					print $vs_element.", ";
				}
				if($vs_side){
					print $vs_side;
				}
				if($vs_notes = $q_media_files->get("notes")){
					print "<p>".$vs_notes."</p>";
				}
				if($this->request->isLoggedIn() && $t_media->userCanDownloadMedia($this->request->user->get("user_id"))){
					print "<div class='mediaFileButtons'>";
					print caNavLink($this->request, "<i class='fa fa-download'></i>", "button buttonSmall", "Detail", "MediaDetail", "DownloadMedia", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $t_media->get("media_id"), "download" => 1), array("title" => "Download file"));
					print "<span>".addToCartLink($this->request, $q_media_files->get("media_file_id"), $this->request->user->get("user_id"), null, array("class" => "button buttonSmall"))."</span>";
					print "</div>";
				}
				
				print "</div>";
?>
			</div><!-- end mediaImage -->
<?php
		}
		print "</div><!-- end scrollArea -->";
if ($this->request->isLoggedIn()) {
		if($vb_show_download_link){
			print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download All Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadAllMedia", array("media_id" => $t_media->get("media_id")))."</div>";		
		}else{
			switch((int)$t_media->get('published')) {
				case 1:
					print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download All Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadAllMedia", array("media_id" => $t_media->get("media_id")))."</div>";
					break;
				case 2:
					if (is_array($va_prev_requests = $t_media->getDownloadRequests(null, array('user_id' => $this->request->getUserID(), 'status' => __MS_DOWNLOAD_REQUEST_NEW__))) && (sizeof($va_prev_requests) > 0)){
						print "<div style='float:right; clear: right; cursor:default;' class='button buttonLarge' onclick='return false;'>"._t("Access to Media Pending")."</div>";
					} else {
						if (is_array($va_prev_requests = $t_media->getDownloadRequests(null, array('user_id' => $this->request->getUserID(), 'status' => __MS_DOWNLOAD_REQUEST_APPROVED__))) && (sizeof($va_prev_requests) > 0)){
							print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download All Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadAllMedia", array("media_id" => $t_media->get("media_id")))."</div>";
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
		}
		if($vb_show_edit_link){
			print "<div style='float:right; padding-right:10px;'>".caNavLink($this->request, _t("Edit"), "button buttonLarge", "MyProjects", "Media", "mediaInfo", array("media_id" => $t_media->get("media_id"), "select_project_id" => $t_media->get("project_id")))."</div>";
		}
} else {
		print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Login to download"), "button buttonLarge", "", "LoginReg", "form", array("media_id" => $t_media->get("media_id"), 'site_last_page' => 'MediaDetail'))."</div>";
}
		print "</div><!-- end mediaDetailMedia -->";
	}else{
?>
		<div class='formErrors'>There are no media files linked to this media record</div>
<?php
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
		if($t_media->get("element")){
			print "<b>Element:</b> ".$t_media->get("element")."<br/>";
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

// 					$vs_mimetype = $t_media->getMediaInfo('media', 'original', 'MIMETYPE');
// 					$vs_media_class = caGetMediaClassForDisplay($vs_mimetype); 
// 					$vs_mimetype_name = caGetDisplayNameForMimetype($vs_mimetype);
// 					print "<b>Type: </b>{$vs_media_class} ({$vs_mimetype_name})<br/>\n";
// 					
// 					$va_versions = $t_media->getMediaVersions('media');
// 					$va_properties = $t_media->getMediaInfo('media', in_array('_archive_', $va_versions) ? '_archive_' : 'original');
// 					print "<b>Filesize: </b>".caFormatFilesize(isset($va_properties['FILESIZE']) ? $va_properties['FILESIZE'] : $va_properties['PROPERTIES']['filesize'])."<br/>\n";
					
	$va_fields = $t_media->getFormFields();
	$va_media_display_fields = array("title", "notes", "facility_id", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_type", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_shading_correction", "scanner_calibration_description", "scanner_technicians", "grant_support", "media_citation_instruction1", "created_on", "last_modified_on");
	foreach($va_fields as $vs_field => $va_field_attr){
		if(in_array($vs_field, $va_media_display_fields) && (in_array($vs_field, array("scanner_wedge", "scanner_calibration_shading_correction")) || $t_media->get($vs_field))){
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
					print "<b>".$va_field_attr["LABEL"].": </b>".$t_media->getChoiceListValue($vs_field, $t_media->get($vs_field))."<br/>";
				break;
				# ------------------------------
				case "scanner_calibration_shading_correction":
					print "<b>Scanner calibrations: </b>";
					$va_calibration_options = array();
					if($t_media->get("scanner_calibration_shading_correction")){
						$va_calibration_options[] = "shading correction";
					}
					if($t_media->get("scanner_calibration_flux_normalization")){
						$va_calibration_options[] = "flux normalization";
					}
					if($t_media->get("scanner_calibration_geometric_calibration")){
						$va_calibration_options[] = "geometric calibration";
					}
					if(sizeof($va_calibration_options)){
						print implode(", ", $va_calibration_options);
					}else{
						print "No calibrations are listed";
					}
					print "<br/>";
				break;
				# ------------------------------
				case "scanner_wedge":
					print "<b>".$va_field_attr["LABEL"].": </b>".(($t_media->get($vs_field)) ? $t_media->get($vs_field) : "air");
					print "<br/>";
				break;
				# ------------------------------
				case "media_citation_instruction1":
					print "<b>".$va_field_attr["LABEL"].": </b>".$t_media->getMediaCitationInstructions();
					print "<br/>";
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
			if($va_citation_info["media_file_id"]){
				print "<br/><i>References M".$t_media->get("media_id")."-".$va_citation_info["media_file_id"]."</i>";
			}
			print "</div>";
		}
	}
	print "</div>";
?>
</div>