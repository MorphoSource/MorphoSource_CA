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
				foot of <span style="font-style:normal;">Daubentonia madagscariensis</span> scanned at 38micron resolution at Duke Evolutionary Anthropology department's new high resolution microCt facility. <a href="https://smif.lab.duke.edu/Description.asp?ID=88" target="_blank">Click here if you are interested in details on the facility</a>
			</div><!-- end hpImageCaption -->
			<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/homePageFeaured.jpg">
		</div><!-- end hpImage -->
		
		<div id="hpFeatures">
			<div class="blueRule"><!-- empty --></div>
			<H1>Getting Started</H1>
			<div class="blueRule"><!-- empty --></div>
			<div class="hpFeature hpFeaturePadding">
				<H2 class="tealBottomRule">Find & Download Datasets</H2>
				<p style='text-align:center; padding-top:3px;'>
					<?php print caNavLink($this->request, _t("Browse"), "button buttonLarge", "", "Browse", "Index"); ?>&nbsp;&nbsp;&nbsp;&nbsp;or
					<div id="searchBox"><div id="searchBoxBg"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get"><input type="text" name="search" placeholder="enter search terms"/><a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/magGlass.png"></a></form></div><!-- end searchBoxBg --></div><!-- end searchBox -->
				</p>
				<p>
				</p>
			</div><!-- end hpfeature -->
			<div class="hpFeature">
				<H2 class="tealBottomRule">Useful Info</H2>
				<p>
<?php 
				print "<ul style='padding-left:20px;'><li>".caNavLink($this->request, 'Information for Users', 'blueText', '', 'About', 'userInfo')."</li>";
				print "<li>".caNavLink($this->request, 'Information for Contributors', 'blueText', '', 'About', 'contributorInfo')."</li>";
				print "<li>".caNavLink($this->request, 'Terms', 'blueText', '', 'About', 'terms')."</li>";
				print "<li>".caNavLink($this->request, 'User Guide', 'blueText', '', 'About', 'userGuide')."</li></ul>";
?>				
				</p>
			</div><!-- end hpfeature -->

<?php
			if(!$this->request->isLoggedIn()){
				print "<p style='text-align:center; clear:both; padding:40px 0px 0px 0px;'>".caNavLink($this->request, _t("Login or Register"), "button buttonLarge", "", "LoginReg", "form")."<p>";
			}
		if($x && $this->getVar("recent_media")){
?>
			<div class="hpFeature">
				<H2 class="tealTopBottomRule">Recently Published</H2>
				<?php print caNavLink($this->request, $this->getVar("recent_media"), '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $this->getVar("recent_media_id"))); ?>
			</div><!-- end hpfeature -->
<?php
		}
?>
		</div><!-- end hpfeatures -->
		<div id="hpStats">
			<div id="hpStatsCol1">
<?php
	$va_recent_media = $this->getVar("recent_media");
	if(is_array($va_recent_media) && sizeof($va_recent_media)){
?>
				<div class="blueRule"><!-- empty --></div>
				<H1>Recently Published</H1>
<?php
				foreach($va_recent_media as $vn_media_id => $vs_media){
					print "<div class='recentlyPublished'>".caNavLink($this->request, $vs_media, '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $vn_media_id))."</div>";	
				}
	}
?>
			</div>
			<div id="hpStatsCol2">
<?php
			if($this->request->isLoggedIn()){
				if($this->request->user->isFullAccessUser()){
					$t_project = new ms_projects();
					$va_projects = $t_project->getProjectsForMember($this->request->user->get("user_id"));
					if(sizeof($va_projects)){
	?>
						<div class="blueRule"><!-- empty --></div>
						<div class="tealBottomRule" style="margin-top:10px;">
						<H2 style="float:right;">last updated</H2>
						<H2>Your Project List</H2>
						</div>
	<?php
						foreach($va_projects as $va_project){
							print '<div class="projectListItem"><div class="date">'.date("m.d.y", $va_project["last_modified_on"]).'</div>';
							if($va_project["membership_type"] == 2){
								print caNavLink($this->request, $va_project["name"], "", "MyProjects", "ReadOnly", "dashboard", array("project_id" => $va_project["project_id"]))." <i>(Read Only)</i>";
							}else{
								print caNavLink($this->request, $va_project["name"], "", "MyProjects", "Dashboard", "dashboard", array("select_project_id" => $va_project["project_id"]));
							}
							print '</div><!-- end projectListItem -->';
						}
	?>
	<?php
					}else{
						print "<H2 style='text-align:center;'>You have no projects</H2>";	
					}
					print "<p style='text-align:center; margin-top:30px;'>".caNavLink($this->request, _t("Create a MorphoSource Project"), "button buttonLarge", "MyProjects", "Project", "form", array("new_project" => 1))."</p>";
				}else{
					print '<div class="tealRule"><!-- empty --></div>';
					if($this->request->user->isRequestedFullAccessUser()){
						print "<p style='text-align:center;'>Your request to contribute to MorphoSource is being reviewed.</p>";
					}else{
						print "<p style='text-align:center;'>Interested in creating a MorphoSource project?</p>";
						print "<p style='text-align:center; margin-top:30px;'>".caNavLink($this->request, _t("Become a Contributor"), "button buttonLarge", "MyProjects", "Dashboard", "projectList")."</p>";
					}
				}
			}else{
?>				
				<div id="hpText">
					<div class="blueRule"><!-- empty --></div>
					<H1>Welcome</H1>
					<div>
						<b>MorphoSource</b> is a project-based data archive that allows researchers to store and organize, share, and distribute their own 3d data. Furthermore any registered user can immediately search for and download 3d morphological data sets that have been made accessible through the consent of data authors.
		
						<br/><br/>The goal of <b>MorphoSource</b> is to provide rapid access to as many researchers as possible, large numbers of raw microCt data and surface meshes representing vouchered specimens.
		
						<br/><br/>File formats include tiff, dicom, stanford ply, and stl. The website is designed to be self explanatory and to assist you through the process of uploading media and associating it with meta data. If you are interested in using the site for your own data but have questions about security or anything else contact the site administrator. Otherwise please download whatever data you need and check back frequently to see what's new.
					</div>
				</div><!-- end hptext -->
<?php				
			}
?>
				
			</div><!-- end hpStatsCol2 -->
		</div><!-- end hpStats -->
		<div style="clear:both; height:1px;"><!-- empty --></div>