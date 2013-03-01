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
	print caNavLink($this->request, _t("New Project"), "button buttonLarge", "MyProjects", "Project", "form", array("new_project" => 1));
	if($this->request->user->get("user_id") == $t_project->get("user_id")){
		print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Project Info"), "button buttonLarge", "MyProjects", "Project", "form", array("project_id" => $t_project->get("project_id")));
		print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Manage Members"), "button buttonLarge", "MyProjects", "Members", "listForm");
	}
?>
	</div>
</div><!-- end dashboardColLeft -->
<div id="dashboardColRight">
	<div class="dashboardButtons">
<?php
	print caNavLink($this->request, _t("Specimens"), "button buttonLarge", "MyProjects", "Specimens", "list");
	print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Bibliography"), "button buttonLarge", "MyProjects", "Bibliography", "list");
	print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Taxonomy"), "button buttonLarge", "MyProjects", "Taxonomy", "list");
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
		<div class="dataCol"><?php print $t_project->numSpecimens(); ?></div>
		<H2>Number of Specimens</H2>
	</div>
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print $t_project->numCitations(); ?></div>
		<H2>Number of Citations</H2>
	</div>
	<div class="listItemLtBlue">
		<div class="dataCol"><?php print $t_project->get("created_on"); ?></div>
		<H2>Created On</H2>
	</div>
</div><!-- end dashboardColRight -->