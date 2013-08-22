<?php
$va_existing_members = $this->getVar("exisiting_members");
$va_new_members = $this->getVar("new_members");
$va_errors = $this->getVar("invite_errors");
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
	$va_exisiting_member_ids = array();
	if(sizeof($va_existing_members)){
		print "<div class='formLabel' style='font-weight:normal;'>";
		foreach($va_existing_members as $va_existing_member_info){
			$va_exisiting_member_ids[] = $va_existing_member_info["user_id"];
			print $va_existing_member_info['fname']." ".$va_existing_member_info['lname']." [".$va_existing_member_info['email']."], ";
		}
		if(sizeof($va_existing_members) > 1){
			print " are already members of the MorphoSource community.";		
		}else{
			print " is already a member of the MorphoSource community.";
		}
		print "</div>";
	}
	print "<input type='hidden' value='".sizeof($va_new_members)."' name='num_new_members'>";
	if(sizeof($va_new_members)){
		print "<div class='formLabel' style='font-weight:normal;'>The following individual".((sizeof($va_new_members) > 1) ? "s are not yet members" : " is  not yet a member")." of the MorphoSource community. Please provide their first and last name so they may be added.<br/>";
		$vn_i = 1;
		foreach($va_new_members as $vs_email){
			print "<div class='formLabel'>".$vs_email."<br/>";
			if($va_errors["member_fname".$vn_i]){
				print "<div class='formErrors'>".$va_errors["member_fname".$vn_i]."</div>";
			}
			if($va_errors["member_lname".$vn_i]){
				print "<div class='formErrors'>".$va_errors["member_lname".$vn_i]."</div>";
			}
			print "First Name: ";
			print "<input type='text' name='member_fname".$vn_i."' size='20' value='".$this->getVar("member_fname".$vn_i)."'>";
			print "&nbsp;&nbsp;&nbsp;&nbsp;Last Name: ";
			print "<input type='text' name='member_lname".$vn_i."' size='20' value='".$this->getVar("member_lname".$vn_i)."'>";
			print "<input type='hidden' name='member_email".$vn_i."' value='".$vs_email."'>";
			print "</div>";
			$vn_i++;
		}
		print "</div>";
	}
	print "<div class='formLabel'>Enter a message to include in the email invitation".(((sizeof($va_new_members) + sizeof($va_existing_members)) > 1) ? "s" : "")." (optional):<br/>";
	print "<textarea style='width:510px; height:200px;' name='member_message'>".$this->getVar("member_message")."</textarea>";
	print "</div>";
	if(sizeof($va_existing_members)){
		print '<input type="hidden" name="existing_member_ids" value="'.implode(",", $va_exisiting_member_ids).'">';
	}
?>
	<input type="hidden" name="form_submitted" value="1">
	<input type="hidden" name="member_email" value="<?php print $this->getVar("member_email"); ?>">
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
 