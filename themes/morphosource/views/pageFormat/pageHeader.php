<?php $this->request->deauthenticate(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php print $this->request->config->get('html_page_title'); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php print MetaTagManager::getHTML(); ?>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
	
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/global.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/morphosource.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/videojs/video-js.css" type="text/css" media="screen" />
 	<!--[if IE]>
    <link rel="stylesheet" type="text/css" href="<?php print $this->request->getThemeUrlPath(true); ?>/css/iestyles.css" />
	<![endif]-->

	<!--[if (!IE)|(gte IE 8)]><!-->
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/viewer-datauri.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/plain-datauri.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/js/DV/plain.css" media="screen" rel="stylesheet" type="text/css" />
	<!--<![endif]-->
	<!--[if lte IE 7]>
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/viewer.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getBaseUrlPath(); ?>/plain.css" media="screen" rel="stylesheet" type="text/css" />
	<![endif]-->
	<link rel="stylesheet" href="<?php print $this->request->getBaseUrlPath(); ?>/js/jquery/jquery-tileviewer/jquery.tileviewer.css" type="text/css" media="screen" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/font-awesome-4.2.0/css/font-awesome.css" rel="stylesheet" type="text/css" />
<?php
	print JavascriptLoadManager::getLoadHTML($this->request->getBaseUrlPath());

	// get last search ('basic_search' is the find type used by the SearchController)
	$o_result_context = new ResultContext($this->request, 'ca_objects', 'basic_search');
	$vs_search = $o_result_context->getSearchExpression();
?>
	<script type="text/javascript">
		 jQuery(document).ready(function() {
			jQuery('#quickSearch').searchlight('<?php print $this->request->getBaseUrlPath(); ?>/index.php/Search/lookup', {showIcons: false, searchDelay: 100, minimumCharacters: 3, limitPerCategory: 3});
		});
		// initialize CA Utils
			var caUIUtils = caUI.initUtils();
	</script>
    
</head>
<body>
<div id="contentArea">
	<div id="header">
		<div id="logo">
<?php
			print caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/morphosource/morphosourceLogo.png' border='0'>", "", "", "", "");
?>
			<div class="byLine"><a href="https://www.duke.edu/" target="_blank">by Duke University</a></div>
		</div>
<?php
		if(((strToLower($this->request->getController()) == "splash")) || ((strToLower($this->request->getController()) == "about") && (strToLower($this->request->getAction()) == "news"))){
?>
			
<?php
		}
?>
		<div id="nav">
			<div id="searchBox"><div id="searchBoxBg"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get"><input type="text" name="search" value="<?php print ($vs_search) ? $vs_search : ''; ?>" onclick='jQuery("#quickSearch").select();' id="quickSearch"  autocomplete="off"/><a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/magGlass.png"></a></form></div><!-- end searchBoxBg --></div><!-- end searchBox -->
			<div id="navHeaderBar"><!-- empty --></div>
			<ul class="mainNav">
				<li style='position:relative;'><?php print caNavLink($this->request, _t("About"), "", "", "About", "home"); ?>
					<div class='jumpMenu' id='aboutJumpMenu'>
						<div><?php print caNavLink($this->request, 'Information for Users', 'blueText', '', 'About', 'userInfo'); ?></div>
						<div><?php print caNavLink($this->request, 'Information for Contributors', 'blueText', '', 'About', 'contributorInfo'); ?></div>
						<div><?php print caNavLink($this->request, 'Terms', 'blueText', '', 'About', 'terms'); ?></div>
						<div><?php print caNavLink($this->request, 'User Guide', 'blueText', '', 'About', 'userGuide'); ?></div>
                        <div><?php print caNavLink($this->request, 'How to Cite', 'blueText', '', 'About', 'howToCite'); ?></div>
						<div><?php print caNavLink($this->request, 'API', 'blueText', '', 'About', 'API'); ?></div>
						<div><?php print caNavLink($this->request, 'Data Reporting', 'blueText', '', 'About', 'report'); ?></div>
						<div><?php print caNavLink($this->request, 'MS 2.0 Launch', 'blueText', '', 'About', 'launch'); ?></div>
					</div>
				</li>
				<li><?php print caNavLink($this->request, _t("Browse"), "", "", "Browse", "Index"); ?></li>
				<li><?php print ($this->request->session->getVar('current_project_id')) ? caNavLink($this->request, _t("Dashboard"), "", "MyProjects", "Dashboard", "dashboard") : caNavLink($this->request, _t("Dashboard"), "", "MyProjects", "Dashboard", "projectList"); ?></li>
			</ul>
			<ul class="subNav">
<?php
			require_once(__CA_MODELS_DIR__."/ms_projects.php");
			$t_project = new ms_projects();
			if($this->request->isLoggedIn()) {
				#print "<li class='last'>".caNavLink($this->request, _t("Preferences"), "", "system", "Preferences", "EditProfilePrefs")."</li>";
				
				print "<li class='last'><a href='#' onClick='return false;'><i class='fa fa-user'></i></a>";
				print "<div class='jumpMenu' id='userJumpMenu'>\n";
				print "<div>".caNavLink($this->request, _t("Preferences"), "", "", "LoginReg", "profileForm")."</div>\n";
				print "<div>".caNavLink($this->request, _t("Logout"), "", "", "LoginReg", "logout")."</div>\n";
				print "</div>\n";
				print "</li>\n";
				print "<li>".caNavLink($this->request, _t("Stats"), "", "", "Stats", "dashboard")."</li>";
				print "<li>".caNavLink($this->request, _t("Media Cart")." <i class='fa fa-shopping-cart'></i>", "", "", "MediaCart", "cart")."</li>";
				# --- does user have media links shared with them
				$o_db = new Db();
				$q_media_links = $o_db->query("SELECT link_id, media_id FROM ms_media_shares where user_id = ? AND created_on > ".(time() - (60 * 60 * 24 * 30)), $this->request->user->get("user_id"));
				if($q_media_links->numRows()){
					print "<li><a href='#' onClick='return false;'>Shared Media</a>";
					print "<div class='jumpMenu' id='userJumpMenu'>\n";
					while($q_media_links->nextRow()){
						print "<div>".caNavLink($this->request, "M".$q_media_links->get("media_id"), "", "Detail", "MediaDetail", "Show", array("media_id" => $q_media_links->get("media_id")))."</div>\n";
					}
					print "</div>\n";
					print "</li>\n";
				}
				
				# --- display the current project if there is one
				if($this->request->session->getVar('current_project_id')){
					$t_project = new ms_projects($this->request->session->getVar('current_project_id'));
					print "<li style='text-transform:none;'>";
					print "<a href='#' onClick='return false;' class='ltBlueText'>".((strlen($t_project->get("name")) > 25) ? mb_substr($t_project->get("name"), 0, 25)."..." : $t_project->get("name"));
					print " <i class='fa fa-cog'></i></a>";
					print "<div class='jumpMenu' id='projectJumpMenu'>\n";
					if ($this->request->user->isFullAccessUser()) {
						print "<div>NEW:</div>";
						print "<div class='ltBlueBottomRule'>".caNavLink($this->request, _t("New Project"), "", "MyProjects", "Project", "form", array("new_project" => 1))."</div>";
					}
					print "<div>MANAGE:</div>";
					print "<div class='ltBlueBottomRule'>".caNavLink($this->request, _t("All download requests"), "", "MyProjects", "Dashboard", "manageAllDownloadRequests")."</div>\n";
					print "<div>VIEW PROJECT RECORDS:</div>";
					print "<div><a href='".caNavUrl($this->request, "MyProjects", "Dashboard", "dashboard")."'>"._t("Dashboard")."</a></div>\n";
					print "<div>".caNavLink($this->request, _t("Bibliography"), "", "MyProjects", "Bibliography", "ListItems")."</div>\n";
					print "<div>".caNavLink($this->request, _t("Taxonomy"), "", "MyProjects", "Taxonomy", "ListItems")."</div>\n";
					print "<div class='ltBlueBottomRule'>".caNavLink($this->request, _t("Facilities"), "", "MyProjects", "Facilities", "ListItems")."</div>\n";
					print "<div>NEW PROJECT RECORD:</div>";
					print "<div>".caNavLink($this->request, _t("New Specimen"), "", "MyProjects", "Specimens", "lookupSpecimen")."</div>\n";
					print "<div>".caNavLink($this->request, _t("New Bibliographic Citation"), "", "MyProjects", "Bibliography", "form")."</div>\n";
					print "<div>".caNavLink($this->request, _t("New Taxonomic Name"), "", "MyProjects", "Taxonomy", "form")."</div>\n";
					print "<div>".caNavLink($this->request, _t("New Facility"), "", "MyProjects", "Facilities", "form")."</div>\n";
					print "<div class='ltBlueBottomRule'>".caNavLink($this->request, _t("New Project"), "", "MyProjects", "Project", "form", array("new_project" => 1))."</div>";
					if($t_project->get("user_id") == $this->request->user->get("user_id")){
						print "<div>PROJECT ADMIN:</div>";
						print "<div>".caNavLink($this->request, _t("Project Info"), "", "MyProjects", "Project", "form", array("project_id" => $t_project->get("project_id")))."</div>";
						print "<div class='ltBlueBottomRule'>".caNavLink($this->request, _t("Manage Members"), "", "MyProjects", "Members", "listForm")."</div>";
					}
					$va_projects = $t_project->getProjectsForMember($this->request->user->get("user_id"));
					if(sizeof($va_projects)){
						print "<div>CHANGE PROJECT:</div>";
						foreach($va_projects as $va_project){
							if($va_project["membership_type"] == 1){
								print "<div>".caNavLink($this->request, $va_project["name"], "", "MyProjects", "Dashboard", "dashboard", array("select_project_id" => $va_project["project_id"]))."</div>";
							}else{
								print "<div>".caNavLink($this->request, $va_project["name"], "", "MyProjects", "ReadOnly", "dashboard", array("project_id" => $va_project["project_id"]))."</div>";
							}
						}
					}
					print "</div>\n";
					print "</li>\n";
				}else{
					$va_projects = $t_project->getProjectsForMember($this->request->user->get("user_id"));
					if ($this->request->user->isFullAccessUser() || sizeof($va_projects)) {
						print "<li style='text-transform:none;'>";
						print "<a href='#' onClick='return false;' class='ltBlueText'>Actions <i class='fa fa-cog'></i></a>";
						print "<div class='jumpMenu' id='projectJumpMenu'>\n";
						
						if ($this->request->user->isFullAccessUser()) {
							print "<div>NEW:</div>";
							print "<div class='ltBlueBottomRule'>".caNavLink($this->request, _t("New Project"), "", "MyProjects", "Project", "form", array("new_project" => 1))."</div>";
						}

						if(sizeof($va_projects)){	
							print "<div>MANAGE:</div>";
							print "<div class='ltBlueBottomRule'>".caNavLink($this->request, _t("All download requests"), "", "MyProjects", "Dashboard", "manageAllDownloadRequests")."</div>\n";
							print "<div>CHOOSE PROJECT:</div>";
							foreach($va_projects as $va_project){
								print "<div>";
								if($va_project["membership_type"] == 2){
									print caNavLink($this->request, $va_project["name"], "", "MyProjects", "ReadOnly", "dashboard", array("project_id" => $va_project["project_id"]))." <i>(Read Only)</i>";
								}else{
									print caNavLink($this->request, $va_project["name"], "", "MyProjects", "Dashboard", "dashboard", array("select_project_id" => $va_project["project_id"]));
								}
								print "</div>";
							}						
						}

						print "</div>\n";
						print "</li>\n";
					}
				}
			}else{
				print "<li class='last'>".caNavLink($this->request, _t("Login/Register"), "", "", "LoginReg", "form")."</li>";
			}
?>
			</ul>
<?php
			if($this->request->isLoggedIn()){
				if($this->request->user->canDoAction("is_administrator")){
					print "<ul class='subNavAdmin'><li class='last'>".caNavLink($this->request, _t("Stats"), "", "Administration", "Stats", "ListStats")."</li>";
					print "<li>".caNavLink($this->request, _t("Users"), "", "Administration", "Users", "ListUsers")."</li>";
					print "<li>".caNavLink($this->request, _t("Specimen"), "", "Administration", "List", "listItems", array("table" => "ms_specimens"))."</li>";
					print "<li>".caNavLink($this->request, _t("Taxonomy"), "", "Administration", "List", "listItems", array("table" => "ms_taxonomy_names"))."</li>";
					print "<li>".caNavLink($this->request, _t("Projects"), "", "Administration", "Projects", "ListProjects")."</li>";
					print "<li>".caNavLink($this->request, _t("Institutions"), "", "Administration", "List", "listItems", array("table" => "ms_institutions"))."</li>";
					print "<li>".caNavLink($this->request, _t("Facilities"), "", "Administration", "List", "listItems", array("table" => "ms_facilities"))."</li>";
					print "<li>".caNavLink($this->request, _t("Bibliography"), "", "Administration", "List", "listItems", array("table" => "ms_bibliography"))."</li>";
					print "<li class='last' style='padding-right:0px;'>Manage:</li>";
					print "</ul>";
				}
			}
?>
		</div>
		<div style="clear:both; height:1px;"><!-- empty --></div>
		<div style="margin-top: 20px; margin-bottom: 20px; border: 1px solid #003880; border-radius: 5px; width: 860px;">
			<div style="margin:15px">
				<h1 style="text-align: center; color: red;">MORPHOSOURCE IS TEMPORARILY UNAVAILABLE</h1>
				<div style="margin-top: 5px;">The MorphoSource 3D Data Repository is currently unavailable as we prepare for the launch of the new and improved MorphoSource 2.0 platform. You can still browse and preview media, specimens, and projects on the current site, but for now you will not be able to log in, download, or manage data. The new version of the repository will come with a refreshed user interface and numerous new features including web viewing of CT data, expanded support for 3D modalities like photogrammetry, and support for cultural heritage objects. We expect to launch MorphoSource 2.0 Monday, February 1st.</div>
			</div>
		</div>
	</div><!-- end header -->
	<div id="pageArea">
