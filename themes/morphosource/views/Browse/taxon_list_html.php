<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/taxon_list_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2016 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
	//$pn_browse_institution_id = $this->getVar("browse_institution_id");
	//$ps_browse_genus = $this->getVar("browse_genus");
	//$ps_browse_species = $this->getVar("browse_species");
	
	$ps_rank = 			$this->getVar('rank');
	$ps_next_rank = 	$this->getVar('nextRank');
	$ps_path = 			$this->getVar('path');
	$q_taxa = 			$this->getVar("taxa");
	$pn_taxon_id = 		$this->getVar("taxon_id");
?>	
	<div id="browseList">
		<div class="tealRule"><!-- empty --></div>
		<H2><?php print _t("Taxonomy"); ?></H2>
<?php
	if($ps_path){
		print "<H5 class='ltBlueBottomRule'>Browsing By</H5>";
		print "<div class='taxonBrowseBy'>".join(" &rsaquo; ", $ps_path)."</div>";
	}
	if ($q_taxa->numRows() > 0) {
?>
	<br/><H5 class='ltBlueBottomRule'><?php print ($pn_taxon_id) ? _t("Refine") : _t("Start Browsing by %1", ucfirst($ps_rank)); ?></H5><br/>
<?php
		if($q_taxa->numRows() > 0){
			print "<div class='browseListScrollContainer'>";
			while($q_taxa->nextRow()){
				$vs_name = $q_taxa->get("name");
			
				if ($ps_next_rank) {
					print "<div class='browseItem'><a href='#' onClick='jQuery(\"#browseArea\").load(\"".caNavUrl($this->request, '', 'Browse', 'taxonList', array('taxon_id' => $q_taxa->get('taxon_id'), 'rank' => $ps_next_rank))."\"); return false;' class='blueText".(($vs_name == $ps_browse_species) ? " browseItemSelected" : "")."'>".$q_taxa->get("name")."</a> (".($q_taxa->get('published_specimen_count')).")</div>";
				} else {
					print "<div class='browseItem'>{$vs_name}</div>";
				}
			}
			print "</div>";
		}
	}
?>
</div>
<div id="specimenResults">
	<div class="tealRule"><!-- empty --></div>
<?php 
	// if(sizeof($va_specimens = $this->getVar('specimens'))){
// 		print "<H2>"._t("Specimens")."</H2>";
// 		print '<div id="specimenResultScrollContainer">';
// 		
// 		$t_specimen = new ms_specimens();
// 		foreach($va_specimens as $vn_specimen_result_id){
// 			print "<div class='browseItem'>".caNavLink($this->request, $t_specimen->getSpecimenName($vn_specimen_result_id), 'blueText', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $vn_specimen_result_id))."</div>";
// 		}
// 		print '</div>';
// 	}else{
// 		print "<div class='browseItem'>"._t("There are no specimens available")."</div>";
// 	}
	if(sizeof($va_specimens = $this->getVar('specimens'))){
		print "<H2>"._t("Species")."</H2>";
		print '<div id="specimenResultScrollContainer">';
		
		$t_specimen = new ms_specimens();
		$va_species_by_specimen_list = $t_specimen->getSpecimenTaxonomy($va_specimens);
		
		$va_species = [];
		foreach($va_species_by_specimen_list as $vn_alt_id => $vs_name) {
			$va_species[$vs_name][$vn_alt_id] = $t_specimen->getSpecimenName($vn_alt_id, ['omitTaxonomy' => true]);
		}
		
		ksort($va_species);
		
		$vn_c = 0;
		foreach($va_species as $vs_name => $va_specimens){
			print "<div class='browseItem'><a href='#' onclick='jQuery(this).parent().find(\".specimenList\").slideToggle(100); return false;'>{$vs_name}</a>";
			
			print "<div class='specimenList' style='display: ".($vn_c ? 'none' : 'block').";'>";
			
			asort($va_specimens);
			foreach($va_specimens as $vn_alt_id => $vs_name) {
				print "<div class='browseItem'>".caNavLink($this->request, $vs_name, 'blueText', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $vn_alt_id))."</div>";
			}
			print "</div>\n";
			print "</div>\n";
			
			$vn_c++;
		}
		print '</div>';
	}else{
		print "<div class='browseItem'>"._t("There are no species available")."</div>";
	}
?>
</div>