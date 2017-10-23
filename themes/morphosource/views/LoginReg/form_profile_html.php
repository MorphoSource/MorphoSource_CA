<?php
	$va_errors = $this->getVar("errors");
	$t_user = $this->getVar("t_user");
?>
<script type="text/javascript">
	// initialize CA Utils
	caUI.initUtils();

</script>
<div class="blueRule"><!-- empty --></div>
<H1>
	<?php print _t("Account Preferences"); ?>
</H1>
<div id="formArea">
<p><i>* indicates required fields.</i></p>

<?php
	if($va_errors["general"]){
		print "<div class='alert alert-danger'>".$va_errors["general"]."</div>";
	}
?>
	<form id="ProfileForm" action="<?php print caNavUrl($this->request, "", "LoginReg", "profileSave"); ?>" class="form-horizontal" role="form" method="POST">
<?php
		if($va_errors["password"]){
			print "<div class='formErrors'>".$va_errors["password"]."</div>";
		}
?>
		<div class='formLabel'><?php print _t('Reset Password (Only enter if you would like to change your current password)'); ?><br/>
			<input type="password" name="password" size="40" class="form-control" />
		</div>
		<div class='formLabel'><?php print _t('Re-Type password'); ?><br/>
			<input type="password" name="password2" size="40" class="form-control" />
		</div>
<?php
		foreach(array("fname", "lname", "email") as $vs_field){
			if($va_errors[$vs_field]){
				print "<div class='formErrors'>".$va_errors[$vs_field]."</div>";
			}	
			print $t_user->htmlFormElement($vs_field,"<div class='formLabel'>^LABEL<br/>^ELEMENT</div>\n");
		}
		$va_profile_settings = $this->getVar("profile_settings");
		if(is_array($va_profile_settings) and sizeof($va_profile_settings)){
			foreach($va_profile_settings as $vs_field => $va_profile_element){
				if($vs_field == "user_upload_directory"){
					continue;
				}
				if($vs_field == "user_profile_terms_conditions"){
					print "<input type='hidden' name='user_profile_terms_conditions'>";
				}
				if($va_errors[$vs_field]){
					print "<div class='formErrors'>".$va_errors[$vs_field]."</div>";
				}				
				if($va_profile_element["label"] == "Other"){
					print "<div class='formLabel other' style='font-weight:normal;'>".$va_profile_element["label"].": ".$va_profile_element["element"]."</div>";
				}else{
					print "<div class='formLabel'>";
					print $va_profile_element["label"]."<br/><span style='font-weight:normal;'>".$va_profile_element["element"]."</span>";
					print "</div>";
				}
			}
		}
?>		
		<div class="formButtons tealTopBottomRule">
			<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#ProfileForm').submit(); return false;"><?php print _t("Save"); ?></a>
		</div>
	</form>
</div>
