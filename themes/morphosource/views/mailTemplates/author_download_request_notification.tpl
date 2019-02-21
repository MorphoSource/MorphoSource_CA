<?php
	$t_author = $this->getVar('author');
	$t_user = $this->getVar('user');
	$t_media = $this->getVar('media');
	$t_project = $this->getVar('project');
	$t_project_owner = $this->getVar('project_owner');
	$t_specimen = $this->getVar('specimen');
	$t_download_request = $this->getVar('downloadRequest');
?>
<p><?php print $t_user->get('fname').' '.$t_user->get('lname').', '.$t_user->get('email'); ?> has requested download access to the following media:</p>

<p>
Media Name: 
<?php
	print "<strong><a href='http://www.morphosource.org/Detail/MediaDetail/Show/media_id/".$t_media->getPrimaryKey()."' target='_blank'>";
	print "M".$t_media->getPrimaryKey();
	print "</a></strong>";
?>
<br/>
Description: 
<?php
	if ($t_media->get('element')) {
		print $t_media->get('element');
	} else {
		print "None";
	}
?>
<br/>
Media Creator: 
<?php
	print $t_author->get('fname').' '.$t_author->get('lname').', '.$t_author->get('email');
?>
<br/>
Specimen: 
<?php 
	if ($t_specimen->getPrimaryKey()) {
		print "<strong><a href='http://www.morphosource.org/Detail/SpecimenDetail/Show/specimen_id/".$t_specimen->getPrimaryKey()."' target='_blank'>";
		print $t_specimen->getSpecimenName();
		print "</a></strong>";
	} else {
		print "None";
	} 
?>
<br/>
<br/>
Project: 
<?php
	print "<strong><a href='http://www.morphosource.org/MyProjects/Dashboard/dashboard/select_project_id/".$t_project->getPrimaryKey()."' target='_blank'>";
	print $t_project->get('name');
	print "</a></strong>";
?>
<br/>
Project Owner:
<?php
	print $t_project_owner->get('fname').' '.$t_project_owner->get('lname').', '.$t_project_owner->get('email'); 
?>
</p>

<p>The user described the proposed usage as: <em><?php print $t_download_request->get('request'); ?></em></p>

<p>
<?php
	print "Log into your <a href='http://www.morphosource.org/MyProjects/Dashboard/dashboard/select_project_id/".$t_project->getPrimaryKey()."' target='_blank'>project dashboard</a> to approve or deny this request.";
?>
</p>