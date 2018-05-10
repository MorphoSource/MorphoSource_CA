<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$va_fields = $t_media->getFormFields();
	# --- get all media files linked to this media record
	$o_db = new Db();
	$q_media_files = $o_db->query("
		SELECT mf.*, m.published group_published 
		FROM ms_media_files mf
		INNER JOIN ms_media AS m ON m.media_id = mf.media_id
		WHERE
			mf.media_id = ?", $pn_media_id);
			
	$t_media_file = $this->getVar("t_media_file");
	$pn_media_file_id = $t_media_file->get("media_file_id");
	$va_media_downloads_per_file = $t_media->numDownloadsPerFile();
	$vs_specimen_info = "";
	if($t_media->get("specimen_id")){
		$t_specimen = new ms_specimens($t_media->get("specimen_id"));
		$vs_specimen_info = $t_specimen->getSpecimenName();
	}
	$vs_mediaFileMessage = $this->getVar("mediaFileMessage");
	$vs_new_mediaFileMessage = $this->getVar("new_mediaFileMessage");
	$vs_general_error = $this->getVar("general_error");
	$va_mediaFileErrors = $this->getVar("mediaFileErrors");
	if($vs_mediaFileMessage || $vs_new_mediaFileMessage || $vs_general_error){
		print "<div class='formErrors' style='font-size:24px;'><br/>".$vs_mediaFileMessage.$vs_new_mediaFileMessage.$vs_general_error."<br/><br/></div>";
	}
					
?>
<div id="mediaInfo">
		<div id="newMediaButton"><?php print caNavLink($this->request, _t("New Media Group"), "button buttonLarge", "MyProjects", "Media", "form"); ?></div><!-- end newMediaButton -->
		<div class="blueRule"><!-- empty --></div>
<?php
		if($vs_specimen_info){
			print "<H1>".$vs_specimen_info.(($t_media->get("element")) ? ", ".$t_media->get("element") : "")."</H1>";
		}
?>		
		<H2>
			<?php print _t("Media Group: M%1", $t_media->get("media_id"))."; ".$q_media_files->numRows(); ?> media file<?php print ($q_media_files->numRows() == 1) ? "" : "s"; ?>
<?php
				if($t_media->get("published")){
					print ";&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("View public page"), "", "Detail", "MediaDetail", "Show", array("media_id" => $t_media->get("media_id")))." or <a href='#' onClick='copyToClipboard(\"#mediaGroupLink\"); return false;' class='button buttonSmall' title='click to copy link to clipboard'>Copy <i class='fa fa-external-link'></i></a>";
					print "<div style='display:none;' id='mediaGroupLink'>".caNavUrl($this->request, "Detail", "MediaDetail", "Show", array("media_id" => $t_media->get("media_id")))."</div>";
				}
			?>
		</H2>
<?php
		if($t_media->get("notes")){
			print "<div class='mediaGroupNotes'>".nl2br($t_media->get("title"))."</div>";
		}
		$vb_files_published = false;
		$vb_files_DOI = false;
        
        // smc: check if form is just submitted (and add a jquery file upload widget)
        $isFormSaved = false;

		if($q_media_files->numRows()){
            $isFormSaved = true;
            
?>
			<div class='mediaImageScrollArea'>
<?php
			while($q_media_files->nextRow()){
				if($q_media_files->get("published") > 0){
					$vb_files_published = true;
				}
?>
				<div class="mediaImage">
					<a href="#" onclick="msMediaPanel.showPanel('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'mediaViewer', array('media_id' => $t_media->getPrimaryKey(), 'media_file_id' => $q_media_files->get("media_file_id"))); ?>'); return false;"><?php print $q_media_files->getMediaTag("media", "preview190"); ?></a>
<?php 
					if(!$q_media_files->get('doi') && !$q_media_files->get("published")){
						print "<div class='mediaFileButtonsDelete'>".caNavLink($this->request, "<i class='fa fa-remove'></i>", "button buttonSmall", "MyProjects", "Media", "DeleteMediaFile", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id), array("title" => _t("Delete media file")))."</div>";
					}else{
						$va_message = array();
						if($q_media_files->get('doi')){
							$vb_files_DOI = true;
							$va_message[] = "media files with a DOI";
						}
						if($q_media_files->get("published")){
							$va_message[] = "published media files";
						}
						print "<div class='mediaFileButtonsDelete' title='You cannot delete ".join(", ", $va_message)."'><div class='button buttonSmall buttonGray'><i class='fa fa-remove'></i></div></div>";
						
					}
					print "<div class='mediaFileButtons'>";
				
					//if (($q_media_files->get('published') > 0) && ($q_media_files->get('group_published') > 0) && $this->request->user->canDoAction('can_create_doi') && !$q_media_files->get('doi')) { 
					if ($this->request->user->canDoAction('can_create_doi') && !$q_media_files->get('doi')) { 
						//print caNavLink($this->request, "DOI", "button buttonSmall doiButton", "MyProjects", "Media", "GetDOI", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id), array("title" => _t("Get Digital Object Identifier (DOI)")));
						print "<a href='#' class='button buttonSmall doiButton' data-media_id='".$q_media_files->get("media_id")."' data-media_file_id='".$q_media_files->get("media_file_id")."'>DOI</a>";
					}
					print caNavLink($this->request, "<i class='fa fa-download'></i>", "button buttonSmall", "MyProjects", "Media", "DownloadMedia", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id, 'download' => 1), array("title" => _t("Download media file")));
					print "<a href='#' onClick='return false;' id='info".$q_media_files->get("media_file_id")."' class='button buttonSmall'><i class='fa fa-info'></i></a>";
					if($q_media_files->get("use_for_preview") == 1){
						print "<a href='#' onClick='return false;' class='button buttonOrange buttonSmall pointer' title='"._t("File used for media preview")."'><i class='fa fa-file-image-o'></i></a>";
					}else{
						print caNavLink($this->request, "<i class='fa fa-file-image-o'></i>", "button buttonSmall", "MyProjects", "Media", "setMediaPreview", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id), array("title" => _t("Set as preview for media group")));
					}
					#print caNavLink($this->request, "<i class='fa fa-edit'></i>", "button buttonSmall", "MyProjects", "Media", "mediaInfo", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id, "formaction" => "editMediaFile"), array("title" => _t("Edit media file")));
					#if(!$q_media_files->get('doi')){
						print "<a href='".caNavUrl($this->request, "MyProjects", "Media", "mediaInfo", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id, "formaction" => "editMediaFile"))."#editForm' class='button buttonSmall' title='"._t("Edit media file")."'><i class='fa fa-edit'></i></a>";
					#}else{
					#	print "<a href='#' onClick='return false;' class='button buttonSmall buttonGray' title='"._t("You cannot edit media files with a DOI")."'><i class='fa fa-edit'></i></a>";
					#}
					print "<span>".addToCartLink($this->request, $q_media_files->get("media_file_id"), $this->request->user->get("user_id"), null, array("class" => "button buttonSmall"))."</span>";
					print "</div>\n";
					print "<div class='mediaFileFormCaption'>";
					print "M".$pn_media_id."-".$q_media_files->get("media_file_id").", ";
					print ($q_media_files->get("use_for_preview") == 1) ? "<b>used for media preview</b> " : "";
					$vs_file_info = msGetMediaFormatDisplayString($q_media_files)."; ";
					$va_versions = $q_media_files->getMediaVersions('media');
					$va_properties = $q_media_files->getMediaInfo('media', in_array('_archive_', $va_versions) ? '_archive_' : 'original');
					$vs_file_info .= caFormatFilesize(isset($va_properties['FILESIZE']) ? $va_properties['FILESIZE'] : $va_properties['PROPERTIES']['filesize']);
					if($q_media_files->get("title")){
						print (mb_strlen($q_media_files->get("title")) > 60) ? mb_substr($q_media_files->get("title"), 0, 60)."..." : $q_media_files->get("title");
						print "<br/>";
					}
					if ($vs_doi = $q_media_files->get('doi')) { 
						$vs_file_info .= "<br/><a href='https://doi.org/".
							trim(str_replace('doi:', '', $vs_doi))."'>{$vs_doi}</a>";
					}
					if (($vs_ark = $q_media_files->get('ark')) 
						&& (!$q_media_files->get('ark_reserved'))) 
					{ 
						$vs_file_info .= "<br/><a href='http://ezid.cdlib.org/id/".
							trim($vs_ark)."'>{$vs_ark}</a>";
					}
					print $vs_file_info;
					$vs_side = $t_media_file->getChoiceListValue("side", $q_media_files->get("side"));
					if($q_media_files->get("published") == null){
						# --- get the pub setting from the group
						$vs_published = $t_media->getChoiceListValue("published", $t_media->get("published"));
					}else{
						$vs_published = $t_media_file->getChoiceListValue("published", $q_media_files->get("published"));
					}
					$vs_downloads = "<br/><b>Downloads: </b>".((is_array($va_media_downloads_per_file) && isset($va_media_downloads_per_file[$q_media_files->get("media_file_id")])) ? sizeof($va_media_downloads_per_file[$q_media_files->get("media_file_id")]) : "0");
					$vs_derived_from = "";
					if($q_media_files->get("derived_from_media_file_id")){
						$t_parent = new ms_media_files($q_media_files->get("derived_from_media_file_id"));
						$vs_derived_from = "M".$t_parent->get("media_id")."-".$t_parent->get("media_file_id");
					}
					$vs_more_info = "<b>M".$pn_media_id."-".$q_media_files->get("media_file_id")."</b>".(($q_media_files->get("use_for_preview") == 1) ? ", <b>Used for media preview</b> " : "")."<br/><b>File info: </b>".$vs_file_info."<br/><b>Title: </b>".(($q_media_files->get("title")) ? $q_media_files->get("title") : "-")."<br/><b>Description/Element: </b>".(($q_media_files->get("element")) ? $q_media_files->get("element") : "-")."<br/><b>Side: </b>".(($vs_side) ? $vs_side : "-")."<br/><b>File publication status: </b>".(($vs_published) ? $vs_published : "-")."<br/><b>File type: </b>".(($t_media_file->getChoiceListValue("file_type", $q_media_files->get("file_type"))) ? $t_media_file->getChoiceListValue("file_type", $q_media_files->get("file_type")) : "-").(($vs_derived_from) ? "<br/><b>Derived From: </b>".$vs_derived_from : "")."<br/><b>Notes: </b>".nl2br(($q_media_files->get("notes")) ? $q_media_files->get("notes") : "-").$vs_downloads;
					TooltipManager::add(
						"#info".$q_media_files->get("media_file_id"), $vs_more_info
					);
					# --- media citation info
?>
					<br/><a href="#" onClick="jQuery('#fileCitationElements<?php print $q_media_files->get("media_file_id"); ?>').toggle(); return false;" style="text-decoration:none; position:relative;"><i class='fa fa-info'></i> Citation Elements</a>
<div id="fileCitationElements<?php print $q_media_files->get("media_file_id"); ?>" class="fileCitationOverlay" style="display:none;">
	<H2 style="padding-left:0px;">File Citation Elements</H2>
	<b>Media number:</b> 
<?php
		print "<b class='blueText'>M".$t_media->get("media_id")."-".$q_media_files->get("media_file_id")."</b><br/><br/>";
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
		<br/><br/><b>Public URL:</b> http://www.morphosource.org/Detail/MediaDetail/Show/media_id/<?php print $t_media->get("media_id"); ?>
		<div style="text-align:center; padding-top:20px;"><a href="#" onClick="jQuery('#fileCitationElements<?php print $q_media_files->get("media_file_id"); ?>').toggle(); return false;" style="text-decoration:none;"><i class='fa fa-close'></i> Close</a></div>
</div>
<?php
					print "</div>";
?>
				</div><!-- end mediaImage -->
<?php
			}
			print "</div>";
		}
?>
	<div id="leftCol">
		<div id="mediaMd">
<?php
		print "<div class='listItemLtBlue'>";
		
		if($q_media_files->numRows()){
			print caNavLink($this->request, _t("Download"), "button buttonSmall", "MyProjects", "Media", "DownloadAllMedia", array("media_id" => $t_media->get("media_id"), 'download' => 1), array("title" => _t("Download all files")));
		}
		if(!$t_media->get("published")){
			print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Publish"), "button buttonSmall", "MyProjects", "Media", "Publish", array("media_id" => $pn_media_id), array("title" => "Publish group"));
		}
		print "&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onClick='jQuery(\"#mediaMd\").load(\"".caNavUrl($this->request, 'MyProjects', 'Media', 'form', array('media_id' => $pn_media_id))."\"); return false;'>"._t("Edit")."</a>";
		print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Clone Group"), "button buttonSmall", "MyProjects", "Media", "form", array("clone_id" => $pn_media_id, "specimen_id" => $t_media->get("specimen_id")));
		# --- can not delete group with media is published
		# --- can not delete group when media files have a doi assigned
		if(!$t_media->get("published") && !$vb_files_published && !$vb_files_DOI){
			print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete Group"), "button buttonSmall", "MyProjects", "Media", "Delete", array("media_id" => $pn_media_id));
		}else{
			$vs_button_message = "You cannot delete media groups that ";
			$va_message_parts = array();
			if($t_media->get("published")){
				$va_message_parts[] = "are published";
			}
			if($vb_files_published){
				$va_message_parts[] = "have published media files";
			}
			if($vb_files_DOI){
				$va_message_parts[] = "have media files with a DOI";
			}
			$vs_button_message .= join(" or ", $va_message_parts);
			print "&nbsp;&nbsp;&nbsp;<div class='button buttonSmall buttonGray' title='".$vs_button_message."'>"._t("Delete Group")."</div>";
		}
		print "</div>";
		
		// Output file size and type first
?>

<?php	
		print "<div class='listItemLtBlue blueText'><div class='listItemRightCol ltBlueText'>".$t_media->numViews()."</div>Public Views</div>";
		print "<div class='listItemLtBlue blueText'><div class='listItemRightCol ltBlueText'>Total: ".$t_media->numDownloads();
		if(is_array($va_media_downloads_per_file) && sizeof($va_media_downloads_per_file)){
			print "<br/>";
			foreach($va_media_downloads_per_file as $vn_file_id => $va_file_download_info){
				if($vn_file_id){
					print " M".$pn_media_id."-".$vn_file_id.": ".sizeof($va_file_download_info).";";
				}
			}
		}
		print "</div>Downloads<div style='clear:both;'><!-- empty --></div></div>";
		$va_media_display_fields = array("derived_from_media_id", "title", "side", "element", "published", "reviewer_id", "notes", "facility_id", "scanner_id", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_type", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_exposure_time", "scanner_filter", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_shading_correction", "scanner_calibration_description", "scanner_technicians", "created_on", "created_on", "last_modified_on", "grant_support", "media_citation_instruction1");
		foreach($va_fields as $vs_field => $va_field_attr){
			if(in_array($vs_field, $va_media_display_fields) && (in_array($vs_field, array("published", "scanner_calibration_shading_correction", "scanner_wedge", "reviewer_id")) || $t_media->get($vs_field))){
				print "<div class='listItemLtBlue blueText'>";
				print "<div class='listItemRightCol ltBlueText'>";
				switch($vs_field){
					case "derived_from_media_id":
						if($t_media->get("derived_from_media_id")){
							$t_parent = new ms_media($t_media->get("derived_from_media_id"));
							print "<b>M".$t_parent->get("media_id")."</b>, ".$t_specimen->getSpecimenName($t_parent->get("specimen_id"));
						}
					break;
					# ------------------------------
					case "facility_id":
						if($t_media->get("facility_id")){
							$t_facility = new ms_facilities($t_media->get("facility_id"));
							print $t_facility->get("name");
						}
					break;
					# ------------------------------
					case "scanner_id":
						if($t_media->get("scanner_id")){
							$o_db = new Db();
							$q_scanner = $o_db->query("SELECT name FROM ms_scanners WHERE scanner_id = ?", $t_media->get("scanner_id"));
							if($q_scanner->numRows()){
								$q_scanner->nextRow();
									print $q_scanner->get("name");
							}
						}
					break;
					# ------------------------------
					case "is_copyrighted":
						if($t_media->get("is_copyrighted")){
							print "Yes";
						}else{
							print "No";
						}
					break;
					# ------------------------------
					case "scanner_calibration_shading_correction":
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
							print implode("<br/>", $va_calibration_options);
						}else{
							print "No calibrations are listed";
						}
					break;
					# ------------------------------
					case "published":
					case "copyright_permission":
					case "copyright_license":
					case "scanner_type":
					case "scanner_calibration_check":
						print $t_media->getChoiceListValue($vs_field, $t_media->get($vs_field));
					break;
					# ------------------------------
					case "scanner_wedge":
						if($t_media->get($vs_field)){
							print $t_media->get($vs_field);
						}else{
							print "air";
						}
					break;
					# ------------------------------
					case "media_citation_instruction1":
						print $t_media->getMediaCitationInstructions();
					break;
					# ------------------------------
					case "reviewer_id":
						if ($t_media->get('published') == 2) {
							if ($vn_reviewer_id = $t_media->get($vs_field)){
								$t_reviewer = new ca_users($vn_reviewer_id);
								print $t_reviewer->get('fname')." ".
									$t_reviewer->get('lname');
							} else {
								print "Use project default";
							}
						} else {
							print "Not applicable due to publication status";
						}
						
					break;
					# ------------------------------
					default:
						print ($vs_field_value = $t_media->get($vs_field));
						if (is_numeric($vs_field_value) && ($vs_suffix = $t_media->getFieldInfo($vs_field, 'SUFFIX'))) {
							print " {$vs_suffix}";
						}
					break;
					# ------------------------------
				}
				print "</div>";
				switch($vs_field){
					case "facility_id":
						print "Scanner facility";
					break;
					# -------------------------
					case "scanner_id":
						print "Scanner used";
					break;
					# -------------------------
					case "scanner_calibration_shading_correction":
						print "Calibration options";
					break;
					# -------------------------
					case "scanner_calibration_flux_normalization":
					case "scanner_calibration_geometric_calibration":
						continue;
					break;
					# -------------------------
					default:
						print $va_field_attr["LABEL"];
					break;
					# ------------------------------
				}
				print "<div style='clear:both;'><!-- empty --></div></div>";
			}
		}
?>
		</div><!-- end mediaMd -->
		<div>
			<br/><a href="#" onClick="jQuery('#groupCitationElements').toggle(); return false;" style="text-decoration:none;"><i class='fa fa-info'></i> Citation Elements</a>
		</div>
		<div id="groupCitationElements" style="padding:10px; display:none;">
			<div class="tealRule"><!-- empty --></div>
			<H2 style="padding-top:10px;">Media Group Citation Elements</H2>
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
	</div><!-- end leftCol -->
	<div id="rightCol"><a name='editForm'></a>
		<div class="tealRule"><!-- empty --></div>
		<div id="uploadbuttonContainer" style="text-align:center; <?php print ($pn_media_file_id) ? "display:none;" : ""; ?>">
			<a href="#" onClick="jQuery('#uploadbuttonContainer').hide(); jQuery('#uploadWarning').show(); return false;" class="button buttonLarge">Upload Media Files To Group</a><br/><br/>
		</div>
		<div id="uploadWarning" class="blueText" style="display:none;">
			Are you sure the media you are about to upload represents either
			<br/>&nbsp;&nbsp;&nbsp;a) raw scan data used to generate other media in this group, or
			<br/>&nbsp;&nbsp;&nbsp;b) derivative data created from raw data of this media group, or 
			<br/>&nbsp;&nbsp;&nbsp;c) derivative data created from the same raw data as other derivative data in this group.
			
			<br/><br/>For detailed definitions of 'raw' and 'derivative' data visit the FAQ page.
			<p style="text-align:center">
				<a href="#" onClick="continueUpload();" class="button buttonLarge">Continue Uploading Files</a>
				&nbsp;&nbsp;<a href="#" onClick="jQuery('#uploadWarning').hide(); jQuery('#uploadbuttonContainer').show(); return false;" class="button buttonLarge">Cancel</a>
			</p>
		
		</div>
		<div id="mediaFileForm" style="display:none" class='<?php print ($pn_media_file_id || (is_array($va_mediaFileErrors) && sizeof($va_mediaFileErrors))) ? "showThis" : "hideThis"; ?>'>
			<H2><?php print ($pn_media_file_id) ? "Edit media file" : "Upload Media Files To This Group"; ?></H2>
			<div id="mediaFilesInfo">
	<?php
				#$vs_mediaFileMessage = $this->getVar("mediaFileMessage");
				#$vs_new_mediaFileMessage = $this->getVar("new_mediaFileMessage");
				#$va_mediaFileErrors = $this->getVar("mediaFileErrors");
	?>
				<div id="formArea" class="mediaFilesForm"><div class="ltBlueTopRule"><br/>

<div class="jr-group">

    <div class="jfu-container">
        <!-- The file upload form used as target for the file upload widget -->
            <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
            <div class="row fileupload-buttonbar">
                <div class="col-sm-5">
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-success fileinput-button button buttonMedium">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Upload from computer</span>
                        <input id="fileupload" type="file" name="files[]" multiple>
                    </span>
                    <!-- The global file processing state -->
                    <!--span class="fileupload-process"></span-->
                    <!-- The table listing the files available for upload/download -->
                    <table role="presentation" class="jfu-presentation table table-striped"><tbody class="files"><input type="hidden" id="presentation_0" class="presentation"></tbody></table>
                </div>
                <!-- The global progress state -->
                <div class="col-sm-5 fileupload-progress fade">
                    <!-- The global progress bar -->
                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                    </div>
                    <!-- The extended global progress state -->
                    <div class="progress-extended">&nbsp;</div>
                </div>
            </div>
        </form>
    </div>

</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade jfu-hide">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button id="jfu_cancel_file_{%=file.name.replace(/\.[^/.]+$/, '')%}" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>_Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr id="download_tr_{%=i%}" class="template-download fade jfu-hide">
        <td>
            <p class="name">
                    <span>{%=file.name%}</span>
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">({%=o.formatFileSize(file.size)%})</span>
        </td>
        <!-- smctodo: need to hide the delete button -->
        <td>
            {% if (file.deleteUrl) { %}
                <button id="jfu_delete_file_{%=file.name.replace(/\.[^/.]+$/, '')%}" class="btn btn-danger delete jfu-hide" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
                    
				<?php
					print caFormTag($this->request, ($pn_media_file_id) ? 'updateMediaFile' : 'linkMediaFile', 'mediaFilesForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
                    
                    if($va_mediaFileErrors["title"]){
						print "<div class='formErrors' style='clear:left;>".$va_mediaFileErrors["title"]."</div>";
					}
					print $t_media_file->htmlFormElement("title","<div class='formLabelFloat'>^LABEL<br>^ELEMENT</div>");

                    //Hidden fields for storing jquery file upload file name and temp path
                    print(' <input readonly class="jfu-hide jfu_media_file_name" id="jfu_media_file_name_0" name="jfu_media_file_name[0]" value="">');					
                    print(' <input type="hidden" id="jfu_media_file_path_0" name="jfu_media_file_path[0]" value="">');					
                    
					if($pn_media_file_id){
						print "<div class='formLabel' style='clear:left;'>".$t_media_file->getMediaTag("media", "thumbnail")."</div>";
					}else{
						if($va_mediaFileErrors["media"]){
							print "<div class='formErrors' style='clear:left;'>".$va_mediaFileErrors["media"]."</div>";
						}
						$vb_show_server_dir = false;
						if (
							($vs_user_upload_directory = $this->request->user->getPreference('user_upload_directory'))
							&&
							($vs_upload_base_directory = $t_media_file->getAppConfig()->get('upload_base_directory'))
							&&
							(preg_match('!^'.$vs_upload_base_directory.'!', $vs_user_upload_directory))
                            &&
                            (file_exists($vs_user_upload_directory))   // Make sure the user upload dir exists                         
						) {
							$vb_show_server_dir = true;
						}
                        //print "<div class='formLabel jfu-label'><!--Select a file for upload from your computer<br/-->";
						//print $t_media_file->htmlFormElement("media", "^ELEMENT")."</div>";
                        //print "</div>";
						if($vb_show_server_dir){
							$va_files = caGetDirectoryContentsAsList($vs_user_upload_directory);
							$va_files_proc = array('- Select from the server -' => '');
							foreach($va_files as $vs_path) {
								$va_files_proc[$vs_path_proc = preg_replace('!^'.$vs_user_upload_directory.'!', '', $vs_path)] = $vs_path_proc;
							}
							print "<div class='server-upload-option'> OR ".caHTMLSelect('mediaServerPath', $va_files_proc, array("onChange" => "serverSelectChange(this);", "class" => "mediaServerSelect", "id" => "media_0_mediaServerPath"), array())."</div>";				
						}
					}				

        print(' <input type="hidden" id="jfu_media_file_partial_0" name="jfu_media_file_partial[0]" value=""> ');					
        // important: must keep the jfu_file_status next to jfu_media_file_partial_++ for main.js to populate
        print(' <div class="jfu_file_status"><!--no file uploaded--></div> ');
                    
					print "<div class='formLabel imgPreview' style='clear:both;'>Image to use as preview:<br/><input type='file' name='mediaPreviews'/></div>";

				
				foreach(array("side", "element") as $vs_f){
					if($va_mediaFileErrors[$vs_f]){
						print "<div class='formErrors' style='clear:left;>".$va_mediaFileErrors[$vs_f]."</div>";
					}
					print $t_media_file->htmlFormElement($vs_f,"<div class='formLabelFloat'>^LABEL<br>^ELEMENT</div>");
				}
				print "<div style='clear:both;'><!-- end --></div>";
				
				
				# --- file type set to derivative if the group is a derivative
				$va_derived_from_media_file_ids = array();
				$q_group_media_file_ids = $o_db->query("select media_file_id, title, media_id from ms_media_files where media_id = ?", $t_media->get("media_id"));
				if($q_group_media_file_ids->numRows()){
					while($q_group_media_file_ids->nextRow()){
						if($q_group_media_file_ids->get("media_file_id") != $t_media_file->get("media_file_id")){
							$va_derived_from_media_file_ids[$q_group_media_file_ids->get("media_file_id")] = "M".$q_group_media_file_ids->get("media_id")."-".$q_group_media_file_ids->get("media_file_id")."; ".$q_group_media_file_ids->get("title");
						}
					}
				}
				if($t_media->get("derived_from_media_id")){
					# --- get the file numbers of the media group this group was derived from so can add to derived from file id dropdown
					$q_derived_from_media_file_ids = $o_db->query("select media_file_id, media_id, title from ms_media_files where media_id = ?", $t_media->get("derived_from_media_id"));
					if($q_derived_from_media_file_ids->numRows()){
						while($q_derived_from_media_file_ids->nextRow()){
							$va_derived_from_media_file_ids[$q_derived_from_media_file_ids->get("media_file_id")] = "M".$q_derived_from_media_file_ids->get("media_id")."-".$q_derived_from_media_file_ids->get("media_file_id")."; ".$q_derived_from_media_file_ids->get("title");
						}
					}
					# --- file is a derivative
					print "<div class='formLabel'>File Type: Derivative File</div>";
					print "<input type='hidden' value='2' name='file_type' id='file_type'>";
				}else{
					$vs_f = "file_type";
					print $t_media_file->htmlFormElement($vs_f,"<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
			
				}
				$vb_show_derived_from = false;
				if(is_array($va_derived_from_media_file_ids) && sizeof($va_derived_from_media_file_ids)){
					$vs_derived_from_file_select = "<div class='formLabel' id='derivedFromFile'>Derived from file<br/><select name='derived_from_media_file_id' id='derived_from_media_file_id' style='width:100%;'><option value=''>-</option>";
					foreach($va_derived_from_media_file_ids as $vn_derived_from_id => $vs_derived_from_title){
						if($t_media_file->get("media_file_id") != $vn_derived_from_id){
							$vs_derived_from_file_select .= "<option value='".$vn_derived_from_id."'".(($vn_derived_from_id == $t_media_file->get("derived_from_media_file_id")) ? " selected" : "").">".$vs_derived_from_title."</option>";	
							$vb_show_derived_from = true;
						}
					}
					$vs_derived_from_file_select .= "</select></div>";
				}
				if($vb_show_derived_from){
					print $vs_derived_from_file_select;
				}
				
				foreach(array("published", "notes") as $vs_f){
					if($va_mediaFileErrors[$vs_f]){
						print "<div class='formErrors'  style='clear:left;>".$va_mediaFileErrors[$vs_f]."</div>";
					}
					print $t_media_file->htmlFormElement($vs_f,
						"<div class='formLabel'>^LABEL<br>^ELEMENT</div>", 
						array("width" => "368px"));
				}		
					//print "<div class='formLabel' style='clear:left;'><a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#mediaFilesForm\").submit(); return false;'>"._t("Save")."</a>";
					print "<div class='formLabel' style='clear:left;'><button id='btn-save' name='save' class='button buttonSmall' onclick='return btnSaveClick();'>"._t("Save")."</button>";
					print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Cancel"), "button buttonSmall", "MyProjects", "Media", "MediaInfo", array("media_id" => $pn_media_id), array("title" => _t("Cancel")));
					print "</div>";
					
					print "<input type='hidden' value='{$pn_media_file_id}' name='media_file_id' id='media_file_id'>";
					print "<input type='hidden' value='{$pn_media_id}' name='media_id'>";
				?>
				</form>
				</div><!-- end ltBlueTopRule --></div><!-- end formArea -->
			</div><!-- end mediaFilesInfo -->
		</div><!-- end mediaFilesForm -->
		<div class="tealRule"><!-- empty --></div>
		<H2>Media Specimen</H2>
		<div id="mediaSpecimenInfo">
			<!-- load Specimen form here -->
		</div><!-- end mediaSpecimenInfo -->
		<div class="tealRule"><!-- empty --></div>
		<H2>Media Bibliography</H2>
		<div id="mediaBibliographyInfo">
			<!-- load Bib form here -->
		</div><!-- end mediaBibliographyInfo -->

		<div class="tealRule"><!-- empty --></div>
		<H2>Move Media</H2>
		<div id="mediaMove">
<?php
			$t_projects = new ms_projects();
			$t_projects->load($t_media->get("project_id"));
			$q_projects = $o_db->query("SELECT project_id, name from ms_projects WHERE project_id != ? AND deleted = 0 ORDER BY name", $t_media->get("project_id"));
			# --- find any existing requests that have not been processed
			$q_requests = $o_db->query("SELECT m.to_project_id, p.name, u.fname, u.lname, u.email FROM ms_media_movement_requests m INNER JOIN ms_projects as p ON m.to_project_id = p.project_id INNER JOIN ca_users as u ON p.user_id = u.user_id WHERE m.media_id = ? AND m.status = 0 AND m.type = 1", $t_media->get("media_id"));
			if($q_requests->numRows()){
				$q_requests->nextRow();
				print "<div style='padding-bottom:20px;'><span class='formErrors'>A move request has been sent to P".$q_requests->get("to_project_id").", ".$q_requests->get("name").".</span><br/><br/>You will be notified via email when the request is accepted or denied by the project administrator, ".trim($q_requests->get("fname")." ".$q_requests->get("lname")).", ".$q_requests->get("email").".</div>";
			}else{
				if($q_projects->numRows()){					
?>
					This media file is part of <i><b><?php print $t_projects->get("name"); ?></b></i>.<br/>Move file to 
					<select id="move_project_id" style="width:250px;">
					<option></option>
<?php
					while($q_projects->nextRow()){
						print "<option value='".$q_projects->get("project_id")."'>".$q_projects->get("name").", P".$q_projects->get("project_id")."</option>";
					}
?>
					</select>&nbsp;&nbsp;
					<a href='#' id='moveMediaButton' class='button buttonSmall'>Move</a>
					<div style="font-size:10px; font-style:italic; padding:10px 0px 10px 0px;">If you transfer your media to a project you are not a member of, the administrator of that project will have to approve the move for it to take effect.  Upon approval, you will no longer be able to edit the media.  It will still appear in your project as "Read Only Media".</div>				
<?php
				}
			}
?>
			</div><!-- end mediaMove -->
<?php			
			print $this->render('Media/share_media_form_html.php');
?>
<?php			
			print $this->render('Media/share_media_user_form_html.php');
?>
	</div><!-- end rightCol -->
</div><!-- end mediaInfo -->

<script type="text/javascript">
        
    var continueUpload = function() {
        
        jQuery('#uploadWarning').hide(); 
        jQuery('#mediaFileForm').show(); 

        // position the widget
        var contObj = $('div[class=jr-group]');
        //console.log('contObj '+contObj.length);
        jfuInit(contObj, '0');
        var posElem = $('input#title');
        //console.log('postElem '+posElem.length);
        contObj.position({
            my:        "left top",
            at:        "left bottom",
            of:        posElem, // or $("#otherdiv")
            collision: "none"
        })        
          
        jfu_widgetCount = 1;
        console.log('Widget added, jfu_widgetCount ='+jfu_widgetCount);
        return false;
    }
    
	jQuery(document).ready(function() {			
		jQuery('#mediaSpecimenInfo').load(
			'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'specimenLookup', array('media_id' => $pn_media_id)); ?>'
		);	
		jQuery('#mediaBibliographyInfo').load(
			'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'bibliographyLookup', array('media_id' => $pn_media_id)); ?>'
		);
		
		jQuery(".doiButton").bind('click', function() { 
			var media_id = jQuery(this).data('media_id');
			var media_file_id = jQuery(this).data('media_file_id');
			jQuery("#doiConfirm").dialog({
				resizable: false,
				width: 500,
				height:260,
				modal: true,
				closeOnEscape: false,
				buttons: {
					"Get a DOI": function() {
					$( this ).dialog( "close" ); 
					window.location = '<?php print caNavUrl($this->request, "MyProjects", "Media", "GetDOI", array("media_file_id" => '')); ?>' + media_file_id + '/media_id/' + media_id; 
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}});
			e.preventDefault();
			return false;
		});
		
		if(jQuery('#file_type').val() == 2){
			jQuery('#derivedFromFile').show();
		}else{
			jQuery('#derivedFromFile').hide();
		}
		jQuery('#file_type').bind('change', function(event) {
			if(jQuery('#file_type').val() == 2){
				jQuery('#derivedFromFile').show();
			}else{
				jQuery('#derivedFromFile').val("");
				jQuery('#derivedFromFile').hide();
			}
		});

		if (jQuery('#mediaFileForm').hasClass('showThis')) {
            //console.log('show the media form (e.g. if there is an error)');
            continueUpload();
        }
		//return false;
	});

	jQuery('#moveMediaButton').click(function () {
		var url = "<?php
			print caNavUrl($this->request, 'MyProjects', 'Media', 'confirmMove', 
				array(
					"media_id" => $t_media->getPrimaryKey(), 
					"proj_from" => $t_projects->get("project_id"), 
					"proj_to" => "placeholdertext"));
			?>";
		var proj_to = jQuery('#move_project_id').val();
		if (proj_to) {
			url = url.replace("placeholdertext", proj_to);
			msMediaPanel.showPanel(url);
		}
		return false;
	});

</script>

<div id="doiConfirm" style="display: none;" title="Assign DOI">
  <p>
  		By assigning a Digital Object Identifier (DOI) to this media item you warrant that there are no known errors in the data 
  		and that the data are as accurate and complete as possible. Once a DOI is assigned it cannot be 
  		removed so please ensure that your data is ready <strong>before</strong> you assign it!  You will not be able to delete the media file once a DOI is assigned.
  </p>
</div>
