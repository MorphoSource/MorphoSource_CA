<?php
	$va_downloads = $this->getVar("download_info");
	if(sizeof($va_downloads)){
		foreach($va_downloads as $va_download){
			print "<div style='border-bottom:1px solid #DEDEDE; padding:5px;'>";
			print "<b>M".$va_download["media_id"].(($va_download["media_file_id"]) ? "-".$va_download["media_file_id"] : "")."</b>, ".$va_download["specimen"].", ".$va_download["name"];
			print "</div>";
		}
	}
?>