<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/stats_list_html.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008 Whirl-i-Gig
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
	$t_project = $this->getVar('project');
	$q_all_projects = $this->getVar('all_projects');
	$t_media = new ms_media();

?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#msStatsList').caFormatListTable();
	});
/* ]]> */
</script>
<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print _t("Site Stats"); ?>
	</H1>
	<div class="stats">Projects: <span class="ltBlueText"><?php print $this->getVar("num_projects"); ?></span></div>
	<div class="stats">Downloads: <span class="ltBlueText"><?php print $this->getVar("num_downloads"); ?> download<?php print (($this->getVar("num_downloads") == 1) ? "" : "s"); ?> of <?php print $this->getVar("num_downloads_media"); ?> media files by <?php print $this->getVar("num_downloads_users"); ?> user<?php print (($this->getVar("num_downloads_users") == 1) ? "" : "s"); ?>.</span></div>
	<div class="stats">Users: <span class="ltBlueText"><?php print $this->getVar("num_users"); ?></span></div>
	<div class="stats">Specimens: <span class="ltBlueText"><?php print $this->getVar("num_specimens"); ?></span></div>
	<div class="stats">Media: <span class="ltBlueText"><?php print $this->getVar("num_media"); ?></span></div>
	<div class="stats">Media files: <span class="ltBlueText"><?php print $this->getVar("num_media_files"); ?></span></div>
	<div class="stats">Taxonomic names: <span class="ltBlueText"><?php print $this->getVar("num_taxonomy_names"); ?></span></div>
	<div class="stats">Bibliographic citations: <span class="ltBlueText"><?php print $this->getVar("num_bibliography"); ?></span></div>
	<div class="stats">Facilities: <span class="ltBlueText"><?php print $this->getVar("num_facilities"); ?></span></div>
	<div class="stats">Institutions: <span class="ltBlueText"><?php print $this->getVar("num_institutions"); ?></span></div>
	
<?php
		$q_users = $this->getVar("users");
		$t_user = new ca_users();
		$va_country_list = caGetCountryList();
		$va_prefs = array("user_profile_country" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_country")),
							"user_profile_professional_affiliation" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_professional_affiliation")),
							"user_profile_professional_affiliation_other" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_professional_affiliation_other")),
							"user_profile_visualize_software" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_visualize_software")),
							"user_profile_visualize_software_other" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_visualize_software_other")),
							"user_profile_mesh_filetype" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_mesh_filetype")),
							"user_profile_mesh_filetype_other" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_mesh_filetype_other")),
							"user_profile_volume_filetype" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_volume_filetype")),
							"user_profile_volume_filetype_other" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_profile_volume_filetype_other")),
							"user_3D_printer" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_3D_printer")),
							"user_3D_printer_software" => array("values" => array(), "info" => $t_user->getPreferenceInfo("user_3D_printer_software")));
		
		if($q_users->numRows()){
			while($q_users->nextRow()){
				$t_user->load($q_users->get("user_id"));
				foreach($va_prefs as $vs_pref_code => $va_pref_info){
					$vs_preference = $t_user->getPreference($vs_pref_code);
					if($vs_preference){
						if(is_array($vs_preference)){
							foreach($vs_preference as $vs_option){
								$va_prefs[$vs_pref_code]["values"][$vs_option] = ($va_prefs[$vs_pref_code]["values"][$vs_option]) ? $va_prefs[$vs_pref_code]["values"][$vs_option] + 1 : 1;
							}
						}else{
							if($vs_pref_code == "user_profile_country"){
								$vs_preference = array_search($vs_preference, $va_country_list);
							}
							$va_prefs[$vs_pref_code]["values"][$vs_preference] = ($va_prefs[$vs_pref_code]["values"][$vs_preference]) ? $va_prefs[$vs_pref_code]["values"][$vs_preference] + 1 : 1;
						}
					}
				}	
			}
			print "<div style='float:left; width:45%; padding-right:5%;'><H1>"._t("User Demographic Info")."</H1>";
			foreach($va_prefs as $va_prefs_info){
				$vb_other = false;
				print '<div class="stats">';
				if($va_prefs_info["info"]["label"] == "Other"){
					print "&nbsp;&nbsp;".$va_prefs_info["info"]["label"].": ";
					$vb_other = true;
				}else{
					print "<b>".$va_prefs_info["info"]["label"]."</b><br/>";
				}
				arsort($va_prefs_info["values"]);
				if(is_array($va_prefs_info["values"])){
					print "<span class='ltBlueText'>&nbsp;&nbsp;";
					$va_values_display = array();
					foreach($va_prefs_info["values"] as $vs_label => $vn_count){
						$va_values_display[] = $vs_label." (".$vn_count.")";
					}
					if($vb_other){
						print join($va_values_display, ", ");
					}else{
						print join($va_values_display, "<br/>&nbsp;&nbsp;");
					}
					print "</span>";
				}
				print "</div>\n";
			}
			print "</div>\n";
		}
		$q_download_survey = $this->getVar("download_survey");
		$t_download_stats = new ms_media_download_stats();
		$va_download_stats = array("intended_use" => array("values" => array(), "label" => "What is your intended use?"), "intended_use_other" => array("values" => array(), "label" => "Other"), "3d_print" => array("values" => array(), "label" => "Do you intend to 3D print the file?"));
		if($q_download_survey->numRows()){
			while($q_download_survey->nextRow()){
				$t_download_stats->load($q_download_survey->get("download_id"));
				foreach($va_download_stats as $vs_field => $va_stat_info){
					$vs_stat = $t_download_stats->get($vs_field);
					if($vs_field == "3d_print"){
						$vs_stat = $t_download_stats->getChoiceListValue($vs_field, $vs_stat);
					}
					if($vs_stat){
						if(is_array($vs_stat)){
							foreach($vs_stat as $vs_option){
								$vs_option = $t_download_stats->getChoiceListValue($vs_field, $vs_option);
								$va_download_stats[$vs_field]["values"][$vs_option] = ($va_download_stats[$vs_field]["values"][$vs_option]) ? $va_download_stats[$vs_field]["values"][$vs_option] + 1 : 1;
							}
						}else{
							$va_download_stats[$vs_field]["values"][$vs_stat] = ($va_download_stats[$vs_field]["values"][$vs_stat]) ? $va_download_stats[$vs_field]["values"][$vs_stat] + 1 : 1;
						}
					}
				}
			}
			print "<div style='float:left; width:50%;'><H1>"._t("Download Use")."</H1>";
			foreach($va_download_stats as $va_download_stat_info){
				$vb_other = false;
				print '<div class="stats">';
				if($va_download_stat_info["label"] == "Other"){
					print "&nbsp;&nbsp;".$va_download_stat_info["label"].": ";
					$vb_other = true;
				}else{
					print "<b>".$va_download_stat_info["label"]."</b><br/>";
				}
				arsort($va_download_stat_info["values"]);
				if(is_array($va_download_stat_info["values"])){
					print "<span class='ltBlueText'>&nbsp;&nbsp;";
					$va_values_display = array();
					foreach($va_download_stat_info["values"] as $vs_label => $vn_count){
						$va_values_display[] = $vs_label." (".$vn_count.")";
					}
					if($vb_other){
						print join($va_values_display, ", ");
					}else{
						print join($va_values_display, "<br/>&nbsp;&nbsp;");
					}
					print "</span>";
				}
				print "</div>\n";
			}

			print "</div>";
		}
		
?>

</div>