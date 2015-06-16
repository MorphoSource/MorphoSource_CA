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
$t_media = new ms_media();
$o_db = new Db();
if($vo_result) {
	$vn_display_cols = 2;
	$vn_col = 0;
	$vn_item_count = 0;
	
	$t_specimen = new ms_specimens();
	while(($vn_item_count < $vn_items_per_page) && ($vo_result->nextHit())) {		
		$va_taxonomy = array();
		$vs_specimen_name = "";
		$vs_media_tag = "";
		$vs_file_format = "";
		$vn_media_id = $vo_result->get('media_id');
		$va_preview_file_info = $t_media->getPreviewMediaFile($vn_media_id, array("thumbnail", "medium"), 1);
		print "<div class='searchResultFull' id='searchResult".$vn_media_id."'>";
		$vs_media_cart_link = "";
		$vb_user_can_download_media = true;
		if(isset($va_preview_file_info["media"]["thumbnail"])){
			if($this->request->isLoggedIn()){
				if($vo_result->get("published") == 2){
					$t_media->load($vn_media_id);
					if($t_media->userCanDownloadMedia($this->request->user->get("user_id"))){
						$vb_user_can_download_media = true;
					}else{
						$vb_user_can_download_media = false;
					}
				}
				# --- check each file to see if any are request permission to download
				$q_files = $o_db->query("SELECT published, media_file_id from ms_media_files WHERE media_id = ?", $vn_media_id);
				if($q_files->numRows()){
					while($q_files->nextRow()){
						if($q_files->get("published") == 2){
							$vb_user_can_download_media = $t_media->userCanDownloadMediaFile($this->request->user->get("user_id"), $vn_media_id, $q_files->get("media_file_id"));
						}
					}
				}
				if($vb_user_can_download_media){
					$vs_media_cart_link = "<div style='clear:left; margin-top:2px;'>".addGroupToCartLink($this->request, $vn_media_id, $this->request->user->get("user_id"), null, array("class" => "button buttonSmall"))."</div>";
				}else{
					$vs_media_cart_link = "<br/><span style='padding-top:5px; line-height:1em; font-size:10px; font-weight:bold;'>Must request access to download/add all files to cart</span>";
				}
			}else{
				$vs_media_cart_link = "<div style='clear:left; margin-top:2px;'><a href='#' onClick='return false;' class='button buttonSmall mediaCartLogin'>"._t("add <i class='fa fa-shopping-cart'></i>")."</a></div>";
				TooltipManager::add(
					".mediaCartLogin", $this->render('system/media_cart_login_message_html.php')
				);
			}
			print "<div class='searchFullThumb'>".caNavLink($this->request, $va_preview_file_info["media"]["thumbnail"], '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $vn_media_id)).$vs_media_cart_link."</div>";
		}
		print "<div class='searchFullText'>";
		print "<b>".caNavLink($this->request, "M".$vn_media_id, 'blueText', 'Detail', 'MediaDetail', 'Show', array('media_id' => $vn_media_id))."</b><br/>";
		if($vn_specimen_id = $vo_result->get("specimen_id")){
			$t_specimen->load($vn_specimen_id);
			if($vs_specimen_name = $t_specimen->getSpecimenName()){
				print caNavLink($this->request, $vs_specimen_name, 'blueText', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $vn_specimen_id))."<br/>";
			}
			#if($va_taxonomy = $t_specimen->getSpecimenTaxonomy()){
			#	print "<b>Specimen taxonomy:</b> ".join(", ", $va_taxonomy)."<br/>";
			#}
		}
		if($vo_result->get('title')){
			print $vo_result->get('title')."<br/>";
		}
		//$vs_mimetype = $vo_result->getMediaInfo('ms_media.media', 'original', 'MIMETYPE');
		//			$vs_media_class = caGetMediaClassForDisplay($vs_mimetype); 
		//			$vs_mimetype_name = caGetDisplayNameForMimetype($vs_mimetype);
		//			print "<b>Type: </b>{$vs_media_class} ({$vs_mimetype_name})<br/>\n";
		// $vs_file_format = msGetMediaFormatDisplayString($vo_result);
// 		print $vs_file_format."<br/>\n";
// 					
// 		$va_properties = $vo_result->getMediaInfo('ms_media.media', 'original');
// 		if($vs_file_size = caFormatFilesize($va_properties['PROPERTIES']['filesize'])){
// 			print $vs_file_size."<br/>\n";
// 		}			
		print $va_preview_file_info["numFiles"]." file".(($va_preview_file_info["numFiles"] == 1) ? "" : "s");
		print "</div><!-- end searchFullText -->";
		print "</div><!-- end searchResultFull -->";
		
		// set view vars for tooltip
		$this->setVar('tooltip_representation', $vs_media_tag = $va_preview_file_info["media"]["medium"]);
		$this->setVar('tooltip_id', $vo_result->get("media_id"));
		$this->setVar('tooltip_element', $vo_result->get("ms_media.element"));
		$this->setVar('tooltip_facility', $vo_result->get("ms_facilities.name"));
		$this->setVar('tooltip_specimen', $vs_specimen_name);
		$this->setVar('tooltip_file_size', $vs_file_size);
		$this->setVar('tooltip_file_format', $vs_file_format);
		if($vs_media_tag){
			TooltipManager::add(
				"#searchResult{$vn_media_id}", $this->render('Results/ms_media_result_tooltip_html.php')
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
if(!$this->request->isLoggedIn()){
	print caNavLink($this->request, _t("Login to download"), "button buttonLarge", "", "LoginReg", "form", array('site_last_page' => 'Search'));
}
?>
