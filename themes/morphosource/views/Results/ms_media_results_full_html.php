<?php
/* ----------------------------------------------------------------------
 * themes/default/views/Results/ca_objects_results_thumbnail_html.php :
 * 		thumbnail search results
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2010 Whirl-i-Gig
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
 
require_once(__CA_MODELS_DIR__."/ms_specimens.php");	
$vo_result 					= $this->getVar('result');
$vn_items_per_page 	= $this->getVar('current_items_per_page');
$va_access_values 		= $this->getVar('access_values');

if($vo_result) {
	$vn_display_cols = 2;
	$vn_col = 0;
	$vn_item_count = 0;
	
	$t_specimen = new ms_specimens();
	while(($vn_item_count < $vn_items_per_page) && ($vo_result->nextHit())) {
		$va_taxonomy = array();
		$vs_specimen_name = "";
		$vn_media_id = $vo_result->get('media_id');
		print "<div class='searchResultFull' id='searchResult".$vn_media_id."'>";
		print "<div class='searchFullThumb'>".caNavLink($this->request, $vo_result->getMediaTag('ms_media.media', 'thumbnail'), '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $vn_media_id))."</div>";
		print "<div class='searchFullText'>";
		print caNavLink($this->request, "M".$vn_media_id, 'blueText', 'Detail', 'MediaDetail', 'Show', array('media_id' => $vn_media_id))."<br/>";
		if($vo_result->get("specimen_id")){
			$t_specimen->load($vo_result->get("specimen_id"));
			if($vs_specimen_name = $t_specimen->getSpecimenName()){
				print "<b>Specimen:</b> ".$vs_specimen_name."<br/>";
			}
			if($va_taxonomy = $t_specimen->getSpecimenTaxonomy()){
				print "<b>Specimen taxonomy:</b> ".join(", ", $va_taxonomy)."<br/>";
			}
		}
		if($vo_result->get("ms_media.element")){
			print "<b>Element: </b>".$vo_result->get("ms_media.element")."<br/>";
		}

		if($vo_result->get("ms_facilities.name")){
			print "<b>Facility: </b>".$vo_result->get("ms_facilities.name")."<br/>";
		}
		
		//$vs_mimetype = $vo_result->getMediaInfo('ms_media.media', 'original', 'MIMETYPE');
		//			$vs_media_class = caGetMediaClassForDisplay($vs_mimetype); 
		//			$vs_mimetype_name = caGetDisplayNameForMimetype($vs_mimetype);
		//			print "<b>Type: </b>{$vs_media_class} ({$vs_mimetype_name})<br/>\n";
		print "<b>Type: </b>". msGetMediaFormatDisplayString($vo_result)."<br/>\n";
					
		$va_properties = $vo_result->getMediaInfo('ms_media.media', 'original');
					print "<b>Filesize: </b>".caFormatFilesize($va_properties['PROPERTIES']['filesize'])."<br/>\n";
					
		print "</div><!-- end searchFullText -->";
		print "</div><!-- end searchResultFull -->";
		
		// set view vars for tooltip
		$this->setVar('tooltip_representation', $vs_media_tag = $vo_result->getMediaTag('ms_media.media', 'medium'));
		$this->setVar('tooltip_id', $vo_result->get("media_id"));
		if($vs_media_tag){
			TooltipManager::add(
				".searchResult{$vn_media_id}", $this->render('Results/ms_media_result_tooltip_html.php')
			);
		}
		
		$vn_col++;
		if($vn_col == $vn_display_cols){
			print "<div style='clear:both;'><!-- empty --></div>\n";
			$vn_col = 0;
		}
		
		$vn_item_count++;
	}
}
?>
