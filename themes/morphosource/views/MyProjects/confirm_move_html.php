<?php
	$vn_specimen_id = $this->getVar("specimen_id");
	$vn_media_id = $this->getVar("media_id");
	$vn_proj_from = $this->getVar("proj_from");
	$vn_proj_to = $this->getVar("proj_to");

	if($vn_specimen_id && $vn_media_id){
		$this->notification->addNotification(
			"Cannot move specimen and media at the same time", 
			__NOTIFICATION_TYPE_ERROR__);
 		$this->response->setRedirect(
 			caNavUrl($this->request, "MyProjects", "Dashboard", "projectList"));
	}elseif($vn_specimen_id){
		$vb_type = 1;
		$vs_addt_text = "Media groups associated with this specimen record will not be moved.";
		$vs_move_url = caNavUrl(
			$this->request, 'MyProjects', 'Specimens', 'moveSpecimen', 
			array(
				"specimen_id" => $vn_specimen_id, 
				"move_project_id" => $vn_proj_to
		));
	}elseif($vn_media_id){
		$vb_type = 0;
		$vs_addt_text = "Additionally, the administrator of that project will have to approve the move for it to take effect. The media group will still appear in your project, but with read-only access. Media files associated with this media group <b>will</b> be moved. The specimen record associated with this media group <b>will not</b> be moved.";
		$vs_move_url = caNavUrl(
			$this->request, 'MyProjects', 'Media', 'moveMedia', 
			array(
				"media_id" => $vn_media_id, 
				"move_project_id" => $vn_proj_to
		));
	}

	$t_from_project = new ms_projects($vn_proj_from);
	$t_to_project = new ms_projects($vn_proj_to);

	$x_img_url = $this->request->getThemeUrlPath().
		'/graphics/morphosource/ic_clear_black_24dp_1x.png';

?>
<div class="panelDialog">
	<a href="#" style="float:right;" onclick="msMediaPanel.hidePanel(); return false;">
		<img src="<?php print $x_img_url; ?>"></img>
	</a>
	<div style='text-align: center;'>
	<H1>
		Move <?php print ($vb_type ? "Specimen" : "Media Group"); ?>
	</H1>
	<div class="blueRule"><!-- empty --></div>
<?php
	print "<p>Are you sure you want to move the selected ".
		($vb_type ? "specimen" : "media group")." record from project <b>".
		$t_from_project->get("name")."</b> to project <b>".
		$t_to_project->get("name")."</b>?</p>";
	print "<p>If you do not own the project to which you are moving this ".
		($vb_type ? "specimen record" : "media group record").
		", you will lose editing access to the ".
		($vb_type ? "specimen" : "media group")." record. ".$vs_addt_text."</p>";
?>
	</div>
	<div style='text-align: center;'>
<?php

	print "<a href='".$vs_move_url."' class='button buttonLarge'>"._t("Confirm Move")."</a>";
?>
	</div>
</div>	
