<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$va_fields = $t_media->getFormFields();
	# --- get all media files linked to this media record
	$o_db = new Db();
	$q_media_files = $o_db->query("SELECT media, media_file_id, use_for_preview, published, side, element, title, notes FROM ms_media_files where media_id = ?", $pn_media_id);
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
	$va_mediaFileErrors = $this->getVar("mediaFileErrors");
	if($vs_mediaFileMessage || $vs_new_mediaFileMessage){
		print "<div class='formErrors' style='font-size:24px;'><br/>".$vs_mediaFileMessage.$vs_new_mediaFileMessage."<br/><br/></div>";
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
		<H2><?php print _t("Media Group: M%1", $t_media->get("media_id"))."; ".$q_media_files->numRows(); ?> media file<?php print ($q_media_files->numRows() == 1) ? "" : "s"; ?></H2>
<?php
		if($t_media->get("notes")){
			print "<div class='mediaGroupNotes'>".$t_media->get("title")."</div>";
		}
		if($q_media_files->numRows()){
?>
			<div class='mediaImageScrollArea'>
<?php
			while($q_media_files->nextRow()){
?>
				<div class="mediaImage">
					<a href="#" onclick="msMediaPanel.showPanel('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'mediaViewer', array('media_id' => $t_media->getPrimaryKey(), 'media_file_id' => $q_media_files->get("media_file_id"))); ?>'); return false;"><?php print $q_media_files->getMediaTag("media", "preview190"); ?></a>
<?php 
					print "<div class='mediaFileButtonsDelete'>".caNavLink($this->request, "<i class='fa fa-remove'></i>", "button buttonSmall", "MyProjects", "Media", "DeleteMediaFile", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id), array("title" => _t("Delete media file")))."</div>";
					print "<div class='mediaFileButtons'>";
					print caNavLink($this->request, "<i class='fa fa-download'></i>", "button buttonSmall", "MyProjects", "Media", "DownloadMedia", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id, 'download' => 1), array("title" => _t("Download media file")));
					print "<a href='#' onClick='return false;' id='info".$q_media_files->get("media_file_id")."' class='button buttonSmall'><i class='fa fa-info'></i></a>";
					if($q_media_files->get("use_for_preview") == 1){
						print "<a href='#' onClick='return false;' class='button buttonOrange buttonSmall pointer' title='"._t("File used for media preview")."'><i class='fa fa-file-image-o'></i></a>";
					}else{
						print caNavLink($this->request, "<i class='fa fa-file-image-o'></i>", "button buttonSmall", "MyProjects", "Media", "setMediaPreview", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id), array("title" => _t("Set as preview for media group")));
					}
					#print caNavLink($this->request, "<i class='fa fa-edit'></i>", "button buttonSmall", "MyProjects", "Media", "mediaInfo", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id, "formaction" => "editMediaFile"), array("title" => _t("Edit media file")));
					print "<a href='".caNavUrl($this->request, "MyProjects", "Media", "mediaInfo", array("media_file_id" => $q_media_files->get("media_file_id"), "media_id" => $pn_media_id, "formaction" => "editMediaFile"))."#editForm' class='button buttonSmall' title='"._t("Edit media file")."'><i class='fa fa-edit'></i></a>";
					
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
					print $vs_file_info;
					$vs_side = $t_media_file->getChoiceListValue("side", $q_media_files->get("side"));
					$vs_published = $t_media_file->getChoiceListValue("published", $q_media_files->get("published"));
					$vs_downloads = "<br/><b>Downloads: </b>".((is_array($va_media_downloads_per_file) && isset($va_media_downloads_per_file[$q_media_files->get("media_file_id")])) ? sizeof($va_media_downloads_per_file[$q_media_files->get("media_file_id")]) : "0");
					$vs_more_info = "<b>M".$pn_media_id."-".$q_media_files->get("media_file_id")."</b>".(($q_media_files->get("use_for_preview") == 1) ? ", <b>Used for media preview</b> " : "")."<br/><b>File info: </b>".$vs_file_info."<br/><b>Title: </b>".(($q_media_files->get("title")) ? $q_media_files->get("title") : "-")."<br/><b>Description/Element: </b>".(($q_media_files->get("element")) ? $q_media_files->get("element") : "-")."<br/><b>Side: </b>".(($vs_side) ? $vs_side : "-")."<br/><b>File publication status: </b>".(($vs_published) ? $vs_published : "-")."<br/><b>Notes: </b>".(($q_media_files->get("notes")) ? $q_media_files->get("notes") : "-").$vs_downloads;
					TooltipManager::add(
						"#info".$q_media_files->get("media_file_id"), $vs_more_info
					);
					
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
		print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete Group"), "button buttonSmall", "MyProjects", "Media", "Delete", array("media_id" => $pn_media_id));
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
		$va_media_display_fields = array("title", "side", "element", "published", "notes", "facility_id", "scanner_id", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_type", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_shading_correction", "scanner_calibration_description", "scanner_technicians", "created_on", "created_on", "last_modified_on", "grant_support", "media_citation_instruction1");
		foreach($va_fields as $vs_field => $va_field_attr){
			if(in_array($vs_field, $va_media_display_fields) && (in_array($vs_field, array("published", "scanner_calibration_shading_correction", "scanner_wedge")) || $t_media->get($vs_field))){
				print "<div class='listItemLtBlue blueText'>";
				print "<div class='listItemRightCol ltBlueText'>";
				switch($vs_field){
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
				<a href="#" onClick="jQuery('#uploadWarning').hide(); jQuery('#mediaFileForm').show(); return false;" class="button buttonLarge">Continue Uploading Files</a>
				&nbsp;&nbsp;<a href="#" onClick="jQuery('#uploadWarning').hide(); jQuery('#uploadbuttonContainer').show(); return false;" class="button buttonLarge">Cancel</a>
			</p>
		
		</div>
		<div id="mediaFileForm" style='<?php print ($pn_media_file_id || (is_array($va_mediaFileErrors) && sizeof($va_mediaFileErrors))) ? "" : "display:none;"; ?>'>
			<H2><?php print ($pn_media_file_id) ? "Edit media file" : "Upload Media Files To This Group"; ?></H2>
			<div id="mediaFilesInfo">
	<?php
				#$vs_mediaFileMessage = $this->getVar("mediaFileMessage");
				#$vs_new_mediaFileMessage = $this->getVar("new_mediaFileMessage");
				#$va_mediaFileErrors = $this->getVar("mediaFileErrors");
	?>
				<div id="formArea" class="mediaFilesForm"><div class="ltBlueTopRule"><br/>
				<?php
					print caFormTag($this->request, ($pn_media_file_id) ? 'updateMediaFile' : 'linkMediaFile', 'mediaFilesForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
					
					if($va_mediaFileErrors["title"]){
						print "<div class='formErrors' style='clear:left;>".$va_mediaFileErrors["title"]."</div>";
					}
					print $t_media_file->htmlFormElement("title","<div class='formLabelFloat'>^LABEL<br>^ELEMENT</div>");
					if($pn_media_file_id){
						print "<div class='formLabel'>".$t_media_file->getMediaTag("media", "thumbnail")."</div>";
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
						) {
							$vb_show_server_dir = true;
						}
		
						print "<div class='formLabelFloat'>Select a file for upload".(($vb_show_server_dir) ? "<br/>" : " ")."from your computer<br/>";
						print $t_media_file->htmlFormElement("media", "^ELEMENT")."</div>";					
						if($vb_show_server_dir){
							$va_files = caGetDirectoryContentsAsList($vs_user_upload_directory);
							$va_files_proc = array('[SELECT A FILE]' => '');
							foreach($va_files as $vs_path) {
								$va_files_proc[$vs_path_proc = preg_replace('!^'.$vs_user_upload_directory.'!', '', $vs_path)] = $vs_path_proc;
							}
							print "<div class='formLabelFloat'><br/>OR from the server<br/>".caHTMLSelect('mediaServerPath', $va_files_proc, array(), array())."</div>";				
						}
					}				
					print "<div class='formLabel' style='clear:both;'>Image to use as preview:<br/><input type='file' name='mediaPreviews'/></div>";
				
				foreach(array("side", "element") as $vs_f){
					if($va_mediaFileErrors[$vs_f]){
						print "<div class='formErrors' style='clear:left;>".$va_mediaFileErrors[$vs_f]."</div>";
					}
					print $t_media_file->htmlFormElement($vs_f,"<div class='formLabelFloat'>^LABEL<br>^ELEMENT</div>");
				}
				print "<div style='clear:both;'><!-- end --></div>";
				foreach(array("published", "notes") as $vs_f){
					if($va_mediaFileErrors[$vs_f]){
						print "<div class='formErrors'  style='clear:left;>".$va_mediaFileErrors[$vs_f]."</div>";
					}
					print $t_media_file->htmlFormElement($vs_f,"<div class='formLabel'>^LABEL<br>^ELEMENT</div>");
				}
					
					
					
					
					
					print "<div class='formLabel' style='clear:left;'><a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#mediaFilesForm\").submit(); return false;'>"._t("Save")."</a>";
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
<?php
			$t_projects = new ms_projects();
			$q_projects = $o_db->query("SELECT project_id, name from ms_projects WHERE project_id != ? ORDER BY name", $t_media->get("project_id"));
			if($q_projects->numRows()){
?>
				<div class="tealRule"><!-- empty --></div>
				<H2>Move Media</H2>
				<div id="mediaMove">
<?php
				print caFormTag($this->request, 'moveMedia', 'mediaMoveForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
				$t_projects->load($t_media->get("project_id"));
?>
					This media file is part of <i><b><?php print $t_projects->get("name"); ?></b></i>.<br/>Move file to <select name="move_project_id" style="width:250px;">
<?php
				while($q_projects->nextRow()){
					print "<option value='".$q_projects->get("project_id")."'>".$q_projects->get("name").", P".$q_projects->get("project_id")."</option>";
				}
?>
				</select>&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery("#mediaMoveForm").submit(); return false;'>Move</a>
				<input type="hidden" name="media_id" value="<?php print $pn_media_id; ?>">
				</div><!-- end mediaMove -->
				<div style="font-size:10px; font-style:italic;">If you transfer your media to a project you are not a member of, you will no longer be able to edit the media.  It will still appear in your project as "Read Only Media".</div>
				</form>
				
				<div class="tealRule"><!-- empty --></div>
				<H2>Share Media</H2>
				<div id="mediaMove">
<?php
				print caFormTag($this->request, 'shareMedia', 'mediaShareForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
?>
					Grant read only access to this media to:<br/><select name="share_project_id" style="width:250px;">
<?php
				$q_projects->seek(0);
				while($q_projects->nextRow()){
					print "<option value='".$q_projects->get("project_id")."'>".$q_projects->get("name").", P".$q_projects->get("project_id")."</option>";
				}
?>
				</select>&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery("#mediaShareForm").submit(); return false;'>Share</a>
				<input type="hidden" name="media_id" value="<?php print $pn_media_id; ?>">
				</div><!-- end mediaMove -->
				<div style="font-size:10px; font-style:italic;">Read only media will only be available for download if the media is published.  Please make sure to publish this media if you want other projects to be able to download it.</div>
				</form>
<?php
				# --- list out any projects with read only access
				$q_share_projects = $o_db->query("SELECT p.project_id, p.name, mp.link_id FROM ms_media_x_projects mp INNER JOIN ms_projects AS p ON mp.project_id = p.project_id WHERE mp.media_id = ? ORDER BY p.name", $t_media->get("media_id"));
				if($q_share_projects->numRows()){
					print "<div class='ltBlueTopRule'>";
					while($q_share_projects->nextRow()){
						print "<div class='listItemLtBlue'>";
						print "<div class='listItemRightCol'>".caNavLink($this->request, "Remove", "button buttonSmall", "MyProjects", "Media", "removeShareMedia", array("media_id" => $t_media->get("media_id"), "link_id" => $q_share_projects->get("link_id"), ))."</div>";			
						print "(P".$q_share_projects->get("project_id").") ".$q_share_projects->get("name");
						print "</div>";
					}
					print "</div>";
				}
			}
?>

	</div><!-- end rightCol -->
</div><!-- end mediaInfo -->

<script type="text/javascript">
	jQuery(document).ready(function() {			
		jQuery('#mediaSpecimenInfo').load(
			'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'specimenLookup', array('media_id' => $pn_media_id)); ?>'
		);
		return false;
	});
	jQuery(document).ready(function() {			
		jQuery('#mediaBibliographyInfo').load(
			'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'bibliographyLookup', array('media_id' => $pn_media_id)); ?>'
		);
		return false;
	});
</script>