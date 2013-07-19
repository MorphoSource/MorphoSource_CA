<?php
	$t_item = $this->getVar("item");
	
	$va_fields = $t_item->getFormFields();
	$va_errors = $this->getVar("errors");
	$ps_primary_key = $this->getVar("primary_key");
	
	# --- formatting variables
	# --- all fields in float_fields array  will be floated to the left
	$va_float_fields = array("institution_code", "collection_code", "catalog_number", "element", "side", "sex", "relative_age", "absolute_age", "body_mass", "body_mass_comments", "locality_description", "locality_coordinates", "locality_absolute_age", "locality_relative_age", "created_on", "last_modified_on");
	# --- all fields in clear_fields array  will have a clear output after them
	$va_clear_fields = array("catalog_number", "sex", "absolute_age", "body_mass_comments", "locality_coordinates", "locality_relative_age", "last_modified_on");

?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Specimen Information"); ?>
	</H1>
	<div id='formArea'>
<?php
	$t_specimen_project = new ms_projects($t_item->get("project_id"));
	print "<div class='formLabelError' style='text-align:center; margin-bottom: 20px;'>This specimen was created by the project, ".$t_specimen_project->get("name").". If you need to edit this specimen please contact <a href='mailto:".$this->request->config->get("ca_admin_email")."'>".$this->request->config->get("ca_admin_email")."</a></div>";
?>
		<div id="rightCol">
<?php
		# list out bib citations
		 	$va_bib_citations = array();
 				$o_db = new Db();
 				$q_bib = $o_db->query("SELECT b.*, mxb.link_id, mxb.pp FROM ms_specimens_x_bibliography mxb INNER JOIN ms_bibliography as b on mxb.bibref_id = b.bibref_id WHERE mxb.specimen_id = ?", $t_item->get("specimen_id"));
 				$t_bibliography = new ms_bibliography;
 				if($q_bib->numRows()){
 					$vn_bib = 1;
 ?>
					<div class="tealRule" style="margin-top:20px;"><!-- empty --></div>
					<H2>Specimen Bibliography</H2>
					<div id="specimenBibliographyInfo">
 <?php
 					while($q_bib->nextRow()){
 						print "<div class='listItemLtBlue'>";
 						print $t_bibliography->getCitationText($q_bib->getRow());
 						if($q_bib->get("pp")){
							print "<br/>Page(s): ".$q_bib->get("pp");
						}
 						print "</div>";
 					}
 ?>
 					</div><!-- end specimenBibliographyInfo -->
 <?php
 				}

	// Media list
?>
			<div class="tealRule" style="margin-top:<?php print ($vn_bib) ? "40px" : "20px"; ?>;"><!-- empty --></div>
<?php
	print caNavLink($this->request, _t("Add media"), "button buttonSmall", "MyProjects", 'Media', "form", array('specimen_id' => $t_item->get('specimen_id')), array('style' => 'float: right;'));
?>
			<H2>Project Specimen Media</H2>
			<div id="specimenMediaList">
<?php
			$va_media_list = $t_item->getSpecimenMedia(null, array('versions' => array('preview190')));
			if (is_array($va_media_list) && sizeof($va_media_list)) {
				foreach($va_media_list as $vn_media_id => $va_media_info) {
					$t_media = new ms_media($vn_media_id);
					$vs_side = $t_media->getChoiceListValue("side", $va_media_info['side']);
			
					print '<div class="specimenMediaListContainer">';
					if (!($vs_media_tag = $va_media_info['tags']['preview190'])) {
						$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
					}
					print "<div class='specimenMediaListSlide'>".caNavLink($this->request, $vs_media_tag, "", "MyProjects", "Media", "mediaInfo", array("media_id" => $vn_media_id))."</div>";
					print "<span class='mediaID'>M{$vn_media_id}</span>";
					print " {$va_media_info['title']}".(($vs_side && (strtolower($vs_side) != 'unknown')) ? " ({$vs_side})" : "").(($vs_element = $va_media_info['element']) ? " ({$vs_element})" : "");
					
				
					print "<br/>".msGetMediaFormatDisplayString($t_media);
					print ", ".caFormatFilesize($va_media_info['filesize']);
					print "<br>".$t_media->formatPublishedText();
					print "</div><!-- end specimenMediaListContainer -->\n";
				}
			} else {
				print "<H2>"._t("This specimen has no media.  Use the \"ADD MEDIA\" button to add media files for this specimen.")."</H2>";
			}
?>
			</div><!-- end specimenMediaList -->
		</div><!-- end rightCol -->
		
	<div id="leftCol">
<?php
	$t_bib = new ms_bibliography();
	$t_institution = new ms_institutions();
	while (list($vs_f,$vs_field_info) = each($va_fields)) {
		switch($vs_f){
			case "created_on":
			case "last_modified_on":
				if($t_item->get($vs_f)){
					print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>".$vs_field_info["LABEL"]."<br/><span style='font-weight:normal;'>".$t_item->get($vs_f)."</span></div>";
				}
			break;
			# -----------------------------------------------
			case "body_mass_bibref_id":
			case "locality_absolute_age_bibref_id":
			case "locality_relative_age_bibref_id":
				$vs_name = "";
				if($t_item->get($vs_f)){
					$t_bib->load($t_item->get($vs_f));
					$vs_name = strip_tags($t_bib->getCitationText());
				}
				switch($vs_f){
					case "body_mass_bibref_id":
						$vs_label = "Body mass citation";
					break;
					# -------------------------------------
					case "locality_absolute_age_bibref_id":
						$vs_label = "Absolute age citation";
					break;
					# -------------------------------------
					case "locality_relative_age_bibref_id":
						$vs_label = "Relative age citation";
					break;
				}
				print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>".$vs_label."<br/><span style='font-weight:normal;'>".(($vs_name) ? $vs_name : "NA")."</span></div>";
			break;
			# -----------------------------------------------
			case "institution_id":
				$vs_name = "";
				if($t_item->get($vs_f)){
					$t_institution->load($t_item->get($vs_f));
					$vs_name = strip_tags($t_institution->get("name"));
				}
				print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>Institution<br/><span style='font-weight:normal;'>".(($vs_name) ? $vs_name : "NA")."</span></div>";
			break;
			# -----------------------------------------------
			case "project_id":
			case "user_id":
			case "approval_status":
			case "specimen_id":
				continue;
			break;
			# -----------------------------------------------
			case "reference_source":
				print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>".$vs_field_info["LABEL"]."<br/><span style='font-weight:normal;'>".$t_item->get($vs_f, array('convertCodesToDisplayText' => true))."</span></div>";
			break;
			# -----------------------------------------------
			case "catalog_number":
				print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>".$vs_field_info["LABEL"]."<br/><span style='font-weight:normal;'>".(($t_item->get($vs_f)) ? $t_item->get($vs_f) : "NA")."</span></div>";
				$va_specimen_taxonomy = $t_item->getSpecimenTaxonomy();
				if(is_array($va_specimen_taxonomy) && sizeof($va_specimen_taxonomy)){
					foreach($va_specimen_taxonomy as $vn_taxonomy_id => $vs_specimen_taxonomy){
						print "<div style='clear:both;'><!--empty--></div><div class='formLabel'>Taxonomy<br/><span style='font-weight:normal;'>".$vs_specimen_taxonomy."</span></div>";
						break;
					}
				}
			break;
			# -----------------------------------------------
			default:
				print "<div class='formLabel".((in_array($vs_f, $va_float_fields)) ? "Float" : "")."'>".$vs_field_info["LABEL"]."<br/><span style='font-weight:normal;'>".(($t_item->get($vs_f)) ? $t_item->get($vs_f) : "NA")."</span></div>";
			break;
			# -----------------------------------------------
		}
		if(in_array($vs_f, $va_clear_fields)){
			print "<div style='clear:both;'><!--empty--></div>";
		}
	}
?>
	</div>
		
	</div><!-- end formArea -->