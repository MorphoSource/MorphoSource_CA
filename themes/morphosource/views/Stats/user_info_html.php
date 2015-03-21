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
 
$va_downloads_for_user = $this->getVar('downloads');
$va_user_info = $this->getVar('user_info');
if (sizeof($va_user_info)) {	
	print "<br/><div class='blueRule'><!-- empty --></div>";
	print "<H1>User Download Info</H1>";
	print "<H2>".$va_user_info["name"].", ".$va_user_info["email"];
	print "<br/>Total Media Downloads: ".$va_user_info["num_downloads"].", Total Specimen Views: ".$va_user_info["num_specimen_views"].", Total Media Views: ".$va_user_info["num_media_views"]."</H2>";
	if (sizeof($va_downloads_for_user)) {
?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#msUserList').caFormatListTable();
	});
/* ]]> */
</script>
	<div style="margin-bottom:5px;">Filter: <input type="text" name="filter" value="" onkeyup="$('#msUserList').caFilterTable(this.value); return false;" size="20" style="border:1px solid #828282;"/></div>
<div style="height:400px; overflow-y:auto; border-top:1px solid #DEDEDE;">
	<table id="msUserList" class="listtable" border="0" cellpadding="0" cellspacing="1" style="margin-top:0px;">
		<thead>
			<tr>
				<th class="list-header-unsorted">
					<?php print _t('Project'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Specimen'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Media'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Downloads'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach($va_downloads_for_user as $vn_media_id => $va_row) {
?>	
			<tr>
				
				<td>
<?php
					print $va_row[0]["name"];
?>
				</td>
				<td>
<?php
					print $va_row[0]["specimen"];
?>
				</td>
				<td>
<?php
					print "<b>M".$vn_media_id."</b>";
?>
				</td>
				<td>
<?php
					print "<b>Total: </b>".sizeof($va_row);
					print "<br/>";
					$va_downloads_by_file_id = array();
					foreach($va_row as $va_download_info){
						$va_downloads_by_file_id[$va_download_info["media_file_id"]][] = true;
					}
					foreach($va_downloads_by_file_id as $vn_file_id => $va_downloads_for_file){
						print "<b>M".$vn_media_id.(($vn_file_id) ? "-".$vn_file_id : "").":</b> ".sizeof($va_downloads_for_file)."<br/>";
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
			<?php print _t('no user information'); ?>
		</H2>
<?php			
	}
?>