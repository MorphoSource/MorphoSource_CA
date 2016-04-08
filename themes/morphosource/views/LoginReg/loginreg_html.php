<?php
	global $g_ui_locale;
?>
			<div id="loginForm">
				<div class="blueRule" style="margin-bottom:0px;"><!-- empty --></div>
				<H1><?php print _t("Login"); ?></H1>
<?php
				if($this->getVar("loginMessage")){
					print "<div class='formErrors'>".$this->getVar("loginMessage")."</div>";
				}
?>
				<form action="<?php print caNavUrl($this->request, '', 'LoginReg', 'login', array()); ?>" method="post" name="login">
					<div>
						<b><?php print _t("Username"); ?></b><br/>
						<input type="text" name="username" />
					</div>
					<div>
						<b><?php print _t("Password"); ?></b><br/>
						<input type="password" name="password" />
					</div>
					<div>
						<a href="#" name="login" class="button buttonSmall" onclick="document.forms.login.submit(); return false;"><?php print _t("Login"); ?></a>
					</div>
					<input type="submit" value="Submit" style="position: absolute; top: 0; left: 0; z-index: 0; width: 1px; height: 1px; visibility: hidden;" /> 
					
				</form>
				<div style="<?php print ($this->getVar("resetFormOpen")) ? "display:none;" : ""; ?>"><a href="#"  onclick='jQuery("#resetPasswordLink").slideDown(1); $("#resetPasswordLink").slideUp(1); jQuery("#resetPassword").slideDown(250); return false;' id="resetPasswordLink"><?php print _t("Forgot your password?"); ?></a></div>
				<div id="resetPassword" style="<?php print ($this->getVar("resetFormOpen")) ? "" : "display:none;"; ?>">
					<div class="blueRule" style="margin:50px 0px 0px 0px;"><!-- empty --></div>
					<h1><?php print _t("Reset your password"); ?></h1>
					<p>
<?php
					print _t("To reset your password enter the e-mail address you used to register below. A message will be sent to the address with instructions on how to reset your password.");
?>
					</p>
			
					<form action="<?php print caNavUrl($this->request, '', 'LoginReg', 'resetSend'); ?>" name="reset_password" method="post">
<?php
						if($this->getVar("reset_email_error")){
							print "<div class='formErrors' style='text-align: left;'>".$this->getVar("reset_email_error")."</div>";
						}

?>
						<div><b><?php print _t("Your e-mail address"); ?></b><br/>
							<input type="text" name="reset_email" value="" />&nbsp;&nbsp;<a href="#" class="button buttonCircle" onclick="document.forms.reset_password.submit(); return false;"><?php print _t("Go"); ?></a>
						</div>
						<input type="hidden" name="action" value="send">
					</form>
				</div>
				
			</div><!-- end loginForm -->
			<div id="registerForm">
				<div class="blueRule" style="margin-bottom:0px;"><!-- empty --></div>
				<h1><?php print _t("Register"); ?></h1>
				<form action="<?php print caNavUrl($this->request, '', 'LoginReg', 'register', array()); ?>" method="post" name="registration">
				<p>
<?php
					print _t("As a member, you can download media from MorphoSource.");
?>
				</p>
<?php
				if($this->getVar("regMessage")){
					print "<div class='formErrors'>".$this->getVar("regMessage")."</div>";
				}
					$va_errors = $this->getVar("reg_errors");
					
					if($va_errors["fname"]){
						print "<div class='formErrors' style='text-align: left;'>".$va_errors["fname"]."</div>";
					}
					print $this->getVar("fname");
					if($va_errors["lname"]){
						print "<div class='formErrors' style='text-align: left;'>".$va_errors["lname"]."</div>";
					}
					print $this->getVar("lname");
					if($va_errors["email"]){
						print "<div class='formErrors' style='text-align: left;'>".$va_errors["email"]."</div>";
					}
					print $this->getVar("email");
					if($va_errors["security"]){
						print "<div class='formErrors' style='text-align: left;'>".$va_errors["security"]."</div>";
					}
					$vn_num1 = rand(1,10);
					$vn_num2 = rand(1,10);
					$vn_sum = $vn_num1 + $vn_num2;
?>
					<div><b><?php print _t("Security Question (to prevent SPAMbots)"); ?></b><br/>
						<span id="securityText"><?php print $vn_num1; ?> + <?php print $vn_num2; ?> = </span><input name="security" value="" id="security" type="text" size="3" />
					</div>
<?php
					if($va_errors["password"]){
						print "<div class='formErrors' style='text-align: left;'>".$va_errors["password"]."</div>";
					}
					print $this->getVar("password");
?>
					<div><b><?php print _t('Re-Type password'); ?></b><br/><input type="password" name="password2" size="60" /></div>
<?php										
					// Output user profile settings if defined
					$va_user_profile_settings = $this->getVar('profile_settings');
					if (is_array($va_user_profile_settings) && sizeof($va_user_profile_settings)) {
						foreach($va_user_profile_settings as $vs_field => $va_info) {
							if($va_errors[$vs_field]){
								print "<div class='formErrors' style='text-align: left;'>".$va_errors[$vs_field]."</div>";
							}
							if($va_info["label"] == "Other"){
								print "<div class='other'>".$va_info["label"].": ".$va_info["element"]."</div>";
							}else{
								print "<div><b>".$va_info["label"]."</b><br/>".$va_info["element"]."</div>";
							}
						}
					}
?>
					<div><a href="#" name="register" class="button buttonSmall" onclick="document.forms.registration.submit(); return false;"><?php print _t("Register"); ?></a></div>
								
					<input type="hidden" name="sum" value="<?php print $vn_sum; ?>">
				</form>
			</div><!-- end registerForm -->