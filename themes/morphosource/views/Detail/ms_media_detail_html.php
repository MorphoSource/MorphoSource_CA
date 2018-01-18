<?php
	$t_media = $this->getVar("item");
	$va_bib_citations = $this->getVar("bib_citations");
	$vb_show_edit_link = $this->getVar("show_edit_link");
	$vb_show_download_link = $this->getVar("show_download_link");
	$vb_show_all_files = $this->getVar("show_all_files");
	
	# --- get all media files linked to this media record
	if(!$vb_show_all_files){
		$vs_publish_wheres= " and ((m.published > 0) OR ((m.published IS NULL) AND (mg.published > 0)))";
	}
	$o_db = new Db();
	$q_media_files = $o_db->query("SELECT m.media, m.media_file_id, m.doi, m.ark, m.ark_reserved, m.side, m.element, m.title, m.notes, m.published, m.file_type, m.derived_from_media_file_id, m.distance_units, m.max_distance_x, m.max_distance_3d, mg.published group_published FROM ms_media_files m INNER JOIN ms_media as mg ON m.media_id = mg.media_id where m.media_id = ?".$vs_publish_wheres, $t_media->get("media_id"));

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
<div style="float:right; position:relative; padding:5px 5px 0px 0px;">
	<a href="#" onClick="jQuery('#groupCitationElements').toggle(); return false;" style="text-decoration:none;"><i class='fa fa-info'></i> Citation Elements</a>
</div>
<div id="groupCitationElements" style="background-color:#FFF; padding:10px; display:none; width:65%; margin-left:auto; margin-right:auto;">
	<br/><div class="tealRule"><!-- empty --></div>
	<H2 style="padding-top:10px;">Citation Elements</H2>
	<div class="unit">
		<b><i>Essential</i></b><br/>
		<b>Media group-file numbers:</b> see info for individual files<br/>
		<b>DOIs:</b> see info for individual files<br/>
<?php
		if($vs_by_author = $t_media->getMediaCitationInstructions()){
			print "<b>Media citation instructions from data author:</b> ".$vs_by_author;
		}
?>	
	</div>
	<div class="unit">
		<b><i>Optional</i></b><br/>
		<b>URL:</b> http://www.morphosource.org/Detail/MediaDetail/Show/media_id/<?php print $t_media->get("media_id"); ?>
	</div>
	<div style="text-align:center;"><a href="#" onClick="jQuery('#groupCitationElements').toggle(); return false;" style="text-decoration:none;"><i class='fa fa-close'></i> Close</a></div>
</div>
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
		$va_file_permissions = array();
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
					print $q_media_files->get("title");
				}
				$vs_side = $t_media->getChoiceListValue("side", $t_media->get("side"));
				if($q_media_files->get("side")){
					$vs_side = $t_media_file->getChoiceListValue("side", $q_media_files->get("side"));
				}
				$vs_element = $t_media->get("element");
				if($q_media_files->get("element")){
					$vs_element = $q_media_files->get("element");
				}
				if($vs_element || $vs_side){
					print "<br/>";
				}
				if($vs_element){
					print $vs_element.", ";
				}
				if($vs_side){
					print $vs_side;
				}
				$vs_file_type = "";
				if($vs_file_type = $t_media_file->getChoiceListValue("file_type", $q_media_files->get("file_type"))){
					print "<br/>".$vs_file_type;
				}
				if($q_media_files->get("derived_from_media_file_id")){
					$t_media_file->load($q_media_files->get("derived_from_media_file_id"));
					print " from M".$t_media_file->get("media_id")."-".$q_media_files->get("derived_from_media_file_id");
				}
				$vs_distance_units = "";
				if($vs_distance_units = $t_media_file->getChoiceListValue("distance_units", $q_media_files->get("distance_units"))){
					print "<br/>Distance units of coordinate system for mesh files: ".$vs_distance_units;
				}
				$vs_max_distance_x = "";
				if($vs_max_distance_x = $q_media_files->get("max_distance_x")){
					print "<br/>Max X distance between points of mesh coordinates: ".$vs_max_distance_x."mm";
				}
				$vs_max_distance_3d = "";
				if($vs_max_distance_3d = $q_media_files->get("max_distance_3d")){
					print "<br/>Max 3d distance between points of mesh coordinates: ".$vs_max_distance_3d."mm";
				}
				if($vs_notes = $q_media_files->get("notes")){
					print "<p>".nl2br($vs_notes)."</p>";
				}
				#---- file level citation elements
?>
				<br/><a href="#" onClick="jQuery('#fileCitationElements<?php print $q_media_files->get("media_file_id"); ?>').toggle(); return false;" style="text-decoration:none;"><i class='fa fa-info'></i> Citation Elements</a>
<div id="fileCitationElements<?php print $q_media_files->get("media_file_id"); ?>" style="display:none; padding:10px; word-wrap: break-word;">
	<b>Media number:</b> 
<?php
		print "<b class='blueText'>M".$t_media->get("media_id")."-".$q_media_files->get("media_file_id")."</b><br/>";
		print "<b>DOI:</b> ";
		if($q_media_files->get("doi")){
			print $q_media_files->get("doi");
		}else{
			print "not requested by data author or assigned";
		}
		
		if($q_media_files->get("ark") && !$q_media_files->get("ark_reserved")){
			print "<br/><b>ARK:</b> ";
			print $q_media_files->get("ark");
		}
?>
		<br/><b>URL:</b> http://www.morphosource.org/Detail/MediaDetail/Show/media_id/<?php print $t_media->get("media_id"); ?>
</div>
<?php
				if($this->request->isLoggedIn()){
					if($t_media->userCanDownloadMediaFile($this->request->user->get("user_id"), $t_media->get("media_id"), $q_media_files->get("media_file_id"))){
						print "<div class='mediaFileButtons'>";
						#print caNavLink($this->request, "<i class='fa fa-download'></i>", "button buttonSmall", "Detail", "MediaDetail", "DownloadMedia", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $t_media->get("media_id"), "download" => 1), array("title" => "Download file"));
						print "<a href='#' onclick='msMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'MediaDetail', 'DownloadMediaSurvey', array("media_id" => $t_media->get("media_id"), "media_file_id" => $q_media_files->get("media_file_id"), "download_action" => "DownloadMedia"))."\"); return false;' title='Download file' class='button buttonSmall'><i class='fa fa-download'></i></a>";
						print "<span>".addToCartLink($this->request, $q_media_files->get("media_file_id"), $this->request->user->get("user_id"), null, array("class" => "button buttonSmall"))."</span>";
						print "</div>";
					}else{
						if(($q_media_files->get("published") == 2) || (($q_media_files->get("published") == null)) && ($t_media->get("published") == 2)){
							print "<br/><b>Please request permission to download</b>";
						}
					}
				}else{
					print "<div style='clear:left; margin-top:2px;'><a href='#' onClick='return false;' class='button buttonSmall mediaCartLogin'>"._t("add <i class='fa fa-shopping-cart'></i>")."</a></div>";
					TooltipManager::add(
						".mediaCartLogin", $this->render('../system/media_cart_login_message_html.php')
					);
				}
				
				print "</div>";
?>
			<div style='clear:both;'></div></div><!-- end mediaImage -->
<?php
			if($q_media_files->get("published") != null){
				$va_file_permissions[] = $q_media_files->get("published");
			}
		}
		print "</div><!-- end scrollArea -->";
if ($this->request->isLoggedIn()) {
		if($vb_show_download_link){
			# --- user has access to project or read only access to media so the pub setting doesn't matter
			#print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download All Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadAllMedia", array("media_id" => $t_media->get("media_id")))."</div>";		
			print "<div style='float:right; clear: right;'><a href='#' onclick='msMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'MediaDetail', 'DownloadMediaSurvey', array("media_id" => $t_media->get("media_id"), "download_action" => "DownloadAllMedia"))."\"); return false;' class='button buttonLarge'>"._t("Download All Media")."</a></div>";
		
		}else{
			if(($t_media->get("published") == 2) || (in_array(2, $va_file_permissions))){
				if (is_array($va_prev_requests = $t_media->getDownloadRequests(null, array('user_id' => $this->request->getUserID(), 'status' => __MS_DOWNLOAD_REQUEST_NEW__))) && (sizeof($va_prev_requests) > 0)){
					print "<div style='float:right; clear: right; cursor:default;' class='button buttonLarge' onclick='return false;'>"._t("Access to Media Pending")."</div>";
				} else {
					if (is_array($va_prev_requests = $t_media->getDownloadRequests(null, array('user_id' => $this->request->getUserID(), 'status' => __MS_DOWNLOAD_REQUEST_APPROVED__))) && (sizeof($va_prev_requests) > 0)){
						#print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download All Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadAllMedia", array("media_id" => $t_media->get("media_id")))."</div>";
						print "<div style='float:right; clear: right;'><a href='#' onclick='msMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'MediaDetail', 'DownloadMediaSurvey', array("media_id" => $t_media->get("media_id"), "download_action" => "DownloadAllMedia"))."\"); return false;' class='button buttonLarge'>"._t("Download All Media")."</a></div>";
		
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
			}else{
				#print "<div style='float:right; clear: right;'>".caNavLink($this->request, _t("Download All Media"), "button buttonLarge", "Detail", "MediaDetail", "DownloadAllMedia", array("media_id" => $t_media->get("media_id")))."</div>";
				print "<div style='float:right; clear: right;'><a href='#' onclick='msMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'MediaDetail', 'DownloadMediaSurvey', array("media_id" => $t_media->get("media_id"), "download_action" => "DownloadAllMedia"))."\"); return false;' class='button buttonLarge'>"._t("Download All Media")."</a></div>";		
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
	
	# --- is this record shared with the current user?
	if($this->getVar("share")){
?>
			<div class="tealRule"><!-- empty --></div>
			<H2>Sharing Details</H2>
			<div class="unit">
<?php		
			print "<b>Person responsible for sharing media:</b> ".$this->getVar("share_shared_by")."</br>";
			print "<b>Access expires:</b> ".$this->getVar("share_expires")."</br>";
			if($this->getVar("share_use_restrictions")){
				print "<b>Use restrictions:</b> ".$this->getVar("share_use_restrictions")."</br>";
			}
			print "</div>";
	}
	if($t_media->get("derived_from_media_id")){
		$t_parent = new ms_media($t_media->get("derived_from_media_id"));
		$t_specimen = new ms_specimens();
?>
		<div class="tealRule"><!-- empty --></div>
		<H2>Derived From</H2>
			<div class="unit">
<?php
				$vb_derivative_access = false;
				if($t_parent->get("published") > 0){
					$vb_derivative_access = true;
				}else{
					if($this->request->isLoggedIn()){
						$t_project = new ms_projects();
						$vb_derivative_access = $t_project->isMember($this->request->user->get("user_id"), $t_parent->get("project_id"));
					}
				}
				$va_parent_media = $t_parent->getPreviewMediaFile(null, array("icon"), ($vb_derivative_access) ? false : true);
				if(is_array($va_parent_media) && sizeof($va_parent_media)){
					print "<div style='float:left; padding-right:20px;'>".$va_parent_media["media"]["icon"]."</div>";
				}
				if($vb_derivative_access){
					print caNavLink($this->request, "<b>M".$t_parent->get("media_id")."</b>", "blueText", "Detail", "MediaDetail", "Show", array("media_id" => $t_parent->get("media_id")));
				}else{
					print "<b>M".$t_parent->get("media_id")."</b>";
				}
				print "<br/>";
				if($t_parent->get("title")){
					print $t_parent->get("title")."<br/>";
				}
				print $t_specimen->getSpecimenName($t_parent->get("specimen_id"));
?>
				<div style='clear:left;'></div>
			</div>
<?php	
	}
	# --- link to project if public
	$t_project = new ms_projects($t_media->get("project_id"));
	if($t_project->get("publication_status")){
?>
		<div class="tealRule"><!-- empty --></div>
		<H2>Project</H2>
			<div class="unit">
<?php
		print caNavLink($this->request, $t_project->get("name"), 'blueText', 'Detail', 'ProjectDetail', 'Show', array('project_id' => $t_project->get("project_id"))); 
?>
			</div>
<?php
	}	
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
	$va_media_display_fields = array("title", "notes", "facility_id", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_type", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_exposure_time", "scanner_filter", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_shading_correction", "scanner_calibration_description", "scanner_technicians", "grant_support", "media_citation_instruction1", "created_on", "last_modified_on");
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
					print "<b>".$va_field_attr["LABEL"].": </b>".nl2br($vs_field_value = $t_media->get($vs_field));
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
	# --- check to see if there are any derivatives of this record
	$q_derivatives = $o_db->query("SELECT * from ms_media WHERE derived_from_media_id = ?", $t_media->get("media_id"));
	if($q_derivatives->numRows()){
		# --- loop through them all to check access first
		$t_project = new ms_projects();
		$t_specimen = new ms_specimens();
		$va_derivatives = array();
		while($q_derivatives->nextRow()){
			$t_derivative = new ms_media($q_derivatives->get("media_id"));
			$vb_derivative_access = false;
			if($q_derivatives->get("published") > 0){
				$vb_derivative_access = true;
			}else{
				if($this->request->isLoggedIn()){
					$vb_derivative_access = $t_project->isMember($this->request->user->get("user_id"), $q_derivatives->get("project_id"));
				}
			}
			if($vb_derivative_access){
				$va_parent_media = $t_derivative->getPreviewMediaFile(null, array("icon"), ($vb_derivative_access) ? false : true);
				$vs_derivative = "";
				if(is_array($va_parent_media) && sizeof($va_parent_media)){
					$vs_derivative .= "<div style='float:left; padding-right:20px;'>".$va_parent_media["media"]["icon"]."</div>";
				}
				if($vb_derivative_access){
					$vs_derivative .= caNavLink($this->request, "<b>M".$t_derivative->get("media_id")."</b>", "blueText", "Detail", "MediaDetail", "Show", array("media_id" => $t_derivative->get("media_id")));
				}else{
					$vs_derivative .= "<b>M".$t_derivative->get("media_id")."</b>";
				}
				$vs_derivative .= "<br/>";
				if($t_derivative->get("title")){
					$vs_derivative .= $t_derivative->get("title")."<br/>";
				}
				$vs_derivative .= $t_specimen->getSpecimenName($t_derivative->get("specimen_id"));
				$va_derivatives[] = $vs_derivative;
			}			
		}
		if(sizeof($va_derivatives)){
?>
			<div class="tealRule"><!-- empty --></div>
			<H2>Derivatives</H2>
<?php
			foreach($va_derivatives as $vs_derivative){
				print "<div class='unit'>".$vs_derivative."<div style='clear:left;'></div></div>";
			}
		}
	}
?>
</div>