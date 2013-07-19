<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/genus_list_html.php : 
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
	$q_genus = $this->getVar("genus");
?>
	<div id="browseList">
		<div class="tealRule"><!-- empty --></div>
		<div id="browseSubList"><!-- load species lis here when genus is selected --></div>
		<H2><?php print _t("Browse by Genus"); ?></H2>
<?php
	if($q_genus->numRows() > 0){
		print "<div class='browseListScrollContainer'>";
		while($q_genus->nextRow()){
			print "<div class='browseItem'><a href='#' onClick='highlightLink(this); jQuery(\"#specimenResults\").load(\"".caNavUrl($this->request, '', 'Browse', 'specimenResults', array('genus' => $q_genus->get("genus")))."\"); jQuery(\"#browseSubList\").load(\"".caNavUrl($this->request, '', 'Browse', 'speciesList', array('genus' => $q_genus->get("genus")))."\"); return false;' class='blueText highlightLink".(($q_genus->get("genus") == $ps_browse_genus) ? " browseItemSelected" : "")."'>".$q_genus->get("genus")."</a></div>";
		}
		print "</div>";
	}else{
		print "<p><b>"._t("There are no genus available")."</b></p>";
	}
?>
	</div>
	<div id="specimenResults"><!-- load the specimen results here --></div>
<script type="text/javascript">
<?php
	if($ps_browse_genus){
?>
	jQuery(document).ready(function() {			
		jQuery('#browseSubList').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'speciesList', array("genus" => $ps_browse_genus)); ?>'
		);
		return false;
	});
<?php	
	}
	if($ps_browse_genus || $ps_browse_species){
?>
	jQuery(document).ready(function() {			
		jQuery('#specimenResults').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'specimenResults', array('genus' => $ps_browse_genus, 'species' => $ps_browse_species)); ?>'
		);
		return false;
	});
<?php	
	}
?>
</script>