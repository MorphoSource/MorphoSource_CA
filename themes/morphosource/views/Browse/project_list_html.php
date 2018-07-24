<?php
/* ----------------------------------------------------------------------
 * morphosource/views/Browse/project_list_html.php : 
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
	$pn_browse_project_id = $this->getVar("browse_project_id");
	$q_project = $this->getVar("projects");
	$t_project = new ms_projects();
?>
	<div id="browseList">
		<div class="tealRule"><!-- empty --></div>
		<H2><?php print _t("Projects"); ?></H2>
		<div class='' style='padding-left: 10px;'>
			Filter: <input type='text' name='filter' value='' onkeyup='filterProjectBrowse(this.value); return false;' size='20' style='border:1px solid #828282; margin-right: 10px; margin-left: 5px;'/>
			<span id='numberProjectsFiltered'></span>
			<br/><br/>
		</div><!-- end class list-filter -->
<?php
	if($q_project->numRows() > 0){
		print "<div class='browseListScrollContainer' style='padding-right: 10px;'>";
		while($q_project->nextRow()){
			$t_project->load($q_project->get("project_id"));
			
			// print "<a href='#' onClick='highlightLink(this); jQuery(\"#specimenResults\").load(\"".caNavUrl($this->request, '', 'Browse', 'specimenResults', array('project_id' => $q_project->get("project_id")))."\"); return false;' class='blueText".(($q_project->get("project_id") == $pn_browse_project_id) ? " browseItemSelected" : "")."'>".$q_project->get("name")."</a>";
			// print caNavLink($this->request, _t($q_project->get("name")), 'blueText', 'Detail', 'ProjectDetail', 'Show', array('project_id' => $q_project->get("project_id")));
			
			if($t_project->get("publication_status")){
				print "<div class='browseItem' style='margin-top: 25px; padding-top: 20px; border-top: 1px solid #578686;'>";

				print "<h2 style='padding: 0px'>".$q_project->get("name")."</h2>";
				print "<div style='display: none;'>".$q_project->get("project_id")."</div>";				

				if($q_project->get("abstract")){
					print "<div style='margin: 15px 0px 0px 0px;'>";
					if(mb_strlen($q_project->get("abstract")) > 250){
						print mb_substr($q_project->get("abstract"), 0, 250);
						print "... <span style='text-decoration:underline;' id='abstract".$q_project->get("project_id")."'>More &rsaquo;</span>";
						TooltipManager::add(
							"#abstract".$q_project->get("project_id"), "<p style='padding:10px 20px 10px 20px; font-size:11px;'>".$q_project->get('abstract')."</p>"
						);
					}else{
						print $q_project->get("abstract");	
					}
					print "</div>";
				}

				print "<div style='margin-top: 10px;'>";
				$va_members = array();
				$va_members = $t_project->getMembers();
				if(sizeof($va_members) > 0){
					print "<b>Members:</b> ";
					$vni = 0;
					foreach($va_members as $va_member){
						$vni++;
						print $va_member["fname"]." ".$va_member["lname"];
						if($vni < sizeof($va_members)){
							print ", ";
						}
					}
					print "<br/>";
				}

				print "<b>Data:</b> ";
				print (int)$t_project->numAllMedia(null, true)." published media";
				print ", ".(int)$t_project->numSpecimens()." specimens";
				print "</div>";

				print "<div style='margin-top: 15px;'>";
				print caNavLink($this->request, _t("Project Page"), 'button buttonSmall', 'Detail', 'ProjectDetail', 'Show', array('project_id' => $q_project->get("project_id")));
				print "<a href='#' style='margin-left: 15px;' class='button buttonSmall' onClick='highlightLink(this); jQuery(\"#specimenResults\").load(\"".caNavUrl($this->request, '', 'Browse', 'specimenResults', array('project_id' => $q_project->get("project_id")))."\"); return false;'>List project specimens</a>";
				print "</div>";
				print "</div>";

			}
					
		}
		print "</div>";
	}else{
		print "<p><b>"._t("There are no projects available")."</b></p>";
	}
?>
	</div>
	<div id="specimenResults"><!-- load the specimen results here --></div>
<script type="text/javascript">
<?php
	if($pn_browse_project_id){
?>
	jQuery(document).ready(function() {			
		jQuery('#specimenResults').load(
			'<?php print caNavUrl($this->request, '', 'Browse', 'specimenResults', array('project_id' => $pn_browse_project_id)); ?>'
		);
		return false;
	});
<?php	
	}
?>
	filterProjectBrowse = function (searchText) {
		if (!searchText) {
			jQuery('#browseList').find('.browseItem').show();
			jQuery('#numberProjectsFiltered').text('');
			return;
		}
		jQuery('#browseList').find('.browseItem').hide();
		var filtered = jQuery('#browseList')
			.find('.browseItem:iContains('+searchText+')');
		filtered.show();
		jQuery('#numberProjectsFiltered').text(filtered.length + ' projects found');
	}
</script>
