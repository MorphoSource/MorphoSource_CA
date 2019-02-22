<?php
	$vb_show_edit_link   = $this->getVar("show_edit_link");
	$va_media_counts     = $this->getVar("media_counts");
	$vn_all_linked_media = $this->getVar("all_linked_media");
	$vn_specimen_count   = $this->getVar("specimen_count");
	$vs_project_name     = $this->getVar("project_name");
	$va_project_members  = $this->getVar("project_members");
	$vs_project_abstract = $this->getVar("project_abstract");
	$vn_project_id	     = $this->getVar("project_id");
	$vs_project_url	     = $this->getVar("project_url");

	$vs_entity_type = $this->getVar('entity_type');
	if (!in_array($vs_entity_type, ['s', 'm'])) {
		$vs_entity_type = 's';
	}
	$sort_opt_selected = $this->getVar("specimens_group_by");
	if (!in_array($sort_opt_selected, ['n', 't', 'a', 'm', 'u', 'v'])) {
		$sort_opt_selected = 'n';
	}
	$vs_entity_format = $this->getVar('entity_format');
	if (!in_array($vs_entity_format, ['t', 'l'])) {
		$vs_entity_format = 't';
	}
?>
<div class="blueRule"><!-- empty --></div>
<?php
	$vs_edit_link = "";
	// $vs_back_link = caNavLink($this->request, _t("Back"), 'button buttonLarge', '', 'Browse', 'Index', array(), array('id' => 'back'));

	if ($vb_show_edit_link) {
		$vs_edit_link = caNavLink($this->request, _t("Edit Project"), 
			'button buttonLarge', 'MyProjects', 'Dashboard', 'dashboard', 
			array('select_project_id' => $vn_project_id) 
		);
	}
	
	print "<div style='float:right; padding:15px 0px 0px 15px;'>".$vs_edit_link."</div>";

?>
<H1>
<?php
	print _t("Project: ").$vs_project_name;
?>
</H1>
<div id="projectDetail">
	<div class="tealRule"><!-- empty --></div>
	<div class='InfoContainerRight'>
<?php
	if($vs_project_abstract){
		print "<H2>"._t("About the project")."</H2><div class='unit'>".$vs_project_abstract."</div>";
	}

	print "<H2>Metadata and usage reports</H2>";
	print "<div class='unit'>";
	print "<div>";
	print "<div style='margin-bottom: 10px; margin-top: 5px;'>".caNavLink($this->request, "<i class='fa fa-download'></i> Project media", "button buttonMedium", "Detail", "ProjectDetail", "exportMediaReport", ["project_id" => $vn_project_id]);
	print "<span style='margin-left:10px;'></span>";
	print caNavLink($this->request, "<i class='fa fa-download'></i> All media of project specimens", "button buttonMedium", "Detail", "ProjectDetail", "exportSpecimenMediaReport", ["project_id" => $vn_project_id]);
	print "</div>";
	print "<p style='margin-top: 0px; margin-left: 0px;'><i>Warning: For large projects, this may take up to several minutes</i></p>";
	print "</div>";
	print "</div>";
?>
	</div>
<?php
	print "<div class='InfoContainerLeft'>";
	print "<H2>"._t("Members")."</H2><div class='unit'>";

	if(sizeof($va_project_members) > 0){
		$vni = 0;
		foreach($va_project_members as $va_member){
			$vni++;
			print $va_member["fname"]." ".$va_member["lname"];
			if($vni < sizeof($va_project_members)){
				print ", ";
			}
		}
	}
	print "</div><!-- end unit -->\n";
	print "<H2>"._t("Data")."</H2><div class='unit'>";

	print ((int)$va_media_counts[1] + (int)$va_media_counts[2])." published media groups owned by this project<br/>";
	print (int)$vn_all_linked_media." published media groups associated with project specimens<br/>";
	print $vn_specimen_count." project specimens";
	print "</div><!-- end unit -->\n";
	if($vs_project_url){
		print "<H2>"._t("More Information")."</H2><div class='unit'>";
		print "<a href='".$vs_project_url."' target='_blank'>".$vs_project_url."</a>";
		print "</div><!-- end unit -->\n";
	}
	print "</div>";
?>
	<div id="dashboardMedia">
	<div class="tealRule"><!-- empty --></div>

<?php 
	if ((((int)$va_media_counts[1] + (int)$va_media_counts[2]) == 0) 
		&& ($vn_specimen_count == 0)) 
	{
		print "<H2>"._t("This project has no published specimen/media")."</H2>";
	} else {
?>
	<!-- entity view header -->
	<div id='dashboardMediaHeader'>
<?php
		print "<div class='dashboardMediaHeaderItem' style='width: 65%;'>";
		$va_type_opts = array('Specimens' => 's', 
			'Media groups' => 'm');
		print "<select class='dashboardMediaHeaderSelect' 
			id='entityTypeSelect'>";
		foreach ($va_type_opts as $label => $type) {
			print "<option value='".$type."'".
			(($vs_entity_type == $type) ? "selected" : "" ).">".
			$label."</option>";
		}
		print "</select>";
		print "</div>";

		print "<div class='dashboardMediaHeaderItem' style='width: 26%;'>";
		$sort_options = array(
			'Flat list' => array('Specimen number' => 'n', 
				'Taxon name' => 't', 'Date added' => 'a', 
				'Date modified' => 'm'), 
			'Taxonomy tree' => array('User-entered data' => 'u', 
				"VertNet data" => 'v'));
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
		print $this->render('entity_view_html.php');		
?>
	</div>
	<!-- end entityView -->
<?php
	} // Ends else statement before dashboardMediaHeader div
?>
</div><!-- end dashboardMedia -->

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
		} else if (entityFormat == 't') {
			jQuery('#entityContainerTile').show();
			jQuery('#entityContainerList').hide();
			jQuery('#entityFormatIconTile').removeClass('entityFormatIconInactive');
			jQuery('#entityFormatIconList').addClass('entityFormatIconInactive');
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
		window.location.href = 
			'/Detail/ProjectDetail/Show/project_id/' + 
			'<?php print($vn_project_id); ?>' + '/s/'+ 
			jQuery('#mediaSortSelect').val() + '/t/' + 
			jQuery('#entityTypeSelect').val() + '/f/' + 
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
			'/Detail/ProjectDetail/Show/project_id/' + 
			'<?php print($vn_project_id); ?>' + '/s/'+ 
			jQuery('#mediaSortSelect').val() + '/t/' + 
			jQuery('#entityTypeSelect').val() + '/f/' + 
			entityFormat;
	});


	// List/tile view toggles, refer to elements from entity_view_html.php
	jQuery('#entityFormatIconTile').click(function () {
		jQuery('#entityContainerTile').show();
		jQuery('#entityContainerList').hide();
		jQuery('#entityFormatIconTile').removeClass('entityFormatIconInactive');
		jQuery('#entityFormatIconList').addClass('entityFormatIconInactive');
		return false;
	});

	jQuery('#entityFormatIconList').click(function () {
		jQuery('#entityContainerTile').hide();
		jQuery('#entityContainerList').show();
		jQuery('#entityFormatIconTile').addClass('entityFormatIconInactive');
		jQuery('#entityFormatIconList').removeClass('entityFormatIconInactive');
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

</div>
