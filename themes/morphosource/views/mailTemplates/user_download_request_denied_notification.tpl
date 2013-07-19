<?php
	$t_user = $this->getVar('user');
	$t_media = $this->getVar('media');
	$t_project = $this->getVar('project');
	$t_download_request = $this->getVar('downloadRequest');
	$o_request = $this->getVar('request');
	if (!($vs_url_root = $o_request->config->get('ca_url_root'))) { $vs_url_root = '/'; }
?>
<p>Dear <?php print $t_user->get('fname').' '.$t_user->get('lname'); ?>,</p>

<p>Your request to download media <strong>M<?php print $t_media->getPrimaryKey(); ?></strong> in Morphosource project <strong><?php print $t_project->get('name'); ?></strong> has been denied.</p>