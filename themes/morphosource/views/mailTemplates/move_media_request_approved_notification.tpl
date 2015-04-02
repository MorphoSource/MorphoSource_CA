<?php
	$t_user = $this->getVar('user');
	$t_media = $this->getVar('media');
	$t_project = $this->getVar('project');
	$t_movement_request = $this->getVar('movementRequest');
	$o_request = $this->getVar('request');
?>
<p>Dear <?php print $t_user->get('fname').' '.$t_user->get('lname'); ?>,</p>

<p>Your request to <?php print (($t_movement_request->get("type") == 1) ? "move" : "share"); ?> media <strong>M<?php print $t_media->getPrimaryKey(); ?></strong> <?php print (($t_movement_request->get("type") == 1) ? "to" : "with"); ?> Morphosource project <strong><?php print $t_project->get('name'); ?></strong> has been approved.</p>

<?php
	if(($t_movement_request->get("type") == 1)){
		print "<p>You will still retain read only access to the media.</p>";
	}
?>
<p>Thank you,<br/> The MorphoSource system administrators</p>


<p><?php print $o_request->config->get("site_host"); ?></p>