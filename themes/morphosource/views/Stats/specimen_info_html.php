<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Stats/specimen_info_html.php : 
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
 
$va_rows = $this->getVar('rows');
$va_downloads_by_file = $this->getVar('downloads_by_file');
$va_specimen_info = $this->getVar('specimen_info');
if (sizeof($va_specimen_info)) {	
	print "<br/><div class='blueRule'><!-- empty --></div>";
	print "<H1>Specimen Info</H1>";
	print "<H2>".$va_specimen_info["specimen_name"].", Specimen Public Views: ".$va_specimen_info["specimen_views"].", ";
	print "Specimen Media: ".$va_specimen_info["num_specimen_media"]."</H2>";
	if (sizeof($va_rows)) {
?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#msSpecimenList').caFormatListTable();
	});
/* ]]> */
</script>
	<div style="margin-bottom:5px;">Filter: <input type="text" name="filter" value="" onkeyup="$('#msSpecimenList').caFilterTable(this.value); return false;" size="20" style="border:1px solid #828282;"/></div>
<div style="height:400px; overflow-y:auto; border-top:1px solid #DEDEDE;">
	<table id="msSpecimenList" class="listtable" border="0" cellpadding="0" cellspacing="1" style="margin-top:0px;">
		<thead>
			<tr>
				<th class="list-header-unsorted">
					<?php print _t('Media'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Media Public Views'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Media Downloads'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach($va_rows as $vn_media_id => $va_row) {
?>	
			<tr>
				<td>
<?php
					print "<b>M".$vn_media_id."</b>";
?>
				</td>
				<td>
<?php
					if(is_array($va_row["views"]) && sizeof($va_row["views"])){
						print "<a href='#' onClick='$(\"#views".$vn_media_id."\").slideToggle(); return false;'><b>".sizeof($va_row["views"])." media view".((sizeof($va_row["views"]) == 1) ? "" : "s")."</b></a><br/>";
						print "<div id='views".$vn_media_id."' style='display:none;'>";
						$vn_anon = 0;
						foreach($va_row["views"] as $vn_view_id => $va_view_info){
							if($va_view_info["user_id"]){
								print $va_view_info["user_info"]."; ";
							}else{
								$vn_anon++;
							}
						}
						if($vn_anon < sizeof($va_row["views"])){
							print "<br/>";
						}
						if($vn_anon){
							print $vn_anon." anonymous view".(($vn_anon == 1) ? "" : "s");
						}
						print "</div>";
					}
?>
				</td>
				<td>
<?php
					if(is_array($va_row["downloads"]) && sizeof($va_row["downloads"])){
						print "<a href='#' onClick='$(\"#downloads".$vn_media_id."\").slideToggle(); return false;'><b>".sizeof($va_row["downloads"])." media download".((sizeof($va_row["downloads"]) == 1) ? "" : "s")."</b><br/>";
						foreach($va_downloads_by_file[$vn_media_id] as $vn_file_id => $va_file_download_info){
							if($vn_file_id){
								print "M".$vn_media_id."-".$vn_file_id.": ".sizeof($va_file_download_info)." downloads; ";
							}
						}
						print "</a><br/>";
						print "<div id='downloads".$vn_media_id."' style='display:none;'>";
						foreach($va_row["downloads"] as $vn_download_id => $va_download_info){
							print (($va_download_info["media_file_id"]) ? "<b>M".$vn_media_id."-".$va_download_info["media_file_id"]."</b>: " : "").$va_download_info["date"].", <a href='#' onClick='jQuery(\"#specimenInfo\").load(\"".caNavUrl($this->request, "", "Stats", "userInfo", array('user_id' => $va_download_info["user_id"]))."\"); return false;'>".$va_download_info["name"]."</a>, (".$va_download_info["email"]."); ";
						}
						print "</div>";
					}
?>
				</td>
<?php
		}
?>		
			</tr>
		</tbody>
	</table>
</div>
<?php
	}
?>	
<?php
	} else {
?>
		<H2>
			<?php print _t('no specimen information'); ?>
		</H2>
<?php			
	}
?>