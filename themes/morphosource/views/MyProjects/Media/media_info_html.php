<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$va_fields = $t_media->getFormFields();
?>
<div id="mediaInfo">
	<div id="leftCol">
		<div class="blueRule"><!-- empty --></div>
		<H1><?php print _t("Project Media: M%1", $t_media->get("media_id")); ?></H1>
		<div id="mediaImage"><a href="#" onclick="caMediaPanel.showPanel('<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'mediaViewer', array('media_id' => $t_media->getPrimaryKey())); ?>'); return false;"><?php print $t_media->getMediaTag("media", "medium"); ?></a></div><!-- end mediaImage -->
		<div id="mediaMd">
<?php
		print "<div class='listItemLtBlue'>";
		if(!$t_media->get("published")){
			print caNavLink($this->request, _t("Publish"), "button buttonLarge", "MyProjects", "Media", "Publish", array("media_id" => $pn_media_id))."&nbsp;&nbsp;&nbsp";
		}
		print "<a href='#' class='button buttonLarge' onClick='jQuery(\"#mediaMd\").load(\"".caNavUrl($this->request, 'MyProjects', 'Media', 'form', array('media_id' => $pn_media_id))."\"); return false;'>"._t("Edit Media")."</a>";
		print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonLarge", "MyProjects", "Media", "Delete", array("media_id" => $pn_media_id));
		print "</div>";
		$va_media_display_fields = array("published", "notes", "facility_id", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_type", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_check", "scanner_calibration_description", "scanner_technicians", "created_on", "created_on", "last_modified_on");
		foreach($va_fields as $vs_field => $va_field_attr){
			if(in_array($vs_field, $va_media_display_fields) && ($vs_field == "published" || $t_media->get($vs_field))){
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
					case "is_copyrighted":
						if($t_media->get("is_copyrighted")){
							print "Yes";
						}else{
							print "No";
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
					default:
						print $t_media->get($vs_field);
					break;
					# ------------------------------
				}
				print "</div>";
				switch($vs_field){
					case "facility_id":
						print "Scanner facility";
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
		</div><!-- end mediaSpecimenInfo -->
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