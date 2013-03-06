<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php print $this->request->config->get('html_page_title'); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php print MetaTagManager::getHTML(); ?>
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
	
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/global.css" rel="stylesheet" type="text/css" />
	<link href="<?php print $this->request->getThemeUrlPath(true); ?>/css/morphosource.css" rel="stylesheet" type="text/css" />
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
		</div>
		<div id="nav">
			<div id="searchBox"><div id="searchBoxBg"><form name="header_search" action="<?php print caNavUrl($this->request, '', 'Search', 'Index'); ?>" method="get"><input type="text" name="search" value="<?php print ($vs_search) ? $vs_search : ''; ?>" onclick='jQuery("#quickSearch").select();' id="quickSearch"  autocomplete="off"/><a href="#" name="searchButtonSubmit" onclick="document.forms.header_search.submit(); return false;"><img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/morphosource/magGlass.png"></a></form></div><!-- end searchBoxBg --></div><!-- end searchBox -->
			<div id="navHeaderBar"><!-- empty --></div>
			<ul class="mainNav">
				<li><?php print caNavLink($this->request, _t("About"), "", "", "", ""); ?></li>
				<li><a href="#">Browse</a></li>
				<li><?php print ($this->request->session->getVar('current_project_id')) ? caNavLink($this->request, _t("Dashboard"), "", "MyProjects", "Dashboard", "dashboard") : caNavLink($this->request, _t("Dashboard"), "", "MyProjects", "Dashboard", "projectList"); ?></li>
			</ul>
			<ul class="subNav">
<?php
			if($this->request->isLoggedIn()){
				print "<li class='last'>".caNavLink($this->request, _t("Preferences"), "", "", "LoginReg", "logout")."</li>";
				print "<li>".caNavLink($this->request, _t("Logout"), "", "", "LoginReg", "logout")."</li>";
			}else{
				print "<li class='last'>".caNavLink($this->request, _t("Login/Register"), "", "", "LoginReg", "form")."</li>";
			}
?>
			</ul>
		</div>
		<div style="clear:both; height:1px;"><!-- empty --></div>
	</div><!-- end header -->
	<div id="pageArea">