<?php
	$t_parent = $this->getVar("parent");
	$t_specimen = new ms_specimens();
	$vb_copyright_restrictions = false;
	if($t_parent->get("media_id")){
		$vb_derivative_access = false;
		if($t_parent->get("published") > 0){
			$vb_derivative_access = true;
		}else{
			$t_project = new ms_projects();
			$vb_derivative_access = $t_project->isMember($this->request->user->get("user_id"), $t_parent->get("project_id"));
		}
?>
		<div style='padding:20px; width:50%; background-color:#FFF; margin-bottom:20px; margin-left:auto; margin-right:auto;'>
			<a href="#" class="blueText" style="float:right;" onClick="jQuery('#media_derivative_id').val(''); jQuery('#msMediaDerivativeID').val(''); jQuery('#derivativePreview').html(''); return false;"><i class='fa fa-times-circle'></i></a>
<?php
		$va_parent_media = $t_parent->getPreviewMediaFile(null, array("icon"), ($vb_derivative_access) ? false : true);
		# --- check if copyright allows derivatives
		if(in_array($t_parent->get("copyright_license"), array(6,7,8))){
			$vb_copyright_restrictions = true;
			print "<div class='formErrors' style='text-align:center;'>";
			print "This media group's copyright license has been set to:<br/><i>".$t_parent->getChoiceListValue("copyright_license", $t_parent->get("copyright_license"))."</i><br/>and does not permit modifications or derivatives.";
			print "<br/><br/></div>";
		}
		if(is_array($va_parent_media) && sizeof($va_parent_media)){
			print "<div style='float:left; padding-right:20px;'>".$va_parent_media["media"]["icon"]."</div>";
		}
		print "<b>M".$t_parent->get("media_id")."</b><br/>";
		print $t_parent->get("title")."<br/>";
		print $t_specimen->getSpecimenName($t_parent->get("specimen_id"));
		print "<div style='clear:both;'></div>";
		if(!$vb_copyright_restrictions){
			print "<p style='text-align:center;'>".caNavLink($this->request, _t("Create Derivative Media Group"), "button buttonSmall", "MyProjects", "Media", "form", array("lookup_derived_from_media_id" => $t_parent->get("media_id")))."</p>";
		}
		print "</div>";
	}
?>