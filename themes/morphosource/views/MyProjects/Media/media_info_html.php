<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$va_fields = $t_media->getFormFields();
?>
<div id="mediaInfo">
	<div id="leftCol">
		<div class="blueRule"><!-- empty --></div>
		<H1><?php print _t("Project Media: M%1", $t_media->get("media_id")); ?></H1>
		<div id="mediaImage"><a href="#" onclick="msMediaPanel.showPanel('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'mediaViewer', array('media_id' => $t_media->getPrimaryKey())); ?>'); return false;"><?php print $t_media->getMediaTag("media", "medium"); ?></a></div><!-- end mediaImage -->
		<div id="mediaMd">
<?php
		print "<div class='listItemLtBlue'>";
		
		if($t_media->getMediaUrl("media", "original")){
			print caNavLink($this->request, _t("Download"), "button buttonSmall", "MyProjects", "Media", "DownloadMedia", array("media_id" => $t_media->get("media_id"), 'download' => 1));
			print "&nbsp;&nbsp;&nbsp;<span>".addToCartLink($this->request, $t_media->get("media_id"), $this->request->user->get("user_id"), null, array("class" => "button buttonSmall"))."</span>";
		}
		if(!$t_media->get("published")){
			print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Publish"), "button buttonSmall", "MyProjects", "Media", "Publish", array("media_id" => $pn_media_id));
		}
		print "&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onClick='jQuery(\"#mediaMd\").load(\"".caNavUrl($this->request, 'MyProjects', 'Media', 'form', array('media_id' => $pn_media_id))."\"); return false;'>"._t("Edit")."</a>";
		print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Clone Media"), "button buttonSmall", "MyProjects", "Media", "form", array("clone_id" => $pn_media_id, "specimen_id" => $t_media->get("specimen_id")));
		print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "MyProjects", "Media", "Delete", array("media_id" => $pn_media_id));
		print "</div>";
		
		// Output file size and type first
?>
		<div class='listItemLtBlue blueText'>
			<div class='listItemRightCol ltBlueText'>
				<?php 
					//$vs_mimetype = $t_media->getMediaInfo('media', 'original', 'MIMETYPE');
					//$vs_media_class = caGetMediaClassForDisplay($vs_mimetype); 
					//$vs_mimetype_name = caGetDisplayNameForMimetype($vs_mimetype);
					
					//print "{$vs_media_class} ({$vs_mimetype_name})";
					print msGetMediaFormatDisplayString($t_media);
				?>
			</div>
			Type
			<div style='clear:both;'><!-- empty --></div>
		</div>
		<div class='listItemLtBlue blueText'>
			<div class='listItemRightCol ltBlueText'>
				<?php 
					$va_versions = $t_media->getMediaVersions('media');
					$va_properties = $t_media->getMediaInfo('media', in_array('_archive_', $va_versions) ? '_archive_' : 'original');
					print caFormatFilesize(isset($va_properties['FILESIZE']) ? $va_properties['FILESIZE'] : $va_properties['PROPERTIES']['filesize']);
				?>
			</div>
			Filesize
			<div style='clear:both;'><!-- empty --></div>
		</div>
<?php	
		print "<div class='listItemLtBlue blueText'><div class='listItemRightCol ltBlueText'>".$t_media->numViews()."</div>Public Views</div>";
		print "<div class='listItemLtBlue blueText'><div class='listItemRightCol ltBlueText'>".$t_media->numDownloads()."</div>Downloads</div>";
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
	<div id="rightCol">
		<div id="newMediaButton"><?php print caNavLink($this->request, _t("New Media"), "button buttonLarge", "MyProjects", "Media", "form"); ?></div><!-- end newMediaButton -->
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
			$va_member_projects = $t_projects->getProjectsForMember($this->request->user->get("user_id"));
			if(sizeof($va_member_projects) > 1){
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
				foreach($va_member_projects as $va_member_project){
					if($va_member_project["project_id"] != $t_media->get("project_id")){
						print "<option value='".$va_member_project["project_id"]."'>".$va_member_project["name"]."</option>";
					}
				}
?>
				</select>&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery("#mediaMoveForm").submit(); return false;'>Move</a>
				<input type="hidden" name="media_id" value="<?php print $pn_media_id; ?>">
				</div><!-- end mediaMove --></form>
<?php
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