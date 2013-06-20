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
?>
<div id="dashboardColLeft">
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print $t_project->get("name"); ?>
	</H1>
	<div id="dashboardAbstract">
		<?php print $t_project->get("abstract"); ?>
	</div><!-- end dashboardAbstract -->
	<div class="dashboardButtons">
<?php
	print caNavLink($this->request, _t("New Project"), "button buttonSmall", "MyProjects", "Project", "form", array("new_project" => 1));
	if($this->request->user->get("user_id") == $t_project->get("user_id")){
		print "&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Project Info"), "button buttonSmall", "MyProjects", "Project", "form", array("project_id" => $t_project->get("project_id")));
		print "&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Manage Members"), "button buttonSmall", "MyProjects", "Members", "listForm");
	}
	if($this->getVar("num_projects") > 1){
		print "&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Change Project"), "button buttonSmall", "MyProjects", "Dashboard", "projectList");
	}
?>
	</div>
</div><!-- end dashboardColLeft -->
<div id="dashboardColRight">
	<div class="dashboardButtons">
<?php
	print caNavLink($this->request, _t("Specimens"), "button buttonLarge", "MyProjects", "Specimens", "listItems");
	print "&nbsp;".caNavLink($this->request, _t("Bibliography"), "button buttonLarge", "MyProjects", "Bibliography", "listItems");
	print "&nbsp;".caNavLink($this->request, _t("Taxonomy"), "button buttonLarge", "MyProjects", "Taxonomy", "listItems");
	print "&nbsp;".caNavLink($this->request, _t("Facilities"), "button buttonLarge", "MyProjects", "Facilities", "listItems");
?>
	</div>
	<div class="tealRule"><!-- empty --></div>
<?php
	# get project members
	$va_members = $t_project->getMembers();
	if(sizeof($va_members)){
		$va_member_name_list = array();
		foreach($va_members as $va_member){
			$va_member_name_list[] = trim($va_member["fname"]." ".$va_member["lname"]);
		}
?>
	<div class="listItemLtBlue">		
		<div class="dataCol"><?php print join(", ", $va_member_name_list); ?></div>
		<H2>Project Members</H2>
		<div style="clear:both; height:1px;"><!-- empty --></div>
	</div>
<?php
	}
?>
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print $t_project->numMedia(); ?></div>
		<H2>Number of Media</H2>
	</div>
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print caFormatFilesize($t_project->get('total_storage_allocation')); ?></div>
		<H2>Storage used</H2>
	</div>
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print $t_project->numSpecimens(); ?></div>
		<H2>Number of Specimens</H2>
	</div>
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print $t_project->numCitations(); ?></div>
		<H2>Number of Citations</H2>
	</div>
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print caGetLocalizedDate(($vn_created_on = (int)$t_project->get("created_on", array("GET_DIRECT_DATE" => true))), array('timeOmit' => true))." (".caFormatInterval(time() - $vn_created_on, 2).")"; ?></div>
		<H2>Created On</H2>
	</div>
</div><!-- end dashboardColRight -->
<div id="dashboardMedia">
	<div class="tealRule"><!-- empty --></div>
	<div style="float:right; padding-top:10px;"><?php print caNavLink($this->request, _t("New Specimen"), "button buttonLarge", "MyProjects", "Specimens", "form"); ?></div>
	<H1>Project Specimens</H1>
<?php
	$t_specimen = new ms_specimens();
	$va_specimens = $t_project->getProjectSpecimens();
	if(is_array($va_specimens) && ($vn_num_media = sizeof($va_specimens))){
		foreach($va_specimens as $vn_specimen_id => $va_specimen) {
			$vn_num_media = is_array($va_specimen['media']) ? sizeof($va_specimen['media']) : 0;
			
			print "<div class='projectMediaContainer'>";
			print "<div class='projectMedia".(($vn_num_media > 1) ? " projectMediaSlideCycle" : "")."'>";
			
			if (is_array($va_specimen['media']) && ($vn_num_media > 0)) {
				foreach($va_specimen['media'] as $vn_media_id => $va_media) {
					if (!($vs_media_tag = $va_media['tags']['preview190'])) {
						$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
					}
					print "<div class='projectMediaSlide'>".caNavLink($this->request, $vs_media_tag, "", "MyProjects", "Specimens", "form", array("specimen_id" => $vn_specimen_id))."</div>";
					//print "<span class='mediaID'>M{$vn_media_id}</span>";
				}
			} else {
				print "<div class='projectMediaPlaceholder'> </div>";
			}
			print "</div><!-- end projectMedia -->";
			
			$vs_specimen_taxonomy = join(" ", $t_specimen->getSpecimenTaxonomy($vn_specimen_id));
			print "<div class='projectMediaSlideCaption'>".caNavLink($this->request, $t_specimen->formatSpecimenName($va_specimen), '', "MyProjects", "Specimens", "form", array("specimen_id" => $vn_specimen_id));
			if ($vs_specimen_taxonomy) { print ", <em>{$vs_specimen_taxonomy}</em>"; }
					print ($vs_element = $va_specimen['element']) ? " ({$vs_element})" : "";
			print "</div>\n";
			print "</div><!-- end projectMediaContainer -->";
		}
	}else{
		print "<H2>"._t("Your project has no specimens.  Use the \"NEW SPECIMEN\" button to add specimens, to which media may be added.")."</H2>";
	}
?>
</div><!-- end dashboardMedia -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.projectMediaSlideCycle').cycle();
	});
</script>