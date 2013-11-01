<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/MyProjects/Dashboard/dashboard_html.php : 
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
 
	$t_project = $this->getVar("project");
	$t_media = new ms_media();
	$t_specimen = new ms_specimens();
?>
	<div class="message">You have read only access to this project, you can not contribute or change data</div>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print $t_project->get("name"); ?>
	</H1>
	<div id="dashboardAbstract">
<?php
		if(mb_strlen($t_project->get('abstract')) > 530){
			print mb_substr($t_project->get('abstract'), 0, 530)."... <a href='#' class='blueText abstract".$t_project->get("project_id")."'>more</a> &rsaquo;";
			TooltipManager::add(
				".abstract".$t_project->get("project_id"), "<p style='padding:10px 20px 10px 20px; font-size:11px;'>".$t_project->get('abstract')."</p>"
			);
		}else{
			print $t_project->get('abstract');
		}
?>
	</div><!-- end dashboardAbstract -->

<div id="dashboardMedia">
	<div class="tealRule"><!-- empty --></div>
<?php
	$t_specimen = new ms_specimens();
	$va_specimens = $t_project->getProjectSpecimens();
?>
	<H1><?php print sizeof($va_specimens)." Project Specimen".((sizeof($va_specimens) == 1) ? "" : "s"); ?></H1>
<?php
	if(is_array($va_specimens) && sizeof($va_specimens)){
		print '<div id="itemListings">';
		foreach($va_specimens as $vn_specimen_id => $va_specimen) {
			print "<div class='listItemLtBlue'>".caNavLink($this->request, $t_specimen->getSpecimenName($vn_specimen_id), "", "Detail", "SpecimenDetail", "Show", array("specimen_id" => $vn_specimen_id))."</div>";
		}
		print '</div><!-- end itemListings -->';
	}else{
		print "<H2>"._t("Your project has no specimens.")."</H2>";
	}
?>
</div><!-- end dashboardMedia -->