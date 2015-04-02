<?php
	print caFormTag($this->request, 'moveMedia', 'mediaMoveFormComment', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
?>
		<b>You are requesting to move this media record to a project you are not a member of, <?php print $this->getVar("move_project_name"); ?>.</b>  Your request will be sent to the project administrator, <?php print $this->getVar("move_project_admin_name").", ".$this->getVar("move_project_admin_email"); ?> with the following message:<br/>
		<textarea name="moveComment" rows="4" style="width:100%; margin-bottom:3px;"></textarea>
		<a href='#' name='save' class='button buttonSmall' onclick='jQuery("#mediaMoveFormComment").submit(); return false;'>Send Request</a>
		<input type="hidden" name="media_id" value="<?php print $this->getVar("media_id"); ?>">
		<input type="hidden" name="move_project_id" value="<?php print $this->getVar("move_project_id"); ?>">
		<input type="hidden" name="move_form_submit" value="1">
		<br/><br/><i>You will be notified via email when your request has been granted or denied.</i>
	</form>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#mediaMoveFormComment').submit(function(e){		
			jQuery('#mediaMove').load(
				'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'moveMedia'); ?>',
				jQuery('#mediaMoveFormComment').serialize()
			);
			e.preventDefault();
			return false;
		});
	});
</script>				
