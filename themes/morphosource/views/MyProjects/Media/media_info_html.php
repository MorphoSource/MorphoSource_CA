<?php
	$pn_media_id = $this->getVar("item_id");
	$t_media = $this->getVar("item");
	$va_fields = $t_media->getFormFields();
?>
<div id="mediaInfo">
	<div id="leftCol">
		<div class="blueRule"><!-- empty --></div>
		<H1><?php print _t("Project Media: M%1", $t_media->get("media_id")); ?></H1>
		<div id="mediaImage"><?php print $t_media->getMediaTag("media", "medium"); ?></div><!-- end mediaImage -->
		<div id="mediaMd">
<?php
		$va_media_display_fields = array("notes", "facility_id", "is_copyrighted", "copyright_info", "copyright_permission", "copyright_license", "scanner_type", "scanner_x_resolution", "scanner_y_resolution", "scanner_z_resolution", "scanner_voltage", "scanner_amperage", "scanner_watts", "scanner_projections", "scanner_frame_averaging", "scanner_wedge", "scanner_calibration_check", "scanner_calibration_description", "scanner_technicians", "created_on", "created_on", "last_modified_on");
		foreach($va_fields as $vs_field => $va_field_attr){
			if(in_array($vs_field, $va_media_display_fields) && $t_media->get($vs_field)){
				print "<div class='listItemLtBlue blueText'>";
				print "<div class='listItemRightCol ltBlueText'>".$t_media->get($vs_field)."</div>";
				print $va_field_attr["LABEL"];
				print "</div>";
			}
		}
		print "<p>";
		print "<a href='#' class='button buttonLarge' onClick='jQuery(\"#mediaMd\").load(\"".caNavUrl($this->request, 'MyProjects', 'Media', 'form', array('media_id' => $pn_media_id))."\"); return false;'>"._t("Edit Media")."</a>";
		print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonLarge", "MyProjects", "Media", "Delete", array("media_id" => $pn_media_id));
		print "</p>";
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