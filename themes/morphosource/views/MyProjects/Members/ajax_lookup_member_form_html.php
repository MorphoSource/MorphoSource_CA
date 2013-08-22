<?php
$va_errors = $this->getVar("errors");
if(!is_array($va_errors)){
	$va_errors = array();
}
print caFormTag($this->request, 'lookUpMember', 'lookupMemberForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));	
?>
	<div class="tealRule" style="margin-top:50px;"><!-- empty --></div>
	<H1>
		<?php print _t("Invite Members"); ?>
	</H1>

<?php
	print "<div class='formLabel'>Enter the email address of the individual(s) you would like to invite to your project, enter multiple email addresses separated by commas:<br/>";
	if($va_errors["member_email"]){
		print "<div class='formErrors'>".$va_errors["member_email"]."</div>";
	}
	print "<input type='text' name='member_email' size='90' value='".$this->getVar("member_email")."'>";
	print "</div>";
	
?>
	<input type="hidden" name="form_submitted" value="1">
	<div class="formButtons tealTopBottomRule">
		<a href="#" name="save" class="button buttonSmall" onclick="jQuery('#lookupMemberForm').submit(); return false;"><?php print _t("Invite Member"); ?></a>
	</div><!-- end formButtons -->
</form>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#lookupMemberForm').submit(function(e){		
			jQuery('#formArea').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Members', 'lookUpMember'); ?>',
				jQuery('#lookupMemberForm').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>