<?php
$va_errors = $this->getVar("errors");
if(!is_array($va_errors)){
	$va_errors = array();
}
$vn_user_id = $this->getVar("user_id");
$vs_email_address = $this->getVar("email_address");
//print caFormTag($this->request, 'inviteMember', 'addMemberForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
<form>
	<div class="tealRule" style="margin-top:50px;"><!-- empty --></div>
	<H1>
		<?php print _t("Invite Member"); ?>
	</H1>

<?php
	if(!$vn_user_id){
		print "<div class='formLabel'>The individual with the email address maria@blah.com is not yet a member of the Morphobank community. Please provide their first and last name so they may be added.</div>";
		print "<div class='formLabel'>First Name:<br/>";
		if($va_errors["member_fname"]){
			print "<div class='formErrors'>".$va_errors["member_fname"]."</div>";
		}
		print "<input type='text' name='member_fname' size='90'>";
		print "</div>";
		print "<div class='formLabel'>Last Name:<br/>";
		if($va_errors["member_lname"]){
			print "<div class='formErrors'>".$va_errors["member_lname"]."</div>";
		}
		print "<input type='text' name='member_lname' size='90'>";
		print "</div>";
	}
	print "<div class='formLabel'>Enter a message to include in the email invitation sent to the individual:<br/>";
	print "<textarea style='width:510px; height:200px;' name='message'></textarea>";
	print "</div>";
	
?>
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="document.forms.addMemberForm.submit(); return false;"><?php print _t("Invite Member"); ?></a>
	</div><!-- end formButtons -->
</form>


<script type="text/javascript">
	jQuery(document).ready(function() {
		$('#addMemberForm').submit(function(){		
			jQuery('#formArea').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Members', 'inviteMember'); ?>',
				$('#addMemberForm').serialize()
			);
		
			return false;
		});
	});
</script>
 