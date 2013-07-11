<?php
	$t_specimen = $this->getVar("item");
	$va_bib_citations = $this->getVar("bib_citations");
	$t_bibliography = new ms_bibliography();
?>
<div class="blueRule"><!-- empty --></div>
<H1>
<?php 
	print _t("Specimen: S%1", $t_specimen->get("specimen_id"));
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
?>
		<H2>Specimen Information</H2>
			<div class="unit">
<?php
		$t_specimen = new ms_specimens($t_specimen->get("specimen_id"));
		$vs_specimen_name = $t_specimen->getSpecimenName();
		if($vs_specimen_name){
			print "<b>Specimen:</b> ".$vs_specimen_name;
			if($vs_reference_source = $t_specimen->get("reference_source", array("convertCodesToDisplayText" => true))){
				print ", ".$vs_reference_source;
			}
			if($vs_sex = $t_specimen->get("sex", array("convertCodesToDisplayText" => true))){
				print ", ".$vs_sex;
			}
			print "<br/>";
		}
		if($t_specimen->get("description")){
			print $t_specimen->get("description")."<br/>";
		}
		if($t_specimen->get("relative_age")){
			print "<br/><b>Relative Age: </b>".$t_specimen->get("relative_age").", ";
		}
		if($t_specimen->get("absolute_age")){
			print "<b>Absolute Age: </b>".$t_specimen->get("absolute_age");
		}
		if($t_specimen->get("relative_age") || $t_specimen->get("absolute_age")){
			print "<br/>";
		}
		if($t_specimen->get("body_mass")){
			print "<b>Body Mass: </b><span class='bodymassBibref'>".$t_specimen->get("body_mass")."</span>";
			if($t_specimen->get("body_mass_comments")){
				print ", ".$t_specimen->get("body_mass_comments");
			}
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
			if($t_specimen->get("locality_description")){
				print $t_specimen->get("locality_description")."<br/>";
			}
			if($t_specimen->get("locality_coordinates")){
				print $t_specimen->get("locality_coordinates")."<br/>";
			}
			if($t_specimen->get("locality_absolute_age")){
				print "<span class='locality_absolute_age'>".$t_specimen->get("locality_absolute_age")."</span>";
				if($t_specimen->get("locality_absolute_age_bibref_id")){
					$t_bibliography->load($t_specimen->get("locality_absolute_age_bibref_id"));
					TooltipManager::add(
						".locality_absolute_age", $t_bibliography->getCitationText()
					);
				}
			}
			if($t_specimen->get("locality_absolute_age") && $t_specimen->get("locality_relative_age")){
				print ", ";
			}
			if($t_specimen->get("locality_relative_age")){
				print "<span class='locality_relative_age'>".$t_specimen->get("locality_relative_age")."</span>";
				if($t_specimen->get("locality_relative_age_bibref_id")){
					$t_bibliography->load($t_specimen->get("locality_relative_age_bibref_id"));
					TooltipManager::add(
						".locality_relative_age", $t_bibliography->getCitationText()
					);
				}
			}
			if($t_specimen->get("locality_absolute_age") || $t_specimen->get("locality_relative_age")){
				print "<br/>";
			}
		}
		
		#$va_specimen_taxonomy = $t_specimen->getSpecimenTaxonomy();
		#if(is_array($va_specimen_taxonomy) && sizeof($va_specimen_taxonomy)){
		#	print "<b>Taxonomy:</b> ".join(", ", $va_specimen_taxonomy)."<br/>";
		#}
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
			$va_media_list = $t_specimen->getSpecimenMedia(null, array('versions' => array('preview190')));
			if (is_array($va_media_list) && sizeof($va_media_list)) {
				foreach($va_media_list as $vn_media_id => $va_media_info) {
					$t_media = new ms_media($vn_media_id);
					$vs_side = $t_media->getChoiceListValue("side", $va_media_info['side']);
			
					print '<div class="specimenMediaListContainer">';
					if (!($vs_media_tag = $va_media_info['tags']['preview190'])) {
						$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
					}
					print "<div class='specimenMediaListSlide'>".caNavLink($this->request, $vs_media_tag, "", "Detail", "MediaDetail", "Show", array("media_id" => $vn_media_id))."</div>";
					print caNavLink($this->request, "M".$vn_media_id, "blueText", "Detail", "MediaDetail", "Show", array("media_id" => $vn_media_id)).", ";
					print "{$va_media_info['title']}".(($vs_side && (strtolower($vs_side) != 'unknown')) ? " ({$vs_side})" : "").(($vs_element = $va_media_info['element']) ? " ({$vs_element})" : "");
					
				
					print "<br/>".msGetMediaFormatDisplayString($t_media);
					print ", ".caFormatFilesize($va_media_info['filesize']);
					print "</div><!-- end specimenMediaListContainer -->\n";
				}
			} else {
				print "<H2>"._t("This specimen has no media.")."</H2>";
			}
?>
			</div><!-- end specimenMediaList -->
<?php
	}
?>
</div><!-- end specimenDetail -->