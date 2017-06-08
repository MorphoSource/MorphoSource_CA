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
	$va_media_counts = $this->getVar('media_counts');
	$va_media_file_counts = $this->getVar('media_file_counts');
	$t_media = new ms_media();
?>
<div id="dashboardColLeft">
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print $t_project->get("name"); ?>
	</H1>
	<H2>
		P<?php print $t_project->get("project_id"); ?>
	</H2>
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
		if($t_project->get("url")){
			print "<br/><br/><b>"._t("More Information").":</b> <a href='".$t_project->get("url")."' target='_blank'>".$t_project->get("url")."</a>\n";
		}
?>
	</div><!-- end dashboardAbstract -->
	<div class="dashboardButtons">
<?php
	print caNavLink($this->request, _t("New Project"), "button buttonSmall", "MyProjects", "Project", "form", array("new_project" => 1));
	if($this->request->user->canDoAction("is_administrator") || ($this->request->user->get("user_id") == $t_project->get("user_id"))){
		print "&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Project Info"), "button buttonSmall", "MyProjects", "Project", "form", array("project_id" => $t_project->get("project_id")));
		print "&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Manage Members"), "button buttonSmall", "MyProjects", "Members", "listForm");
	}
	if($this->getVar("num_projects") > 1){
		print "&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Change Project"), "button buttonSmall", "MyProjects", "Dashboard", "projectList");
	}
?>
	</div>
<?php
	print $this->render('Dashboard/pending_download_requests_html.php');
	print $this->render('Dashboard/media_movement_requests_html.php');	
?>
</div><!-- end dashboardColLeft -->
<div id="dashboardColRight">
	<div class="dashboardButtons">
<?php
	print caNavLink($this->request, _t("Media"), "button buttonMedium", "MyProjects", "Media", "listItems");
	print caNavLink($this->request, _t("Bibliography"), "button buttonMedium", "MyProjects", "Bibliography", "listItems");
	print caNavLink($this->request, _t("Taxonomy"), "button buttonMedium", "MyProjects", "Taxonomy", "listItems");
	print caNavLink($this->request, _t("Facilities"), "button buttonMedium", "MyProjects", "Facilities", "listItems");
	print caNavLink($this->request, _t("Institutions"), "button buttonMedium", "MyProjects", "Institutions", "listItems");
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
		<div class="dataCol">
<?php
		if($vn_num_media = (int)$t_project->numMedia()){
			if($va_media_counts[1]){
				print (int)$va_media_counts[1]." ".$t_media->formatPublishedText(1)."<br/>"; 
			}
			if($va_media_counts[2]){
				print (int)$va_media_counts[2]." ".$t_media->formatPublishedText(2)."<br/>";
			}
			print (int)$va_media_counts[0]." ".$t_media->formatPublishedText(0)."<br/>";
			print _t('<em>(%1 total)</em>', $vn_num_media);
			if ($va_media_counts[0] > 0) {
				print "<div style='padding-top:10px;'><a href='#' onClick='$(\"#mediaPubOptions\").slideDown(1); return false;' class='button buttonSmall'>"._t("Publish unpublished media (%1)", (int)$va_media_counts[0])."</a></div>";
			}
		}else{
			print "0";
		}
?>
		</div>
		<H2>Number of Project Media Groups</H2><div style="clear:both; height:1px;"><!-- empty --></div>
<?php
		if ($va_media_counts[0] > 0) {
			print "<div id='mediaPubOptions'><br/><b>Publish with:</b> ".caNavLink($this->request, _t("unrestricted download"), "button buttonSmall", "MyProjects", "Dashboard", "publishAllMedia", array("project_id" => $t_project->get("project_id"), "published" => 1))."&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("restricted download"), "button buttonSmall", "MyProjects", "Dashboard", "publishAllMedia", array("project_id" => $t_project->get("project_id"), "published" => 2));
			print "<br/><br/>"._t("Only unpublished media will be affected.")."</div>\n";
		}
?>
	</div>
	<div class="listItemLtBlue">
		<div class="dataCol">
<?php
		if($vn_num_media_files = (int)$t_project->numMediaFiles()){
			if($va_media_file_counts[1]){
				print (int)$va_media_file_counts[1]." ".$t_media->formatPublishedText(1)."<br/>"; 
			}
			print (int)$va_media_file_counts[2]." ".$t_media->formatPublishedText(2)."<br/>";
			print (int)$va_media_file_counts[0]." ".$t_media->formatPublishedText(0)."<br/>";
			print _t('<em>(%1 total)</em>', $vn_num_media_files);
			#if ($va_media_file_counts[0] > 0) {
			#	print "<div style='padding-top:10px;'>".caNavLink($this->request, _t("Publish unpublished media files (%1)", $va_media_file_counts[0]), "button buttonSmall", "MyProjects", "Dashboard", "publishAllMediaFiles", array("project_id" => $t_project->get("project_id")))."</div>";
			#}
		}else{
			print "0";
		}
?>
		</div>
		<H2>Number of Project Media Files</H2><div style="clear:both; height:1px;"><!-- empty --></div>
	</div>		
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print $t_project->numMediaViews()."/".$t_project->numDownloads(); ?></div>
		<H2>Project Media Views/ Downloads</H2>
	</div>
<?php
	if($vn_num_read_only_media = $t_project->numReadOnlyMedia()){
?>
	<div class="listItemLtBlue">
		<div class="dataCol">
<?php
		print $vn_num_read_only_media;
?>
		</div>
		<H2>Number of Read Only/Linked Media Groups</H2><div style="clear:both; height:1px;"><!-- empty --></div>
	</div>	

<?php
	}
?>
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
<a name='dashboardSpecimen'></a>
<div id="dashboardMedia">
	<div class="tealRule"><!-- empty --></div>
	<div style="float:right; padding-top:10px;"><?php print caNavLink($this->request, _t("View as List"), "button buttonLarge", "MyProjects", "Specimens", "listItems")."&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("New Specimen"), "button buttonLarge", "MyProjects", "Specimens", "lookupSpecimen"); ?></div>
<?php
	$vs_specimens_group_by = $this->getVar("specimens_group_by");
	print "<div style='float:right; clear:right; text-align:right; padding:5px 0px 5px 0px;'><b>Group by:</b> ";
	print (($vs_specimens_group_by == "specimen") ? "<b>" : "").caNavLink($this->request, "Specimen Number", "", "MyProjects", "Dashboard", "dashboard", array("specimens_group_by" => "specimen")).(($vs_specimens_group_by == "specimen") ? "</b>" : "")." | ";
	print (($vs_specimens_group_by == "genus") ? "<b>" : "").caNavLink($this->request, "Genus", "", "MyProjects", "Dashboard", "dashboard", array("specimens_group_by" => "genus")).(($vs_specimens_group_by == "genus") ? "</b>" : "")." | ";
	print (($vs_specimens_group_by == "species") ? "<b>" : "").caNavLink($this->request, "Species", "", "MyProjects", "Dashboard", "dashboard", array("specimens_group_by" => "species")).(($vs_specimens_group_by == "species") ? "</b>" : "");
	print "</div>";
	$t_specimen = new ms_specimens();
	switch($vs_specimens_group_by){
		case "genus":
		case "species":
			$va_specimens_by_taxomony = $t_project->getProjectSpecimensByTaxonomy(null, $vs_specimens_group_by);
			$vn_count = $va_specimens_by_taxomony["numSpecimen"];
			$va_specimens = $va_specimens_by_taxomony["specimen"];
?>
			<H1><?php print $vn_count." Project Specimen".((sizeof($va_specimens) == 1) ? "" : "s"); ?></H1>
			<br style="clear:both;" />
<?php

			if(is_array($va_specimens) && ($vn_num_media = sizeof($va_specimens))){
				$vn_taxon_count = 1;
				foreach($va_specimens as $vs_taxon => $va_taxon_specimen) {
					$vn_num_media = is_array($va_taxon_specimen['media']) ? sizeof($va_taxon_specimen['media']) : 0;

					print "<div class='projectMediaContainer'>";
					print "<div class='projectMedia".(($vn_num_media > 1) ? " projectMediaSlideCycle" : "")."'>";
			
					if (is_array($va_taxon_specimen['media']) && ($vn_num_media > 0)) {
						$vn_max = 3;
						$c = 0;
						foreach($va_taxon_specimen['media'] as $vn_media_id => $va_media) {
							$c++;
							if (!($vs_media_tag = $va_media['tags']['preview190'])) {
								$vs_media_tag = "<div class='projectMediaPlaceholder'> </div>";
							}
							print "<div class='projectMediaSlide'>".caNavLink($this->request, $vs_media_tag, "", "MyProjects", "Dashboard", "specimenByTaxonomy", array("taxon_id" => $va_taxon_specimen['taxon_id']))."</div>";
							//print "<span class='mediaID'>M{$vn_media_id}</span>";
							if($c == $vn_max){
								break;
							}
						}
					} else {
						print "<div class='projectMediaPlaceholder'> </div>";
					}
					print "</div><!-- end projectMedia -->";
					$vs_genus = "";
					if($vs_specimens_group_by == "species"){
						$vs_genus = $va_taxon_specimen["genus"]." ";
					}
					print "<div class='projectMediaSlideCaption'><b><em>".$vs_genus.$vs_taxon."</em></b><br/>".caNavLink($this->request, sizeof($va_taxon_specimen["specimens"])." Specimen".((sizeof($va_taxon_specimen["specimens"]) != 1) ? "s" : ""), "", "MyProjects", "Dashboard", "specimenByTaxonomy", array("taxon_id" => $va_taxon_specimen['taxon_id']))."</div>\n";
					print "</div><!-- end projectMediaContainer -->";

					$vn_taxon_count++;
				}
			}else{
				print "<H2>"._t("Your project has no specimens.  Use the \"NEW SPECIMEN\" button to add specimens, to which media may be added.")."</H2>";
			}

		break;
		# --------------------------------------------------------------------
		default:
			$vs_order_by = $this->getVar("specimens_order_by");
			$va_specimens = $t_project->getProjectSpecimens(null, $vs_order_by);
?>
			<H1><?php print sizeof($va_specimens)." Project Specimen".((sizeof($va_specimens) == 1) ? "" : "s"); ?></H1>
<?php
			if(is_array($va_specimens) && ($vn_num_media = sizeof($va_specimens))){
				print "<div style='text-align:right; margin:5px 0px 5px 0px; clear:right;'><b>Order by:</b> ".(($vs_order_by == "number") ? "<b>" : "").caNavLink($this->request, "Specimen number", "", "MyProjects", "Dashboard", "dashboard", array("specimens_order_by" => "number")).(($vs_order_by == "number") ? "</b>" : "")." | ".(($vs_order_by == "taxon") ? "<b>" : "").caNavLink($this->request, "Taxonomic name", "", "MyProjects", "Dashboard", "dashboard", array("specimens_order_by" => "taxon")).(($vs_order_by == "taxon") ? "</b>" : "")."</div>";
		
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
							//print ($vs_element = $va_specimen['element']) ? " ({$vs_element})" : "";
					if($vs_uuid_id = $va_specimen["uuid"]){
						print "<div style='margin-top:3px; '><a href='https://www.idigbio.org/portal/records/".$vs_uuid_id."' target='_blank' class='blueText' style='text-decoration:none; font-weight:bold;'>iDigBio <i class='fa fa-external-link'></i></a></div>";
					}
					print "</div>\n";
					print "</div><!-- end projectMediaContainer -->";
				}
			}else{
				print "<H2>"._t("Your project has no specimens.  Use the \"NEW SPECIMEN\" button to add specimens, to which media may be added.")."</H2>";
			}
	}
?>
</div><!-- end dashboardMedia -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.projectMediaSlideCycle').cycle();
	});
</script>