<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/specimen_results_html.php : 
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

	$va_specimens = $this->getVar("specimens");
	$t_specimen = new ms_specimens();
?>
	<div class="tealRule"><!-- empty --></div>
	<H2><?php print _t("Specimens"); ?></H2>
<?php
	if(sizeof($va_specimens)){
		print '<div id="specimenResultScrollContainer">';
		foreach($va_specimens as $vn_specimen_result_id){
			print "<div class='browseItem'>".caNavLink($this->request, $t_specimen->getSpecimenName($vn_specimen_result_id), 'blueText', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $vn_specimen_result_id))."</div>";
		}
		print '</div>';
	}else{
		print "<div class='browseItem'>"._t("There are no specimens directly owned by this project. See project page for specimens indirectly linked to this project.")."</div>";
	}
?>