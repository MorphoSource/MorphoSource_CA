<?php
	// Formatting functions for specimen/media group entities

	function formatEntityGroup($va_entity_list, $vs_entity_format, $vs_entity_type, $request_class, $hide_group=false)
	{
		// Formats array of specimen/media group entities as tile or list items
		print "<div class='entityGroup'". 
			(($hide_group) ? "style='display: none;' " : "") .">";
		if ($vs_entity_format == 't') {
			foreach ($va_entity_list as $va_entity) {
				formatEntityTile($va_entity, $vs_entity_type, $request_class);
			}
		} elseif ($vs_entity_format == 'l') {
			foreach ($va_entity_list as $va_entity) {
				formatEntityListItem($va_entity, $vs_entity_type, 
					$request_class);
			}
		}
		print "</div><!-- end entityGroup -->";
	}

	function formatEntityListItem($va_entity, $vs_entity_type, $request_class)
	{
		// Formats single specimen/media group entity as list item
		print "<div class='listItemLtBlue entityListItem'>";
		$t_project = new ms_projects();
		if ($vs_entity_type == 's') {
			$t_specimen = new ms_specimens();

			// List item table cells

			$vs_specimen_taxonomy = 
				$t_specimen->getSpecimenTaxonomy($va_entity['specimen_id']);
			print "<div class='entityListCell' 
				style='display: table-cell; width: 150px;'>";
			if ($vs_specimen_taxonomy) { 
				print " <em>".join(" ", $vs_specimen_taxonomy)."</em>"; 
			}
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 120px;'>";
			print caNavLink($request_class->request, 
				$t_specimen->formatSpecimenName($va_entity), 
				'', "MyProjects", "Specimens", "form", 
				array("specimen_id" => $va_entity['specimen_id']), 
				array("target" => "_blank"));
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 80px;'>";
			$desc = $va_entity['description'];
			if (strlen($desc) > 40) {
				$desc = mb_substr($desc, 0, 40,'UTF-8')."...";
			} 
			print $desc;
			print "</div>";
			
			print "<div class='entityListCell' 
				style='display: table-cell; width: 105px; 
				text-align: center;'>";
			if ($va_entity['media']) {
				$media_count = count($va_entity['media']);
				$pub_media_count = count(
					array_filter($va_entity['media'], function ($a) { 
						if ($a['published'] == 1 || $a['published'] == 2) { 
							return true; 
						} 
				}));
			} else {
				$media_count = 0;
				$pub_media_count = 0;
			}
			print $pub_media_count."/".$media_count;
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 90px;'>";
			print caGetLocalizedDate($va_entity['created_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true));
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 90px;'>";
			print caGetLocalizedDate($va_entity['last_modified_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true));
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 55px; text-align: center;'>";
			if (($va_entity['project_id'] == $pn_project_id) || 
				($t_project->isFullAccessMember(
					$request_class->request->user->get('user_id'), 
					$va_entity['project_id']))) {
				print "<img src='/themes/morphosource/graphics/morphosource/ic_done_black_24px.svg' 
					onerror='this.src=\"/themes/morphosource/graphics/morphosource/ic_done_black_24dp_1x.png\"' 
					style='vertical-align: text-top;' />";
			}
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 80px; text-align: center;'>";
			if ($vs_uuid_id = $va_entity["uuid"]){
				print "<a href='https://www.idigbio.org/portal/records/".
					$vs_uuid_id."' target='_blank' class='blueText' 
					style='text-decoration:none; font-weight:bold;'>
					iDigBio <i class='fa fa-external-link'></i></a>";
			}
			print "</div>";
		} elseif ($vs_entity_type == 'm') {
			$t_media = new ms_media();
			$t_specimen = new ms_specimens();
			$pn_project_id = $request_class->getVar('project_id');

			// Is media read only? (I.e. neither project nor user own it?)
			$vb_read_only = 0;
			if (($va_entity['project_id'] != $pn_project_id) && 
				(!$t_project->isFullAccessMember(
					$request_class->request->user->get('user_id'), 
					$va_entity['project_id']))) {
				$vb_read_only = 1;
			}
			
			// List item table cells

			if ($vb_read_only) {
				// Read only access, disabled check and link to detail page
				print "<div class='entityBatchCheckContainer entityListCell' 
					style='display: table-cell; width: 30px;'>";
				print "<input type='checkbox' disabled='disabled' 
					title='You cannot edit media owned by another project' 
					class='entityBatchCheck' style='display: none;'>";
				print "</div>";

				print "<div class='entityListCell' 
					style='display: table-cell; width: 65px; 
					padding-right: 10px; text-align: center;'>";
				print caNavLink($request_class->request, 
					"M".$va_entity['media_id'], "", "Detail", "MediaDetail", 
					"Show", array('media_id' => $va_entity['media_id']), 
					array("target" => "_blank"));
				if ($va_entity['published'] == 1) {
					print "<div>";
					print addOwnedGroupToCartLink($request_class->request, 
						$va_entity['media_id'], 
						$request_class->request->user->get("user_id"), null, 
						array("class" => "button buttonSmall marginTop10Px"));
					print "</div>";
				}
				print "</div>";		
			} else {
				// Editable media, check and link to dashboard page
				print "<div class='entityBatchCheckContainer entityListCell' 
					style='display: table-cell; width: 30px;'>";
				print "<input type='checkbox' name='media_ids[]' 
					value='".$va_entity['media_id']."' 
					title='Select for batch editing' class='entityBatchCheck' 
					style='display: none;'>";
				print "</div>";

				print "<div class='entityListCell' 
					style='display: table-cell; width: 65px; 
					padding-right: 10px; text-align: center;'>";
				print caNavLink($request_class->request, 
					"M".$va_entity['media_id'], "", "MyProjects", "Media", 
					"mediaInfo", array('media_id' => $va_entity['media_id']), 
					array("target" => "_blank"));
				print "<div>";
				print addOwnedGroupToCartLink($request_class->request, 
					$va_entity['media_id'], 
					$request_class->request->user->get("user_id"), null, 
					array("class" => "button buttonSmall marginTop10Px"));
				print "</div>";
				print "</div>";
			}
			
			print "<div class='entityListCell' 
				style='display: table-cell; width: 155px;'>";
			print ucfirst($va_entity['element']);
			print "</div>";

			$vs_specimen_name = $t_specimen->formatSpecimenName($va_entity);
			print "<div class='entityListCell' 
				style='display: table-cell; width: 120px;'>";
			if ($vs_specimen_name) {
				print $vs_specimen_name;	
			}
			print "</div>";

			$vs_specimen_taxonomy = 
				$t_specimen->getSpecimenTaxonomy($va_entity['specimen_id']);
			print "<div class='entityListCell' 
				style='display: table-cell; width: 125px;'>";
			if ($vs_specimen_taxonomy) {
				print "<em>".join(" ", $vs_specimen_taxonomy)."</em>";	
			}
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 100px;'>";
			print $t_media->formatPublishedText($va_entity['published']).
				"<br/>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 85px;'>";
			print caGetLocalizedDate($va_entity['created_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true));
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 85px;'>";
			print caGetLocalizedDate($va_entity['last_modified_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true));
			print "</div>";

			print "<div class='entityListItemFooter'>";
			if ($va_entity['project_id'] != $pn_project_id) {
				$t_own_project = new ms_projects();
				$t_own_project->load($va_entity['project_id']);
				print "<br/>Owned by project ".
					((strlen($t_own_project->get("name")) > 200) ? 
					mb_substr($t_own_project->get("name"), 0, 200)."..." : 
					$t_own_project->get("name"));
			}
			if ($vb_read_only) {
				print " - <b>READ ONLY ACCESS</b>";
			}
			print "</div>";
		}
		print "</div><!-- end entityListItem -->";
	}

	function formatEntityTile($va_entity, $vs_entity_type, $request_class)
	{
		// Formats single specimen/media group entity as tile item
		if ($vs_entity_type == 's') {
			$t_specimen = new ms_specimens();
			$vn_num_media = is_array($va_entity['media']) ? 
				sizeof($va_entity['media']) : 0;

			print "<div class='projectMediaContainer entityTileContainer'>";

			// Entity preview image
			print "<div class='projectMedia".(($vn_num_media > 1) ? 
				" projectMediaSlideCycle" : "")."'>";

			if (is_array($va_entity['media']) && ($vn_num_media > 0)) {
				foreach($va_entity['media'] as $vn_media_id => $va_media) {
					if (!($vs_media_tag = $va_media['tags']['preview190'])) {
						$vs_media_tag = 
							"<div class='projectMediaPlaceholder'> </div>";
					}
					print "<div class='projectMediaSlide'>".
					caNavLink($request_class->request, $vs_media_tag, "", 
						"MyProjects", "Specimens", "form", 
						array("specimen_id" => $va_entity['specimen_id']), 
						array("target" => "_blank"))."</div>";	
				}
			} else {
				print "<div class='projectMediaPlaceholder'> </div>";
			}
			print "</div><!-- end projectMedia -->";

			// Entity caption
			$vs_specimen_taxonomy = 
				$t_specimen->getSpecimenTaxonomy($va_entity['specimen_id']);
			print "<div class='projectMediaSlideCaption'>";
			print caNavLink($request_class->request, 
				$t_specimen->formatSpecimenName($va_entity), '', "MyProjects", 
				"Specimens", "form", 
				array("specimen_id" => $va_entity['specimen_id']), 
				array("target" => "_blank"));
			if ($vs_specimen_taxonomy) { 
				print ", <em>".join(" ", $vs_specimen_taxonomy)."</em>"; 
			}
			print "<br/></br>Created ".
				caGetLocalizedDate($va_entity['created_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true));
			print "</br>Modified ".
				caGetLocalizedDate($va_entity['last_modified_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true));
			if ($vs_uuid_id = $va_entity["uuid"]){
				print "<div style='margin-top:3px; '>
					<a href='https://www.idigbio.org/portal/records/".
					$vs_uuid_id."' target='_blank' class='blueText' 
					style='text-decoration:none; font-weight:bold;'>iDigBio 
					<i class='fa fa-external-link'></i></a></div>";
			}
			print "</div> <!-- end projectMediaSlideCaption -->\n";

			print "</div><!-- end projectMediaContainer -->";
		} elseif ($vs_entity_type == 'm') {
			$t_project = new ms_projects();
			$t_media = new ms_media();
			$t_specimen = new ms_specimens();
			$pn_project_id = $request_class->getVar('project_id');
			print "<div class='projectMediaContainer entityTileContainer'>";
			print "<div class='projectMedia'>";

			// Is media read only? (I.e. neither project nor user own it?)
			$vb_read_only = 0;
			if (($va_entity['project_id'] != $pn_project_id) && 
				(!$t_project->isFullAccessMember(
					$request_class->request->user->get('user_id'), 
					$va_entity['project_id']))) {
				$vb_read_only = 1;
			}

			if ($vb_read_only) {
				// Read only access, disabled check and link to detail page
				print caNavLink($request_class->request, 
					$va_entity['preview']['media']['preview190'], "", "Detail", 
					"MediaDetail", "Show", 
					array('media_id' => $va_entity['media_id']), 
					array("target" => "_blank"));
				print "<div class='entityBatchCheckContainer'>";
				print "<input type='checkbox' disabled='disabled' 
					title='You cannot edit media owned by another project' 
					class='entityBatchCheck' style='display: none;'>";
				print "</div>";
			} else {
				// Editable media group, check and link to dashboard page
				print caNavLink($request_class->request, 
					$va_entity['preview']['media']['preview190'], "", 
					"MyProjects", "Media", "mediaInfo", 
					array('media_id' => $va_entity['media_id']), 
					array("target" => "_blank"));
				print "<div class='entityBatchCheckContainer'>";
				print "<input type='checkbox' name='media_ids[]' 
					value='".$va_entity['media_id']."' 
					title='Select for batch editing' class='entityBatchCheck' 
					style='display: none;'>";
				print "</div>";
			}

			print "</div><!-- end projectMedia -->";

			// Entity caption
			print "<div class='projectMediaSlideCaption'>";

			if ($vb_read_only) {
				print caNavLink($request_class->request, 
					"M".$va_entity['media_id'], 
					"", "Detail", "MediaDetail", "Show", 
					array('media_id' => $va_entity['media_id']), 
					array("target" => "_blank"));
			} else {
				print caNavLink($request_class->request, 
					"M".$va_entity['media_id'], 
					"", "MyProjects", "Media", "mediaInfo", 
					array('media_id' => $va_entity['media_id']), 
					array("target" => "_blank"));
			}
			
			print ", ".ucfirst($va_entity['element'])." (".
				$va_entity['preview']['numFiles']." file".
				(($va_entity['preview']['numFiles'] == 1) ? "" : "s").")<br/>";

			$vs_specimen_name = $t_specimen->formatSpecimenName($va_entity);
			$vs_specimen_taxonomy = 
				$t_specimen->getSpecimenTaxonomy($va_entity['specimen_id']);
			if ($vs_specimen_taxonomy) { 
				$vs_specimen_taxonomy = join(" ", $vs_specimen_taxonomy); 
			} else {
				$vs_specimen_taxonomy = "";
			}
			
			if ($vs_specimen_name && $vs_specimen_taxonomy) {
				print $vs_specimen_name.", <em>".
					$vs_specimen_taxonomy."</em><br/><br/>";
			} elseif ($vs_specimen_name || $vs_specimen_taxonomy) {
				print $vs_specimen_name."<em>".
					$vs_specimen_taxonomy."</em><br/><br/>";
			} else {
				print "<br/><br/>";
			}
			
			print $t_media->formatPublishedText($va_entity['published']).
				"<br/>";
			
			print "Created ".caGetLocalizedDate($va_entity['created_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true)).
				"</br>";
			print "Modified ".caGetLocalizedDate($va_entity['last_modified_on'], 
				array('dateFormat' => delimited, 'timeOmitSeconds' => true));
	
			if ($va_entity['project_id'] != $pn_project_id) {
				$t_own_project = new ms_projects();
				$t_own_project->load($va_entity['project_id']);
				print "<br/><br/><b>Owned by</b> ".
					((strlen($t_own_project->get("name")) > 22) ? 
					mb_substr($t_own_project->get("name"), 0, 22)."..." : 
					$t_own_project->get("name"))."<br/>";
			}

			if ($vb_read_only) {
				print "<b>READ ONLY ACCESS</b>";
			}

			print "</div> <!-- end projectMediaSlideCaption -->\n";

			print "</div><!-- end projectMediaContainer -->";
		}
	}

	function formatListHeader($vs_entity_type) {
		// Format header table for specimen/media group lists
		print "<div id='entityListHeader' class='listItemLtBlue'>";
		if ($vs_entity_type == 's') {
			print "<div class='entityListCell' 
				style='display: table-cell; width: 150px;'>";
			print "<b>Taxonomy</b>";
			print "</div>";
			print "<div class='entityListCell' 
				style='display: table-cell; width: 120px;'>";
			print "<b>Collection code</b>";
			print "</div>";
			print "<div class='entityListCell' 
				style='display: table-cell; width: 80px;'>";
			print "<b>Description</b>";
			print "</div>";
			print "<div class='entityListCell' 
				style='display: table-cell; width: 105px; 
				text-align: center;'>";
			print "<b>Media groups (published/total)</b>";
			print "</div>";
			print "<div class='entityListCell' 
				style='display: table-cell; width: 90px;'>";
			print "<b>Date added</b>";
			print "</div>";
			print "<div class='entityListCell' 
				style='display: table-cell; width: 90px;'>";
			print "<b>Date modified</b>";
			print "</div>";
			print "<div class='entityListCell' 
				style='display: table-cell; width: 55px; text-align: center;'>";
			print "<b>Editable</b>";
			print "</div>";
			print "<div class='entityListCell' 
				style='display: table-cell; width: 80px; text-align: center;'>";
			print "<b>iDigBio Link</b>";
			print "</div>";
		} elseif ($vs_entity_type == 'm') {
			print "<div class='entityListCell' 
				style='display: table-cell; width: 30px;'>";
			print "<div id='entityListHeaderEditText' style='display: none;'>
				<b>Edit</b></div>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 65px; padding-right: 10px; 
				text-align: center;'>";
			print "<b>Media group</b>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 155px;'>";
			print "<b>Element description</b>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 120px;'>";
			print "<b>Collection code</b>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 125px;'>";
			print "<b>Taxonomy</b>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 100px;'>";
			print "<b>Published</b>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 85px;'>";
			print "<b>Date added</b>";
			print "</div>";

			print "<div class='entityListCell' 
				style='display: table-cell; width: 85px;'>";
			print "<b>Date modified</b>";
			print "</div>";
		}
		print "</div><!-- end entityListHeader -->";
	}

	function formatNestGroupLabel($vs_taxon_level, $vs_taxon_term, $vs_entity_type, $va_entity, $request_class, $left_padding=null, $right_padding=null) {
		// Format label for taxon collection of entities (specimens/media groups)
		$va_taxon_levels = 
			['ht_class', 'ht_order', 'ht_family', 'genus', 'species'];
		if (!in_array($vs_taxon_level, $va_taxon_levels)) { return false; }
		if ($vs_taxon_term == "no_link") { return false; }
		$va_arrow_up = ['ht_class' => 1, 'ht_order' => 0, 'ht_family' => 0, 
			'genus' => 0, 'species' => 0];
		$arrow_up = $va_arrow_up[$vs_taxon_level];  
		
		print "<div class='entityGroupLabel' style='".
			($left_padding ? "padding-left: ".$left_padding."px; " : "").
			($right_padding ? "padding-right: ".$right_padding."px; " : "").
			"''>";

		print "<div class='entityGroupLabelItem' style='width: 3%;'>";
		print "<a href='#' class='entityGroupToggle'><img src='".
			$request_class->request->getThemeUrlPath().
			"/graphics/morphosource/".
			($arrow_up? "ic_keyboard_arrow_down" : "ic_chevron_right").
			"_black_24px.svg' 
			onerror='this.src=\"/themes/morphosource/graphics/morphosource/".
			($arrow_up? "ic_keyboard_arrow_down" : "ic_chevron_right").
			"_black_24dp_1x.png\"' /></a>";
		print "</div>";

		if ($vs_taxon_level == 'species') {
			$vs_taxon_name = "<em>".ucfirst($va_entity['genus'])." ".
				$vs_taxon_term."</em>";
			if ($vs_entity_type == 's') {
				$vn_count = sizeof($va_entity['specimens']);
			} else if ($vs_entity_type == 'm') {
				$vn_count = sizeof($va_entity['media']);
			}
		} else if ($vs_taxon_level == 'genus') {
			$vs_taxon_name = "<em>".ucfirst($vs_taxon_term)."</em>";
			$vn_count = sizeof($va_entity);
			if (array_key_exists('no_link', $va_entity)) { 
				$vn_count = $vn_count - 1; 
			}
		} else {
			$vs_taxon_name = ucfirst($vs_taxon_term);
			$vn_count = sizeof($va_entity);
			if (array_key_exists('no_link', $va_entity)) { 
				$vn_count = $vn_count - 1; 
			}
		}
		
		print "<div class='entityGroupLabelItem entityGroupLabelText' 
			style='width: 60%;'>";
		print "<b>".$vs_taxon_name." (".$vn_count.")</b> ";
		print "</div>";

		// Links to open species/genus/family specimen collections in new tab	
		if (($vs_entity_type == 's') && 
			($vs_taxon_level == 'ht_family' || $vs_taxon_level == 'genus' || 
				$vs_taxon_level == 'species')) {
			print "<div class='entityGroupLabelItem entityGroupLabelText' 
				style='width: 36%'>";
			print "<div style='float: right'>";
			
			$vn_taxon_id = null;
			if (is_bool(current($va_entity))) {
			 	$va_child = next($va_entity);
			} else if (is_array(current($va_entity))) {
			 	$va_child = current($va_entity);
			}
			
			switch ($vs_taxon_level) {
			 	case 'ht_family':
			 		$vn_taxon_id = current($va_child)['taxon_id'];
			 		break;
			 	case 'genus':
			 		$vn_taxon_id = $va_child['taxon_id'];
			 		break;
			 	case 'species':
			 		$vn_taxon_id = $va_entity['taxon_id'];
			 		break;
			 	default:
			 		break;
			} 

			if ($va_entity['no_link']) {
				print caNavLink($request_class->request, "<img src='".$request_class->request->getThemeUrlPath().
					"/graphics/morphosource/ic_open_in_new_black_18px.svg' 
					onerror='this.src=\"/themes/morphosource/graphics/morphosource/ic_open_in_new_orange_18px_1x.png\"' 
					/>", 
					'', 'MyProjects', 'Dashboard', 'specimenWithoutTaxonomy', 
					array('specimens_group_by' => $vs_taxon_level) 
				);
			}else{
				print caNavLink($request_class->request, "<img src='".$request_class->request->getThemeUrlPath().
					"/graphics/morphosource/ic_open_in_new_black_18px.svg' 
					onerror='this.src=\"/themes/morphosource/graphics/morphosource/ic_open_in_new_orange_18px_1x.png\"'
					 />", 
					'', 'MyProjects', 'Dashboard', 'specimenByTaxonomy', 
					array('specimens_group_by' => $vs_taxon_level,
						'taxon_id' => $vn_taxon_id) 
				);
			}

			print "</div>";
			print "</div>";
		}
			
		print "</div><!-- end entityGroupLabel -->";
	}

	function formatEntityNest($va_entity, $vs_specimens_group_by, $vs_entity_format, $vs_entity_type,  $request_class)
	{
		// Format taxonomically nested (family, order, etc.) specimen/media group entities
		print "<div class='entityNestContainer'>";
		foreach ($va_entity as $vs_class_name => $va_class_entity) {
			if ($vs_class_name == 'no_link') { continue; }
			print "<div class='entityNestGroupContainer' 
				style='background-color: #FFFFFF; margin-bottom: 10px'>";
			formatNestGroupLabel("ht_class", $vs_class_name, $vs_entity_type, 
				$va_class_entity, $request_class, 0, 0);
			foreach ($va_class_entity as $vs_order_name => $va_order_entity) {
				if ($vs_order_name == 'no_link') { continue; }
				print "<div class='entityNestGroupContainer' 
					style='background-color: #C7D3E3; margin-bottom: 5px'>";
				formatNestGroupLabel("ht_order", $vs_order_name, 
					$vs_entity_type, $va_order_entity, $request_class, 30, 0);
				foreach ($va_order_entity as $vs_family_name => $va_family_entity) {
					if ($vs_family_name == 'no_link') { continue; }
					print "<div class='entityNestGroupContainer' 
						style='display: none; background-color: #FFFFFF; 
						margin: 3px;'>";
					formatNestGroupLabel("ht_family", $vs_family_name, 
						$vs_entity_type, $va_family_entity, $request_class, 
						50, 16);
					foreach ($va_family_entity as $vs_genus_name => $va_genus_entity) {
						if ($vs_genus_name == 'no_link') { continue; }
						print "<div class='entityNestGroupContainer' 
							style='display: none; background-color: #C7D3E3; 
							margin: 3px;'>";
						formatNestGroupLabel("genus", $vs_genus_name, 
							$vs_entity_type, $va_genus_entity, $request_class, 
							70, 13);
						foreach ($va_genus_entity as $vs_species_name => $va_species_entity) {
							if ($vs_species_name == 'no_link') { continue; }
							print "<div class='entityNestGroupContainer' 
								style='display: none; background-color: #FFFFFF; 
								margin: 3px;'>";
							formatNestGroupLabel("species", $vs_species_name, 
								$vs_entity_type, $va_species_entity, 
								$request_class, 90, 0);
							print "<div class='entityNestGroupContainer' 
								style='display: none;'>";
							if ($vs_entity_format == 'l') {
								print "<div class='entityListContainer'>";
								formatListHeader($vs_entity_type);
							}
							if ($vs_entity_type == 's') {
								formatEntityGroup(
									$va_species_entity['specimens'], 
									$vs_entity_format, $vs_entity_type, 
									$request_class, $hide_group=false);
							} elseif ($vs_entity_type == 'm') {
								formatEntityGroup($va_species_entity['media'], 
									$vs_entity_format, $vs_entity_type, 
									$request_class, $hide_group=false);
							}
							if ($vs_entity_format == 'l') { 
								print "</div><!-- end entityListContainer -->";
							}
							print "</div><!-- end entityNestGroupContainer -->";
							print "</div><!-- end entityNestGroupContainer -->";
						}
						print "</div><!-- end entityNestGroupContainer -->";
					}
					print "</div><!-- end entityNestGroupContainer -->";
				}
				print "</div><!-- end entityNestGroupContainer -->";
			}
			print "</div><!-- end entityNestGroupContainer -->";
		}
		print "</div><!-- end entityNestContainer -->";

		print "<div style='clear: both'></div>";
	}
	// End formatting functions

	// View specimen/media group entities

	$pn_project_id = $this->getVar('project_id');

	$va_entity = $this->getVar('va_entity');
	$vn_count = $this->getVar('vn_count');
	$vb_entity_nest = $this->getVar('vb_entity_nest');
	$vs_specimens_group_by = $this->getVar('specimens_group_by');
	$vs_entity_format = $this->getVar('entity_format');
	$vs_entity_type = $this->getVar('entity_type');
	$request_class = $this;

	if ($va_entity && $vn_count) {
		print "<div id='entityContainerTile'".
			($vs_entity_format != 't' ? " style='display: none;'" : "").">";
		if ($vb_entity_nest) {
			formatEntityNest($va_entity, $vs_specimens_group_by, 't', 
				$vs_entity_type, $request_class);
		} else {
			formatEntityGroup($va_entity, 't', $vs_entity_type, 
				$request_class);
		}
		print "</div><!-- end entityContainerTile -->";

		print "<div id='entityContainerList'".
			($vs_entity_format != 'l' ? " style='display: none;'" : "").">";
		if ($vb_entity_nest) {
			formatEntityNest($va_entity, $vs_specimens_group_by, 'l', 
				$vs_entity_type, $request_class);
		} else {
			formatListHeader($vs_entity_type);
			formatEntityGroup($va_entity, 'l', $vs_entity_type, 
				$request_class);
		}
		print "</div><!-- end entityContainerList -->";
	}
?>