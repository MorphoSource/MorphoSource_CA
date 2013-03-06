<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Splash/splash_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
?>
		<div id="hpImage">
			<div id="hpImageCaption">
				Quisque egestas arcu non est venenatis eget molestie nulla aliquet.
			</div><!-- end hpImageCaption -->
			<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/hpMainImage.jpg">
		</div><!-- end hpImage -->
		<div id="hpText">
			<div class="blueRule"><!-- empty --></div>
			<H1>Welcome</H1>
			<div>
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id sapien erat, eu ultricies nibh. Aliquam erat volutpat. Mauris nisi diam, luctus non varius vel, dapibus vel nunc. Ut libero elit, sollicitudin non sodales eget, tristique a est. Ut at tellus magna. Nullam mauris purus, mattis eu venenatis ut, volutpat in orci. Nam cursus varius accumsan. Donec eu eros eu purus interdum tristique. Pellentesque lacus lorem, venenatis ut tempus vitae, fermentum a sapien. Integer ut dolor eu est varius aliquet. 
			</div>
		</div><!-- end hptext -->
		<div id="hpFeatures">
			<div class="hpFeature hpFeaturePadding">
				<H2 class="tealTopBottomRule">Most Recent Project</H2>
				<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/hpFeature1.jpg">
			</div><!-- end hpfeature -->
			<div class="hpFeature hpFeaturePadding">
				<H2 class="tealTopBottomRule">Most Viewed Project</H2>
				<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/hpFeature2.jpg">
			</div><!-- end hpfeature -->
			<div class="hpFeature hpFeaturePadding">
				<H2 class="tealTopBottomRule">Project Spotlight</H2>
				<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/hpFeature3.jpg">
			</div><!-- end hpfeature -->
			<div class="hpFeature">
				<H2 class="tealTopBottomRule">Featured Member</H2>
				<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/hpFeature4.jpg">
			</div><!-- end hpfeature -->
		</div><!-- end hpfeatures -->
		<div id="hpStats">
			<div id="hpStatsCol1">
				<div class="tealRule"><!-- empty --></div>
<?php
			if($this->request->isLoggedIn()){
				$t_project = new ms_projects();
				$va_projects = $t_project->getProjectsForMember($this->request->user->get("user_id"));
				if(sizeof($va_projects)){
?>
					<H2 style="float:right;">last updated</H2>
					<H2>Your Project List</H2>
					<div class="tealTopBottomRule">
<?php
					foreach($va_projects as $va_project){
						print '<div class="projectListItem"><div class="date">'.date("m.d.y", $va_project["last_modified_on"]).'</div>';
						print caNavLink($this->request, $va_project["name"], "", "MyProjects", "Dashboard", "dashboard", array("project_id" => $va_project["project_id"]));
						print '</div><!-- end projectListItem -->';
					}
?>
					</div><!-- end tealTopBottomRule -->
<?php
				}else{
					print "<H2 style='text-align:center;'>You have no projects</H2>";	
				}
				print "<p style='text-align:center;'>".caNavLink($this->request, _t("Create a MorphoSource Project"), "button buttonLarge", "MyProjects", "Project", "form", array("new_project" => 1))."</p>";
			}else{
				print "<H2 style='text-align:center;'>New to MorphoSource?</H2>";
				print "<p style='text-align:center;'>".caNavLink($this->request, _t("Login or Register"), "button buttonLarge", "", "LoginReg", "login")."<p>";
			}
?>
				
			</div><!-- end hpStatsCol1 -->
			<div id="hpStatsCol2">
				<div class="tealRule"><!-- empty --></div>
				<H2>Recent Messages</H2>
				<div class="tealTopBottomRule">
					<div class="time">02.20.13</div>
					<div class="projectListItem" style="border-bottom:0px;">
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nibh arcu, mollis nec sodales id, commodo non erat. 
					</div><!-- end projectListItem -->
					<div class="time">02.20.13</div>
					<div class="projectListItem" style="border-bottom:0px;">
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nibh arcu, mollis nec sodales id, commodo non erat. 
					</div><!-- end projectListItem -->
					<div class="time">02.20.13</div>
					<div class="projectListItem" style="border-bottom:0px;">
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nibh arcu, mollis nec sodales id, commodo non erat. 
					</div><!-- end projectListItem -->
					<div style="clear:both; height:1px;"><!-- empty --></div>
				</div><!-- end tealTopBottomRule -->
			</div><!-- end hpStatsCol1 -->
		</div><!-- end hpStats -->
		<div style="clear:both; height:1px;"><!-- empty --></div>