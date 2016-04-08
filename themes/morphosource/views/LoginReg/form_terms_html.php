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
	<?php print _t("Please update your account information"); ?>
</H1>
<p>
	MorphoSource terms and conditons have changed, please accept the revised terms and conditions to continue using the site.  Also please take a moment to answer the following survey questions that will help us improve MorphoSource.
</p>
<div id="formArea">
<p><i>* indicates required fields.</i></p>

<?php
	if($va_errors["general"]){
		print "<div class='alert alert-danger'>".$va_errors["general"]."</div>";
	}
?>
	<form id="ProfileForm" action="<?php print caNavUrl($this->request, "", "LoginReg", "termsSave"); ?>" class="form-horizontal" role="form" method="POST">
<?php
		$va_profile_settings = $this->getVar("profile_settings");
		if(is_array($va_profile_settings) and sizeof($va_profile_settings)){
			foreach($va_profile_settings as $vs_field => $va_profile_element){
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
<script type="text/javascript">
	jQuery(document).ready(function() {
		$('body').click(function(evt){    
		   if(evt.target.id == "formArea")
			  return;
		   //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
		   if($(evt.target).closest('#formArea').length)
			  return;             

			// alert to submit form
			alert("Please agree to the terms and conditions and save the form");
			return false;
		});
	});
</script>
