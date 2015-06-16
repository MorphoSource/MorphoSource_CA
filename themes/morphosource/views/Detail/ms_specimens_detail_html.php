<?php
	$t_specimen = $this->getVar("item");
	$va_bib_citations = $this->getVar("bib_citations");
	$vb_show_edit_link = $this->getVar("show_edit_link");
	$t_bibliography = new ms_bibliography();
	$t_specimen = new ms_specimens($t_specimen->get("specimen_id"));
	$vs_specimen_name = $t_specimen->getSpecimenName();
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
			print "<div style='float:right; padding:10px 0px 0px 15px;'>".caNavLink($this->request, _t("Next"), 'button buttonLarge', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $this->getVar('next_id')), array('id' => 'next'))."</div>";
		}
		print "<div style='float:right; padding:10px 0px 0px 15px;'>".$vs_back_link."</div>";
		if ($this->getVar('previous_id')) {
			print "<div style='float:right; padding:10px 0px 0px 15px;'>".caNavLink($this->request, _t("Previous"), 'button buttonLarge', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $this->getVar('previous_id')), array('id' => 'previous'))."</div>";
		}
	}
?>
<H1 class="specimenDetailTitle">
<?php 
	print _t("Specimen: ").$vs_specimen_name;
?>
</H1>
<div id="specimenDetail">
<?php
	if($t_specimen->get("specimen_id")){
?>
		<div class="tealRule"><!-- empty --></div>
<?php
		if(is_array($va_bib_citations) && sizeof($va_bib_citations)){
?>
			<div id="specimenDetailBibContainer">
			<H2>Bibliography</H2>
				<div class="unit">
<?php
			foreach($va_bib_citations as $vn_link_id => $va_citation_info){
				print "<div class='specimenDetailcitation'>".$va_citation_info["citation"];
				if($va_citation_info["page"]){
					print "<br/>Page(s): ".$va_citation_info["page"];
				}
				print "</div>";
			}
			print "</div><!-- end unit --></div><!-- end specimenDetailBibContainer -->";
			print "<div id='specimenDetailInfoContainer'>";
		}
		if($vb_show_edit_link){
			print "<div style='float:right; padding:0px 0px 0px 15px;'>".caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", "Specimens", "form", array("specimen_id" => $t_specimen->get("specimen_id"), "select_project_id" => $t_specimen->get("project_id")))."</div>";
		}
?>
		<H2>Specimen Information</H2>
			<div class="unit">
<?php
		if($vs_reference_source = $t_specimen->get("reference_source", array("convertCodesToDisplayText" => true))){
			print $vs_reference_source."<br/>";
		}
		if($vs_sex = $t_specimen->get("sex", array("convertCodesToDisplayText" => true))){
			print "<b>Sex: </b>".$vs_sex."<br/>";
		}
		if($t_specimen->get("description")){
			print "<br/><b>Description: </b>".$t_specimen->get("description")."<br/>";
		}
		if($t_specimen->get("notes")){
			print "<br/><b>Notes: </b>".$t_specimen->get("notes")."<br/>";
		}
		if($t_specimen->get("relative_age")){
			print "<br/><b>Relative Age: </b>".$t_specimen->get("relative_age")."<br/>";
		}
		if($t_specimen->get("absolute_age")){
			print "<b>Absolute Age: </b>".$t_specimen->get("absolute_age")."<br/>";
		}
		if($t_specimen->get("body_mass")){
			print "<b>Body Mass: </b><span class='bodymassBibref'>".$t_specimen->get("body_mass");
			if($t_specimen->get("body_mass_comments")){
				print ", ".$t_specimen->get("body_mass_comments");
			}
			print "</span>";
			if($t_specimen->get("body_mass_bibref_id")){
				$t_bibliography->load($t_specimen->get("body_mass_bibref_id"));
				TooltipManager::add(
					".bodymassBibref", $t_bibliography->getCitationText()
				);
			}
			print "<br/>";
		}
		if($t_specimen->get("locality_description")){
			print "<br/><b>Locality: </b>";
			print $t_specimen->get("locality_description")."<br/>";
		}
		if($t_specimen->get("locality_coordinates") || $t_specimen->get("locality_northing_coordinate") || $t_specimen->get("locality_easting_coordinate") || $t_specimen->get("locality_datum_zone")){
			print "<b>Coordinates: </b>";
			$va_coor_info = array();
			if($t_specimen->get("locality_coordinates")){
				$va_coor_info[] = $t_specimen->get("locality_coordinates");
			}
			if($t_specimen->get("locality_northing_coordinate")){
				$va_coor_info[] = $t_specimen->get("locality_northing_coordinate");
			}
			if($t_specimen->get("locality_easting_coordinate")){
				$va_coor_info[] = $t_specimen->get("locality_easting_coordinate");
			}
			if($t_specimen->get("locality_datum_zone")){
				$va_coor_info[] = $t_specimen->get("locality_datum_zone");
			}
			print join(", ", $va_coor_info)."<br/>";
		}
		if($t_specimen->get("locality_absolute_age")){
			print "<b>Locality absolute age: </b><span class='locality_absolute_age'>".$t_specimen->get("locality_absolute_age")."</span>";
			if($t_specimen->get("locality_absolute_age_bibref_id")){
				$t_bibliography->load($t_specimen->get("locality_absolute_age_bibref_id"));
				TooltipManager::add(
					".locality_absolute_age", $t_bibliography->getCitationText()
				);
			}
			print "<br/>";
		}
		if($t_specimen->get("locality_relative_age")){
			print "<b>Locality relative age: </b><span class='locality_relative_age'>".$t_specimen->get("locality_relative_age")."</span>";
			if($t_specimen->get("locality_relative_age_bibref_id")){
				$t_bibliography->load($t_specimen->get("locality_relative_age_bibref_id"));
				TooltipManager::add(
					".locality_relative_age", $t_bibliography->getCitationText()
				);
			}
			print "<br/>";
		}
		
		if($t_specimen->get("institution_id")){
			$t_institution = new ms_institutions($t_specimen->get("institution_id"));
			print "<br/><b>Institution: </b>".$t_institution->get("name");
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
		if(is_array($va_bib_citations) && sizeof($va_bib_citations)){
			print "</div>";
		}
?>
		<div class="tealRule" style="clear:both;"><!-- empty --></div>
			<H2>Specimen Media</H2>
			<div id="specimenMediaList" class="unit">
<?php
			$va_options = array('versions' => array('preview190'), 'published' => true);
			if($this->request->isLoggedIn()){
				$va_options["user_id"] = $this->request->user->get("user_id");
			}
			$va_media_list = $t_specimen->getSpecimenMedia(null, $va_options);
			if (is_array($va_media_list) && sizeof($va_media_list)) {
				$vn_media_output = false;
				foreach($va_media_list as $vn_media_id => $va_media_info) {
					if($va_media_info["numFiles"]){
						$vn_media_output = true;
						$t_media = new ms_media($vn_media_id);
						$vs_side = $t_media->getChoiceListValue("side", $va_media_info['side']);
				
						print '<div class="specimenMediaListContainer">';
						if (!($vs_media_tag = $va_media_info['media']['preview190'])) {
							$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
						}
						print "<div class='specimenMediaListSlide'>".caNavLink($this->request, $vs_media_tag, "", "Detail", "MediaDetail", "Show", array("media_id" => $vn_media_id))."</div>";
						print caNavLink($this->request, "M".$vn_media_id, "blueText", "Detail", "MediaDetail", "Show", array("media_id" => $vn_media_id)).", ".$va_media_info["numFiles"]." file".(($va_media_info["numFiles"] == 1) ? "" : "s")."<br/>";
						if($va_media_info['title']){
							print $va_media_info['title']."<br/>";
						}
						print (($vs_side && (strtolower($vs_side) != 'unknown')) ? " ({$vs_side})" : "").(($vs_element = $va_media_info['element']) ? " ({$vs_element})" : "");
						print "</div><!-- end specimenMediaListContainer -->\n";
					}
				}
			}
			if(!$vn_media_output){
				print "<H2>"._t("This specimen has no media.")."</H2>";
			}
?>
			</div><!-- end specimenMediaList -->
<?php
	}
?>
</div><!-- end specimenDetail -->