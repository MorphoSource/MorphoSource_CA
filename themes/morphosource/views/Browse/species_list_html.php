<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/species_list_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2013 Whirl-i-Gig
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
	$pn_browse_institution_id = $this->getVar("browse_institution_id");
	$ps_browse_genus = $this->getVar("browse_genus");
	$ps_browse_species = $this->getVar("browse_species");
	
	$q_species = $this->getVar("species");
?>
	<H2><?php print _t("Refine by Species"); ?></H2>
<?php
	if($q_species->numRows() > 0){
		print "<div class='browseListScrollContainer'>";
		while($q_species->nextRow()){
			print "<div class='browseItem'><a href='#' onClick='highlightLinkSpecies(this); jQuery(\"#specimenResults\").load(\"".caNavUrl($this->request, '', 'Browse', 'specimenResults', array('species' => $q_species->get("species")))."\"); return false;' class='blueText".(($q_species->get("species") == $ps_browse_species) ? " browseItemSelected" : "")."'>".$q_species->get("species")."</a></div>";
		}
		print "</div>";
	}
?>
