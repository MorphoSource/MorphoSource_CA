<?php
/* ----------------------------------------------------------------------
 * app/views/Administration/projectlist_html.php :
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
		$('#msProjectList').caFormatListTable();
	});
/* ]]> */
</script>
<div class="blueRule"><!-- empty --></div>
	<div style="float:right; margin-top:25px;">
		<?php print caNavLink($this->request, _t("Download Media Report"), 'button buttonSmall', 'Administration', 'Projects', 'DownloadMediaReport', array('download' => 1)); ?>
	</div>
	<H1>
		<?php print $q_all_projects->numRows()." ".(($q_all_projects->numRows() == 1) ? "Project" : "Projects"); ?>
	</H1>
	
	<?php 
		print caFormControlBox(
			'', 
			'',
			'<div class="list-filter">'._t('Filter').': <input type="text" name="filter" value="" onkeyup="$(\'#msProjectList\').caFilterTable(this.value); return false;" size="20" style="border:1px solid #828282;"/></div>'
		); 
	?>
<div id='formArea'>		
		<br style="clear: both"/>
	<table id="msProjectList" class="listtable" border="0" cellpadding="0" cellspacing="1">
		<thead>
			<tr>
				<th class="list-header-unsorted">
					<?php print _t('Name'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Description'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Media'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Downloads'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Storage used'); ?>
				</th>
				<th class="{sorter: false} list-header-nosort">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php
	if ($q_all_projects->numRows()) {
		while($q_all_projects->nextRow()) {
			$t_project->load($q_all_projects->get("project_id"));
?>	
			<tr>
				<td>
					<?php print $t_project->get('name'); ?>
				</td>
				<td>
<?php
					if(mb_strlen($t_project->get('abstract')) > 255){
						print mb_substr($t_project->get('abstract'), 0, 255)."... <a href='#' class='blueText abstract".$t_project->get("project_id")."'>more</a> &rsaquo;";
						TooltipManager::add(
							".abstract".$t_project->get("project_id"), "<p style='padding:10px 20px 10px 20px; font-size:11px;'>".$t_project->get('abstract')."</p>"
						);
					}else{
						print $t_project->get('abstract');
					}
?>
				</td>
				<td width="100">
<?php
					if($vn_num_media = (int)$t_project->numMedia()){
						$va_media_counts = $t_project->getProjectMediaCounts();
						print "<b>Media Groups:</b><br/>";
						if($va_media_counts[1]){
							print (int)$va_media_counts[1]." ".$t_media->formatPublishedText(1)."<br/>"; 
						}
						if($va_media_counts[2]){
							print (int)$va_media_counts[2]." ".$t_media->formatPublishedText(2)."<br/>";
						}
						print (int)$va_media_counts[0]." ".$t_media->formatPublishedText(0)."<br/>";
						print _t('<em>(%1 total)</em>', $vn_num_media);
					
						# --- media files
						if($vn_num_media_files = (int)$t_project->numMediaFiles()){
							$va_media_file_counts = $t_project->getProjectMediaFileCounts();
							print "<br/><br/><b>Media Files:</b><br/>";
							if($va_media_file_counts[1]){
								print (int)$va_media_file_counts[1]." ".$t_media->formatPublishedText(1)."<br/>"; 
							}
							print (int)$va_media_file_counts[2]." ".$t_media->formatPublishedText(2)."<br/>";
							print (int)$va_media_file_counts[0]." ".$t_media->formatPublishedText(0)."<br/>";
							print _t('<em>(%1 total)</em>', $vn_num_media_files);
						
							# --- media files
						}
					}else{
						print "0";
					}
?>
				</td>
				<td>
					<?php print $t_project->numDownloads(); ?>
				</td>
				<td>
					<?php print caFormatFilesize($t_project->get('total_storage_allocation')); ?>
				</td>
				<td>
<?php
					#print caNavButton($this->request, '', _t("Edit"), 'MyProjects', 'Dashboard', 'dashboard', array('select_project_id' => $t_project->get('project_id')), array(), array('icon_position' => __CA_NAV_BUTTON_ICON_POS_LEFT__, 'use_class' => 'button buttonSmall', 'no_background' => true, 'dont_show_content' => true));
					print caNavLink($this->request, _t("Edit"), "button buttonSmall", "MyProjects", "Dashboard", "dashboard", array('select_project_id' => $t_project->get('project_id')));
?>
				</td>
			</tr>
<?php
		}
	} else {
?>
		<tr>
			<td colspan='4'>
				<div align="center">
					<?php print _t('No projects have been entered'); ?>
				</div>
			</td>
		</tr>
<?php			
	}
?>
		</tbody>
	</table>
</div>