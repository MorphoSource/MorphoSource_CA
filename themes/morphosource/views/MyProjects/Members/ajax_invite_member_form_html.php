<?php
$pn_member_user_id = $this->getVar("member_user_id");
$ps_member_email = $this->getVar("member_email");
$ps_member_lname = $this->getVar("member_lname");
$ps_member_fname = $this->getVar("member_fname");
$va_errors = $this->getVar("errors");
if(!is_array($va_errors)){
	$va_errors = array();
}
print caFormTag($this->request, 'inviteMember', 'inviteMemberForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
<form>
	<div class="tealRule" style="margin-top:50px;"><!-- empty --></div>
	<H1>
		<?php print _t("Invite Member"); ?>
	</H1>

<?php
	if($va_errors["general"]){
		print "<p class='formErrors'>".$va_errors["general"]."</p>";
	}
	if(!$pn_member_user_id){
		print "<div class='formLabel'>The individual with the email address ".$ps_member_email." is not yet a member of the MorphoSource community. Please provide their first and last name so they may be added.</div>";
		print "<div class='formLabel'>First Name:<br/>";
		if($va_errors["member_fname"]){
			print "<div class='formErrors'>".$va_errors["member_fname"]."</div>";
		}
		print "<input type='text' name='member_fname' size='90' value='".$this->getVar("member_fname")."'>";
		print "</div>";
		print "<div class='formLabel'>Last Name:<br/>";
		if($va_errors["member_lname"]){
			print "<div class='formErrors'>".$va_errors["member_lname"]."</div>";
		}
		print "<input type='text' name='member_lname' size='90' value='".$this->getVar("member_lname")."'>";
		print "</div>";
	}else{
		print "<div class='formLabel'>".$ps_member_fname." ".$ps_member_lname.", ".$ps_member_email." is already a member of the MorphoSource community.</div>";
	}
	print "<div class='formLabel'>Enter a message to include in the email invitation (optional):<br/>";
	print "<textarea style='width:510px; height:200px;' name='member_message'>".$this->getVar("member_message")."</textarea>";
	print "</div>";
	if($pn_member_user_id){
		print '<input type="hidden" name="member_user_id" value="'.$pn_member_user_id.'">';
	}
	print '<input type="hidden" name="member_email" value="'.$ps_member_email.'">';
?>
	<input type="hidden" name="form_submitted" value="1">
	<div class="formButtons tealTopBottomRule">
		<div style="float:right;"><a href="#" class="button buttonSmall" onclick="jQuery('#formArea').load('<?php print caNavUrl($this->request, 'MyProjects', 'Members', 'lookUpMember'); ?>', function() { $('#formArea').show('slide'); }); return false;"><?php print _t("Back"); ?></a></div>
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#inviteMemberForm').submit(); return false;"><?php print _t("Invite Member"); ?></a>
	</div><!-- end formButtons -->
</form>


<script type="text/javascript">
	jQuery(document).ready(function() {
		$('#inviteMemberForm').submit(function(){		
			jQuery('#formArea').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Members', 'inviteMember'); ?>',
				$('#inviteMemberForm').serialize(),
				function(responseText, textStatus, XMLHttpRequest){
					if(responseText == ''){
						window.location.href = "<?php print caNavUrl($this->request, 'MyProjects', 'Members', 'listForm'); ?>";
					}
				}
			);
		
			return false;
		});
	});
</script>
 