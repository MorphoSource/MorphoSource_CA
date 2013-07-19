<?php
	$t_author = $this->getVar('author');
	$t_user = $this->getVar('user');
	$t_media = $this->getVar('media');
	$t_project = $this->getVar('project');
	$t_download_request = $this->getVar('downloadRequest');
?>
<p>Dear <?php print $t_author->get('fname').' '.$t_author->get('lname'); ?>,</p>

<p><?php print $t_user->get('fname').' '.$t_user->get('lname'); ?> has requested download access to media <strong>M<?php print $t_media->getPrimaryKey(); ?></strong> in your project <strong><?php print $t_project->get('name'); ?></strong>.</p>
<p>The user described they proposed usage as: <em><?php print $t_download_request->get('request'); ?></em></p>

<p>Log into your project dashboard to approve or deny this request.</p>