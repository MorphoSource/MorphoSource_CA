<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/MyProjects/Dashboard/projects_list_html.php : 
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
 
	$va_projects = $this->getVar("projects");
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
		<div style="float:right;"><?php print caNavLink($this->request, _t("New Project"), "button buttonLarge", "MyProjects", "Project", "form", array("new_project" => 1)); ?></div>
		Dashboard
	</H1>
	<div id="dashboardProjectsList">
<?php
	if(sizeof($va_projects)){
?>
			<div class="blueTopBottomRule">
				<H2>
					<div class="column">last modified</div>
					<div class="column">your last login</div>
					<div class="column">number of media</div>
					Your Projects
				</H2>
			</div>
<?php
			$i = 0;
			$o_db = new Db();
			foreach($va_projects as $va_project){
				$q_last_accessed = $o_db->query("SELECT last_access_on FROM ms_project_users WHERE project_id = ? AND user_id = ?", $va_project["project_id"], $this->request->user->get("user_id"));
				$q_last_accessed->nextRow();
				$q_num_media = $o_db->query("SELECT media_id FROM ms_media WHERE project_id = ?", $va_project["project_id"]);
				$i++;
				print '<div class="listItem'.(($i < sizeof($va_projects)) ? "Lt" : "").'Blue">';
				print caNavLink($this->request, $va_project["name"], "", "MyProjects", "Dashboard", "dashboard", array("project_id" => $va_project["project_id"]));
				print '<div class="column">'.$va_project["last_modified_on"].'</div>';
				print '<div class="column">'.(($q_last_accessed->get("last_access_on")) ? $q_last_accessed->get("last_access_on") : "never").'</div>';
				print '<div class="column">'.(($q_num_media->numRows()) ? $q_num_media->numRows() : "0").'</div>';
				print "</div>";
			}
	}else{
		print "<div class='blueTopBottomRule'><H2 style='text-align:center;'>"._t("You do not have any projects, use the link above to create a project.")."</H2></div>";
	}
?>
	</div><!-- end dashboardProjectsList -->