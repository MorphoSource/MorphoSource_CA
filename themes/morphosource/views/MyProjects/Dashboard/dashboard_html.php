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
	$vn_specimen_with_family = $t_project->getProjectSpecimensCountWithFamily(array("published_media_only" => true));
	
	$va_media_counts = $this->getVar('media_counts');
	$va_media_file_counts = $this->getVar('media_file_counts');
	$t_media = new ms_media();

?>
<div id="dashboardColLeft">
	<div class="dashboardButtons" style="margin: 15px 0px 15px 0px">
	<?php
		if($this->request->user->canDoAction("is_administrator") || ($this->request->user->get("user_id") == $t_project->get("user_id"))){
			print "&nbsp;".caNavLink($this->request, _t("Project Settings"), "button buttonMedium", "MyProjects", "Project", "form", array("project_id" => $t_project->get("project_id")));
			print "&nbsp;".caNavLink($this->request, _t("Manage Members"), "button buttonMedium", "MyProjects", "Members", "listForm");
		}
		if($this->request->user->canDoAction('batch_upload_enabled')){
			print "&nbsp;".caNavLink($this->request, _t("Batch Import"), "button buttonMedium", "MyProjects", "BatchImport", "overview");
		}
	?>
	</div>
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
			print "<br/><br/><a href='".$t_project->get("url")."' target='_blank' title='".$t_project->get("url")."'><b>"._t("More Information")."</b></a>\n";
		}
		if($t_project->get("publication_status")){
			print "<div id='projectLink' style='display:none;'>".caNavUrl($this->request, "Detail", "ProjectDetail", "Show", array("project_id" => $t_project->get("project_id")))."</div>";
			print "<br/><br/><b>"._t("Your project is public.")."</b><br/>".caNavLink($this->request, _t("View public page"), "publicProjectLink", "Detail", "ProjectDetail", "Show", array("project_id" => $t_project->get("project_id")))." or <a href='#' onClick='copyToClipboard(\"#projectLink\"); return false;' title='click to copy link to clipboard'>copy to clipboard <i class='fa fa-clipboard'></i></a><br/>";
		}else{
			print "<br/><br/><b>"._t("Your project is private.")."</b>";
		}
?>
	</div><!-- end dashboardAbstract -->


<div id='usageReports' style='margin-top: 30px; margin-bottom: 20px;'>
	<div class="tealRule"></div>
	<h2 style="padding-bottom: 2px;">Metadata and usage reports</h2>
	<div style="margin-top: 10px; margin-left: 5px;">
<?php
	print caNavLink($this->request, "<i class='fa fa-download'></i> Project media", "button buttonSmall", "MyProjects", "Dashboard", "exportMediaReport");
	print "<span style='margin-left:10px;'></span>";
	print caNavLink($this->request, "<i class='fa fa-download'></i> All media of project specimens", "button buttonSmall", "MyProjects", "Dashboard", "exportSpecimenMediaReport");
?>
	</div>
	<div style='margin-top: 10px; margin-left:5px;'><i>Warning: For large projects, this may take up to several minutes</i></div>
</div>

<?php
	print $this->render('Dashboard/pending_download_requests_html.php');
	print $this->render('Dashboard/media_movement_requests_html.php');	
?>
</div><!-- end dashboardColLeft -->
<div id="dashboardColRight">
	<div class="dashboardButtons">
<?php
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
	<!-- entity view header -->
	<div id='dashboardMediaHeader'>
<?php
		print "<div class='dashboardMediaHeaderItem' style='width: 65%;'>";
		$va_type_opts = array('Specimens' => 's', 
			'Media groups' => 'm');
		$vs_entity_type = $this->getVar('entity_type');
		if (!in_array($vs_entity_type, ['s', 'm'])) {
			$vs_entity_type = 's';
		}
		print "<select class='dashboardMediaHeaderSelect' 
			id='entityTypeSelect'>";
		foreach ($va_type_opts as $label => $type) {
			print "<option value='".$type."'".
			(($vs_entity_type == $type) ? "selected" : "" ).">".
			$label."</option>";
		}
		print "</select>";

		if ($vs_entity_type == 's') {
			print caNavLink($this->request, "New Specimen", 
				"button buttonMedium buttonWhiteBorder", "MyProjects", 
				"Specimens", "lookupSpecimen");
			print "<a href='#' name='specimenMediaBatchButton' ". 
				"class='button buttonMedium buttonWhiteBorder' id='specimenMediaBatchButton' ".
				"title='Batch edit media groups' >Batch Edit Media</a>";
			print caNavLink($this->request, _t("Add All Media to Cart"), 
				"button buttonMedium buttonWhiteBorder", "", "MediaCart", 
				"addProjectMediaToCart", 
				array("project_id" => $t_project->get("project_id")));
		} else if ($vs_entity_type == 'm') {
			print "<a href='#' name='mediaBatchButton' ". 
				"class='button buttonMedium buttonWhiteBorder' id='mediaBatchButton' ".
				"title='Batch edit media groups' >Batch Edit Media</a>";
			print caNavLink($this->request, _t("Add All Media to Cart"), 
				"button buttonMedium buttonWhiteBorder", "", "MediaCart", 
				"addProjectMediaToCart", 
				array("project_id" => $t_project->get("project_id")));
		}

		$sort_opt_selected = $this->getVar("specimens_group_by");
		if (!in_array($sort_opt_selected, ['n', 't', 'd', 'p', 'a', 'm', 'u', 'v'])) {
			$sort_opt_selected = 'n';
		}

		print "</div>";

		print "<div class='dashboardMediaHeaderItem' style='width: 26%;'>";
		$sort_options = array(
			'Flat list' => array(
				'Specimen Number' => 'n', 
				'Taxon Name' => 't',
				'Description' => 'd',
				'Date Added' => 'a', 
				'Date Modified' => 'm',
				'Media Publication' => 'p'), 
			'Taxonomy tree' => array(
				'User-entered data' => 'u', 
				"External taxonomy" => 'v'));
		print "<span class='entityViewHeaderText'>sort by</span>";
		print "<select class='dashboardMediaHeaderSelect' id='mediaSortSelect'>";
		foreach ($sort_options as $group => $opts) {
			print "<optgroup label='".$group."'>";
			foreach ($opts as $label => $value) {
				print "<option value='".$value."'".
				(($sort_opt_selected == $value) ? "selected" : "" ).">".$label.
				"</option>";
			}
			print "</optgroup>";
		}
		print "</select>";
		print "</div>";	

		$vs_entity_format = $this->getVar('entity_format');
		if (!in_array($vs_entity_format, ['t', 'l'])) {
			$vs_entity_format = 't';
		}
		print "<div class='dashboardMediaHeaderItem' style='width: 8%;'>";
		print "<a href='#' id='entityFormatIconTile' style='float: right;' class='".
			($vs_entity_format != 't' ? "entityFormatIconInactive" : "").
			"' style='display: inline-block;'>
			<img src='/themes/morphosource/graphics/morphosource/ic_view_module_white_24px.svg' 
			onerror='this.src=\"/themes/morphosource/graphics/morphosource/ic_view_module_white_24dp_1x.png\"' 
			style='display: inline-block;' /></a>";
		print "<a href='#' id='entityFormatIconList' style='float: right;' class='".
			($vs_entity_format != 'l' ? "entityFormatIconInactive" : "").
			"'  style='display: inline-block;'>
			<img src='/themes/morphosource/graphics/morphosource/ic_view_headline_white_24px.svg' 
			onerror='this.src=\"/themes/morphosource/graphics/morphosource/ic_view_headline_white_24dp_1x.png\"' 
			style='display: inline-block;'></a>";
		print "</div>";	
?>
	</div><!-- end dashboardMediaHeader --> 
	<!-- end entity view header -->


	<!-- batch media edit pane (hidden by default) -->
<?php
	$vs_batch_vis = $this->getVar('batch_vis');
	print "<div id='entityMediaBatchEditContainer' style='".( $vs_batch_vis == 1 ? "" : "display: none;")."'>";

		// tab buttons
		print "<a href='#' name='generalButton' ".
			"class='tab tabActive' id='generalButton' ".
			"title='Batch Edit Media Menu' ".
			">Batch Edit</a>";

		print "<a href='#' name='mediaBatchSelectNoneButton' ". 
				"class='button buttonLarge batchTopButton' 
				id='mediaBatchSelectNoneButton' ".
				"title='Unselect all media groups' 
				style='display: none; float: right;'>Select None</a>";
		print "<a href='#' name='mediaBatchSelectAllButton' ". 
				"class='button buttonLarge batchTopButton' 
				id='mediaBatchSelectAllButton' ".
				"title='Select all media groups' 
				style='display: none; float: right;'>Select All</a>";

		print "<div id='entityBatchPaneContainer'>";
		// form divs
		print $this->render('Dashboard/entity_batch_html.php');
		print "</div><!-- end entityBatchPaneContainer -->";
?>
	</div> <!-- end entityMediaBatchEditContainer -->
	<!-- end batch media edit pane -->
<?php
	if ($sort_opt_selected == 'u' || $sort_opt_selected == 'v') {
		print "<div class='treeSortButtons'>";
		print "<a href='#' name='expandNestButton' ". 
			"class='button buttonMedium' id='expandNestButton' ".
			"title='Expand all taxonomy tree nodes' 
			style='margin-right: 10px;'>Expand All Tree Nodes</a>";
		print "<a href='#' name='collapseNestButton' ". 
			"class='button buttonMedium' id='collapseNestButton' ".
			"title='Collapse all taxonomy tree nodes'>
			Collapse All Tree Nodes</a>";
		print "</div><!-- end treeSortButtons -->";
	}
?>
	<div id='entityView'>
<?php
		print $this->render('Dashboard/entity_view_html.php');		
?>
	</div>
	<!-- end entityView -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.projectMediaSlideCycle').cycle();

		var entityFormat = '<?php print($vs_entity_format); ?>';
		if (!entityFormat || entityFormat == 'undefined') { 
			entityFormat = 't'; 
		};
		if (entityFormat == 'l') {
			jQuery('#entityContainerTile').hide();
			jQuery('#entityContainerList').show();
			jQuery('#entityFormatIconTile').addClass('entityFormatIconInactive');
			jQuery('#entityFormatIconList').removeClass('entityFormatIconInactive');
			jQuery('.entityBatchCheck').prop('checked', false);
		} else if (entityFormat == 't') {
			jQuery('#entityContainerTile').show();
			jQuery('#entityContainerList').hide();
			jQuery('#entityFormatIconTile').removeClass('entityFormatIconInactive');
			jQuery('#entityFormatIconList').addClass('entityFormatIconInactive');
			jQuery('.entityBatchCheck').prop('checked', false);
		}
	});

	// Entity view header behavior

	// When sort select value changes, reload page with new value
	jQuery('#mediaSortSelect').change(function() {
		if (jQuery('#entityContainerTile').is(':visible')) {
			var entityFormat = 't';
		} else if (jQuery('#entityContainerList').is(':visible')) {
			var entityFormat = 'l';
		}

		if (jQuery('#mediaSortSelect').val() == 'p') {
			var entityType = 'm';
		} else {
			var entityType = jQuery('#entityTypeSelect').val();
		}

		window.location.href = 
			'/MyProjects/Dashboard/dashboard/s/'+ 
			jQuery('#mediaSortSelect').val() + '/t/' + 
			entityType + '/f/' + 
			entityFormat;
	});

	// When type select value changes, reload page with new value
	jQuery('#entityTypeSelect').change(function() {
		if (jQuery('#entityContainerTile').is(':visible')) {
			var entityFormat = 't';
		} else if (jQuery('#entityContainerList').is(':visible')) {
			var entityFormat = 'l';
		} else {
			var entityFormat = 't';
		}

		window.location.href = 
			'/MyProjects/Dashboard/dashboard/s/' + jQuery('#mediaSortSelect').val() + 
			'/t/' + jQuery('#entityTypeSelect').val() + 
			'/f/' + entityFormat;
	});


	// List/tile view toggles, refer to elements from entity_view_html.php
	jQuery('#entityFormatIconTile').click(function () {
		jQuery('#entityContainerTile').show();
		jQuery('#entityContainerList').hide();
		jQuery('#entityFormatIconTile').removeClass('entityFormatIconInactive');
		jQuery('#entityFormatIconList').addClass('entityFormatIconInactive');
		jQuery('.entityBatchCheck').prop('checked', false);
		return false;
	});

	jQuery('#entityFormatIconList').click(function () {
		jQuery('#entityContainerTile').hide();
		jQuery('#entityContainerList').show();
		jQuery('#entityFormatIconTile').addClass('entityFormatIconInactive');
		jQuery('#entityFormatIconList').removeClass('entityFormatIconInactive');
		jQuery('.entityBatchCheck').prop('checked', false);
		return false;
	});

	// Batch edit menu and related buttons behavior

	// Bibliography autocomplete field from entity_batch_html.php
	jQuery('#msBibliographyID').autocomplete({ 
		source: '<?php print caNavUrl($this->request, 'lookup', 'Bibliography', 'Get', array("max" => 500, "quickadd" => false)); ?>',
		minLength: 3, delay: 800, html: true,
		select: function(event, ui) {
			var bibliography_id = parseInt(ui.item.id);
			if (bibliography_id < 1) {
				// nothing found...
				jQuery("#mediaBibLookupContainer").load("<?php print caNavUrl($this->request, 'MyProjects', 'Bibliography', 'form', array('media_id' => $pn_media_id)); ?>");
			} else {
				// found an id
				jQuery('input[name=bibliography_id]').val(bibliography_id);
			}
		}
	}).click(function() { this.select(); });

	jQuery('.buttonGray').click(function() {
		return false;
	});

	jQuery('#specimenMediaBatchButton').click(function () {
		if (jQuery('#entityContainerTile').is(':visible')) {
			var entityFormat = 't';
		} else if (jQuery('#entityContainerList').is(':visible')) {
			var entityFormat = 'l';
		} else {
			var entityFormat = 't';
		}

		window.location.href = 
			'/MyProjects/Dashboard/dashboard/s/' + jQuery('#mediaSortSelect').val() + 
			'/t/m' + '/f/' + entityFormat + '/b/1';
		return false;
	});

	jQuery('#mediaBatchButton').click(function () {
		jQuery('#entityMediaBatchEditContainer').show();
		jQuery('#entityListHeaderEditText').show();
		jQuery('#mediaBatchSelectAllButton').show();
		jQuery('#mediaBatchSelectNoneButton').show();
		jQuery('.entityBatchCheck').show();
		return false;
	});

	jQuery('#mediaBatchSelectAllButton').click(function () {
		if (jQuery('#entityContainerTile').is(':visible')) {
			jQuery('#entityContainerTile').
				find('.entityBatchCheck').prop('checked', true);
		} else if (jQuery('#entityContainerList').is(':visible')) {
			jQuery('#entityContainerList').
				find('.entityBatchCheck').prop('checked', true);
		}
		return false;
	});

	jQuery('#mediaBatchSelectNoneButton').click(function () {
		jQuery('.entityBatchCheck').prop('checked', false);
		return false;

	});

	jQuery('#generalButton').click(function () {
		if (!jQuery('#generalButton').hasClass('buttonGray')) {
			// Hide/show form divs, Make buttons active/gray
			jQuery('#mediaBatchGeneralContainer').show();
			jQuery('#mediaBatchScanOriginContainer').hide();
			jQuery('#generalButton').addClass('tabActive');
			jQuery('#scanOriginButton').removeClass('tabActive');
		}
		return false;
	});

	jQuery('#scanOriginButton').click(function () {
		return false; // disable temporarily
		if (!jQuery('#scanOriginButton').hasClass('buttonGray')) {
			// Hide/show form divs, Make buttons active/gray
			jQuery('#mediaBatchScanOriginContainer').show();
			jQuery('#mediaBatchGeneralContainer').hide();
			jQuery('#scanOriginButton').addClass('tabActive');
			jQuery('#generalButton').removeClass('tabActive');
		}
		return false;
	});

	jQuery('#batchSaveButton').click(function () {
		var formName = '';
		if (jQuery('#mediaBatchGeneralContainer').is(':visible')) {
			formName = 'batchGeneralForm';
		} else if (jQuery('#mediaBatchScanOriginContainer').is(':visible')) {
			formName = 'batchScanOriginForm';
		}
		jQuery('.entityBatchCheck').attr('form', formName);
		jQuery('#' + formName).submit();
		return false;
	});

	jQuery('#batchCancelButton').click(function () {
		jQuery('#entityMediaBatchEditContainer').hide();
		jQuery('#entityListHeaderEditText').hide();
		jQuery('#mediaBatchSelectAllButton').hide();
		jQuery('#mediaBatchSelectNoneButton').hide();
		jQuery('.entityBatchCheck').hide();
		return false;
	});

	// Taxonomically (class, order, etc.) nested entity display behavior

	jQuery('.entityGroupToggle').click(function() {
		var group_div = jQuery(this).closest('.entityNestGroupContainer');
		var subgroup_div = group_div.children('.entityNestGroupContainer');

		var target_img = jQuery(this).children('img').attr('src');
		var img_root = target_img.substring(0, Math.max(
			target_img.lastIndexOf("/"), target_img.lastIndexOf("\\")));

		if (subgroup_div.is(':visible')) {
			subgroup_div.hide();
			jQuery(this).children('img').attr('src', 
				img_root+'/ic_chevron_right_black_24px.svg');
			jQuery(this).children('img').attr('onerror', 
				'this.src=\"' + img_root + 
				'/ic_chevron_right_black_24px.svg\"');
		} else {
			subgroup_div.show();
			jQuery(this).children('img').attr('src', 
				img_root+'/ic_keyboard_arrow_down_black_24px.svg');
			jQuery(this).children('img').attr('onerror', 
				'this.src=\"' + img_root + 
				'/ic_keyboard_arrow_down_black_24px.svg\"');
		}
		return false;
	});

	jQuery('#expandNestButton').click(function () {
		var target_div = jQuery('.entityNestContainer').
			find('.entityNestGroupContainer');
		target_div.show();

		var target_img =  target_div.children('.entityGroupLabel').
			find('.entityGroupToggle').find('img');
		var img_src = target_img.attr('src');
		var img_root = img_src.substring(0, Math.max(
			img_src.lastIndexOf("/"), img_src.lastIndexOf("\\")));
		target_img.attr('src', 
			img_root + '/ic_keyboard_arrow_down_black_24px.svg');
		target_img.attr('onerror', 
			'this.src=\"' + img_root + 
			'/ic_keyboard_arrow_down_black_24px.svg\"');

		return false; 
	});

	jQuery('#collapseNestButton').click(function () {
		var top_level_div = jQuery('.entityNestContainer').
			children('.entityNestGroupContainer');
		var target_div = top_level_div.find('.entityNestGroupContainer');
		target_div.hide();

		var target_img =  jQuery('.entityNestContainer').
			find('.entityNestGroupContainer').
			children('.entityGroupLabel').
			find('.entityGroupToggle').find('img');
		var img_src = target_img.attr('src');
		var img_root = img_src.substring(0, Math.max(
			img_src.lastIndexOf("/"), img_src.lastIndexOf("\\")));
		target_img.attr('src', img_root + '/ic_chevron_right_black_24px.svg');
		target_img.attr('onerror', 'this.src=\"' + img_root + 
			'/ic_chevron_right_black_24px.svg\"');

		return false; 
	});
	
</script>